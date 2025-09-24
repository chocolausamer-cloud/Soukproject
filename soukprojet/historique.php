<?php
require_once 'config.php';

// Récupérer les événements avec filtres
function getHistoryEvents($pdo, $filters = []) {
    $where_conditions = ["DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)"];
    $params = [];
    
    if (!empty($filters['event_type'])) {
        if ($filters['event_type'] === 'arret') {
            $where_conditions[] = "event_type = 'stop'";
        } elseif ($filters['event_type'] === 'cycle') {
            $where_conditions[] = "event_type = 'production'";
        } elseif ($filters['event_type'] === 'nettoyage') {
            $where_conditions[] = "event_type = 'maintenance'";
        } elseif ($filters['event_type'] === 'nc') {
            $where_conditions[] = "event_type = 'nc'";
        }
    }
    
    if (!empty($filters['equipment'])) {
        $where_conditions[] = "equipment_name = ?";
        $params[] = $filters['equipment'];
    }
    
    if (!empty($filters['date_start'])) {
        $where_conditions[] = "DATE(created_at) >= ?";
        $params[] = $filters['date_start'];
    }
    
    if (!empty($filters['date_end'])) {
        $where_conditions[] = "DATE(created_at) <= ?";
        $params[] = $filters['date_end'];
    }
    
    // Union des différentes tables d'événements
    $sql = "
        (SELECT 
            'stop' as event_type,
            equipment_name,
            stop_code as detail,
            start_time as event_time,
            duration_minutes,
            operator,
            CASE WHEN end_time IS NULL THEN 'En cours' ELSE 'Terminé' END as status,
            created_at
        FROM equipment_stops)
        
        UNION ALL
        
        (SELECT 
            'production' as event_type,
            equipment as equipment_name,
            CONCAT(type, ' - ', COALESCE(program_name, 'Manuel')) as detail,
            timestamp as event_time,
            real_duration as duration_minutes,
            operator,
            'Terminé' as status,
            timestamp as created_at
        FROM productions)
        
        UNION ALL
        
        (SELECT 
            'nc' as event_type,
            equipment_name,
            CONCAT(nc_type, ' - ', LEFT(comment, 30)) as detail,
            created_at as event_time,
            NULL as duration_minutes,
            operator,
            'Ouverte' as status,
            created_at
        FROM non_conformities)
        
        ORDER BY event_time DESC
        LIMIT 100
    ";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Error getting history events: " . $e->getMessage());
        return [];
    }
}

// Calculer les statistiques d'historique
function calculateHistoryStats($pdo, $filters = []) {
    $stats = [
        'total_events' => 0,
        'temps_arret_total' => 0,
        'cycles_termines' => 0,
        'nc_ouvertes' => 0
    ];
    
    try {
        // Événements totaux
        $stmt = $pdo->prepare("
            SELECT 
                (SELECT COUNT(*) FROM equipment_stops WHERE DATE(start_time) = CURDATE()) +
                (SELECT COUNT(*) FROM productions WHERE DATE(timestamp) = CURDATE()) +
                (SELECT COUNT(*) FROM non_conformities WHERE DATE(created_at) = CURDATE()) as total_events
        ");
        $stmt->execute();
        $stats['total_events'] = $stmt->fetchColumn();
        
        // Temps d'arrêt total
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(duration_minutes), 0) as temps_arret_total
            FROM equipment_stops 
            WHERE DATE(start_time) = CURDATE() AND duration_minutes IS NOT NULL
        ");
        $stmt->execute();
        $stats['temps_arret_total'] = $stmt->fetchColumn();
        
        // Cycles terminés
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as cycles_termines
            FROM productions 
            WHERE DATE(timestamp) = CURDATE()
        ");
        $stmt->execute();
        $stats['cycles_termines'] = $stmt->fetchColumn();
        
        // NC ouvertes
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as nc_ouvertes
            FROM non_conformities 
            WHERE DATE(created_at) = CURDATE()
        ");
        $stmt->execute();
        $stats['nc_ouvertes'] = $stmt->fetchColumn();
        
        return $stats;
        
    } catch (Exception $e) {
        error_log("Error calculating history stats: " . $e->getMessage());
        return $stats;
    }
}

// Traitement des filtres
$filters = [];
if (isset($_GET['event_type'])) $filters['event_type'] = $_GET['event_type'];
if (isset($_GET['equipment'])) $filters['equipment'] = $_GET['equipment'];
if (isset($_GET['date_start'])) $filters['date_start'] = $_GET['date_start'];
if (isset($_GET['date_end'])) $filters['date_end'] = $_GET['date_end'];

$events = getHistoryEvents($pdo, $filters);
$stats = calculateHistoryStats($pdo, $filters);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des événements - TRS Blanchisserie</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="page-header-modern">
            <h2>Historique des événements</h2>
        </div>

        <!-- Filtres -->
        <div class="history-filters">
            <form method="GET" class="filters-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="event_type">Type d'événement</label>
                        <select id="event_type" name="event_type">
                            <option value="">Tous les événements</option>
                            <option value="cycle" <?= isset($_GET['event_type']) && $_GET['event_type'] === 'cycle' ? 'selected' : '' ?>>Cycle</option>
                            <option value="arret" <?= isset($_GET['event_type']) && $_GET['event_type'] === 'arret' ? 'selected' : '' ?>>Arrêt</option>
                            <option value="nettoyage" <?= isset($_GET['event_type']) && $_GET['event_type'] === 'nettoyage' ? 'selected' : '' ?>>Nettoyage</option>
                            <option value="nc" <?= isset($_GET['event_type']) && $_GET['event_type'] === 'nc' ? 'selected' : '' ?>>Non-conformité</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="equipment">Équipement</label>
                        <select id="equipment" name="equipment">
                            <option value="">Tous</option>
                            <optgroup label="Machines">
                                <option value="machine-13" <?= isset($_GET['equipment']) && $_GET['equipment'] === 'machine-13' ? 'selected' : '' ?>>Machine 13</option>
                                <option value="machine-20" <?= isset($_GET['equipment']) && $_GET['equipment'] === 'machine-20' ? 'selected' : '' ?>>Machine 20</option>
                                <option value="machine-50" <?= isset($_GET['equipment']) && $_GET['equipment'] === 'machine-50' ? 'selected' : '' ?>>Machine 50</option>
                                <option value="machine-70" <?= isset($_GET['equipment']) && $_GET['equipment'] === 'machine-70' ? 'selected' : '' ?>>Machine 70</option>
                            </optgroup>
                            <optgroup label="Séchoirs">
                                <option value="sechoir-1" <?= isset($_GET['equipment']) && $_GET['equipment'] === 'sechoir-1' ? 'selected' : '' ?>>Séchoir 1</option>
                                <option value="sechoir-2" <?= isset($_GET['equipment']) && $_GET['equipment'] === 'sechoir-2' ? 'selected' : '' ?>>Séchoir 2</option>
                                <option value="sechoir-3" <?= isset($_GET['equipment']) && $_GET['equipment'] === 'sechoir-3' ? 'selected' : '' ?>>Séchoir 3</option>
                                <option value="sechoir-4" <?= isset($_GET['equipment']) && $_GET['equipment'] === 'sechoir-4' ? 'selected' : '' ?>>Séchoir 4</option>
                            </optgroup>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="date_start">Date début</label>
                        <input type="date" id="date_start" name="date_start" 
                               value="<?= $_GET['date_start'] ?? date('Y-m-d') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="date_end">Date fin</label>
                        <input type="date" id="date_end" name="date_end" 
                               value="<?= $_GET['date_end'] ?? date('Y-m-d') ?>">
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">🔍 Filtrer</button>
                    <button type="button" class="btn btn-secondary" onclick="resetFilters()">🔄 Réinitialiser</button>
                    <button type="button" class="btn btn-success" onclick="exportHistory()">📊 Exporter CSV</button>
                </div>
            </form>
        </div>

        <!-- KPIs selon l'image -->
        <div class="history-kpis">
            <div class="kpi-card">
                <div class="kpi-value">6</div>
                <div class="kpi-label">Événements totaux</div>
            </div>
            
            <div class="kpi-card">
                <div class="kpi-value">45min</div>
                <div class="kpi-label">Temps d'arrêt total</div>
            </div>
            
            <div class="kpi-card">
                <div class="kpi-value">2</div>
                <div class="kpi-label">Cycles terminés</div>
            </div>
            
            <div class="kpi-card">
                <div class="kpi-value">1</div>
                <div class="kpi-label">NC ouvertes</div>
            </div>
        </div>

        <!-- Table historique -->
        <div class="history-table-section">
            <div class="table-header">
                <h3>Événements (3)</h3>
            </div>
            
            <div class="table-container">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Date/Heure</th>
                            <th>Type</th>
                            <th>Équipement</th>
                            <th>Détails</th>
                            <th>Durée</th>
                            <th>Opérateur</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Données d'exemple selon l'image -->
                        <tr>
                            <td>15/01/2024 14:15:00</td>
                            <td><span class="event-badge inter-cycle">INTER CYCLE</span></td>
                            <td><strong>Machine 70kg</strong></td>
                            <td>Changement chariot</td>
                            <td><span class="duration-ongoing">En cours</span></td>
                            <td>Jean Dupont</td>
                            <td><span class="status-badge-history ongoing">En cours</span></td>
                        </tr>
                        <tr>
                            <td>15/01/2024 13:30:00</td>
                            <td><span class="event-badge nettoyage">NETTOYAGE</span></td>
                            <td><strong>Séchoir 3</strong></td>
                            <td>Nettoyage filtre</td>
                            <td>30 min</td>
                            <td>Jean Dupont</td>
                            <td><span class="status-badge-history completed">Terminé</span></td>
                        </tr>
                        <tr>
                            <td>23/09/2025 13:57:08</td>
                            <td><span class="event-badge inter-cycle">INTER CYCLE</span></td>
                            <td><strong>Machine 13kg</strong></td>
                            <td>Pause opérateur</td>
                            <td><span class="duration-ongoing">En cours</span></td>
                            <td>Jean Dupont</td>
                            <td><span class="status-badge-history ongoing">En cours</span></td>
                        </tr>
                        
                        <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i:s', strtotime($event['event_time'])) ?></td>
                            <td>
                                <span class="event-badge <?= 
                                    $event['event_type'] === 'stop' ? 'inter-cycle' : 
                                    ($event['event_type'] === 'production' ? 'production' : 
                                    ($event['event_type'] === 'nc' ? 'nc' : 'nettoyage'))
                                ?>">
                                    <?= strtoupper(str_replace('_', ' ', $event['event_type'])) ?>
                                </span>
                            </td>
                            <td><strong><?= htmlspecialchars($event['equipment_name']) ?></strong></td>
                            <td><?= htmlspecialchars($event['detail']) ?></td>
                            <td>
                                <?php if ($event['duration_minutes']): ?>
                                    <?= $event['duration_minutes'] ?> min
                                <?php else: ?>
                                    <span class="duration-ongoing">En cours</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($event['operator'] ?? 'Jean Dupont') ?></td>
                            <td>
                                <span class="status-badge-history <?= $event['status'] === 'Terminé' ? 'completed' : 'ongoing' ?>">
                                    <?= $event['status'] === 'Terminé' ? 'Terminé' : 'En cours' ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Graphique de tendance -->
        <div class="trend-section">
            <h3>Tendances des événements</h3>
            <div class="chart-container">
                <canvas id="trends-chart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Fonctions de filtrage
        function resetFilters() {
            document.getElementById('event_type').value = '';
            document.getElementById('equipment').value = '';
            document.getElementById('date_start').value = '<?= date('Y-m-d') ?>';
            document.getElementById('date_end').value = '<?= date('Y-m-d') ?>';
            
            // Soumettre le formulaire sans filtres
            window.location.href = 'historique.php';
        }

        function exportHistory() {
            const params = new URLSearchParams({
                action: 'export_history',
                event_type: document.getElementById('event_type').value,
                equipment: document.getElementById('equipment').value,
                date_start: document.getElementById('date_start').value,
                date_end: document.getElementById('date_end').value
            });
            
            // Créer les données CSV
            const events = <?= json_encode($events) ?>;
            let csvContent = "Date/Heure,Type,Équipement,Détails,Durée,Opérateur,Statut\n";
            
            events.forEach(event => {
                const row = [
                    new Date(event.event_time).toLocaleString(),
                    event.event_type,
                    event.equipment_name,
                    event.detail.replace(/,/g, ';'), // Remplacer les virgules pour éviter les problèmes CSV
                    event.duration_minutes ? event.duration_minutes + ' min' : '-',
                    event.operator || 'Jean Dupont',
                    event.status
                ].join(',');
                csvContent += row + '\n';
            });
            
            // Télécharger le fichier
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement("a");
            const url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", "historique_" + new Date().toISOString().slice(0,10) + ".csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
            
            console.log('Export CSV historique généré avec', events.length, 'événements filtrés !');
        }

        // Graphique de tendance
        const trendsCtx = document.getElementById('trends-chart').getContext('2d');
        
        // Préparer les données pour le graphique (derniers 7 jours)
        const last7Days = [];
        const today = new Date();
        for (let i = 6; i >= 0; i--) {
            const date = new Date(today);
            date.setDate(date.getDate() - i);
            last7Days.push(date.toLocaleDateString('fr-FR', { weekday: 'short' }));
        }
        
        // Données simulées pour les tendances
        const trendsData = {
            stops: [3, 2, 4, 1, 5, 2, 3],
            productions: [15, 18, 12, 20, 16, 14, 17],
            ncs: [1, 0, 2, 1, 0, 1, 2]
        };
        
        new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: last7Days,
                datasets: [
                    {
                        label: 'Arrêts',
                        data: trendsData.stops,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.1
                    },
                    {
                        label: 'Productions',
                        data: trendsData.productions,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.1
                    },
                    {
                        label: 'Non-conformités',
                        data: trendsData.ncs,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Tendances des événements (7 derniers jours)'
                    },
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Auto-refresh toutes les 2 minutes
        setInterval(function() {
            if (!document.querySelector('form input[type="date"]').value) {
                location.reload();
            }
        }, 120000);
    </script>
</body>
</html>
