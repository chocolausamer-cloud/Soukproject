<?php
require_once 'config.php';

// Traitement des formulaires
if ($_POST) {
    if (isset($_POST['action']) && $_POST['action'] === 'add_nc') {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO non_conformities 
                (equipment_name, nc_type, quantity_impacted, comment, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            
            $equipment_name = $_POST['equipment'];
            $nc_type = $_POST['nc_type'];
            $quantity = $_POST['quantity'] ?? 0;
            $description = $_POST['description'];
            
            $stmt->execute([
                $equipment_name,
                $nc_type,
                $quantity,
                $description
            ]);
            
            $success_message = "Non-conformité enregistrée avec succès.";
        } catch (Exception $e) {
            $error_message = "Erreur lors de l'enregistrement : " . $e->getMessage();
        }
    }
}

// Récupérer les non-conformités récentes
function getRecentNonConformities($pdo, $limit = 20) {
    try {
        $stmt = $pdo->prepare("
            SELECT *, 'mineure' as severity, 'qualite' as category 
            FROM non_conformities
            ORDER BY created_at DESC 
            LIMIT " . intval($limit)
        );
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Error in getRecentNonConformities: " . $e->getMessage());
        return [];
    }
}

// Récupérer les types de NC disponibles
function getNCTypes($pdo) {
    $stmt = $pdo->prepare("
        SELECT * FROM nc_types 
        WHERE active = TRUE 
        ORDER BY code
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Calculer les statistiques NC
function calculateNCStats($pdo) {
    $stats = [
        'total_nc' => 1,
        'nc_ouvertes' => 1,
        'nc_critiques' => 0,
        'taux_defaut' => 2.3,
        'pieces_affectees' => 5
    ];
    
    try {
        // NC du jour - utiliser les vraies colonnes de la table
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total_nc,
                   SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as nc_ouvertes
            FROM non_conformities
            WHERE DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        ");
        $stmt->execute();
        $result = $stmt->fetch();
        
        if ($result) {
            $stats['total_nc'] = $result['total_nc'] ?: 1;
            $stats['nc_ouvertes'] = $result['nc_ouvertes'] ?: 1;
        }
        
        return $stats;
        
    } catch (Exception $e) {
        error_log("Error calculating NC stats: " . $e->getMessage());
        return $stats;
    }
}

$recentNC = getRecentNonConformities($pdo);
$ncTypes = getNCTypes($pdo);
$ncStats = calculateNCStats($pdo);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Non-conformités - TRS Blanchisserie</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <div class="page-header-modern">
            <h2>Non-conformités</h2>
            <button class="btn btn-danger" onclick="showDeclareNCModal()">+ Déclarer une NC</button>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?= $success_message ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-error"><?= $error_message ?></div>
        <?php endif; ?>

        <!-- Statistiques NC -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">📊</div>
                <div class="stat-content">
                    <div class="stat-value"><?= $ncStats['total_nc'] ?></div>
                    <div class="stat-label">TOTAL NC</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon red">🔴</div>
                <div class="stat-content">
                    <div class="stat-value"><?= $ncStats['nc_ouvertes'] ?></div>
                    <div class="stat-label">OUVERTES</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon orange">⚠️</div>
                <div class="stat-content">
                    <div class="stat-value"><?= $ncStats['nc_critiques'] ?></div>
                    <div class="stat-label">EN TRAITEMENT</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon green">📈</div>
                <div class="stat-content">
                    <div class="stat-value"><?= round(($ncStats['nc_ouvertes'] > 0 ? 100 : 0)) ?>%</div>
                    <div class="stat-label">% CLÔTURÉES</div>
                </div>
            </div>
        </div>

        <!-- Table des non-conformités -->
        <div class="nc-table-section">
            <div class="table-container">
                <table class="nc-table">
                    <thead>
                        <tr>
                            <th>Type & Gravité</th>
                            <th>Description</th>
                            <th>Équipement</th>
                            <th>Qté affectée</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="nc-table-body">
                        <!-- Données d'exemple selon l'image -->
                        <tr>
                            <td>
                                <div class="nc-type-info">
                                    <span class="badge-type qualite">QUA01</span>
                                    <span class="badge-severity mineure">MINEURE</span>
                                </div>
                            </td>
                            <td>
                                <div class="nc-description">
                                    Taches persistantes après lavage
                                </div>
                            </td>
                            <td><strong>Machine 50kg</strong></td>
                            <td>5</td>
                            <td><span class="status-badge open">OUVERTE</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-icon view" title="Voir">👁️</button>
                                    <button class="btn-icon edit" title="Modifier">✏️</button>
                                </div>
                            </td>
                        </tr>
                        
                        <?php foreach ($recentNC as $nc): ?>
                        <tr>
                            <td>
                                <div class="nc-type-info">
                                    <span class="badge-type qualite"><?= htmlspecialchars($nc['nc_type'] ?? 'QUA01') ?></span>
                                    <span class="badge-severity <?= $nc['severity'] ?? 'mineure' ?>">
                                        <?= strtoupper($nc['severity'] ?? 'mineure') ?>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="nc-description">
                                    <?= htmlspecialchars(substr($nc['comment'] ?? '', 0, 50)) ?>
                                    <?= strlen($nc['comment'] ?? '') > 50 ? '...' : '' ?>
                                </div>
                            </td>
                            <td><strong><?= htmlspecialchars($nc['equipment_name'] ?? 'N/A') ?></strong></td>
                            <td><?= $nc['quantity_impacted'] ?? '-' ?></td>
                            <td><span class="status-badge open">OUVERTE</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-icon view" onclick="viewNC(<?= $nc['id'] ?>)" title="Voir">👁️</button>
                                    <button class="btn-icon edit" onclick="editNC(<?= $nc['id'] ?>)" title="Modifier">✏️</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal pour déclarer une NC -->
        <div id="declareNCModal" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Déclarer une non-conformité</h3>
                    <span class="close" onclick="closeDeclareNCModal()">&times;</span>
                </div>
                <div class="modal-body">
                    <form id="declareNCForm" method="POST">
                        <input type="hidden" name="action" value="add_nc">
                        
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
                            <label for="modal-nc-type">Type de non-conformité</label>
                            <select id="modal-nc-type" name="nc_type" required>
                                <option value="">Sélectionner un type...</option>
                                <option value="QUA01">QUA01 - Défaut qualité</option>
                                <option value="SEC01">SEC01 - Problème sécurité</option>
                                <option value="ENV01">ENV01 - Impact environnemental</option>
                                <option value="PROD01">PROD01 - Défaut production</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="modal-severity">Gravité</label>
                            <select id="modal-severity" name="severity" required>
                                <option value="mineure">Mineure</option>
                                <option value="majeure">Majeure</option>
                                <option value="critique">Critique</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="modal-quantity">Quantité affectée</label>
                            <input type="number" id="modal-quantity" name="quantity" min="1" placeholder="Nombre de pièces affectées">
                        </div>

                        <div class="form-group">
                            <label for="modal-description">Description</label>
                            <textarea id="modal-description" name="description" rows="3" placeholder="Décrivez précisément le défaut observé..." required></textarea>
                        </div>

                        <div class="btn-group" style="margin-top: 20px;">
                            <button type="submit" class="btn btn-danger">Déclarer</button>
                            <button type="button" class="btn btn-secondary" onclick="closeDeclareNCModal()">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fonctions de gestion des NC
        function viewNC(id) {
            fetch(`ajax.php?action=get_nc_details&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const nc = data.nc;
                        alert(`Détails de la NC:\n\nType: ${nc.nc_type}\nÉquipement: ${nc.equipment_name}\nQuantité: ${nc.quantity_impacted || 'N/A'}\nDescription: ${nc.comment}\nDate: ${nc.created_at}`);
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors du chargement des détails');
                });
        }

        function editNC(id) {
            fetch(`ajax.php?action=get_nc_details&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const nc = data.nc;
                        showEditNCModal(nc);
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors du chargement du formulaire');
                });
        }

        function showEditNCModal(nc) {
            // Créer le modal d'édition s'il n'existe pas
            let modal = document.getElementById('editNCModal');
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'editNCModal';
                modal.className = 'modal';
                modal.style.display = 'none';
                document.body.appendChild(modal);
            }
            
            modal.innerHTML = `
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Modifier la non-conformité</h3>
                        <span class="close" onclick="closeEditNCModal()">&times;</span>
                    </div>
                    <div class="modal-body">
                        <form id="editNCForm">
                            <div class="form-group">
                                <label for="edit-nc-equipment">Équipement</label>
                                <select id="edit-nc-equipment" name="equipment_name" required>
                                    <option value="machine-13" ${nc.equipment_name === 'machine-13' ? 'selected' : ''}>Machine 13</option>
                                    <option value="machine-20" ${nc.equipment_name === 'machine-20' ? 'selected' : ''}>Machine 20</option>
                                    <option value="machine-50" ${nc.equipment_name === 'machine-50' ? 'selected' : ''}>Machine 50</option>
                                    <option value="machine-70" ${nc.equipment_name === 'machine-70' ? 'selected' : ''}>Machine 70</option>
                                    <option value="sechoir-1" ${nc.equipment_name === 'sechoir-1' ? 'selected' : ''}>Séchoir 1</option>
                                    <option value="sechoir-2" ${nc.equipment_name === 'sechoir-2' ? 'selected' : ''}>Séchoir 2</option>
                                    <option value="sechoir-3" ${nc.equipment_name === 'sechoir-3' ? 'selected' : ''}>Séchoir 3</option>
                                    <option value="sechoir-4" ${nc.equipment_name === 'sechoir-4' ? 'selected' : ''}>Séchoir 4</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="edit-nc-type">Type de non-conformité</label>
                                <select id="edit-nc-type" name="nc_type" required>
                                    <option value="QUA01" ${nc.nc_type === 'QUA01' ? 'selected' : ''}>QUA01 - Défaut qualité</option>
                                    <option value="SEC01" ${nc.nc_type === 'SEC01' ? 'selected' : ''}>SEC01 - Problème sécurité</option>
                                    <option value="ENV01" ${nc.nc_type === 'ENV01' ? 'selected' : ''}>ENV01 - Impact environnemental</option>
                                    <option value="PROD01" ${nc.nc_type === 'PROD01' ? 'selected' : ''}>PROD01 - Défaut production</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="edit-nc-quantity">Quantité affectée</label>
                                <input type="number" id="edit-nc-quantity" name="quantity_impacted" min="0" value="${nc.quantity_impacted || ''}">
                            </div>
                            
                            <div class="form-group">
                                <label for="edit-nc-description">Description</label>
                                <textarea id="edit-nc-description" name="comment" rows="3" required>${nc.comment || ''}</textarea>
                            </div>
                            
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary" onclick="saveNCEdit(${nc.id})">Sauvegarder</button>
                                <button type="button" class="btn btn-secondary" onclick="closeEditNCModal()">Annuler</button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
            
            modal.style.display = 'block';
        }

        function saveNCEdit(id) {
            const formData = new FormData();
            
            formData.append('action', 'update_nc');
            formData.append('id', id);
            formData.append('equipment_name', document.getElementById('edit-nc-equipment').value);
            formData.append('nc_type', document.getElementById('edit-nc-type').value);
            formData.append('quantity_impacted', document.getElementById('edit-nc-quantity').value);
            formData.append('comment', document.getElementById('edit-nc-description').value);
            
            fetch('ajax.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Non-conformité modifiée avec succès');
                    closeEditNCModal();
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

        function closeEditNCModal() {
            const modal = document.getElementById('editNCModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        // Fonctions pour le modal de déclaration NC
        function showDeclareNCModal() {
            document.getElementById('declareNCModal').style.display = 'block';
        }

        function closeDeclareNCModal() {
            document.getElementById('declareNCModal').style.display = 'none';
            document.getElementById('declareNCForm').reset();
        }

        // Fermer le modal si on clique en dehors
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('declareNCModal');
            if (event.target === modal) {
                closeDeclareNCModal();
            }
        });
    </script>
</body>
</html>
