<?php
require_once 'config.php';

// Traitement des formulaires
if ($_POST) {
    if (isset($_POST['action']) && $_POST['action'] === 'add_stop') {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO equipment_stops 
                (equipment, reason, start_time, end_time, duration_minutes, comment, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            
            // Traitement sécurisé des données POST
            $equipment = $_POST['equipment'] ?? '';
            
            // Traitement des dates
            $start_time = $_POST['start_time'] ?? date('Y-m-d H:i:s');
            $end_time = !empty($_POST['end_time']) ? $_POST['end_time'] : null;
            $duration = null;
            
            // Calcul de la durée si les deux dates sont présentes
            if ($start_time && $end_time) {
                $start = new DateTime($start_time);
                $end = new DateTime($end_time);
                $diff = $end->diff($start);
                $duration = $diff->i + ($diff->h * 60) + ($diff->d * 24 * 60);
            }
            
            // Traitement du motif
            $reason = $_POST['reason'] ?? 'Motif non spécifié';
            $comment = $_POST['comment'] ?? '';
            
            $stmt->execute([
                $equipment,
                $reason,
                $start_time,
                $end_time,
                $duration,
                $comment
            ]);
            
            $success_message = "Arrêt enregistré avec succès.";
        } catch (Exception $e) {
            $error_message = "Erreur lors de l'enregistrement : " . $e->getMessage();
        }
    }
}

// Récupérer les arrêts récents
function getRecentStops($pdo, $limit = 20) {
    try {
        $stmt = $pdo->prepare("
            SELECT *, 
                   CASE 
                       WHEN end_time IS NULL THEN 'En cours'
                       ELSE 'Terminé'
                   END as status
            FROM equipment_stops 
            ORDER BY created_at DESC 
            LIMIT " . intval($limit)
        );
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Error in getRecentStops: " . $e->getMessage());
        return [];
    }
}

// Récupérer les pauses machines actives
function getActivePauses($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT * 
            FROM equipment_stops 
            WHERE end_time IS NULL 
            AND (comment LIKE '%pause%' OR comment LIKE '%Pause%')
            ORDER BY start_time DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Error in getActivePauses: " . $e->getMessage());
        return [];
    }
}

$recentStops = getRecentStops($pdo);
$activePauses = getActivePauses($pdo);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Arrêts - TRS Blanchisserie</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="page-header-modern">
            <h2>Gestion des arrêts</h2>
            <button class="btn btn-success" onclick="showDeclareArretModal()">+ Déclarer un arrêt</button>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?= $success_message ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-error"><?= $error_message ?></div>
        <?php endif; ?>

        <!-- Filtres -->
        <div class="form-section">
            <h3>🔍 Filtres</h3>
            <form class="filters-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="filter-equipment">Équipement</label>
                        <select id="filter-equipment">
                            <option value="">Tous les équipements</option>
                            <optgroup label="Machines">
                                <option value="machine-13">Machine 13</option>
                                <option value="machine-20">Machine 20</option>
                                <option value="machine-50">Machine 50</option>
                                <option value="machine-70">Machine 70</option>
                            </optgroup>
                            <optgroup label="Séchoirs">
                                <option value="sechoir-1">Séchoir 1</option>
                                <option value="sechoir-2">Séchoir 2</option>
                                <option value="sechoir-3">Séchoir 3</option>
                                <option value="sechoir-4">Séchoir 4</option>
                            </optgroup>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="filter-type">Type d'arrêt</label>
                        <select id="filter-type">
                            <option value="">Tous types</option>
                            <option value="planifie">Planifié</option>
                            <option value="non-planifie">Non planifié</option>
                            <option value="inter-cycle">Inter-cycle</option>
                            <option value="nettoyage">Nettoyage</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="filter-date-start">Date début</label>
                        <input type="date" id="filter-date-start" value="<?= date('Y-m-d') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="filter-date-end">Date fin</label>
                        <input type="date" id="filter-date-end" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-primary" onclick="applyFilters()">🔍 Filtrer</button>
                    <button type="button" class="btn btn-secondary" onclick="resetFilters()">🔄 Réinitialiser</button>
                </div>
            </form>
        </div>

        <!-- Table des arrêts -->
        <div class="arrets-table-section">
            <div class="table-container">
                <table class="arrets-table">
                    <thead>
                        <tr>
                            <th>Équipement</th>
                            <th>Type</th>
                            <th>Motif</th>
                            <th>Début</th>
                            <th>Durée</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="stops-table-body">
                        <!-- Données d'exemple selon l'image -->
                        <tr>
                            <td><strong>Machine 70kg</strong></td>
                            <td><span class="badge-type inter-cycle">INTER CYCLE</span></td>
                            <td>Changement chariot</td>
                            <td>15/01/2024 14:15:00</td>
                            <td><span class="duration-ongoing">888424 min (en cours)</span></td>
                            <td><span class="status-badge open">Ouvert</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-icon view" title="Voir">👁️</button>
                                    <button class="btn-icon edit" title="Modifier">✏️</button>
                                    <button class="btn-icon close" title="Clôturer">✅</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Séchoir 3</strong></td>
                            <td><span class="badge-type nettoyage">NETTOYAGE</span></td>
                            <td>Nettoyage filtre</td>
                            <td>15/01/2024 13:30:00</td>
                            <td>30 min</td>
                            <td><span class="status-badge closed">Clôturé</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-icon view" title="Voir">👁️</button>
                                    <button class="btn-icon edit" title="Modifier">✏️</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Machine 13kg</strong></td>
                            <td><span class="badge-type inter-cycle">INTER CYCLE</span></td>
                            <td>Pause opérateur</td>
                            <td>23/09/2025 13:57:08</td>
                            <td><span class="duration-ongoing">22 min (en cours)</span></td>
                            <td><span class="status-badge open">Ouvert</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-icon view" title="Voir">👁️</button>
                                    <button class="btn-icon edit" title="Modifier">✏️</button>
                                    <button class="btn-icon close" title="Clôturer">✅</button>
                                </div>
                            </td>
                        </tr>
                        
                        <?php foreach ($recentStops as $stop): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($stop['equipment']) ?></strong></td>
                            <td>
                                <span class="badge-type non-planifie">NON PLANIFIÉ</span>
                            </td>
                            <td><?= htmlspecialchars($stop['reason']) ?></td>
                            <td><?= date('d/m/Y H:i:s', strtotime($stop['start_time'])) ?></td>
                            <td>
                                <?php if ($stop['duration_minutes']): ?>
                                    <?= $stop['duration_minutes'] ?> min
                                <?php else: ?>
                                    <span class="duration-ongoing">En cours</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge <?= $stop['status'] === 'Terminé' ? 'closed' : 'open' ?>">
                                    <?= $stop['status'] === 'Terminé' ? 'Clôturé' : 'Ouvert' ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-icon view" onclick="viewStop(<?= $stop['id'] ?>)" title="Voir">👁️</button>
                                    <button class="btn-icon edit" onclick="editStop(<?= $stop['id'] ?>)" title="Modifier">✏️</button>
                                    <?php if ($stop['status'] === 'En cours'): ?>
                                        <button class="btn-icon close" onclick="closeStop(<?= $stop['id'] ?>)" title="Clôturer">✅</button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pauses machines actives -->
        <?php if (!empty($activePauses)): ?>
        <div class="form-section">
            <h3>⏸️ Pauses Machines Actives</h3>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Machine</th>
                            <th>Motif</th>
                            <th>Début</th>
                            <th>Durée actuelle</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activePauses as $pause): ?>
                        <tr>
                            <td><?= htmlspecialchars($pause['equipment_name']) ?></td>
                            <td><?= htmlspecialchars($pause['stop_code']) ?></td>
                            <td><?= date('H:i', strtotime($pause['start_time'])) ?></td>
                            <td>
                                <span class="timer-display" data-start="<?= $pause['start_time'] ?>">
                                    Calcul en cours...
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-success btn-sm" onclick="resumeMachine('<?= $pause['equipment_name'] ?>', <?= $pause['id'] ?>)">
                                    ▶️ Reprendre
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Formulaire de déclaration d'arrêt -->
        <div class="form-section">
            <h3>➕ Déclarer un nouvel arrêt</h3>
            <form method="POST" class="stop-form">
                <input type="hidden" name="action" value="add_stop">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="equipment">Équipement concerné *</label>
                        <select id="equipment" name="equipment" required>
                            <option value="">Sélectionner l'équipement</option>
                            <optgroup label="Machines">
                                <option value="machine-13">Machine 13</option>
                                <option value="machine-20">Machine 20</option>
                                <option value="machine-50">Machine 50</option>
                                <option value="machine-70">Machine 70</option>
                            </optgroup>
                            <optgroup label="Séchoirs">
                                <option value="sechoir-1">Séchoir 1</option>
                                <option value="sechoir-2">Séchoir 2</option>
                                <option value="sechoir-3">Séchoir 3</option>
                                <option value="sechoir-4">Séchoir 4</option>
                            </optgroup>
                            <optgroup label="Postes manuels">
                                <option value="calandre">Calandre</option>
                                <option value="repassage">Repassage</option>
                            </optgroup>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="reason">Raison de l'arrêt *</label>
                        <select id="reason" name="reason" required>
                            <option value="panne-mecanique">Panne mécanique</option>
                            <option value="panne-electrique">Panne électrique</option>
                            <option value="maintenance">Maintenance préventive</option>
                            <option value="reglage">Réglage/Mise au point</option>
                            <option value="attente-linge">Attente linge</option>
                            <option value="attente-operateur">Attente opérateur</option>
                            <option value="nettoyage">Nettoyage</option>
                            <option value="changement-production">Changement production</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="start_time">Début de l'arrêt *</label>
                        <input type="datetime-local" id="start_time" name="start_time" required 
                               value="<?= date('Y-m-d\TH:i') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="end_time">Fin de l'arrêt</label>
                        <input type="datetime-local" id="end_time" name="end_time">
                        <small>Laissez vide si l'arrêt est toujours en cours</small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="comment">Commentaire</label>
                    <textarea id="comment" name="comment" rows="4" 
                              placeholder="Détails de l'arrêt, actions entreprises, etc."></textarea>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">
                        💾 Enregistrer l'arrêt
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        🔄 Réinitialiser
                    </button>
                </div>
            </form>
        </div>

        <!-- Modal pour déclarer un arrêt -->
        <div id="declareArretModal" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Déclarer un arrêt</h3>
                    <span class="close" onclick="closeDeclareArretModal()">&times;</span>
                </div>
                <div class="modal-body">
                    <form id="declareArretForm" method="POST">
                        <input type="hidden" name="action" value="add_stop">
                        
                        <div class="form-group">
                            <label for="modal-equipment">Équipement</label>
                            <select id="modal-equipment" name="equipment" required>
                                <option value="">Sélectionner un équipement...</option>
                                <optgroup label="Machines">
                                    <option value="machine-13">Machine 13</option>
                                    <option value="machine-20">Machine 20</option>
                                    <option value="machine-50">Machine 50</option>
                                    <option value="machine-70">Machine 70</option>
                                </optgroup>
                                <optgroup label="Séchoirs">
                                    <option value="sechoir-1">Séchoir 1</option>
                                    <option value="sechoir-2">Séchoir 2</option>
                                    <option value="sechoir-3">Séchoir 3</option>
                                    <option value="sechoir-4">Séchoir 4</option>
                                </optgroup>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="modal-type">Type d'arrêt</label>
                            <select id="modal-type" name="stop_type" required>
                                <option value="">Sélectionner un type...</option>
                                <option value="non-planifie">Non planifié</option>
                                <option value="planifie">Planifié</option>
                                <option value="inter-cycle">Inter-cycle</option>
                                <option value="nettoyage">Nettoyage</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="modal-reason">Motif</label>
                            <select id="modal-reason" name="reason" required>
                                <option value="">Sélectionner un motif...</option>
                                <option value="panne-mecanique">Panne mécanique</option>
                                <option value="panne-electrique">Panne électrique</option>
                                <option value="maintenance-preventive">Maintenance préventive</option>
                                <option value="attente-operateur">Attente opérateur</option>
                                <option value="changement-production">Changement production</option>
                                <option value="nettoyage-filtre">Nettoyage filtre</option>
                                <option value="pause-operateur">Pause opérateur</option>
                                <option value="changement-chariot">Changement chariot</option>
                            </select>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="modal-start-time">Début de l'arrêt</label>
                                <input type="datetime-local" id="modal-start-time" name="start_time" required 
                                       value="<?= date('Y-m-d\TH:i') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="modal-end-time">Fin de l'arrêt</label>
                                <input type="datetime-local" id="modal-end-time" name="end_time">
                                <small>Optionnel si en cours</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="modal-comment">Commentaire</label>
                            <textarea id="modal-comment" name="comment" rows="3" placeholder="Détails de l'arrêt..."></textarea>
                        </div>

                        <div class="btn-group" style="margin-top: 20px;">
                            <button type="submit" class="btn btn-success">Déclarer</button>
                            <button type="button" class="btn btn-secondary" onclick="closeDeclareArretModal()">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal pour édition -->
        <div id="editModal" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Modifier l'arrêt</h3>
                    <span class="close" onclick="closeModal()">&times;</span>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <!-- Formulaire d'édition sera chargé ici -->
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mise à jour des timers en temps réel
        function updateTimers() {
            document.querySelectorAll('.timer-display').forEach(timer => {
                const startTime = new Date(timer.dataset.start);
                const now = new Date();
                const diff = Math.floor((now - startTime) / 1000);
                
                const hours = Math.floor(diff / 3600);
                const minutes = Math.floor((diff % 3600) / 60);
                const seconds = diff % 60;
                
                timer.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            });
        }

        // Démarrer la mise à jour des timers
        setInterval(updateTimers, 1000);
        updateTimers(); // Première mise à jour immédiate

        // Fonctions de gestion des arrêts
        function viewStop(id) {
            fetch(`ajax.php?action=get_stop_details&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const stop = data.stop;
                        alert(`Détails de l'arrêt:\n\nÉquipement: ${stop.equipment}\nMotif: ${stop.reason}\nDébut: ${stop.start_time}\nFin: ${stop.end_time || 'En cours'}\nDurée: ${stop.duration_minutes || 'En cours'} min\nCommentaire: ${stop.comment || 'Aucun'}`);
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors du chargement des détails');
                });
        }

        function editStop(id) {
            fetch(`ajax.php?action=get_stop_details&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const stop = data.stop;
                        showEditStopModal(stop);
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors du chargement du formulaire');
                });
        }

        function showEditStopModal(stop) {
            const modal = document.getElementById('editModal');
            const form = document.getElementById('editForm');
            
            form.innerHTML = `
                <div class="form-group">
                    <label for="edit-equipment">Équipement</label>
                    <select id="edit-equipment" name="equipment" required>
                        <option value="machine-13" ${stop.equipment === 'machine-13' ? 'selected' : ''}>Machine 13</option>
                        <option value="machine-20" ${stop.equipment === 'machine-20' ? 'selected' : ''}>Machine 20</option>
                        <option value="machine-50" ${stop.equipment === 'machine-50' ? 'selected' : ''}>Machine 50</option>
                        <option value="machine-70" ${stop.equipment === 'machine-70' ? 'selected' : ''}>Machine 70</option>
                        <option value="sechoir-1" ${stop.equipment === 'sechoir-1' ? 'selected' : ''}>Séchoir 1</option>
                        <option value="sechoir-2" ${stop.equipment === 'sechoir-2' ? 'selected' : ''}>Séchoir 2</option>
                        <option value="sechoir-3" ${stop.equipment === 'sechoir-3' ? 'selected' : ''}>Séchoir 3</option>
                        <option value="sechoir-4" ${stop.equipment === 'sechoir-4' ? 'selected' : ''}>Séchoir 4</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit-reason">Motif</label>
                    <select id="edit-reason" name="reason" required>
                        <option value="panne-mecanique" ${stop.reason === 'panne-mecanique' ? 'selected' : ''}>Panne mécanique</option>
                        <option value="panne-electrique" ${stop.reason === 'panne-electrique' ? 'selected' : ''}>Panne électrique</option>
                        <option value="maintenance-preventive" ${stop.reason === 'maintenance-preventive' ? 'selected' : ''}>Maintenance préventive</option>
                        <option value="attente-operateur" ${stop.reason === 'attente-operateur' ? 'selected' : ''}>Attente opérateur</option>
                        <option value="changement-production" ${stop.reason === 'changement-production' ? 'selected' : ''}>Changement production</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit-start-time">Début</label>
                        <input type="datetime-local" id="edit-start-time" name="start_time" value="${stop.start_time ? stop.start_time.slice(0, 16) : ''}" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-end-time">Fin</label>
                        <input type="datetime-local" id="edit-end-time" name="end_time" value="${stop.end_time ? stop.end_time.slice(0, 16) : ''}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit-comment">Commentaire</label>
                    <textarea id="edit-comment" name="comment" rows="3">${stop.comment || ''}</textarea>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-primary" onclick="saveStopEdit(${stop.id})">Sauvegarder</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Annuler</button>
                </div>
            `;
            
            modal.style.display = 'block';
        }

        function saveStopEdit(id) {
            const form = document.getElementById('editForm');
            const formData = new FormData();
            
            formData.append('action', 'update_stop');
            formData.append('id', id);
            formData.append('equipment', document.getElementById('edit-equipment').value);
            formData.append('reason', document.getElementById('edit-reason').value);
            formData.append('start_time', document.getElementById('edit-start-time').value);
            formData.append('end_time', document.getElementById('edit-end-time').value);
            formData.append('comment', document.getElementById('edit-comment').value);
            
            fetch('ajax.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Arrêt modifié avec succès');
                    closeModal();
                    location.reload();
                } else {
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erreur lors de la sauvegarde');
            });
        }

        function closeStop(id) {
            if (confirm('Clôturer cet arrêt maintenant ?')) {
                fetch('ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=close_stop&id=${id}&end_time=${new Date().toISOString().slice(0, 19).replace('T', ' ')}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erreur lors de la clôture: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors de la clôture');
                });
            }
        }

        function resumeMachine(equipmentName, stopId) {
            if (confirm(`Reprendre la machine ${equipmentName} ?`)) {
                closeStop(stopId);
            }
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Filtres
        function applyFilters() {
            const equipment = document.getElementById('filter-equipment').value;
            const type = document.getElementById('filter-type').value;
            const dateStart = document.getElementById('filter-date-start').value;
            const dateEnd = document.getElementById('filter-date-end').value;
            
            const params = new URLSearchParams({
                action: 'filter_stops',
                equipment: equipment,
                type: type,
                date_start: dateStart,
                date_end: dateEnd
            });
            
            fetch(`ajax.php?${params}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('stops-table-body').innerHTML = html;
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors du filtrage');
                });
        }

        function resetFilters() {
            document.getElementById('filter-equipment').value = '';
            document.getElementById('filter-type').value = '';
            document.getElementById('filter-date-start').value = '<?= date('Y-m-d') ?>';
            document.getElementById('filter-date-end').value = '<?= date('Y-m-d') ?>';
            location.reload();
        }

        // Auto-calculer la durée quand les dates sont saisies
        document.getElementById('start_time').addEventListener('change', calculateDuration);
        document.getElementById('end_time').addEventListener('change', calculateDuration);

        function calculateDuration() {
            const start = document.getElementById('start_time').value;
            const end = document.getElementById('end_time').value;
            
            if (start && end) {
                const startTime = new Date(start);
                const endTime = new Date(end);
                const diffMinutes = Math.floor((endTime - startTime) / (1000 * 60));
                
                if (diffMinutes > 0) {
                    // Afficher la durée calculée (optionnel)
                    console.log(`Durée calculée: ${diffMinutes} minutes`);
                }
            }
        }

        // Fonctions pour le modal de déclaration d'arrêt
        function showDeclareArretModal() {
            document.getElementById('declareArretModal').style.display = 'block';
        }

        function closeDeclareArretModal() {
            document.getElementById('declareArretModal').style.display = 'none';
            document.getElementById('declareArretForm').reset();
        }

        // Fermer le modal si on clique en dehors
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('declareArretModal');
            if (event.target === modal) {
                closeDeclareArretModal();
            }
        });
    </script>
</body>
</html>
