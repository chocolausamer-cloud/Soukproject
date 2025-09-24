<?php
require_once 'config.php';

// Calculer les données TRS actuelles
function calculateCurrentTRS() {
    global $pdo;
    
    $data = [
        'machines_actives' => 0,
        'sechoirs_actifs' => 0,
        'postes_actifs' => 0,
        'arrets_jour' => 0,
        'nc_ouvertes' => 0,
        'production_kg' => 0,
        'cycles_total' => 0,
        'trs_global' => 0,
        'disponibilite' => 0,
        'performance' => 0,
        'qualite' => 0
    ];
    
    try {
        // Compter les productions du jour
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as cycles_total,
                COALESCE(SUM(weight), 0) as production_kg
            FROM productions 
            WHERE DATE(timestamp) = CURDATE()
        ");
        $stmt->execute();
        $result = $stmt->fetch();
        $data['cycles_total'] = $result['cycles_total'];
        $data['production_kg'] = $result['production_kg'];
        
        // Compter les arrêts du jour
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as arrets_jour 
            FROM equipment_stops 
            WHERE DATE(start_time) = CURDATE()
        ");
        $stmt->execute();
        $data['arrets_jour'] = $stmt->fetchColumn();
        
        // Compter les NC ouvertes
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as nc_ouvertes 
            FROM non_conformities 
            WHERE DATE(created_at) = CURDATE()
        ");
        $stmt->execute();
        $data['nc_ouvertes'] = $stmt->fetchColumn();
        
        // Sessions manuelles actives
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as postes_actifs 
            FROM manual_sessions 
            WHERE status IN ('En cours', 'Pause') AND DATE(session_start) = CURDATE()
        ");
        $stmt->execute();
        $data['postes_actifs'] = $stmt->fetchColumn();
        
        // Calculer TRS approximatif
        $temps_planifie = 8 * 60; // 8h en minutes
        $temps_arrets = 0;
        
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(duration_minutes), 0) as temps_arrets
            FROM equipment_stops 
            WHERE DATE(start_time) = CURDATE() AND duration_minutes IS NOT NULL
        ");
        $stmt->execute();
        $temps_arrets = $stmt->fetchColumn();
        
        $data['disponibilite'] = max(0, ($temps_planifie - $temps_arrets) / $temps_planifie * 100);
        $data['performance'] = 85; // Valeur simulée
        $data['qualite'] = 97; // Valeur simulée
        $data['trs_global'] = ($data['disponibilite'] * $data['performance'] * $data['qualite']) / 10000;
        
        return $data;
        
    } catch (Exception $e) {
        error_log("Error calculating TRS: " . $e->getMessage());
        return $data;
    }
}

$trsData = calculateCurrentTRS();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard TRS - Blanchisserie</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Dashboard TRS Blanchisserie</h1>
            <div class="header-info">
                <span id="current-time"><?= date('H:i:s') ?></span>
                <span>Jean Dupont - Opérateur</span>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="tab-navigation">
            <button class="tab-btn active" onclick="showTab('dashboard')">📊 Dashboard</button>
            <button class="tab-btn" onclick="showTab('washing')">🔧 Machines à laver</button>
            <button class="tab-btn" onclick="showTab('drying')">🌡️ Séchoirs</button>
            <button class="tab-btn" onclick="showTab('finishing')">📏 Finition</button>
            <button class="tab-btn" onclick="showTab('stops')">⚠️ Arrêts</button>
            <button class="tab-btn" onclick="showTab('nc')">❌ Non-conformités</button>
            <button class="tab-btn" onclick="showTab('history')">📈 Historique</button>
            <button class="tab-btn" onclick="showTab('trs-tracking')">📊 Suivi TRS</button>
            <button class="tab-btn" onclick="showTab('settings')">⚙️ Réglages</button>
            <button class="tab-btn production" onclick="showTab('production')">🏭 Hub Production</button>
        </div>

        <!-- Dashboard Tab -->
        <div id="dashboard" class="tab-content active">
            <!-- KPIs principaux -->
            <div class="kpis-grid">
                <div class="kpi-card">
                    <div class="kpi-indicator blue"></div>
                    <div class="kpi-label">TÂCHES ASSIGNÉES</div>
                    <div class="kpi-value"><?= $trsData['cycles_total'] ?></div>
                    <div class="kpi-subtitle">Total des tâches</div>
                </div>
                
                <div class="kpi-card">
                    <div class="kpi-indicator green"></div>
                    <div class="kpi-label">MACHINES ACTIVES</div>
                    <div class="kpi-value"><?= $trsData['machines_actives'] ?></div>
                    <div class="kpi-subtitle">En fonctionnement</div>
                </div>
                
                <div class="kpi-card">
                    <div class="kpi-indicator yellow"></div>
                    <div class="kpi-label">SÉCHOIRS ACTIFS</div>
                    <div class="kpi-value"><?= $trsData['sechoirs_actifs'] ?></div>
                    <div class="kpi-subtitle">Séchage en cours</div>
                </div>
                
                <div class="kpi-card">
                    <div class="kpi-indicator red"></div>
                    <div class="kpi-label">FINITION TERMINÉE</div>
                    <div class="kpi-value"><?= $trsData['postes_actifs'] ?></div>
                    <div class="kpi-subtitle">Opérations finies</div>
                </div>
                
                <div class="kpi-card">
                    <div class="kpi-indicator purple"></div>
                    <div class="kpi-label">ARRÊTS TOTAL</div>
                    <div class="kpi-value"><?= $trsData['arrets_jour'] ?></div>
                    <div class="kpi-subtitle">Arrêts cumulés</div>
                </div>
                
                <div class="kpi-card">
                    <div class="kpi-indicator cyan"></div>
                    <div class="kpi-label">TRS MOYEN</div>
                    <div class="kpi-value"><?= number_format($trsData['trs_global'], 0) ?>%</div>
                    <div class="kpi-subtitle">Performance globale</div>
                </div>
                
                <div class="kpi-card">
                    <div class="kpi-indicator orange"></div>
                    <div class="kpi-label">PRODUCTION KG</div>
                    <div class="kpi-value"><?= number_format($trsData['production_kg'], 0) ?></div>
                    <div class="kpi-subtitle">Poids traité</div>
                </div>
                
                <div class="kpi-card">
                    <div class="kpi-indicator teal"></div>
                    <div class="kpi-label">CYCLES RÉALISÉS</div>
                    <div class="kpi-value"><?= $trsData['cycles_total'] ?></div>
                    <div class="kpi-subtitle">Opérations</div>
                </div>
            </div>

            <!-- Équipements Section -->
            <div class="equipment-section">
                <h2>Vue détaillée des équipements</h2>
                
                <!-- Séchoirs -->
                <div class="equipment-category">
                    <h3>Séchoirs</h3>
                    <div id="sechoirs-dashboard" class="equipment-cards">
                        <!-- Will be populated via AJAX -->
                    </div>
                </div>
                
                <!-- Postes de finition -->
                <div class="equipment-category">
                    <h3>Postes de finition</h3>
                    <div id="finition-dashboard" class="equipment-cards">
                        <!-- Will be populated via AJAX -->
                    </div>
                </div>
            </div>

            <!-- Activité récente -->
            <div class="recent-activity">
                <h3>Activité Récente</h3>
                <div id="recent-activities">
                    <!-- Will be populated via AJAX -->
                </div>
            </div>

            <!-- Résumé production -->
            <div class="production-summary">
                <h3>Résumé de Production</h3>
                <div class="summary-grid">
                    <div class="summary-item">
                        <span class="summary-label">Cycles terminés</span>
                        <span class="summary-value blue"><?= $trsData['cycles_total'] ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Poids traité</span>
                        <span class="summary-value green"><?= number_format($trsData['production_kg'], 0) ?> kg</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">En attente</span>
                        <span class="summary-value orange">12</span>
                    </div>
                </div>
                
                <?php if ($trsData['arrets_jour'] > 0): ?>
                <div class="alert-section">
                    <div class="alert-header">
                        <span>⚠️</span>
                        <span>Alerte en Cours</span>
                    </div>
                    <p>Machine 70 - Maintenance Maintenance</p>
                    <p class="alert-impact"><?= $trsData['arrets_jour'] ?> opérations affectées</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Machines à laver Tab -->
        <div id="washing" class="tab-content">
            <h2>🔧 Machines à laver</h2>
            
            <!-- Machine Cards Grid -->
            <div class="machines-modern-grid">
                <!-- Machine 13kg Card -->
                <div class="machine-modern-card" id="machine-card-13">
                    <div class="machine-modern-header">
                        <h3>Machine 13kg - Poste Manuel Avancé</h3>
                    </div>
                    
                    <div class="machine-layout">
                        <div class="machine-form-section">
                            <form id="machineForm-13" onsubmit="saveMachineProduction(event, 13)">
                                <input type="hidden" name="machine" value="13">
                                
                                <div class="form-group">
                                    <label for="program-select-13">Programme</label>
                                    <select id="program-select-13" name="program" onchange="updateProgramInfo(13)" required>
                                        <option value="">Choisir un programme préparé...</option>
                                    </select>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="machine-weight-13">Poids (kg)</label>
                                        <input type="number" id="machine-weight-13" name="weight" min="1" max="13" step="0.1" placeholder="Poids du linge" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="machine-batch-13">Code Batch</label>
                                        <input type="text" id="machine-batch-13" name="batch" placeholder="Code Batch...">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="machine-client-13">Client</label>
                                    <select id="machine-client-13" name="client">
                                        <option value="">Client Général</option>
                                        <option value="hotel">Hôtel Le Grand</option>
                                        <option value="restaurant">Restaurant La Table</option>
                                        <option value="clinique">Clinique Saint-Michel</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="machine-comment-13">Commentaire Opérateur</label>
                                    <textarea id="machine-comment-13" name="comment" rows="3" placeholder="Commentaire opérateur..."></textarea>
                                </div>
                            </form>
                        </div>

                        <div class="machine-control-section">
                            <div class="machine-timer-container">
                                <div class="machine-timer-display" id="machine-timer-13">00:00:00</div>
                                <div class="machine-timer-status" id="machine-timer-status-13">Arrêté</div>
                            </div>

                            <div class="machine-control-buttons">
                                <button type="button" class="machine-btn machine-btn-start" id="machine-start-13" onclick="startMachine(13)">
                                    Démarrer
                                </button>
                                <button type="button" class="machine-btn machine-btn-pause" id="machine-pause-13" onclick="pauseMachine(13)" disabled>
                                    Pause
                                </button>
                                <button type="button" class="machine-btn machine-btn-stop" id="machine-stop-13" onclick="stopMachine(13)" disabled>
                                    Stop
                                </button>
                                <button type="button" class="machine-btn machine-btn-orange" id="machine-relance-13" onclick="relanceMachine(13)">
                                    Relance
                                </button>
                            </div>

                            <div class="machine-status-indicator">
                                <div class="status-dot" id="status-dot-13"></div>
                                <span class="status-text" id="status-text-13">Arrêté</span>
                            </div>
                        </div>
                    </div>

                    <div class="machine-production-info">
                        <div class="production-info-item">
                            <span class="info-label">Prochaine production planifiée:</span>
                            <div class="info-value">Synthétique Restaurant - 16:45</div>
                        </div>
                        <div class="production-info-item">
                            <span class="info-label">Dernière production:</span>
                            <div class="info-value">Coton 60°C - 45min</div>
                        </div>
                        <div class="production-info-item">
                            <span class="info-label">Cycles du jour:</span>
                            <div class="info-value">12 cycles terminés</div>
                        </div>
                    </div>
                </div>

                <!-- Machine 20kg Card -->
                <div class="machine-modern-card" id="machine-card-20">
                    <div class="machine-modern-header">
                        <h3>Machine 20kg - Poste Manuel Avancé</h3>
                    </div>
                    
                    <div class="machine-layout">
                        <div class="machine-form-section">
                            <form id="machineForm-20" onsubmit="saveMachineProduction(event, 20)">
                                <input type="hidden" name="machine" value="20">
                                
                                <div class="form-group">
                                    <label for="program-select-20">Programme</label>
                                    <select id="program-select-20" name="program" onchange="updateProgramInfo(20)" required>
                                        <option value="">Choisir un programme préparé...</option>
                                    </select>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="machine-weight-20">Poids (kg)</label>
                                        <input type="number" id="machine-weight-20" name="weight" min="1" max="20" step="0.1" placeholder="Poids du linge" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="machine-batch-20">Code Batch</label>
                                        <input type="text" id="machine-batch-20" name="batch" placeholder="Code Batch...">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="machine-client-20">Client</label>
                                    <select id="machine-client-20" name="client">
                                        <option value="">Client Général</option>
                                        <option value="hotel">Hôtel Le Grand</option>
                                        <option value="restaurant">Restaurant La Table</option>
                                        <option value="clinique">Clinique Saint-Michel</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="machine-comment-20">Commentaire Opérateur</label>
                                    <textarea id="machine-comment-20" name="comment" rows="3" placeholder="Commentaire opérateur..."></textarea>
                                </div>
                            </form>
                        </div>

                        <div class="machine-control-section">
                            <div class="machine-timer-container">
                                <div class="machine-timer-display" id="machine-timer-20">00:00:00</div>
                                <div class="machine-timer-status" id="machine-timer-status-20">Arrêté</div>
                            </div>

                            <div class="machine-control-buttons">
                                <button type="button" class="machine-btn machine-btn-start" id="machine-start-20" onclick="startMachine(20)">
                                    Démarrer
                                </button>
                                <button type="button" class="machine-btn machine-btn-pause" id="machine-pause-20" onclick="pauseMachine(20)" disabled>
                                    Pause
                                </button>
                                <button type="button" class="machine-btn machine-btn-stop" id="machine-stop-20" onclick="stopMachine(20)" disabled>
                                    Stop
                                </button>
                                <button type="button" class="machine-btn machine-btn-orange" id="machine-relance-20" onclick="relanceMachine(20)">
                                    Relance
                                </button>
                            </div>

                            <div class="machine-status-indicator">
                                <div class="status-dot" id="status-dot-20"></div>
                                <span class="status-text" id="status-text-20">Arrêté</span>
                            </div>
                        </div>
                    </div>

                    <div class="machine-production-info">
                        <div class="production-info-item">
                            <span class="info-label">Prochaine production planifiée:</span>
                            <div class="info-value">Coton Hôtel - 18:30</div>
                        </div>
                        <div class="production-info-item">
                            <span class="info-label">Dernière production:</span>
                            <div class="info-value">Coton 60°C - 45min</div>
                        </div>
                        <div class="production-info-item">
                            <span class="info-label">Cycles du jour:</span>
                            <div class="info-value">12 cycles terminés</div>
                        </div>
                    </div>
                </div>

                <!-- Machine 50kg Card -->
                <div class="machine-modern-card" id="machine-card-50">
                    <div class="machine-modern-header">
                        <h3>Machine 50kg - Poste Manuel Avancé</h3>
                    </div>
                    
                    <div class="machine-layout">
                        <div class="machine-form-section">
                            <form id="machineForm-50" onsubmit="saveMachineProduction(event, 50)">
                                <input type="hidden" name="machine" value="50">
                                
                                <div class="form-group">
                                    <label for="program-select-50">Programme</label>
                                    <select id="program-select-50" name="program" onchange="updateProgramInfo(50)" required>
                                        <option value="">Choisir un programme préparé...</option>
                                    </select>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="machine-weight-50">Poids (kg)</label>
                                        <input type="number" id="machine-weight-50" name="weight" min="1" max="50" step="0.1" placeholder="Poids du linge" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="machine-batch-50">Code Batch</label>
                                        <input type="text" id="machine-batch-50" name="batch" placeholder="Code Batch...">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="machine-client-50">Client</label>
                                    <select id="machine-client-50" name="client">
                                        <option value="">Client Général</option>
                                        <option value="hotel">Hôtel Le Grand</option>
                                        <option value="restaurant">Restaurant La Table</option>
                                        <option value="clinique">Clinique Saint-Michel</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="machine-comment-50">Commentaire Opérateur</label>
                                    <textarea id="machine-comment-50" name="comment" rows="3" placeholder="Commentaire opérateur..."></textarea>
                                </div>
                            </form>
                        </div>

                        <div class="machine-control-section">
                            <div class="machine-timer-container">
                                <div class="machine-timer-display" id="machine-timer-50">00:00:00</div>
                                <div class="machine-timer-status" id="machine-timer-status-50">Arrêté</div>
                            </div>

                            <div class="machine-control-buttons">
                                <button type="button" class="machine-btn machine-btn-start" id="machine-start-50" onclick="startMachine(50)">
                                    Démarrer
                                </button>
                                <button type="button" class="machine-btn machine-btn-pause" id="machine-pause-50" onclick="pauseMachine(50)" disabled>
                                    Pause
                                </button>
                                <button type="button" class="machine-btn machine-btn-stop" id="machine-stop-50" onclick="stopMachine(50)" disabled>
                                    Stop
                                </button>
                                <button type="button" class="machine-btn machine-btn-orange" id="machine-relance-50" onclick="relanceMachine(50)">
                                    Relance
                                </button>
                            </div>

                            <div class="machine-status-indicator">
                                <div class="status-dot" id="status-dot-50"></div>
                                <span class="status-text" id="status-text-50">Arrêté</span>
                            </div>
                        </div>
                    </div>

                    <div class="machine-production-info">
                        <div class="production-info-item">
                            <span class="info-label">Prochaine production planifiée:</span>
                            <div class="info-value">Synthétique Restaurant - 16:45</div>
                        </div>
                        <div class="production-info-item">
                            <span class="info-label">Dernière production:</span>
                            <div class="info-value">Coton 60°C - 45min</div>
                        </div>
                        <div class="production-info-item">
                            <span class="info-label">Cycles du jour:</span>
                            <div class="info-value">12 cycles terminés</div>
                        </div>
                    </div>
                </div>

                <!-- Machine 70kg Card -->
                <div class="machine-modern-card" id="machine-card-70">
                    <div class="machine-modern-header">
                        <h3>Machine 70kg - Poste Manuel Avancé</h3>
                    </div>
                    
                    <div class="machine-layout">
                        <div class="machine-form-section">
                            <form id="machineForm-70" onsubmit="saveMachineProduction(event, 70)">
                                <input type="hidden" name="machine" value="70">
                                
                                <div class="form-group">
                                    <label for="program-select-70">Programme</label>
                                    <select id="program-select-70" name="program" onchange="updateProgramInfo(70)" required>
                                        <option value="">Choisir un programme préparé...</option>
                                    </select>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="machine-weight-70">Poids (kg)</label>
                                        <input type="number" id="machine-weight-70" name="weight" min="1" max="70" step="0.1" placeholder="Poids du linge" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="machine-batch-70">Code Batch</label>
                                        <input type="text" id="machine-batch-70" name="batch" placeholder="Code Batch...">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="machine-client-70">Client</label>
                                    <select id="machine-client-70" name="client">
                                        <option value="">Client Général</option>
                                        <option value="hotel">Hôtel Le Grand</option>
                                        <option value="restaurant">Restaurant La Table</option>
                                        <option value="clinique">Clinique Saint-Michel</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="machine-comment-70">Commentaire Opérateur</label>
                                    <textarea id="machine-comment-70" name="comment" rows="3" placeholder="Commentaire opérateur..."></textarea>
                                </div>
                            </form>
                        </div>

                        <div class="machine-control-section">
                            <div class="machine-timer-container">
                                <div class="machine-timer-display" id="machine-timer-70">00:00:00</div>
                                <div class="machine-timer-status" id="machine-timer-status-70">En pause</div>
                                
                                <div class="machine-cycle-info" id="machine-cycle-info-70" style="display: block;">
                                    <div class="cycle-program">Standard 55°C</div>
                                    <div class="cycle-weight">65 kg</div>
                                </div>
                            </div>

                            <div class="machine-control-buttons">
                                <button type="button" class="machine-btn machine-btn-start" id="machine-start-70" onclick="startMachine(70)">
                                    Démarrer
                                </button>
                                <button type="button" class="machine-btn machine-btn-pause" id="machine-pause-70" onclick="pauseMachine(70)" disabled>
                                    Pause
                                </button>
                                <button type="button" class="machine-btn machine-btn-stop" id="machine-stop-70" onclick="stopMachine(70)" disabled>
                                    Stop
                                </button>
                                <button type="button" class="machine-btn machine-btn-orange" id="machine-relance-70" onclick="relanceMachine(70)">
                                    Relance
                                </button>
                            </div>

                            <div class="machine-status-indicator">
                                <div class="status-dot paused" id="status-dot-70"></div>
                                <span class="status-text" id="status-text-70">En pause</span>
                            </div>
                        </div>
                    </div>

                    <div class="machine-production-info">
                        <div class="production-info-item">
                            <span class="info-label">Prochaine production planifiée:</span>
                            <div class="info-value">Synthétique Restaurant - 16:45</div>
                        </div>
                        <div class="production-info-item">
                            <span class="info-label">Dernière production:</span>
                            <div class="info-value">Standard 55°C - 40min</div>
                        </div>
                        <div class="production-info-item">
                            <span class="info-label">Cycles du jour:</span>
                            <div class="info-value">12 cycles terminés</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Séchoirs Tab -->
        <div id="drying" class="tab-content">
            <h2>🌡️ Séchoirs</h2>
            
            <!-- Séchoirs Cards Grid -->
            <div class="machines-modern-grid">
                <!-- Séchoir 1 Card -->
                <div class="machine-modern-card" id="sechoir-card-1">
                    <div class="machine-modern-header">
                        <h3>Séchoir 1 - Poste Manuel Avancé</h3>
                    </div>
                    
                    <div class="machine-layout">
                        <div class="machine-form-section">
                            <form id="sechoirForm-1" onsubmit="saveSechoirProduction(event, 1)">
                                <input type="hidden" name="sechoir" value="1">
                                
                                <div class="form-group">
                                    <label for="program-sechoir-1">Programme en cours</label>
                                    <input type="text" id="program-sechoir-1" name="program" value="Standard 55°C" disabled class="disabled-input">
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="temperature-sechoir-1">Température (°C)</label>
                                        <input type="number" id="temperature-sechoir-1" name="temperature" value="55" disabled class="disabled-input">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="batch-sechoir-1">Code Batch</label>
                                        <input type="text" id="batch-sechoir-1" name="batch" placeholder="Code Batch..." disabled class="disabled-input">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="client-sechoir-1">Client</label>
                                    <select id="client-sechoir-1" name="client" disabled class="disabled-input">
                                        <option value="">Client Général</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="comment-sechoir-1">Commentaire Opérateur</label>
                                    <textarea id="comment-sechoir-1" name="comment" rows="3" placeholder="Commentaires opérateur..." disabled class="disabled-input"></textarea>
                                </div>
                            </form>
                        </div>

                        <div class="machine-control-section">
                            <div class="machine-timer-container">
                                <div class="machine-timer-display" id="sechoir-timer-1">00:20:00</div>
                                <div class="machine-timer-status" id="sechoir-timer-status-1">Temps restant</div>
                                
                                <div class="machine-cycle-info" id="sechoir-cycle-info-1" style="display: block;">
                                    <div class="cycle-program">Standard 55°C</div>
                                    <div class="cycle-weight">55°C</div>
                                    <div class="cycle-duration">Durée: 40 min</div>
                                </div>
                            </div>

                            <div class="machine-control-buttons">
                                <button type="button" class="machine-btn machine-btn-pause" id="sechoir-pause-1" onclick="pauseSechoir(1)">
                                    Pause
                                </button>
                                <button type="button" class="machine-btn machine-btn-stop" id="sechoir-stop-1" onclick="stopSechoir(1)">
                                    Stop
                                </button>
                                <button type="button" class="machine-btn machine-btn-info" id="sechoir-clean-1" onclick="cleanSechoir(1)">
                                    Nettoyage
                                </button>
                                <button type="button" class="machine-btn machine-btn-orange" id="sechoir-relance-1" onclick="relanceSechoir(1)">
                                    Relance
                                </button>
                            </div>

                            <div class="machine-status-indicator">
                                <div class="status-dot running" id="status-dot-sechoir-1"></div>
                                <span class="status-text" id="status-text-sechoir-1">En fonctionnement</span>
                            </div>
                        </div>
                    </div>

                    <div class="machine-production-info">
                        <div class="production-info-item">
                            <span class="info-label">Prochaine production planifiée:</span>
                            <div class="info-value">Serviettes Hôtel - 17:15</div>
                        </div>
                        <div class="production-info-item">
                            <span class="info-label">Dernière production:</span>
                            <div class="info-value">Standard 55°C - 40min</div>
                        </div>
                        <div class="production-info-item">
                            <span class="info-label">Cycles du jour:</span>
                            <div class="info-value">8 cycles terminés</div>
                        </div>
                    </div>
                </div>

                <!-- Séchoir 2 Card -->
                <div class="machine-modern-card" id="sechoir-card-2">
                    <div class="machine-modern-header">
                        <h3>Séchoir 2 - Poste Manuel Avancé</h3>
                    </div>
                    
                    <div class="machine-layout">
                        <div class="machine-form-section">
                            <form id="sechoirForm-2" onsubmit="saveSechoirProduction(event, 2)">
                                <input type="hidden" name="sechoir" value="2">
                                
                                <div class="form-group">
                                    <label for="program-sechoir-2">Programme</label>
                                    <select id="program-sechoir-2" name="program" onchange="updateSechoirProgramInfo(2)" required>
                                        <option value="">Choisir un programme préparé...</option>
                                    </select>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="temperature-sechoir-2">Température (°C)</label>
                                        <input type="number" id="temperature-sechoir-2" name="temperature" placeholder="55" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="batch-sechoir-2">Code Batch</label>
                                        <input type="text" id="batch-sechoir-2" name="batch" placeholder="Code Batch...">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="client-sechoir-2">Client</label>
                                    <select id="client-sechoir-2" name="client">
                                        <option value="">Client Général</option>
                                        <option value="hotel">Hôtel Le Grand</option>
                                        <option value="restaurant">Restaurant La Table</option>
                                        <option value="clinique">Clinique Saint-Michel</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="comment-sechoir-2">Commentaire Opérateur</label>
                                    <textarea id="comment-sechoir-2" name="comment" rows="3" placeholder="Commentaires opérateur..."></textarea>
                                </div>
                            </form>
                        </div>

                        <div class="machine-control-section">
                            <div class="machine-timer-container">
                                <div class="machine-timer-display" id="sechoir-timer-2">00:00:00</div>
                                <div class="machine-timer-status" id="sechoir-timer-status-2">Arrêté</div>
                            </div>

                            <div class="machine-control-buttons">
                                <button type="button" class="machine-btn machine-btn-start" id="sechoir-start-2" onclick="startSechoir(2)">
                                    Démarrer
                                </button>
                                <button type="button" class="machine-btn machine-btn-pause" id="sechoir-pause-2" onclick="pauseSechoir(2)" disabled>
                                    Pause
                                </button>
                                <button type="button" class="machine-btn machine-btn-stop" id="sechoir-stop-2" onclick="stopSechoir(2)" disabled>
                                    Stop
                                </button>
                                <button type="button" class="machine-btn machine-btn-info" id="sechoir-clean-2" onclick="cleanSechoir(2)">
                                    Nettoyage
                                </button>
                                <button type="button" class="machine-btn machine-btn-orange" id="sechoir-relance-2" onclick="relanceSechoir(2)">
                                    Relance
                                </button>
                            </div>

                            <div class="machine-status-indicator">
                                <div class="status-dot" id="status-dot-sechoir-2"></div>
                                <span class="status-text" id="status-text-sechoir-2">Arrêté</span>
                            </div>
                        </div>
                    </div>

                    <div class="machine-production-info">
                        <div class="production-info-item">
                            <span class="info-label">Prochaine production planifiée:</span>
                            <div class="info-value">Standard Restaurant - 16:30</div>
                        </div>
                        <div class="production-info-item">
                            <span class="info-label">Dernière production:</span>
                            <div class="info-value">Standard 55°C - 40min</div>
                        </div>
                        <div class="production-info-item">
                            <span class="info-label">Cycles du jour:</span>
                            <div class="info-value">8 cycles terminés</div>
                        </div>
                    </div>
                </div>

                <!-- Séchoir 3 Card -->
                <div class="machine-modern-card" id="sechoir-card-3">
                    <div class="machine-modern-header">
                        <h3>Séchoir 3 - Poste Manuel Avancé</h3>
                    </div>
                    
                    <div class="machine-layout">
                        <div class="machine-form-section">
                            <form id="sechoirForm-3" onsubmit="saveSechoirProduction(event, 3)">
                                <input type="hidden" name="sechoir" value="3">
                                
                                <div class="form-group">
                                    <label for="program-sechoir-3">Programme en cours</label>
                                    <input type="text" id="program-sechoir-3" name="program" value="Nettoyage filtre" disabled class="disabled-input">
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="temperature-sechoir-3">Température (°C)</label>
                                        <input type="number" id="temperature-sechoir-3" name="temperature" value="0" disabled class="disabled-input">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="batch-sechoir-3">Code Batch</label>
                                        <input type="text" id="batch-sechoir-3" name="batch" placeholder="Code Batch..." disabled class="disabled-input">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="client-sechoir-3">Client</label>
                                    <select id="client-sechoir-3" name="client" disabled class="disabled-input">
                                        <option value="">Maintenance</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="comment-sechoir-3">Commentaire Opérateur</label>
                                    <textarea id="comment-sechoir-3" name="comment" rows="3" placeholder="Maintenance en cours..." disabled class="disabled-input"></textarea>
                                </div>
                            </form>
                        </div>

                        <div class="machine-control-section">
                            <div class="machine-timer-container">
                                <div class="machine-timer-display" id="sechoir-timer-3">00:12:00</div>
                                <div class="machine-timer-status" id="sechoir-timer-status-3">Maintenance</div>
                                
                                <div class="machine-cycle-info" id="sechoir-cycle-info-3" style="display: block;">
                                    <div class="cycle-program">Nettoyage filtre</div>
                                    <div class="cycle-weight">Maintenance</div>
                                    <div class="cycle-duration">Durée restante: 12 min</div>
                                </div>
                            </div>

                            <div class="machine-control-buttons">
                                <button type="button" class="machine-btn machine-btn-start" id="sechoir-start-3" onclick="startSechoir(3)" style="display: none;">
                                    Démarrer
                                </button>
                                <button type="button" class="machine-btn machine-btn-pause" id="sechoir-pause-3" onclick="pauseSechoir(3)" disabled>
                                    Pause
                                </button>
                                <button type="button" class="machine-btn machine-btn-start" id="sechoir-resume-3" onclick="resumeSechoir(3)">
                                    Reprendre après maintenance
                                </button>
                                <button type="button" class="machine-btn machine-btn-info" id="sechoir-clean-3" onclick="cleanSechoir(3)" disabled>
                                    Nettoyage
                                </button>
                                <button type="button" class="machine-btn machine-btn-orange" id="sechoir-relance-3" onclick="relanceSechoir(3)">
                                    Relance
                                </button>
                            </div>

                            <div class="machine-status-indicator">
                                <div class="status-dot maintenance" id="status-dot-sechoir-3"></div>
                                <span class="status-text" id="status-text-sechoir-3">Maintenance</span>
                            </div>
                        </div>
                    </div>

                    <div class="machine-production-info">
                        <div class="production-info-item">
                            <span class="info-label">Prochaine production planifiée:</span>
                            <div class="info-value">Standard Restaurant - 17:45</div>
                        </div>
                        <div class="production-info-item">
                            <span class="info-label">Dernière production:</span>
                            <div class="info-value">Synthétique 45°C - 35min</div>
                        </div>
                        <div class="production-info-item">
                            <span class="info-label">Cycles du jour:</span>
                            <div class="info-value">6 cycles terminés</div>
                        </div>
                    </div>
                </div>

                <!-- Séchoir 4 Card -->
                <div class="machine-modern-card" id="sechoir-card-4">
                    <div class="machine-modern-header">
                        <h3>Séchoir 4 - Poste Manuel Avancé</h3>
                    </div>
                    
                    <div class="machine-layout">
                        <div class="machine-form-section">
                            <form id="sechoirForm-4" onsubmit="saveSechoirProduction(event, 4)">
                                <input type="hidden" name="sechoir" value="4">
                                
                                <div class="form-group">
                                    <label for="program-sechoir-4">Programme</label>
                                    <select id="program-sechoir-4" name="program" onchange="updateSechoirProgramInfo(4)" required>
                                        <option value="">Choisir un programme préparé...</option>
                                    </select>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="temperature-sechoir-4">Température (°C)</label>
                                        <input type="number" id="temperature-sechoir-4" name="temperature" placeholder="55" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="batch-sechoir-4">Code Batch</label>
                                        <input type="text" id="batch-sechoir-4" name="batch" placeholder="Code Batch...">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="client-sechoir-4">Client</label>
                                    <select id="client-sechoir-4" name="client">
                                        <option value="">Client Général</option>
                                        <option value="hotel">Hôtel Le Grand</option>
                                        <option value="restaurant">Restaurant La Table</option>
                                        <option value="clinique">Clinique Saint-Michel</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="comment-sechoir-4">Commentaire Opérateur</label>
                                    <textarea id="comment-sechoir-4" name="comment" rows="3" placeholder="Commentaires opérateur..."></textarea>
                                </div>
                            </form>
                        </div>

                        <div class="machine-control-section">
                            <div class="machine-timer-container">
                                <div class="machine-timer-display" id="sechoir-timer-4">00:00:00</div>
                                <div class="machine-timer-status" id="sechoir-timer-status-4">Arrêté</div>
                            </div>

                            <div class="machine-control-buttons">
                                <button type="button" class="machine-btn machine-btn-start" id="sechoir-start-4" onclick="startSechoir(4)">
                                    Démarrer
                                </button>
                                <button type="button" class="machine-btn machine-btn-pause" id="sechoir-pause-4" onclick="pauseSechoir(4)" disabled>
                                    Pause
                                </button>
                                <button type="button" class="machine-btn machine-btn-stop" id="sechoir-stop-4" onclick="stopSechoir(4)" disabled>
                                    Stop
                                </button>
                                <button type="button" class="machine-btn machine-btn-info" id="sechoir-clean-4" onclick="cleanSechoir(4)">
                                    Nettoyage
                                </button>
                                <button type="button" class="machine-btn machine-btn-orange" id="sechoir-relance-4" onclick="relanceSechoir(4)">
                                    Relance
                                </button>
                            </div>

                            <div class="machine-status-indicator">
                                <div class="status-dot" id="status-dot-sechoir-4"></div>
                                <span class="status-text" id="status-text-sechoir-4">Arrêté</span>
                            </div>
                        </div>
                    </div>

                    <div class="machine-production-info">
                        <div class="production-info-item">
                            <span class="info-label">Prochaine production planifiée:</span>
                            <div class="info-value">Délicat Clinique - 18:15</div>
                        </div>
                        <div class="production-info-item">
                            <span class="info-label">Dernière production:</span>
                            <div class="info-value">Délicat 45°C - 35min</div>
                        </div>
                        <div class="production-info-item">
                            <span class="info-label">Cycles du jour:</span>
                            <div class="info-value">5 cycles terminés</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Finition Tab -->
        <div id="finishing" class="tab-content">
            <h2>📏 Finition</h2>
            
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
        
        <div id="stops" class="tab-content">
            <iframe src="arrets.php" style="width:100%;height:800px;border:none;"></iframe>
        </div>
        
        <div id="nc" class="tab-content">
            <iframe src="nonconformites.php" style="width:100%;height:800px;border:none;"></iframe>
        </div>
        
        <div id="history" class="tab-content">
            <iframe src="historique.php" style="width:100%;height:800px;border:none;"></iframe>
        </div>
        
        <div id="trs-tracking" class="tab-content">
            <iframe src="suivi-trs.php" style="width:100%;height:800px;border:none;"></iframe>
        </div>
        
        <!-- Réglages Tab -->
        <div id="settings" class="tab-content">
            <h2>⚙️ Réglages</h2>
            
            <!-- Onglets réglages -->
            <div class="finition-tabs">
                <button class="finition-tab-btn active" onclick="showSettingsTab('programmes')">Programmes</button>
                <button class="finition-tab-btn" onclick="showSettingsTab('operateurs')">Opérateurs</button>
                <button class="finition-tab-btn" onclick="showSettingsTab('motifs')">Motifs d'arrêts</button>
                <button class="finition-tab-btn" onclick="showSettingsTab('types-nc')">Types NC</button>
                <button class="finition-tab-btn" onclick="showSettingsTab('causes-5m')">Causes 5M</button>
            </div>

            <!-- Onglet Programmes -->
            <div id="programmes" class="finition-tab-content active">
                <div class="form-section">
                    <div class="page-header-modern">
                        <h3>Programmes</h3>
                        <button class="btn btn-primary" onclick="addNewProgram()">Nouveau programme</button>
                    </div>
                    
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Nom de programme</th>
                                    <th>Durée</th>
                                    <th>Température</th>
                                    <th>Cadence nominale</th>
                                    <th>Options</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Coton 60°C</strong></td>
                                    <td>45min</td>
                                    <td>60°C</td>
                                    <td>26kg/h</td>
                                    <td>Pré-lavage</td>
                                    <td><span class="badge badge-success">Actif</span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-icon edit" title="Modifier">✏️</button>
                                            <button class="btn-icon view" title="Supprimer">🗑️</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Synthétique 40°C</strong></td>
                                    <td>35min</td>
                                    <td>40°C</td>
                                    <td>34kg/h</td>
                                    <td>-</td>
                                    <td><span class="badge badge-success">Actif</span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-icon edit" title="Modifier">✏️</button>
                                            <button class="btn-icon view" title="Supprimer">🗑️</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Standard 55°C</strong></td>
                                    <td>40min</td>
                                    <td>55°C</td>
                                    <td>30kg/h</td>
                                    <td>Pré-lavage</td>
                                    <td><span class="badge badge-success">Actif</span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-icon edit" title="Modifier">✏️</button>
                                            <button class="btn-icon view" title="Supprimer">🗑️</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Onglet Opérateurs -->
            <div id="operateurs" class="finition-tab-content">
                <div class="form-section">
                    <div class="page-header-modern">
                        <h3>Opérateurs</h3>
                        <button class="btn btn-primary" onclick="addNewOperator()">Nouvel opérateur</button>
                    </div>
                    
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Login</th>
                                    <th>Rôle</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Jean Dupont</strong></td>
                                    <td>jdupont</td>
                                    <td><span class="badge badge-info">OPERATEUR</span></td>
                                    <td><span class="badge badge-success">Actif</span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-icon edit" title="Modifier">✏️</button>
                                            <button class="btn btn-info btn-sm">Reset MDP</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Marie Leroy</strong></td>
                                    <td>mleroy</td>
                                    <td><span class="badge badge-warning">CHEF_ATELIER</span></td>
                                    <td><span class="badge badge-success">Actif</span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-icon edit" title="Modifier">✏️</button>
                                            <button class="btn btn-info btn-sm">Reset MDP</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Sophie Admin</strong></td>
                                    <td>sadmin</td>
                                    <td><span class="badge badge-danger">ADMIN</span></td>
                                    <td><span class="badge badge-success">Actif</span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-icon edit" title="Modifier">✏️</button>
                                            <button class="btn btn-info btn-sm">Reset MDP</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Onglet Motifs d'arrêts -->
            <div id="motifs" class="finition-tab-content">
                <div class="form-section">
                    <div class="page-header-modern">
                        <h3>Motifs d'arrêt</h3>
                        <button class="btn btn-primary" onclick="addNewMotif()">Nouveau motif</button>
                    </div>
                    
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Description</th>
                                    <th>Type</th>
                                    <th>Catégorie</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>AT01</strong></td>
                                    <td>Attente opérateur</td>
                                    <td><span class="badge badge-warning">INTER_CYCLE</span></td>
                                    <td>ORGANISATION</td>
                                    <td><span class="badge badge-success">Actif</span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-icon edit" title="Modifier">✏️</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>NF01</strong></td>
                                    <td>Nettoyage filtre</td>
                                    <td><span class="badge badge-info">NETTOYAGE</span></td>
                                    <td>MECANIQUE</td>
                                    <td><span class="badge badge-success">Actif</span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-icon edit" title="Modifier">✏️</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>MP01</strong></td>
                                    <td>Maintenance préventive</td>
                                    <td><span class="badge badge-success">PLANIFIE</span></td>
                                    <td>MECANIQUE</td>
                                    <td><span class="badge badge-success">Actif</span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-icon edit" title="Modifier">✏️</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Onglet Types NC -->
            <div id="types-nc" class="finition-tab-content">
                <div class="form-section">
                    <div class="page-header-modern">
                        <h3>Types de non-conformités</h3>
                        <button class="btn btn-primary" onclick="addNewTypeNC()">Nouveau type</button>
                    </div>
                    
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Description</th>
                                    <th>Gravité par défaut</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>QUA01</strong></td>
                                    <td>Défaut qualité</td>
                                    <td><span class="badge badge-secondary">MINEURE</span></td>
                                    <td><span class="badge badge-success">Actif</span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-icon edit" title="Modifier">✏️</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>SEC01</strong></td>
                                    <td>Problème sécurité</td>
                                    <td><span class="badge badge-warning">MAJEURE</span></td>
                                    <td><span class="badge badge-success">Actif</span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-icon edit" title="Modifier">✏️</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Onglet Causes 5M -->
            <div id="causes-5m" class="finition-tab-content">
                <div class="form-section">
                    <h3>Causes 5M</h3>
                    <div class="analysis-5m">
                        <div class="analysis-category">
                            <div class="page-header-modern">
                                <h4>👥 Main-d'œuvre</h4>
                                <button class="btn btn-primary btn-sm" onclick="addCause5M('mainoeuvre')">Ajouter</button>
                            </div>
                            <div class="causes-management">
                                <div class="cause-item">
                                    <span>Formation insuffisante</span>
                                    <div class="action-buttons">
                                        <button class="btn-icon edit">✏️</button>
                                        <button class="btn-icon view">🗑️</button>
                                    </div>
                                </div>
                                <div class="cause-item">
                                    <span>Fatigue opérateur</span>
                                    <div class="action-buttons">
                                        <button class="btn-icon edit">✏️</button>
                                        <button class="btn-icon view">🗑️</button>
                                    </div>
                                </div>
                                <div class="cause-item">
                                    <span>Erreur humaine</span>
                                    <div class="action-buttons">
                                        <button class="btn-icon edit">✏️</button>
                                        <button class="btn-icon view">🗑️</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="analysis-category">
                            <div class="page-header-modern">
                                <h4>📋 Méthode</h4>
                                <button class="btn btn-primary btn-sm" onclick="addCause5M('methode')">Ajouter</button>
                            </div>
                            <div class="causes-management">
                                <div class="cause-item">
                                    <span>Procédure incorrecte</span>
                                    <div class="action-buttons">
                                        <button class="btn-icon edit">✏️</button>
                                        <button class="btn-icon view">🗑️</button>
                                    </div>
                                </div>
                                <div class="cause-item">
                                    <span>Consignes peu claires</span>
                                    <div class="action-buttons">
                                        <button class="btn-icon edit">✏️</button>
                                        <button class="btn-icon view">🗑️</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="analysis-category">
                            <div class="page-header-modern">
                                <h4>🧪 Matière</h4>
                                <button class="btn btn-primary btn-sm" onclick="addCause5M('matiere')">Ajouter</button>
                            </div>
                            <div class="causes-management">
                                <div class="cause-item">
                                    <span>Qualité matière</span>
                                    <div class="action-buttons">
                                        <button class="btn-icon edit">✏️</button>
                                        <button class="btn-icon view">🗑️</button>
                                    </div>
                                </div>
                                <div class="cause-item">
                                    <span>Approvisionnement</span>
                                    <div class="action-buttons">
                                        <button class="btn-icon edit">✏️</button>
                                        <button class="btn-icon view">🗑️</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="analysis-category">
                            <div class="page-header-modern">
                                <h4>🌡️ Milieu</h4>
                                <button class="btn btn-primary btn-sm" onclick="addCause5M('milieu')">Ajouter</button>
                            </div>
                            <div class="causes-management">
                                <div class="cause-item">
                                    <span>Température ambiante</span>
                                    <div class="action-buttons">
                                        <button class="btn-icon edit">✏️</button>
                                        <button class="btn-icon view">🗑️</button>
                                    </div>
                                </div>
                                <div class="cause-item">
                                    <span>Humidité</span>
                                    <div class="action-buttons">
                                        <button class="btn-icon edit">✏️</button>
                                        <button class="btn-icon view">🗑️</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="analysis-category">
                            <div class="page-header-modern">
                                <h4>⚙️ Machine</h4>
                                <button class="btn btn-primary btn-sm" onclick="addCause5M('machine')">Ajouter</button>
                            </div>
                            <div class="causes-management">
                                <div class="cause-item">
                                    <span>Panne équipement</span>
                                    <div class="action-buttons">
                                        <button class="btn-icon edit">✏️</button>
                                        <button class="btn-icon view">🗑️</button>
                                    </div>
                                </div>
                                <div class="cause-item">
                                    <span>Usure normale</span>
                                    <div class="action-buttons">
                                        <button class="btn-icon edit">✏️</button>
                                        <button class="btn-icon view">🗑️</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="production" class="tab-content">
            <iframe src="hub-production-complete.php" style="width:100%;height:800px;border:none;"></iframe>
        </div>
    </div>

    <script>
        // Tab navigation
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }

        function showFinishingTab(tabName) {
            document.querySelectorAll('.finishing-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('#finishing .tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            document.getElementById(tabName + '-tab').classList.add('active');
            event.target.classList.add('active');
        }

        // Update time
        function updateTime() {
            const now = new Date();
            document.getElementById('current-time').textContent = now.toLocaleTimeString();
        }
        setInterval(updateTime, 1000);

        // Load dashboard data
        function loadDashboardData() {
            fetch('ajax.php?action=get_dashboard_data')
                .then(response => response.json())
                .then(data => {
                    updateDashboard(data);
                })
                .catch(error => console.error('Error loading dashboard:', error));
        }

        function updateDashboard(data) {
            // Update equipment sections
            if (data.sechoirs) {
                document.getElementById('sechoirs-dashboard').innerHTML = data.sechoirs;
            }
            if (data.finition) {
                document.getElementById('finition-dashboard').innerHTML = data.finition;
            }
            if (data.activities) {
                document.getElementById('recent-activities').innerHTML = data.activities;
            }
        }

        // Fonctions pour les machines
        function startMachine(machineId) {
            const timer = document.getElementById(`machine-timer-${machineId}`);
            const status = document.getElementById(`machine-timer-status-${machineId}`);
            const statusDot = document.getElementById(`status-dot-${machineId}`);
            const statusText = document.getElementById(`status-text-${machineId}`);
            const startBtn = document.getElementById(`machine-start-${machineId}`);
            const pauseBtn = document.getElementById(`machine-pause-${machineId}`);
            const stopBtn = document.getElementById(`machine-stop-${machineId}`);
            
            status.textContent = 'En cours';
            statusDot.classList.add('running');
            statusText.textContent = 'En fonctionnement';
            
            startBtn.disabled = true;
            pauseBtn.disabled = false;
            stopBtn.disabled = false;
            
            // Démarrer le timer
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

        function pauseMachine(machineId) {
            const timer = document.getElementById(`machine-timer-${machineId}`);
            const status = document.getElementById(`machine-timer-status-${machineId}`);
            const statusDot = document.getElementById(`status-dot-${machineId}`);
            const statusText = document.getElementById(`status-text-${machineId}`);
            
            status.textContent = 'En pause';
            statusDot.classList.remove('running');
            statusDot.classList.add('paused');
            statusText.textContent = 'En pause';
            
            if (timer.dataset.interval) {
                clearInterval(timer.dataset.interval);
            }
        }

        function resumeMachine(machineId) {
            startMachine(machineId);
        }

        function stopMachine(machineId) {
            const timer = document.getElementById(`machine-timer-${machineId}`);
            const status = document.getElementById(`machine-timer-status-${machineId}`);
            const statusDot = document.getElementById(`status-dot-${machineId}`);
            const statusText = document.getElementById(`status-text-${machineId}`);
            const startBtn = document.getElementById(`machine-start-${machineId}`);
            const pauseBtn = document.getElementById(`machine-pause-${machineId}`);
            const stopBtn = document.getElementById(`machine-stop-${machineId}`);
            
            timer.textContent = '00:00:00';
            status.textContent = 'Arrêté';
            statusDot.classList.remove('running', 'paused');
            statusText.textContent = 'Arrêté';
            
            startBtn.disabled = false;
            pauseBtn.disabled = true;
            stopBtn.disabled = true;
            
            if (timer.dataset.interval) {
                clearInterval(timer.dataset.interval);
            }
        }

        // Fonctions pour les séchoirs
        function startSechoir(sechoirId) {
            const timer = document.getElementById(`sechoir-timer-${sechoirId}`);
            const status = document.getElementById(`sechoir-timer-status-${sechoirId}`);
            const statusDot = document.getElementById(`status-dot-sechoir-${sechoirId}`);
            const statusText = document.getElementById(`status-text-sechoir-${sechoirId}`);
            
            status.textContent = 'Temps restant';
            statusDot.classList.add('running');
            statusText.textContent = 'En fonctionnement';
            
            // Démarrer le timer
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

        function pauseSechoir(sechoirId) {
            const timer = document.getElementById(`sechoir-timer-${sechoirId}`);
            const status = document.getElementById(`sechoir-timer-status-${sechoirId}`);
            const statusDot = document.getElementById(`status-dot-sechoir-${sechoirId}`);
            const statusText = document.getElementById(`status-text-sechoir-${sechoirId}`);
            
            status.textContent = 'En pause';
            statusDot.classList.remove('running');
            statusDot.classList.add('paused');
            statusText.textContent = 'En pause';
            
            if (timer.dataset.interval) {
                clearInterval(timer.dataset.interval);
            }
        }

        function stopSechoir(sechoirId) {
            const timer = document.getElementById(`sechoir-timer-${sechoirId}`);
            const status = document.getElementById(`sechoir-timer-status-${sechoirId}`);
            const statusDot = document.getElementById(`status-dot-sechoir-${sechoirId}`);
            const statusText = document.getElementById(`status-text-sechoir-${sechoirId}`);
            
            timer.textContent = '00:00:00';
            status.textContent = 'Arrêté';
            statusDot.classList.remove('running', 'paused');
            statusText.textContent = 'Arrêté';
            
            if (timer.dataset.interval) {
                clearInterval(timer.dataset.interval);
            }
        }

        function cleanSechoir(sechoirId) {
            alert(`Démarrage du cycle de nettoyage automatique du séchoir ${sechoirId}`);
        }

        function relanceSechoir(sechoirId) {
            alert(`Relance du séchoir ${sechoirId} avec nouveaux paramètres`);
        }

        function relanceMachine(machineId) {
            alert(`Relance de la machine ${machineId} avec nouveaux paramètres`);
        }

        // Fonctions pour la finition
        function showFinitionTab(tabName) {
            // Hide all finition tab contents
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

        function startCalandre() {
            const timer = document.getElementById('calandre-timer');
            const status = document.getElementById('calandre-timer-status');
            const statusDot = document.getElementById('status-dot-calandre');
            const statusText = document.getElementById('status-text-calandre');
            
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
            alert('Production Calandre enregistrée avec succès !');
        }

        function saveRepassageProduction(event) {
            event.preventDefault();
            alert('Production Repassage enregistrée avec succès !');
        }

        function saveMachineProduction(event, machineId) {
            event.preventDefault();
            alert(`Production Machine ${machineId} enregistrée avec succès !`);
        }

        function saveSechoirProduction(event, sechoirId) {
            event.preventDefault();
            alert(`Production Séchoir ${sechoirId} enregistrée avec succès !`);
        }

        // Fonctions pour les réglages
        function showSettingsTab(tabName) {
            // Hide all settings tab contents
            document.querySelectorAll('#settings .finition-tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('#settings .finition-tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }

        function addNewProgram() {
            const name = prompt('Nom du programme:');
            const duration = prompt('Durée en minutes:');
            const temperature = prompt('Température en °C:');
            const rate = prompt('Cadence nominale (kg/h):');
            
            if (name && duration && temperature && rate) {
                fetch('ajax.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `action=add_program&name=${encodeURIComponent(name)}&duration=${duration}&temperature=${temperature}&rate=${rate}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Programme créé avec succès !');
                        location.reload();
                    } else {
                        alert('Erreur: ' + data.error);
                    }
                })
                .catch(error => {
                    alert('Erreur de connexion');
                    console.error(error);
                });
            }
        }

        function addNewOperator() {
            const firstName = prompt('Prénom:');
            const lastName = prompt('Nom:');
            const login = prompt('Login:');
            const role = prompt('Rôle (OPERATEUR/CHEF_ATELIER/ADMIN):');
            
            if (firstName && lastName && login && role) {
                fetch('ajax.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `action=add_operator&firstName=${encodeURIComponent(firstName)}&lastName=${encodeURIComponent(lastName)}&login=${login}&role=${role}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Opérateur créé avec succès !');
                        location.reload();
                    } else {
                        alert('Erreur: ' + data.error);
                    }
                })
                .catch(error => {
                    alert('Erreur de connexion');
                    console.error(error);
                });
            }
        }

        function addNewMotif() {
            const code = prompt('Code du motif (ex: AT02):');
            const description = prompt('Description:');
            const type = prompt('Type (NON_PLANIFIE/PLANIFIE/INTER_CYCLE/NETTOYAGE):');
            const category = prompt('Catégorie (MECANIQUE/ELECTRIQUE/ORGANISATION/QUALITE/SECURITE/AUTRE):');
            
            if (code && description && type && category) {
                fetch('ajax.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `action=add_motif&code=${code}&description=${encodeURIComponent(description)}&type=${type}&category=${category}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Motif d\'arrêt créé avec succès !');
                        location.reload();
                    } else {
                        alert('Erreur: ' + data.error);
                    }
                })
                .catch(error => {
                    alert('Erreur de connexion');
                    console.error(error);
                });
            }
        }

        function addNewTypeNC() {
            const code = prompt('Code du type NC (ex: QUA02):');
            const description = prompt('Description:');
            const severity = prompt('Gravité (MINEURE/MAJEURE/CRITIQUE):');
            
            if (code && description && severity) {
                fetch('ajax.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `action=add_type_nc&code=${code}&description=${encodeURIComponent(description)}&severity=${severity}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Type de non-conformité créé avec succès !');
                        location.reload();
                    } else {
                        alert('Erreur: ' + data.error);
                    }
                })
                .catch(error => {
                    alert('Erreur de connexion');
                    console.error(error);
                });
            }
        }

        function addCause5M(axe) {
            const description = prompt(`Nouvelle cause ${axe}:`);
            
            if (description) {
                fetch('ajax.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `action=add_cause_5m&axe=${axe}&description=${encodeURIComponent(description)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Cause 5M créée avec succès !');
                        location.reload();
                    } else {
                        alert('Erreur: ' + data.error);
                    }
                })
                .catch(error => {
                    alert('Erreur de connexion');
                    console.error(error);
                });
            }
        }

        // Load data on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardData();
            // Refresh every 30 seconds
            setInterval(loadDashboardData, 30000);
        });
    </script>
</body>
</html>
