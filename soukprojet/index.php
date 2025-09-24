<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TRS Blanchisserie - Tableau de Bord</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏭 TRS Blanchisserie</h1>
            <p>Système de suivi du Taux de Rendement Synthétique</p>
        </div>

        <!-- Page d'accueil -->
        <div id="home" class="page active">
            <div class="nav-grid">
                <div class="nav-card machine" onclick="showPage('machines')">
                    <div class="nav-card-icon">🔧</div>
                    <h3>Machines à Laver</h3>
                    <p>Gestion des cycles de lavage<br>Machines 13, 20, 50, 70</p>
                </div>
                
                <div class="nav-card sechoir" onclick="showPage('sechoirs')">
                    <div class="nav-card-icon">🌡️</div>
                    <h3>Séchoirs</h3>
                    <p>Gestion du séchage<br>Séchoirs 1, 2, 3, 4</p>
                </div>
                
                <div class="nav-card calandre" onclick="showPage('calandre')">
                    <div class="nav-card-icon">📏</div>
                    <h3>Calandre</h3>
                    <p>Poste manuel<br>Linge plat</p>
                </div>
                
                <div class="nav-card repassage" onclick="showPage('repassage')">
                    <div class="nav-card-icon">👔</div>
                    <h3>Repassage</h3>
                    <p>Poste manuel<br>Chemises, blouses</p>
                </div>
                
                <div class="nav-card arret" onclick="showPage('arrets')">
                    <div class="nav-card-icon">⚠️</div>
                    <h3>Arrêts</h3>
                    <p>Déclaration des pannes<br>et arrêts techniques</p>
                </div>
                
                <div class="nav-card nc" onclick="showPage('nonconformites')">
                    <div class="nav-card-icon">❌</div>
                    <h3>Non-conformités</h3>
                    <p>Déclaration des défauts<br>produit</p>
                </div>
                
                <div class="nav-card trs" onclick="showTRSPage()">
                    <div class="nav-card-icon">📊</div>
                    <h3>Suivi TRS</h3>
                    <p>Tableau de bord<br>Analytics & Reporting</p>
                </div>
                
                <div class="nav-card" style="border-left: 5px solid #8e44ad;" onclick="showPage('reglages')">
                    <div class="nav-card-icon">⚙️</div>
                    <h3>Réglages</h3>
                    <p>Configuration programmes<br>Machines & Séchoirs</p>
                </div>
            </div>
        </div>

        <!-- Page Machines -->
        <div id="machines" class="page">
            <div class="page-header">
                <h2 class="page-title">🔧 Machines à Laver - Gestion Multi-Machines</h2>
                <button class="back-btn" onclick="showPage('home')">← Retour</button>
            </div>

            <!-- Machine Cards Grid -->
            <div class="machines-grid">
                <!-- Machine 13 Card -->
                <div class="machine-card" id="machine-card-13">
                    <div class="machine-header">
                        <h3>🔧 Machine 13 (13kg)</h3>
                        <div class="machine-status-indicator" id="status-indicator-13">●</div>
                    </div>
                    
                    <form id="machineForm-13" onsubmit="saveMachineProduction(event, 13)">
                        <input type="hidden" name="machine" value="13">
                        
                        <div class="form-section">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="program-select-13">N° Programme :</label>
                                    <select id="program-select-13" name="program" onchange="updateProgramInfo(13)" required>
                                        <option value="">Sélectionner un programme</option>
                                    </select>
                                </div>
                            </div>

                            <div id="program-info-13" class="program-info hidden">
                                <h4>Informations du Programme</h4>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <span>📝</span>
                                        <div>
                                            <strong>Nom :</strong>
                                            <span id="program-name-13">-</span>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <span>⏱️</span>
                                        <div>
                                            <strong>Durée théorique :</strong>
                                            <span id="program-duration-13">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="machine-weight-13">Poids (kg) :</label>
                                    <input type="number" id="machine-weight-13" name="weight" min="1" max="13" step="0.1" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="machine-operator-13">Opérateur :</label>
                                    <input type="text" id="machine-operator-13" name="operator" required>
                                </div>
                            </div>
                        </div>

                        <div id="machine-status-13" class="status-display">
                            <div class="timer-display" id="machine-timer-13">00:00:00</div>
                            <p><strong>État :</strong> <span id="machine-state-13">Arrêtée</span></p>
                        </div>

                        <div class="btn-group">
                            <button type="button" class="btn btn-success" id="machine-start-13" onclick="startMachine(13)">
                                ▶️ Démarrer
                            </button>
                            <button type="button" class="btn btn-warning" id="machine-pause-13" onclick="pauseMachine(13)" disabled>
                                ⏸️ Pause
                            </button>
                            <button type="button" class="btn btn-danger" id="machine-stop-13" onclick="stopMachine(13)" disabled>
                                ⏹️ Arrêter
                            </button>
                            <button type="submit" class="btn btn-primary">
                                💾 Enregistrer
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Machine 20 Card -->
                <div class="machine-card" id="machine-card-20">
                    <div class="machine-header">
                        <h3>🔧 Machine 20 (20kg)</h3>
                        <div class="machine-status-indicator" id="status-indicator-20">●</div>
                    </div>
                    
                    <form id="machineForm-20" onsubmit="saveMachineProduction(event, 20)">
                        <input type="hidden" name="machine" value="20">
                        
                        <div class="form-section">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="program-select-20">N° Programme :</label>
                                    <select id="program-select-20" name="program" onchange="updateProgramInfo(20)" required>
                                        <option value="">Sélectionner un programme</option>
                                    </select>
                                </div>
                            </div>

                            <div id="program-info-20" class="program-info hidden">
                                <h4>Informations du Programme</h4>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <span>📝</span>
                                        <div>
                                            <strong>Nom :</strong>
                                            <span id="program-name-20">-</span>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <span>⏱️</span>
                                        <div>
                                            <strong>Durée théorique :</strong>
                                            <span id="program-duration-20">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="machine-weight-20">Poids (kg) :</label>
                                    <input type="number" id="machine-weight-20" name="weight" min="1" max="20" step="0.1" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="machine-operator-20">Opérateur :</label>
                                    <input type="text" id="machine-operator-20" name="operator" required>
                                </div>
                            </div>
                        </div>

                        <div id="machine-status-20" class="status-display">
                            <div class="timer-display" id="machine-timer-20">00:00:00</div>
                            <p><strong>État :</strong> <span id="machine-state-20">Arrêtée</span></p>
                        </div>

                        <div class="btn-group">
                            <button type="button" class="btn btn-success" id="machine-start-20" onclick="startMachine(20)">
                                ▶️ Démarrer
                            </button>
                            <button type="button" class="btn btn-warning" id="machine-pause-20" onclick="pauseMachine(20)" disabled>
                                ⏸️ Pause
                            </button>
                            <button type="button" class="btn btn-danger" id="machine-stop-20" onclick="stopMachine(20)" disabled>
                                ⏹️ Arrêter
                            </button>
                            <button type="submit" class="btn btn-primary">
                                💾 Enregistrer
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Machine 50 Card -->
                <div class="machine-card" id="machine-card-50">
                    <div class="machine-header">
                        <h3>🔧 Machine 50 (50kg)</h3>
                        <div class="machine-status-indicator" id="status-indicator-50">●</div>
                    </div>
                    
                    <form id="machineForm-50" onsubmit="saveMachineProduction(event, 50)">
                        <input type="hidden" name="machine" value="50">
                        
                        <div class="form-section">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="program-select-50">N° Programme :</label>
                                    <select id="program-select-50" name="program" onchange="updateProgramInfo(50)" required>
                                        <option value="">Sélectionner un programme</option>
                                    </select>
                                </div>
                            </div>

                            <div id="program-info-50" class="program-info hidden">
                                <h4>Informations du Programme</h4>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <span>📝</span>
                                        <div>
                                            <strong>Nom :</strong>
                                            <span id="program-name-50">-</span>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <span>⏱️</span>
                                        <div>
                                            <strong>Durée théorique :</strong>
                                            <span id="program-duration-50">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="machine-weight-50">Poids (kg) :</label>
                                    <input type="number" id="machine-weight-50" name="weight" min="1" max="50" step="0.1" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="machine-operator-50">Opérateur :</label>
                                    <input type="text" id="machine-operator-50" name="operator" required>
                                </div>
                            </div>
                        </div>

                        <div id="machine-status-50" class="status-display">
                            <div class="timer-display" id="machine-timer-50">00:00:00</div>
                            <p><strong>État :</strong> <span id="machine-state-50">Arrêtée</span></p>
                        </div>

                        <div class="btn-group">
                            <button type="button" class="btn btn-success" id="machine-start-50" onclick="startMachine(50)">
                                ▶️ Démarrer
                            </button>
                            <button type="button" class="btn btn-warning" id="machine-pause-50" onclick="pauseMachine(50)" disabled>
                                ⏸️ Pause
                            </button>
                            <button type="button" class="btn btn-danger" id="machine-stop-50" onclick="stopMachine(50)" disabled>
                                ⏹️ Arrêter
                            </button>
                            <button type="submit" class="btn btn-primary">
                                💾 Enregistrer
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Machine 70 Card -->
                <div class="machine-card" id="machine-card-70">
                    <div class="machine-header">
                        <h3>🔧 Machine 70 (70kg)</h3>
                        <div class="machine-status-indicator" id="status-indicator-70">●</div>
                    </div>
                    
                    <form id="machineForm-70" onsubmit="saveMachineProduction(event, 70)">
                        <input type="hidden" name="machine" value="70">
                        
                        <div class="form-section">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="program-select-70">N° Programme :</label>
                                    <select id="program-select-70" name="program" onchange="updateProgramInfo(70)" required>
                                        <option value="">Sélectionner un programme</option>
                                    </select>
                                </div>
                            </div>

                            <div id="program-info-70" class="program-info hidden">
                                <h4>Informations du Programme</h4>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <span>📝</span>
                                        <div>
                                            <strong>Nom :</strong>
                                            <span id="program-name-70">-</span>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <span>⏱️</span>
                                        <div>
                                            <strong>Durée théorique :</strong>
                                            <span id="program-duration-70">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="machine-weight-70">Poids (kg) :</label>
                                    <input type="number" id="machine-weight-70" name="weight" min="1" max="70" step="0.1" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="machine-operator-70">Opérateur :</label>
                                    <input type="text" id="machine-operator-70" name="operator" required>
                                </div>
                            </div>
                        </div>

                        <div id="machine-status-70" class="status-display">
                            <div class="timer-display" id="machine-timer-70">00:00:00</div>
                            <p><strong>État :</strong> <span id="machine-state-70">Arrêtée</span></p>
                        </div>

                        <div class="btn-group">
                            <button type="button" class="btn btn-success" id="machine-start-70" onclick="startMachine(70)">
                                ▶️ Démarrer
                            </button>
                            <button type="button" class="btn btn-warning" id="machine-pause-70" onclick="pauseMachine(70)" disabled>
                                ⏸️ Pause
                            </button>
                            <button type="button" class="btn btn-danger" id="machine-stop-70" onclick="stopMachine(70)" disabled>
                                ⏹️ Arrêter
                            </button>
                            <button type="submit" class="btn btn-primary">
                                💾 Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Page Séchoirs -->
        <div id="sechoirs" class="page">
            <div class="page-header">
                <h2 class="page-title">🌡️ Séchoirs</h2>
                <button class="back-btn" onclick="showPage('home')">← Retour</button>
            </div>

            <form id="sechoirForm" onsubmit="saveSechoirProduction(event)">
                <div class="form-section">
                    <h3>Configuration du Séchage</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="sechoir-select">Séchoir :</label>
                            <select id="sechoir-select" name="sechoir" required>
                                <option value="">Sélectionner un séchoir</option>
                                <option value="1">Séchoir 1</option>
                                <option value="2">Séchoir 2</option>
                                <option value="3">Séchoir 3</option>
                                <option value="4">Séchoir 4</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="article-type">Type d'article :</label>
                            <select id="article-type" name="article_type" onchange="updateDryingRules()" required>
                                <option value="">Sélectionner un article</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="sechoir-duration">Durée (minutes) :</label>
                            <input type="number" id="sechoir-duration" name="duration" min="1" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="sechoir-temperature">Température (°C) :</label>
                            <input type="number" id="sechoir-temperature" name="temperature" min="30" max="85" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="sechoir-weight">Poids du linge (kg) :</label>
                            <input type="number" id="sechoir-weight" name="weight" min="1" step="0.1" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="sechoir-operator">Opérateur :</label>
                            <input type="text" id="sechoir-operator" name="operator" required>
                        </div>
                    </div>
                </div>

                <div id="sechoir-status" class="status-display">
                    <div class="timer-display" id="sechoir-timer">00:00:00</div>
                    <p><strong>État :</strong> <span id="sechoir-state">Arrêtée</span></p>
                    <p><strong>Température :</strong> <span id="sechoir-temp-display">-- °C</span></p>
                    <p><strong>Durée programmée :</strong> <span id="sechoir-duration-display">-- min</span></p>
                </div>

                <div class="btn-group">
                    <button type="button" class="btn btn-success" id="sechoir-start" onclick="startSechoir()">
                        ▶️ Démarrer
                    </button>
                    <button type="button" class="btn btn-danger" id="sechoir-stop" onclick="stopSechoir()" disabled>
                        ⏹️ Finir
                    </button>
                    <button type="button" class="btn btn-warning" onclick="recalculateSechoirSettings()">
                        🔄 Relance
                    </button>
                    <button type="submit" class="btn btn-primary">
                        💾 Enregistrer
                    </button>
                </div>
            </form>
        </div>

        <!-- Page Calandre -->
        <div id="calandre" class="page">
            <div class="page-header">
                <h2 class="page-title">📏 Calandre - Poste Manuel</h2>
                <button class="back-btn" onclick="showPage('home')">← Retour</button>
            </div>

            <form id="calandreForm" onsubmit="saveCalandreProduction(event)">
                <div class="form-section">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="calandre-weight">Poids (kg) :</label>
                            <input type="number" id="calandre-weight" name="weight" min="1" step="0.1" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="calandre-pieces">Nombre de pièces :</label>
                            <input type="number" id="calandre-pieces" name="pieces" min="1" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="calandre-operator">Opérateur :</label>
                            <input type="text" id="calandre-operator" name="operator" required>
                        </div>
                    </div>
                </div>

                <div id="calandre-status" class="status-display">
                    <div class="timer-display" id="calandre-timer">00:00:00</div>
                    <p><strong>État :</strong> <span id="calandre-state">Arrêtée</span></p>
                    <p><strong>Temps net :</strong> <span id="calandre-net-time">00:00:00</span></p>
                </div>

                <div class="btn-group">
                    <button type="button" class="btn btn-success" id="calandre-start" onclick="startCalandre()">
                        ▶️ Début
                    </button>
                    <button type="button" class="btn btn-warning" id="calandre-pause" onclick="pauseCalandre()" disabled>
                        ⏸️ Pause
                    </button>
                    <button type="button" class="btn btn-success" id="calandre-resume" onclick="resumeCalandre()" disabled>
                        ▶️ Reprendre
                    </button>
                    <button type="button" class="btn btn-danger" id="calandre-stop" onclick="stopCalandre()" disabled>
                        ⏹️ Finir
                    </button>
                    <button type="submit" class="btn btn-primary">
                        💾 Enregistrer
                    </button>
                </div>
            </form>
        </div>

        <!-- Page Repassage -->
        <div id="repassage" class="page">
            <div class="page-header">
                <h2 class="page-title">👔 Repassage - Poste Manuel</h2>
                <button class="back-btn" onclick="showPage('home')">← Retour</button>
            </div>

            <form id="repassageForm" onsubmit="saveRepassageProduction(event)">
                <div class="form-section">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="repassage-pieces">Nombre de pièces :</label>
                            <input type="number" id="repassage-pieces" name="pieces" min="1" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="repassage-type">Type d'article :</label>
                            <select id="repassage-type" name="article_type" required>
                                <option value="chemise">Chemise</option>
                                <option value="blouse">Blouse</option>
                                <option value="pantalon">Pantalon</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="repassage-operator">Opérateur :</label>
                            <input type="text" id="repassage-operator" name="operator" required>
                        </div>
                    </div>
                </div>

                <div id="repassage-status" class="status-display">
                    <div class="timer-display" id="repassage-timer">00:00:00</div>
                    <p><strong>État :</strong> <span id="repassage-state">Arrêtée</span></p>
                    <p><strong>Temps net :</strong> <span id="repassage-net-time">00:00:00</span></p>
                </div>

                <div class="btn-group">
                    <button type="button" class="btn btn-success" id="repassage-start" onclick="startRepassage()">
                        ▶️ Début
                    </button>
                    <button type="button" class="btn btn-warning" id="repassage-pause" onclick="pauseRepassage()" disabled>
                        ⏸️ Pause
                    </button>
                    <button type="button" class="btn btn-success" id="repassage-resume" onclick="resumeRepassage()" disabled>
                        ▶️ Reprendre
                    </button>
                    <button type="button" class="btn btn-danger" id="repassage-stop" onclick="stopRepassage()" disabled>
                        ⏹️ Finir
                    </button>
                    <button type="submit" class="btn btn-primary">
                        💾 Enregistrer
                    </button>
                </div>
            </form>
        </div>

        <!-- Page Arrêts -->
        <div id="arrets" class="page">
            <div class="page-header">
                <h2 class="page-title">⚠️ Déclaration d'Arrêt</h2>
                <button class="back-btn" onclick="showPage('home')">← Retour</button>
            </div>

            <div class="form-section">
                <h3>Arrêts récents</h3>
                <div id="arrets-table-container">
                    <!-- Table will be loaded via AJAX -->
                </div>
            </div>

            <form id="arretForm" onsubmit="saveArret(event)">
                <div class="form-section">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="arret-equipment">Équipement concerné :</label>
                            <select id="arret-equipment" name="equipment" required>
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
                            <label for="arret-reason">Raison de l'arrêt :</label>
                            <select id="arret-reason" name="reason" required>
                                <option value="panne-mecanique">Panne mécanique</option>
                                <option value="panne-electrique">Panne électrique</option>
                                <option value="maintenance">Maintenance préventive</option>
                                <option value="reglage">Réglage/Mise au point</option>
                                <option value="attente-linge">Attente linge</option>
                                <option value="attente-operateur">Attente opérateur</option>
                                <option value="nettoyage">Nettoyage</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="arret-start">Début de l'arrêt :</label>
                            <input type="datetime-local" id="arret-start" name="start_time" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="arret-end">Fin de l'arrêt :</label>
                            <input type="datetime-local" id="arret-end" name="end_time" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="arret-comment">Commentaire :</label>
                        <textarea id="arret-comment" name="comment" rows="4"></textarea>
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            💾 Enregistrer l'arrêt
                        </button>
                    </div>
                </div>
            </form>

        </div>

        <!-- Page Non-conformités -->
        <div id="nonconformites" class="page">
            <div class="page-header">
                <h2 class="page-title">❌ Déclaration de Non-conformité</h2>
                <button class="back-btn" onclick="showPage('home')">← Retour</button>
            </div>

            <div class="form-section">
                <h3>Non-conformités récentes</h3>
                <div id="nc-table-container">
                    <!-- Table will be loaded via AJAX -->
                </div>
            </div>

            <form id="nonconformiteForm" onsubmit="saveNonConformite(event)">
                <div class="form-section">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nc-equipment">Équipement concerné :</label>
                            <select id="nc-equipment" name="equipment" required>
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
                            <label for="nc-type">Type de non-conformité :</label>
                            <select id="nc-type" name="type" required>
                                <option value="linge-tache">Linge taché</option>
                                <option value="linge-brule">Linge brûlé</option>
                                <option value="mal-seche">Mal séché</option>
                                <option value="mal-lave">Mal lavé</option>
                                <option value="dechire">Déchiré</option>
                                <option value="decolore">Décoloré</option>
                                <option value="froisse">Froissé excessivement</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nc-quantity">Quantité affectée :</label>
                            <input type="number" id="nc-quantity" name="quantity" min="1" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="nc-severity">Gravité :</label>
                            <select id="nc-severity" name="severity" required>
                                <option value="mineure">Mineure</option>
                                <option value="majeure">Majeure</option>
                                <option value="critique">Critique</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nc-description">Description détaillée :</label>
                        <textarea id="nc-description" name="description" rows="4" required></textarea>
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            💾 Enregistrer la NC
                        </button>
                    </div>
                </div>
            </form>

        </div>

        <!-- Page Réglages -->
        <div id="reglages" class="page">
            <div class="page-header">
                <h2 class="page-title">⚙️ Réglages Avancés - Configuration Système</h2>
                <button class="back-btn" onclick="showPage('home')">← Retour</button>
            </div>

            <!-- Configuration Machines -->
            <div class="form-section">
                <h3>🔧 Configuration Machines à Laver</h3>
                <form id="machineProgram" onsubmit="addMachineProgram(event)">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="new-program-number">N° Programme :</label>
                            <input type="number" id="new-program-number" name="program_number" min="1" max="99" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new-program-name">Nom du Programme :</label>
                            <input type="text" id="new-program-name" name="name" placeholder="Ex: LINGE HOTELIER" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="new-program-duration">Durée théorique (minutes) :</label>
                            <input type="number" id="new-program-duration" name="duration_minutes" min="1" max="300" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new-program-capacity">Capacité recommandée (kg) :</label>
                            <input type="number" id="new-program-capacity" name="capacity" min="1" max="100" step="0.1" required>
                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            ➕ Ajouter Programme Machine
                        </button>
                    </div>
                </form>

                <h4>Programmes Machines Existants</h4>
                <div id="machine-programs-table-container">
                    <!-- Table will be loaded via AJAX -->
                </div>
            </div>

            <!-- Configuration Séchoirs -->
            <div class="form-section">
                <h3>🌡️ Configuration Séchoirs</h3>
                <form id="dryingProgram" onsubmit="addDryingProgram(event)">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="new-drying-name">Nom du Programme :</label>
                            <input type="text" id="new-drying-name" name="name" placeholder="Ex: COTON EPAIS" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new-drying-article">Article Concerné :</label>
                            <input type="text" id="new-drying-article" name="article_type" placeholder="Ex: Serviettes, Draps" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="new-drying-duration">Durée consigne (minutes) :</label>
                            <input type="number" id="new-drying-duration" name="duration_minutes" min="1" max="180" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new-drying-temperature">Température consigne (°C) :</label>
                            <input type="number" id="new-drying-temperature" name="temperature" min="30" max="85" required>
                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            ➕ Ajouter Programme Séchoir
                        </button>
                    </div>
                </form>

                <h4>Programmes Séchoirs Existants</h4>
                <div id="drying-programs-table-container">
                    <!-- Table will be loaded via AJAX -->
                </div>
            </div>

            <!-- Configuration Postes Manuels -->
            <div class="form-section">
                <h3>👥 Configuration Postes Manuels</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="calandre-target-rate">Cadence cible Calandre (pièces/h) :</label>
                        <input type="number" id="calandre-target-rate" name="calandre_rate" min="1" max="200" value="60">
                    </div>
                    
                    <div class="form-group">
                        <label for="repassage-target-rate">Cadence cible Repassage (pièces/h) :</label>
                        <input type="number" id="repassage-target-rate" name="repassage_rate" min="1" max="100" value="25">
                    </div>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-primary" onclick="saveManualStationsConfig()">
                        💾 Sauvegarder Cadences
                    </button>
                </div>
            </div>

            <!-- Configuration Codes Arrêts -->
            <div class="form-section">
                <h3>⚠️ Configuration Codes Arrêts</h3>
                <form id="stopCodeForm" onsubmit="addStopCode(event)">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="stop-code">Code Arrêt :</label>
                            <input type="text" id="stop-code" name="code" placeholder="Ex: PM01" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="stop-description">Description :</label>
                            <input type="text" id="stop-description" name="description" placeholder="Ex: Panne mécanique pompe" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="stop-type">Type d'arrêt :</label>
                            <select id="stop-type" name="type" required>
                                <option value="planifie">Planifié</option>
                                <option value="non-planifie">Non planifié</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="stop-category">Catégorie :</label>
                            <select id="stop-category" name="category" required>
                                <option value="mecanique">Mécanique</option>
                                <option value="electrique">Électrique</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="reglage">Réglage</option>
                                <option value="attente">Attente</option>
                                <option value="nettoyage">Nettoyage</option>
                            </select>
                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            ➕ Ajouter Code Arrêt
                        </button>
                    </div>
                </form>

                <h4>Codes Arrêts Existants</h4>
                <div id="stop-codes-table-container">
                    <!-- Table will be loaded via AJAX -->
                </div>
            </div>

            <!-- Configuration Codes Non-Conformités -->
            <div class="form-section">
                <h3>❌ Configuration Codes Non-Conformités</h3>
                <form id="ncCodeForm" onsubmit="addNCCode(event)">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nc-code">Code NC :</label>
                            <input type="text" id="nc-code" name="code" placeholder="Ex: Q01" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="nc-description">Description :</label>
                            <input type="text" id="nc-description" name="description" placeholder="Ex: Linge taché" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nc-severity">Gravité :</label>
                            <select id="nc-severity" name="severity" required>
                                <option value="mineure">Mineure</option>
                                <option value="majeure">Majeure</option>
                                <option value="critique">Critique</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="nc-type">Type :</label>
                            <select id="nc-type" name="type" required>
                                <option value="qualite">Qualité</option>
                                <option value="aspect">Aspect</option>
                                <option value="fonctionnel">Fonctionnel</option>
                                <option value="securite">Sécurité</option>
                            </select>
                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            ➕ Ajouter Code NC
                        </button>
                    </div>
                </form>

                <h4>Codes Non-Conformités Existants</h4>
                <div id="nc-codes-table-container">
                    <!-- Table will be loaded via AJAX -->
                </div>
            </div>

            <!-- Configuration Calendrier -->
            <div class="form-section">
                <h3>📅 Configuration Calendrier</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="working-hours-per-day">Heures de travail par jour :</label>
                        <input type="number" id="working-hours-per-day" name="working_hours" min="1" max="24" step="0.5" value="7">
                    </div>
                    
                    <div class="form-group">
                        <label for="working-days-per-week">Jours ouvrés par semaine :</label>
                        <input type="number" id="working-days-per-week" name="working_days" min="1" max="7" value="5">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="shift-start">Début de poste :</label>
                        <input type="time" id="shift-start" name="shift_start" value="08:00">
                    </div>
                    
                    <div class="form-group">
                        <label for="shift-end">Fin de poste :</label>
                        <input type="time" id="shift-end" name="shift_end" value="15:00">
                    </div>
                </div>

                <div class="btn-group">
                    <button type="button" class="btn btn-primary" onclick="saveCalendarConfig()">
                        💾 Sauvegarder Calendrier
                    </button>
                </div>
            </div>

            <!-- Configuration Seuils TRS -->
            <div class="form-section">
                <h3>🎯 Configuration Seuils TRS</h3>
                <div class="thresholds-grid">
                    <div class="threshold-group">
                        <h4>Disponibilité (%)</h4>
                        <div class="threshold-inputs">
                            <label>Rouge (≤) : <input type="number" id="disponibilite-red" min="0" max="100" value="80"></label>
                            <label>Ambre (≤) : <input type="number" id="disponibilite-amber" min="0" max="100" value="90"></label>
                            <label>Vert (>) : <input type="number" id="disponibilite-green" min="0" max="100" value="90" readonly></label>
                        </div>
                    </div>

                    <div class="threshold-group">
                        <h4>Performance (%)</h4>
                        <div class="threshold-inputs">
                            <label>Rouge (≤) : <input type="number" id="performance-red" min="0" max="100" value="75"></label>
                            <label>Ambre (≤) : <input type="number" id="performance-amber" min="0" max="100" value="85"></label>
                            <label>Vert (>) : <input type="number" id="performance-green" min="0" max="100" value="85" readonly></label>
                        </div>
                    </div>

                    <div class="threshold-group">
                        <h4>Qualité (%)</h4>
                        <div class="threshold-inputs">
                            <label>Rouge (≤) : <input type="number" id="qualite-red" min="0" max="100" value="85"></label>
                            <label>Ambre (≤) : <input type="number" id="qualite-amber" min="0" max="100" value="95"></label>
                            <label>Vert (>) : <input type="number" id="qualite-green" min="0" max="100" value="95" readonly></label>
                        </div>
                    </div>

                    <div class="threshold-group">
                        <h4>TRS Global (%)</h4>
                        <div class="threshold-inputs">
                            <label>Rouge (≤) : <input type="number" id="trs-red" min="0" max="100" value="60"></label>
                            <label>Ambre (≤) : <input type="number" id="trs-amber" min="0" max="100" value="75"></label>
                            <label>Vert (>) : <input type="number" id="trs-green" min="0" max="100" value="75" readonly></label>
                        </div>
                    </div>
                </div>

                <div class="btn-group">
                    <button type="button" class="btn btn-primary" onclick="saveThresholdsConfig()">
                        💾 Sauvegarder Seuils
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="resetThresholdsToDefault()">
                        🔄 Valeurs par défaut
                    </button>
                </div>
            </div>

            <!-- Configuration Capacités par Machine/Programme -->
            <div class="form-section">
                <h3>🏭 Configuration Capacités par Machine/Programme</h3>
                <p class="config-description">Définissez la capacité optimale pour chaque programme sur chaque machine</p>
                
                <div class="machine-program-config">
                    <div class="machine-config-section">
                        <h4>Machine 13 (13kg)</h4>
                        <div id="machine-13-programs-config" class="programs-config-container">
                            <!-- Will be populated via AJAX -->
                        </div>
                    </div>

                    <div class="machine-config-section">
                        <h4>Machine 20 (20kg)</h4>
                        <div id="machine-20-programs-config" class="programs-config-container">
                            <!-- Will be populated via AJAX -->
                        </div>
                    </div>

                    <div class="machine-config-section">
                        <h4>Machine 50 (50kg)</h4>
                        <div id="machine-50-programs-config" class="programs-config-container">
                            <!-- Will be populated via AJAX -->
                        </div>
                    </div>

                    <div class="machine-config-section">
                        <h4>Machine 70 (70kg)</h4>
                        <div id="machine-70-programs-config" class="programs-config-container">
                            <!-- Will be populated via AJAX -->
                        </div>
                    </div>
                </div>

                <div class="btn-group">
                    <button type="button" class="btn btn-primary" onclick="saveMachineProgramCapacities()">
                        💾 Sauvegarder Capacités Machine/Programme
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="loadMachineProgramCapacities()">
                        🔄 Recharger Configuration
                    </button>
                </div>
            </div>

            <!-- Configuration Capacités Équipements -->
            <div class="form-section">
                <h3>🏭 Configuration Capacités Équipements (Poids/kg)</h3>
                <p class="config-description">Définissez la capacité en poids (kg) pour chaque équipement</p>
                
                <div class="equipment-config-grid">
                    <!-- Machines à Laver -->
                    <div class="equipment-config-section">
                        <h4>🔧 Machines à Laver</h4>
                        <div class="equipment-list">
                            <div class="equipment-item">
                                <label>Machine 13 - Capacité max (kg) :</label>
                                <input type="number" id="machine-13-capacity" value="13" min="1" max="100" step="0.1">
                            </div>
                            <div class="equipment-item">
                                <label>Machine 20 - Capacité max (kg) :</label>
                                <input type="number" id="machine-20-capacity" value="20" min="1" max="100" step="0.1">
                            </div>
                            <div class="equipment-item">
                                <label>Machine 50 - Capacité max (kg) :</label>
                                <input type="number" id="machine-50-capacity" value="50" min="1" max="100" step="0.1">
                            </div>
                            <div class="equipment-item">
                                <label>Machine 70 - Capacité max (kg) :</label>
                                <input type="number" id="machine-70-capacity" value="70" min="1" max="100" step="0.1">
                            </div>
                        </div>
                    </div>

                    <!-- Séchoirs -->
                    <div class="equipment-config-section">
                        <h4>🌡️ Séchoirs</h4>
                        <div class="equipment-list">
                            <div class="equipment-item">
                                <label>Séchoir 1 - Capacité max (kg) :</label>
                                <input type="number" id="sechoir-1-capacity" value="25" min="1" max="100" step="0.1">
                            </div>
                            <div class="equipment-item">
                                <label>Séchoir 2 - Capacité max (kg) :</label>
                                <input type="number" id="sechoir-2-capacity" value="25" min="1" max="100" step="0.1">
                            </div>
                            <div class="equipment-item">
                                <label>Séchoir 3 - Capacité max (kg) :</label>
                                <input type="number" id="sechoir-3-capacity" value="25" min="1" max="100" step="0.1">
                            </div>
                            <div class="equipment-item">
                                <label>Séchoir 4 - Capacité max (kg) :</label>
                                <input type="number" id="sechoir-4-capacity" value="25" min="1" max="100" step="0.1">
                            </div>
                        </div>
                    </div>

                    <!-- Calandre -->
                    <div class="equipment-config-section">
                        <h4>📏 Calandre</h4>
                        <div class="equipment-list">
                            <div class="equipment-item">
                                <label>Calandre - Capacité max (kg) :</label>
                                <input type="number" id="calandre-capacity" value="15" min="1" max="100" step="0.1">
                            </div>
                            <div class="equipment-item">
                                <label>Calandre - Cadence cible (pièces/h) :</label>
                                <input type="number" id="calandre-target-rate" value="60" min="1" max="200">
                            </div>
                        </div>
                    </div>

                    <!-- Repassage -->
                    <div class="equipment-config-section">
                        <h4>👔 Repassage</h4>
                        <div class="equipment-list">
                            <div class="equipment-item">
                                <label>Repassage - Capacité max (kg) :</label>
                                <input type="number" id="repassage-capacity" value="10" min="1" max="100" step="0.1">
                            </div>
                            <div class="equipment-item">
                                <label>Repassage - Cadence cible (pièces/h) :</label>
                                <input type="number" id="repassage-target-rate" value="25" min="1" max="100">
                            </div>
                        </div>
                    </div>

                    <!-- Arrêts -->
                    <div class="equipment-config-section">
                        <h4>⚠️ Arrêts</h4>
                        <div class="equipment-list">
                            <div class="equipment-item">
                                <label>Seuil d'alerte arrêts (minutes) :</label>
                                <input type="number" id="arrets-alert-threshold" value="30" min="1" max="480">
                            </div>
                            <div class="equipment-item">
                                <label>Durée max arrêt planifié (minutes) :</label>
                                <input type="number" id="arrets-planned-max" value="120" min="1" max="480">
                            </div>
                        </div>
                    </div>

                    <!-- Non-conformités -->
                    <div class="equipment-config-section">
                        <h4>❌ Non-conformités</h4>
                        <div class="equipment-list">
                            <div class="equipment-item">
                                <label>Seuil d'alerte NC (quantité) :</label>
                                <input type="number" id="nc-alert-threshold" value="5" min="1" max="100">
                            </div>
                            <div class="equipment-item">
                                <label>Poids moyen par pièce NC (kg) :</label>
                                <input type="number" id="nc-average-weight" value="0.5" min="0.1" max="10" step="0.1">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="btn-group">
                    <button type="button" class="btn btn-primary" onclick="saveAllEquipmentCapacities()">
                        💾 Sauvegarder Toutes les Capacités
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="resetEquipmentCapacitiesToDefault()">
                        🔄 Valeurs par défaut
                    </button>
                </div>
            </div>
        </div>

        <!-- Page TRS (with authentication) -->
        <div id="trs-auth" class="auth-container" style="display: none;">
            <h2>🔒 Accès Sécurisé</h2>
            <p>Veuillez vous connecter pour accéder au tableau de bord TRS</p>
            <form onsubmit="authenticateTRS(event)">
                <div style="margin: 20px 0;">
                    <input type="text" id="trs-username" placeholder="Nom d'utilisateur" style="width: 100%; padding: 12px; margin-bottom: 15px;" required>
                    <input type="password" id="trs-password" placeholder="Mot de passe" style="width: 100%; padding: 12px; margin-bottom: 15px;" required>
                    <button type="submit" class="btn btn-primary">🔓 Se connecter</button>
                </div>
            </form>
            <button class="btn btn-secondary" onclick="showPage('home')">← Retour à l'accueil</button>
        </div>

        <!-- Page TRS Dashboard -->
        <div id="trs" class="page">
            <div class="page-header">
                <h2 class="page-title">📊 Tableau de Bord TRS Avancé</h2>
                <div class="header-actions">
                    <button class="btn btn-success" onclick="generatePDFReport()">📄 Rapport PDF</button>
                    <button class="back-btn" onclick="logout()">← Déconnexion</button>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="form-section">
                <h3>🔍 Filtres & Période d'Analyse</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="filter-date-start">Date de début :</label>
                        <input type="date" id="filter-date-start" name="date_start">
                    </div>
                    <div class="form-group">
                        <label for="filter-date-end">Date de fin :</label>
                        <input type="date" id="filter-date-end" name="date_end">
                    </div>
                    <div class="form-group">
                        <label for="filter-equipment">Équipement :</label>
                        <select id="filter-equipment" name="equipment">
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
                            <optgroup label="Postes manuels">
                                <option value="calandre">Calandre</option>
                                <option value="repassage">Repassage</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filter-operator">Opérateur :</label>
                        <input type="text" id="filter-operator" name="operator" placeholder="Nom de l'opérateur">
                    </div>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-primary" onclick="applyFilters()">
                        🔍 Appliquer les filtres
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                        🔄 Réinitialiser
                    </button>
                    <button type="button" class="btn btn-success" onclick="exportToExcel()">
                        📊 Exporter Excel
                    </button>
                </div>
            </div>

            <!-- Global Summary Section -->
            <div class="form-section">
                <h3>📈 Résumé Global</h3>
                <div class="summary-grid">
                    <div class="summary-card">
                        <div class="summary-value" id="global-trs">67.2%</div>
                        <div class="summary-label">TRS Global</div>
                        <div class="summary-trend" id="global-trs-trend">+2.1%</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-value" id="global-disponibilite">89.5%</div>
                        <div class="summary-label">Disponibilité</div>
                        <div class="summary-trend" id="global-disponibilite-trend">-1.2%</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-value" id="global-performance">82.3%</div>
                        <div class="summary-label">Performance</div>
                        <div class="summary-trend" id="global-performance-trend">+3.5%</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-value" id="global-qualite">91.2%</div>
                        <div class="summary-label">Qualité</div>
                        <div class="summary-trend" id="global-qualite-trend">+0.8%</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-value" id="global-weight">1,247 kg</div>
                        <div class="summary-label">Poids Total</div>
                        <div class="summary-trend" id="global-weight-trend">+156 kg</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-value" id="global-cycles">89</div>
                        <div class="summary-label">Cycles Réalisés</div>
                        <div class="summary-trend" id="global-cycles-trend">+12</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-value" id="global-pieces">2,156</div>
                        <div class="summary-label">Pièces Totales</div>
                        <div class="summary-trend" id="global-pieces-trend">+234</div>
                    </div>
                </div>
            </div>

            <!-- Performance by Equipment Section -->
            <div class="form-section">
                <h3>🏭 Performance par Équipement</h3>
                
                <!-- Machines Performance -->
                <div class="equipment-section">
                    <h4>🔧 Machines à Laver</h4>
                    <div id="machines-performance-container">
                        <!-- Will be populated via AJAX -->
                    </div>
                </div>

                <!-- Dryers Performance -->
                <div class="equipment-section">
                    <h4>🌡️ Séchoirs</h4>
                    <div id="sechoirs-performance-container">
                        <!-- Will be populated via AJAX -->
                    </div>
                </div>

                <!-- Manual Stations Performance -->
                <div class="equipment-section">
                    <h4>👥 Postes Manuels</h4>
                    <div id="manual-performance-container">
                        <!-- Will be populated via AJAX -->
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="form-section">
                <h3>📊 Graphiques & Analyses</h3>
                <div class="charts-grid">
                    <div class="chart-container">
                        <h4>Poids traité par programme (Machines)</h4>
                        <canvas id="weight-by-program-chart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h4>Cycles réalisés par séchoir</h4>
                        <canvas id="cycles-by-dryer-chart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h4>Jauges TRS</h4>
                        <div class="gauges-container">
                            <div class="gauge" id="disponibilite-gauge"></div>
                            <div class="gauge" id="performance-gauge"></div>
                            <div class="gauge" id="qualite-gauge"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pareto Analysis Section -->
            <div class="form-section">
                <h3>📉 Analyse Pareto</h3>
                <div class="pareto-grid">
                    <div class="pareto-container">
                        <h4>⚠️ Arrêts par Type</h4>
                        <div class="pareto-filters">
                            <select id="arrets-equipment-filter">
                                <option value="">Tous équipements</option>
                                <option value="machines">Machines</option>
                                <option value="sechoirs">Séchoirs</option>
                                <option value="manuels">Postes manuels</option>
                            </select>
                            <input type="date" id="arrets-date-filter">
                            <button onclick="updateArretsPareto()">Actualiser</button>
                        </div>
                        <canvas id="arrets-pareto-chart"></canvas>
                        <div id="arrets-table-container">
                            <!-- Arrêts table -->
                        </div>
                    </div>
                    
                    <div class="pareto-container">
                        <h4>❌ Non-conformités par Type</h4>
                        <div class="pareto-filters">
                            <select id="nc-equipment-filter">
                                <option value="">Tous équipements</option>
                                <option value="machines">Machines</option>
                                <option value="sechoirs">Séchoirs</option>
                                <option value="manuels">Postes manuels</option>
                            </select>
                            <input type="date" id="nc-date-filter">
                            <button onclick="updateNCPareto()">Actualiser</button>
                        </div>
                        <canvas id="nc-pareto-chart"></canvas>
                        <div id="nc-table-container">
                            <!-- NC table -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Productions Table -->
            <div class="form-section">
                <h3>📋 Productions Détaillées</h3>
                <div id="productions-table-container">
                    <!-- Table will be loaded via AJAX -->
                </div>
            </div>

            <!-- Conclusions & Recommendations -->
            <div class="form-section">
                <h3>💡 Conclusions & Recommandations</h3>
                <div id="conclusions-container">
                    <div class="conclusion-section">
                        <h4>✅ Points Forts</h4>
                        <ul id="strengths-list">
                            <!-- Will be populated dynamically -->
                        </ul>
                    </div>
                    <div class="conclusion-section">
                        <h4>⚠️ Points Faibles</h4>
                        <ul id="weaknesses-list">
                            <!-- Will be populated dynamically -->
                        </ul>
                    </div>
                    <div class="conclusion-section">
                        <h4>🎯 Recommandations</h4>
                        <ul id="recommendations-list">
                            <!-- Will be populated dynamically -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
