<?php
require_once 'config.php';

// Traitement des formulaires
if ($_POST) {
    if (isset($_POST['action']) && $_POST['action'] === 'add_manual_production') {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO manual_sessions 
                (station_type, operator, pieces, weight_kg, comment, session_start, status) 
                VALUES (?, ?, ?, ?, ?, NOW(), 'Finie')
            ");
            
            $station_type = $_POST['station_type']; // 'Calandre' ou 'Repassage'
            $operator = $_POST['operator'] ?? 'Jean Dupont';
            $pieces = (int)($_POST['pieces'] ?? 0);
            $weight = (float)($_POST['weight'] ?? 0);
            $comment = $_POST['comment'] ?? '';
            
            $stmt->execute([
                $station_type,
                $operator,
                $pieces,
                $weight,
                $comment
            ]);
            
            $success_message = "Production $station_type enregistrée avec succès.";
        } catch (Exception $e) {
            $error_message = "Erreur lors de l'enregistrement : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finition - TRS Blanchisserie</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h2>Finition</h2>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?= $success_message ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-error"><?= $error_message ?></div>
        <?php endif; ?>

        <!-- Onglets Finition -->
        <div class="finition-tabs">
            <button class="finition-tab-btn active" onclick="showFinitionTab('calandre')">📏 Calandre</button>
            <button class="finition-tab-btn" onclick="showFinitionTab('repassage')">👔 Repassage</button>
        </div>

        <!-- Onglet Calandre -->
        <div id="calandre" class="finition-tab-content active">
            <div class="machine-modern-card">
                <div class="machine-modern-header">
                    <h3>Calandre - Poste Manuel Avancé</h3>
                </div>
                
                <div class="machine-layout">
                    <div class="machine-form-section">
                        <form id="calandreForm" onsubmit="saveCalandreProduction(event)">
                            <input type="hidden" name="action" value="add_manual_production">
                            <input type="hidden" name="station_type" value="Calandre">
                            
                            <div class="form-group">
                                <label for="calandre-designation">Désignation du Cycle</label>
                                <input type="text" id="calandre-designation" name="designation" placeholder="Nom du cycle..." required>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="calandre-articles">Nombre d'articles</label>
                                    <input type="number" id="calandre-articles" name="articles" placeholder="0" min="0" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="calandre-pieces">Nombre de pièces</label>
                                    <input type="number" id="calandre-pieces" name="pieces" placeholder="0" min="0" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="calandre-operator">Opérateur</label>
                                <select id="calandre-operator" name="operator">
                                    <option value="Jean Dupont">Jean Dupont</option>
                                    <option value="Marie Leroy">Marie Leroy</option>
                                    <option value="Sophie Martin">Sophie Martin</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="calandre-comment">Commentaire Opérateur</label>
                                <textarea id="calandre-comment" name="comment" rows="3" placeholder="Commentaires..."></textarea>
                            </div>
                        </form>
                    </div>

                    <div class="machine-control-section">
                        <div class="machine-timer-container">
                            <div class="machine-timer-display" id="calandre-timer">00:00:00</div>
                            <div class="machine-timer-status" id="calandre-timer-status">Arrêté</div>
                        </div>

                        <div class="machine-control-buttons">
                            <button type="button" class="machine-btn machine-btn-start" id="calandre-start" onclick="startCalandre()">
                                Démarrer
                            </button>
                        </div>

                        <div class="machine-status-indicator">
                            <div class="status-dot" id="status-dot-calandre"></div>
                            <span class="status-text" id="status-text-calandre">Arrêté</span>
                        </div>
                    </div>
                </div>

                <div class="machine-production-info">
                    <div class="production-info-item">
                        <span class="info-label">Prochaine production planifiée:</span>
                        <div class="info-value">Calandrage draps - 16:45</div>
                    </div>
                    <div class="production-info-item">
                        <span class="info-label">Dernière production:</span>
                        <div class="info-value">Draps hôtel - 2h15min</div>
                    </div>
                    <div class="production-info-item">
                        <span class="info-label">Rendement du jour:</span>
                        <div class="info-value">245 pièces traitées</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglet Repassage -->
        <div id="repassage" class="finition-tab-content">
            <div class="machine-modern-card">
                <div class="machine-modern-header">
                    <h3>Repassage - Poste Manuel Avancé</h3>
                </div>
                
                <div class="machine-layout">
                    <div class="machine-form-section">
                        <form id="repassageForm" onsubmit="saveRepassageProduction(event)">
                            <input type="hidden" name="action" value="add_manual_production">
                            <input type="hidden" name="station_type" value="Repassage">
                            
                            <div class="form-group">
                                <label for="repassage-designation">Désignation du Cycle</label>
                                <input type="text" id="repassage-designation" name="designation" placeholder="Nom du cycle..." required>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="repassage-articles">Nombre d'articles</label>
                                    <input type="number" id="repassage-articles" name="articles" placeholder="0" min="0" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="repassage-pieces">Nombre de pièces</label>
                                    <input type="number" id="repassage-pieces" name="pieces" placeholder="0" min="0" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="repassage-operator">Opérateur</label>
                                <select id="repassage-operator" name="operator">
                                    <option value="Jean Dupont">Jean Dupont</option>
                                    <option value="Marie Leroy">Marie Leroy</option>
                                    <option value="Sophie Martin">Sophie Martin</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="repassage-comment">Commentaire Opérateur</label>
                                <textarea id="repassage-comment" name="comment" rows="3" placeholder="Commentaires..."></textarea>
                            </div>
                        </form>
                    </div>

                    <div class="machine-control-section">
                        <div class="machine-timer-container">
                            <div class="machine-timer-display" id="repassage-timer">00:00:00</div>
                            <div class="machine-timer-status" id="repassage-timer-status">Arrêté</div>
                        </div>

                        <div class="machine-control-buttons">
                            <button type="button" class="machine-btn machine-btn-start" id="repassage-start" onclick="startRepassage()">
                                Démarrer
                            </button>
                        </div>

                        <div class="machine-status-indicator">
                            <div class="status-dot" id="status-dot-repassage"></div>
                            <span class="status-text" id="status-text-repassage">Arrêté</span>
                        </div>
                    </div>
                </div>

                <div class="machine-production-info">
                    <div class="production-info-item">
                        <span class="info-label">Prochaine production planifiée:</span>
                        <div class="info-value">Finition serviettes - 18:00</div>
                    </div>
                    <div class="production-info-item">
                        <span class="info-label">Dernière production:</span>
                        <div class="info-value">Draps hôtel - 2h15min</div>
                    </div>
                    <div class="production-info-item">
                        <span class="info-label">Rendement du jour:</span>
                        <div class="info-value">245 pièces traitées</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Gestion des onglets finition
        function showFinitionTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.finition-tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.finition-tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }

        // Fonctions de contrôle Calandre
        function startCalandre() {
            const timer = document.getElementById('calandre-timer');
            const status = document.getElementById('calandre-timer-status');
            const statusDot = document.getElementById('status-dot-calandre');
            const statusText = document.getElementById('status-text-calandre');
            
            // Démarrer le timer
            status.textContent = 'En cours';
            statusDot.classList.add('running');
            statusText.textContent = 'En fonctionnement';
            
            // Simuler le timer (vous pouvez implémenter un vrai timer)
            let seconds = 0;
            const interval = setInterval(() => {
                seconds++;
                const hours = Math.floor(seconds / 3600);
                const minutes = Math.floor((seconds % 3600) / 60);
                const secs = seconds % 60;
                timer.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            }, 1000);
            
            // Stocker l'interval pour pouvoir l'arrêter
            timer.dataset.interval = interval;
        }

        // Fonctions de contrôle Repassage
        function startRepassage() {
            const timer = document.getElementById('repassage-timer');
            const status = document.getElementById('repassage-timer-status');
            const statusDot = document.getElementById('status-dot-repassage');
            const statusText = document.getElementById('status-text-repassage');
            
            status.textContent = 'En cours';
            statusDot.classList.add('running');
            statusText.textContent = 'En fonctionnement';
            
            let seconds = 0;
            const interval = setInterval(() => {
                seconds++;
                const hours = Math.floor(seconds / 3600);
                const minutes = Math.floor((seconds % 3600) / 60);
                const secs = seconds % 60;
                timer.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            }, 1000);
            
            timer.dataset.interval = interval;
        }

        function saveCalandreProduction(event) {
            event.preventDefault();
            
            const formData = new FormData(document.getElementById('calandreForm'));
            
            fetch('finition.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(result => {
                alert('Production Calandre enregistrée avec succès !');
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erreur lors de l\'enregistrement');
            });
        }

        function saveRepassageProduction(event) {
            event.preventDefault();
            
            const formData = new FormData(document.getElementById('repassageForm'));
            
            fetch('finition.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(result => {
                alert('Production Repassage enregistrée avec succès !');
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erreur lors de l\'enregistrement');
            });
        }

        // Mise à jour de l'heure
        function updateTime() {
            const now = new Date();
            document.getElementById('current-time').textContent = now.toLocaleTimeString();
        }
        setInterval(updateTime, 1000);
    </script>
</body>
</html>
