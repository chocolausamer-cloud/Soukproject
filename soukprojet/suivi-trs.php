<?php
require_once 'config.php';

// Calculer les données TRS détaillées
function calculateDetailedTRS($pdo) {
    $data = [
        'disponibilite' => 84.4,
        'performance' => 84.6,
        'qualite' => 96.7,
        'trs_global' => 69.0,
        'evolution_data' => [],
        'pareto_data' => [],
        'machine_data' => [],
        'temps_attente' => []
    ];
    
    try {
        // Données d'évolution simulées
        $data['evolution_data'] = [
            ['hour' => '08h', 'disponibilite' => 76, 'performance' => 73, 'qualite' => 94, 'trs' => 52],
            ['hour' => '10h', 'disponibilite' => 81, 'performance' => 78, 'qualite' => 96, 'trs' => 61],
            ['hour' => '12h', 'disponibilite' => 84, 'performance' => 80, 'qualite' => 96, 'trs' => 65],
            ['hour' => '14h', 'disponibilite' => 88, 'performance' => 82, 'qualite' => 95, 'trs' => 69],
            ['hour' => '16h', 'disponibilite' => 85, 'performance' => 79, 'qualite' => 96, 'trs' => 64],
            ['hour' => '18h', 'disponibilite' => 82, 'performance' => 76, 'qualite' => 95, 'trs' => 59],
        ];
        
        // Données Pareto des pertes
        $data['pareto_data'] = [
            ['cause' => 'Ralentissement Cadence', 'percentage' => 18, 'color' => '#ef4444'],
            ['cause' => 'Pannes Mécaniques', 'percentage' => 15, 'color' => '#f97316'],
            ['cause' => 'Maintenance Préventive', 'percentage' => 12, 'color' => '#eab308'],
            ['cause' => 'Attente Matériel', 'percentage' => 12, 'color' => '#8b5cf6'],
            ['cause' => 'Défauts Qualité Textile', 'percentage' => 10, 'color' => '#06b6d4'],
            ['cause' => 'Pannes Électriques', 'percentage' => 8, 'color' => '#10b981'],
            ['cause' => 'Attente Opérateur', 'percentage' => 8, 'color' => '#6366f1'],
            ['cause' => 'Changement Programme', 'percentage' => 7, 'color' => '#f59e0b'],
            ['cause' => 'Défauts Température', 'percentage' => 5, 'color' => '#84cc16'],
            ['cause' => 'Autres', 'percentage' => 5, 'color' => '#64748b'],
        ];
        
        // Données machines détaillées
        $data['machine_data'] = [
            [
                'nom' => 'Machine 13',
                'trs' => '78%',
                'capTheo' => 1364,
                'capReelle' => 806,
                'tpsPlanifie' => '8h0min',
                'tpsFonct' => '7h55min',
                'tpsArret' => '1h4min',
                'tpsAttente' => '16.7 min',
                'statut' => 'ATT'
            ],
            [
                'nom' => 'Machine 20',
                'trs' => '69.4%',
                'capTheo' => 1078,
                'capReelle' => 876,
                'tpsPlanifie' => '8h0min',
                'tpsFonct' => '6h24min',
                'tpsArret' => '2h35min',
                'tpsAttente' => '15.8 min',
                'statut' => 'KO'
            ],
            [
                'nom' => 'Machine 50',
                'trs' => '80.7%',
                'capTheo' => 1337,
                'capReelle' => 1120,
                'tpsPlanifie' => '8h0min',
                'tpsFonct' => '7h2min',
                'tpsArret' => '1h58min',
                'tpsAttente' => '15.9 min',
                'statut' => 'ATT'
            ],
            [
                'nom' => 'Machine 70',
                'trs' => '72.3%',
                'capTheo' => 1024,
                'capReelle' => 957,
                'tpsPlanifie' => '8h0min',
                'tpsFonct' => '8h32min',
                'tpsArret' => '0h28min',
                'tpsAttente' => '9.6 min',
                'statut' => 'ATT'
            ],
            [
                'nom' => 'Séchoir 1',
                'trs' => '57.6%',
                'capTheo' => 1320,
                'capReelle' => 923,
                'tpsPlanifie' => '8h0min',
                'tpsFonct' => '7h10min',
                'tpsArret' => '1h50min',
                'tpsAttente' => '19.2 min',
                'statut' => 'KO'
            ],
            [
                'nom' => 'Séchoir 2',
                'trs' => '67.4%',
                'capTheo' => 1297,
                'capReelle' => 1139,
                'tpsPlanifie' => '8h0min',
                'tpsFonct' => '7h35min',
                'tpsArret' => '1h25min',
                'tpsAttente' => '6.4 min',
                'statut' => 'KO'
            ],
            [
                'nom' => 'Séchoir 3',
                'trs' => '77.6%',
                'capTheo' => 1155,
                'capReelle' => 1156,
                'tpsPlanifie' => '8h0min',
                'tpsFonct' => '7h18min',
                'tpsArret' => '1h42min',
                'tpsAttente' => '13.1 min',
                'statut' => 'ATT'
            ]
        ];
        
        // Temps d'attente par machine
        $data['temps_attente'] = [
            ['machine' => 'Machine 13', 'temps' => 17.2],
            ['machine' => 'Machine 20', 'temps' => 16.3],
            ['machine' => 'Machine 50', 'temps' => 16.1],
            ['machine' => 'Machine 70', 'temps' => 10.2],
            ['machine' => 'Séchoir 1', 'temps' => 19.8],
            ['machine' => 'Séchoir 2', 'temps' => 6.8],
            ['machine' => 'Séchoir 3', 'temps' => 13.9],
            ['machine' => 'Séchoir 4', 'temps' => 5.2],
            ['machine' => 'Calandre 1', 'temps' => 5.1],
            ['machine' => 'Repassage 1', 'temps' => 12.4],
        ];
        
        return $data;
        
    } catch (Exception $e) {
        error_log("Error calculating detailed TRS: " . $e->getMessage());
        return $data;
    }
}

$trsData = calculateDetailedTRS($pdo);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi TRS - TRS Blanchisserie</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>📊 Suivi TRS</h1>
            <button class="back-btn" onclick="window.history.back()">← Retour</button>
        </div>

        <!-- Filtres avancés -->
        <div class="trs-filters">
            <div class="filters-header">
                <h3>🔍 Filtres</h3>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="filter-period">Période</label>
                    <select id="filter-period">
                        <option value="today">Aujourd'hui</option>
                        <option value="week">Cette semaine</option>
                        <option value="month">Ce mois</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="filter-machines">Machines/Postes</label>
                    <select id="filter-machines">
                        <option value="all">Toutes les machines</option>
                        <option value="lavage">Machines lavage</option>
                        <option value="sechage">Séchoirs</option>
                        <option value="finition">Finition</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="filter-program">Programme</label>
                    <select id="filter-program">
                        <option value="all">Tous les programmes</option>
                        <option value="coton60">Coton 60°C</option>
                        <option value="synthetique">Synthétique</option>
                        <option value="delicat">Délicat</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="filter-operator">Opérateur</label>
                    <select id="filter-operator">
                        <option value="all">Tous les opérateurs</option>
                        <option value="jdupont">Jean Dupont</option>
                        <option value="mleroy">Marie Leroy</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="filter-resume">Filtre Resume</label>
                    <select id="filter-resume">
                        <option value="global">Vue globale</option>
                        <option value="details">Vue détaillée</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Indicateurs TRS avec barres de progression -->
        <div class="trs-indicators">
            <div class="indicator-card disponibilite">
                <div class="indicator-header">
                    <span class="indicator-label">Disponibilité</span>
                    <span class="indicator-value"><?= $trsData['disponibilite'] ?>%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill disponibilite-fill" style="width: <?= $trsData['disponibilite'] ?>%"></div>
                </div>
            </div>

            <div class="indicator-card performance">
                <div class="indicator-header">
                    <span class="indicator-label">Performance</span>
                    <span class="indicator-value"><?= $trsData['performance'] ?>%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill performance-fill" style="width: <?= $trsData['performance'] ?>%"></div>
                </div>
            </div>

            <div class="indicator-card qualite">
                <div class="indicator-header">
                    <span class="indicator-label">Qualité</span>
                    <span class="indicator-value"><?= $trsData['qualite'] ?>%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill qualite-fill" style="width: <?= $trsData['qualite'] ?>%"></div>
                </div>
            </div>

            <div class="indicator-card trs-global">
                <div class="indicator-header">
                    <span class="indicator-label">TRS Global</span>
                    <span class="indicator-value"><?= $trsData['trs_global'] ?>%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill trs-fill" style="width: <?= $trsData['trs_global'] ?>%"></div>
                </div>
            </div>
        </div>

        <!-- Alertes et Notifications -->
        <div class="alerts-section">
            <div class="alerts-header">
                <h3>⚠️ Alertes et Notifications</h3>
            </div>
            
            <div class="alerts-list">
                <div class="alert-item critical">
                    <div class="alert-icon">⚠️</div>
                    <div class="alert-content">
                        <span class="alert-title">Alerte Critique</span>
                        <span class="alert-message">Machine 20 - TRS: 69.4%</span>
                    </div>
                    <button class="alert-action critical">Action requise</button>
                </div>

                <div class="alert-item critical">
                    <div class="alert-icon">⚠️</div>
                    <div class="alert-content">
                        <span class="alert-title">Alerte Critique</span>
                        <span class="alert-message">Séchoir 1 - TRS: 57.6%</span>
                    </div>
                    <button class="alert-action critical">Action requise</button>
                </div>

                <div class="alert-item attention">
                    <div class="alert-icon">⚠️</div>
                    <div class="alert-content">
                        <span class="alert-title">Zone d'Attention</span>
                        <span class="alert-message">Machine 13 - TRS: 78%</span>
                    </div>
                    <button class="alert-action attention">Surveillance</button>
                </div>
            </div>
        </div>

        <!-- Graphiques -->
        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-header">
                    <h3>Évolution des Indicateurs TRS</h3>
                    <div class="chart-controls">
                        <label>Échelle de temps:</label>
                        <select id="time-scale">
                            <option value="auto">Auto (selon période)</option>
                            <option value="hour">Horaire</option>
                            <option value="day">Journalier</option>
                        </select>
                        <label>Type d'affichage:</label>
                        <select id="display-type">
                            <option value="bar">Graphique en barres</option>
                            <option value="line">Courbes</option>
                        </select>
                    </div>
                </div>
                <canvas id="evolution-chart"></canvas>
            </div>
            
            <div class="chart-container">
                <div class="chart-header">
                    <h3>Répartition des Pertes (Pareto)</h3>
                    <div class="chart-controls">
                        <label>Mode:</label>
                        <select id="pareto-mode">
                            <option value="percentage">Pourcentage</option>
                            <option value="minutes">Minutes</option>
                        </select>
                        <label>Filtre:</label>
                        <select id="pareto-filter">
                            <option value="global">Vue globale</option>
                            <option value="machine">Par machine</option>
                        </select>
                    </div>
                </div>
                <canvas id="pareto-chart"></canvas>
            </div>
        </div>

        <!-- Détail des pertes -->
        <div class="losses-detail">
            <h3>Détail des pertes</h3>
            <div class="losses-grid">
                <?php foreach ($trsData['pareto_data'] as $index => $item): ?>
                <div class="loss-item">
                    <div class="loss-color" style="background-color: <?= $item['color'] ?>"></div>
                    <div class="loss-info">
                        <div class="loss-name"><?= $item['cause'] ?></div>
                        <div class="loss-percentage"><?= $item['percentage'] ?>%</div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Temps d'Attente entre Cycles -->
        <div class="chart-container">
            <h3>Temps d'Attente entre Cycles par Machine</h3>
            <canvas id="waiting-time-chart"></canvas>
        </div>

        <!-- Tableau Comparatif des Machines -->
        <div class="machines-comparison">
            <h3>Tableau Comparatif des Machines</h3>
            <div class="table-container">
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th>Machine</th>
                            <th>TRS</th>
                            <th>Cap. Theo</th>
                            <th>Cap. Reelle</th>
                            <th>Tps Planifie</th>
                            <th>Tps Fonct</th>
                            <th>Tps Arret</th>
                            <th>Tps Attente</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($trsData['machine_data'] as $machine): ?>
                        <tr>
                            <td>
                                <div class="machine-info">
                                    <div class="machine-indicator <?= strpos($machine['nom'], 'Machine') !== false ? 'machine' : 'sechoir' ?>"></div>
                                    <span class="machine-name"><?= $machine['nom'] ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="trs-value <?= 
                                    floatval(str_replace('%', '', $machine['trs'])) > 75 ? 'good' : 
                                    (floatval(str_replace('%', '', $machine['trs'])) > 65 ? 'warning' : 'critical') 
                                ?>">
                                    <?= $machine['trs'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="capacity-info">
                                    <div class="capacity-value"><?= $machine['capTheo'] ?></div>
                                    <div class="capacity-unit">kg/cycle</div>
                                </div>
                            </td>
                            <td>
                                <div class="capacity-info">
                                    <div class="capacity-value"><?= $machine['capReelle'] ?></div>
                                    <div class="capacity-percentage">
                                        <?= round(($machine['capReelle'] / $machine['capTheo']) * 100) ?>%
                                    </div>
                                </div>
                            </td>
                            <td><?= $machine['tpsPlanifie'] ?></td>
                            <td>
                                <div class="time-info">
                                    <div class="time-value"><?= $machine['tpsFonct'] ?></div>
                                    <div class="time-percentage">
                                        <?php
                                        $fonct_minutes = 0;
                                        if (preg_match('/(\d+)h(\d+)min/', $machine['tpsFonct'], $matches)) {
                                            $fonct_minutes = $matches[1] * 60 + $matches[2];
                                        }
                                        echo round(($fonct_minutes / 480) * 100) . '%';
                                        ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="time-error"><?= $machine['tpsArret'] ?></span>
                            </td>
                            <td>
                                <span class="time-waiting"><?= $machine['tpsAttente'] ?></span>
                            </td>
                            <td>
                                <span class="status-badge <?= $machine['statut'] === 'ATT' ? 'warning' : 'critical' ?>">
                                    <?= $machine['statut'] ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Synthèses -->
        <div class="summary-section">
            <div class="summary-card">
                <h3>Temps d'Attente - Synthèse</h3>
                <div class="summary-stats">
                    <div class="summary-item">
                        <span class="summary-label">Temps d'attente total:</span>
                        <span class="summary-value">119 min</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Temps d'attente moyen:</span>
                        <span class="summary-value">12 min</span>
                    </div>
                </div>
            </div>

            <div class="summary-card">
                <h3>Production - Résumé</h3>
                <div class="summary-stats">
                    <div class="summary-item">
                        <span class="summary-label">Temps planifié total:</span>
                        <span class="summary-value">80 heures</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Temps fonctionnement total:</span>
                        <span class="summary-value">68 heures</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Production totale:</span>
                        <span class="summary-value">10070 kg</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Configuration des graphiques Chart.js
        Chart.defaults.font.family = 'Arial, sans-serif';
        Chart.defaults.font.size = 12;

        // Graphique d'évolution des indicateurs TRS
        const evolutionCtx = document.getElementById('evolution-chart').getContext('2d');
        const evolutionChart = new Chart(evolutionCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($trsData['evolution_data'], 'hour')) ?>,
                datasets: [
                    {
                        label: 'Disponibilité',
                        data: <?= json_encode(array_column($trsData['evolution_data'], 'disponibilite')) ?>,
                        backgroundColor: '#3b82f6',
                        borderColor: '#1d4ed8',
                        borderWidth: 1
                    },
                    {
                        label: 'Performance',
                        data: <?= json_encode(array_column($trsData['evolution_data'], 'performance')) ?>,
                        backgroundColor: '#10b981',
                        borderColor: '#059669',
                        borderWidth: 1
                    },
                    {
                        label: 'Qualité',
                        data: <?= json_encode(array_column($trsData['evolution_data'], 'qualite')) ?>,
                        backgroundColor: '#f59e0b',
                        borderColor: '#d97706',
                        borderWidth: 1
                    },
                    {
                        label: 'TRS',
                        data: <?= json_encode(array_column($trsData['evolution_data'], 'trs')) ?>,
                        backgroundColor: '#8b5cf6',
                        borderColor: '#7c3aed',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Évolution TRS par heure'
                    },
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });

        // Graphique Pareto des pertes
        const paretoCtx = document.getElementById('pareto-chart').getContext('2d');
        const paretoChart = new Chart(paretoCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_column($trsData['pareto_data'], 'cause')) ?>,
                datasets: [{
                    data: <?= json_encode(array_column($trsData['pareto_data'], 'percentage')) ?>,
                    backgroundColor: <?= json_encode(array_column($trsData['pareto_data'], 'color')) ?>,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Répartition des pertes'
                    },
                    legend: {
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            generateLabels: function(chart) {
                                const data = chart.data;
                                return data.labels.map((label, i) => ({
                                    text: label + ' (' + data.datasets[0].data[i] + '%)',
                                    fillStyle: data.datasets[0].backgroundColor[i],
                                    strokeStyle: data.datasets[0].backgroundColor[i],
                                    pointStyle: 'circle'
                                }));
                            }
                        }
                    }
                }
            }
        });

        // Graphique temps d'attente
        const waitingCtx = document.getElementById('waiting-time-chart').getContext('2d');
        const waitingChart = new Chart(waitingCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($trsData['temps_attente'], 'machine')) ?>,
                datasets: [{
                    label: 'Temps d\'attente (min)',
                    data: <?= json_encode(array_column($trsData['temps_attente'], 'temps')) ?>,
                    backgroundColor: '#8b5cf6',
                    borderColor: '#7c3aed',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Temps d\'attente par machine'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + ' min';
                            }
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45
                        }
                    }
                }
            }
        });

        // Gestion des contrôles de filtres
        document.getElementById('time-scale').addEventListener('change', function() {
            // Logique pour changer l'échelle de temps
            console.log('Time scale changed:', this.value);
        });

        document.getElementById('display-type').addEventListener('change', function() {
            // Logique pour changer le type d'affichage
            const newType = this.value;
            evolutionChart.config.type = newType;
            evolutionChart.update();
        });

        // Fonctions utilitaires
        function exportTRSData() {
            const data = {
                disponibilite: <?= $trsData['disponibilite'] ?>,
                performance: <?= $trsData['performance'] ?>,
                qualite: <?= $trsData['qualite'] ?>,
                trs_global: <?= $trsData['trs_global'] ?>,
                machines: <?= json_encode($trsData['machine_data']) ?>
            };
            
            const csvContent = "data:text/csv;charset=utf-8,";
            const csv = Object.entries(data).map(e => e.join(",")).join("\n");
            
            const encodedUri = encodeURI(csvContent + csv);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "trs_data_" + new Date().toISOString().slice(0,10) + ".csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Auto-refresh des données (optionnel)
        function refreshTRSData() {
            fetch('ajax.php?action=get_trs_data')
                .then(response => response.json())
                .then(data => {
                    // Mettre à jour les indicateurs
                    updateIndicators(data);
                })
                .catch(error => console.error('Error refreshing TRS data:', error));
        }

        function updateIndicators(data) {
            document.querySelector('.disponibilite .indicator-value').textContent = data.disponibilite + '%';
            document.querySelector('.performance .indicator-value').textContent = data.performance + '%';
            document.querySelector('.qualite .indicator-value').textContent = data.qualite + '%';
            document.querySelector('.trs-global .indicator-value').textContent = data.trs_global + '%';
            
            document.querySelector('.disponibilite-fill').style.width = data.disponibilite + '%';
            document.querySelector('.performance-fill').style.width = data.performance + '%';
            document.querySelector('.qualite-fill
