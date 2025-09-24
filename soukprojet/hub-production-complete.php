<?php
require_once 'config.php';

// Définir les structures de données pour la production
if (!class_exists('Client')) {
    class Client {
        public $id, $name, $sector, $phone, $email, $address, $notes, $active, $created_at;
        
        public function __construct($data) {
            foreach ($data as $key => $value) {
                $this->$key = $value;
            }
        }
    }
}

if (!class_exists('Order')) {
    class Order {
        public $id, $client_id, $sector, $articles_label, $poids_kg, $date_depot, $date_retour_prevue, $priority, $status, $notes, $ready_at, $picked_at;
        
        public function __construct($data) {
            foreach ($data as $key => $value) {
                $this->$key = $value;
            }
        }
    }
}

// Traitement des formulaires
if ($_POST) {
    if (isset($_POST['action']) && $_POST['action'] === 'add_order') {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO production_orders 
                (client_name, client_sector, articles_label, poids_kg, date_depot, date_retour_prevue, priority, status, notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'A_PLANIFIER', ?)
            ");
            
            $stmt->execute([
                $_POST['client_name'],
                $_POST['client_sector'],
                $_POST['articles_label'],
                $_POST['poids_kg'],
                $_POST['date_depot'],
                $_POST['date_retour_prevue'],
                $_POST['priority'],
                $_POST['notes'] ?? null
            ]);
            
            $success_message = "Commande créée avec succès.";
        } catch (Exception $e) {
            $error_message = "Erreur lors de la création : " . $e->getMessage();
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'add_client') {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO clients 
                (name, sector, phone, email, address, notes) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $_POST['name'],
                $_POST['sector'],
                $_POST['phone'] ?? null,
                $_POST['email'] ?? null,
                $_POST['address'] ?? null,
                $_POST['notes'] ?? null
            ]);
            
            $success_message = "Client créé avec succès.";
        } catch (Exception $e) {
            $error_message = "Erreur lors de la création du client : " . $e->getMessage();
        }
    }
}

// Créer les tables si elles n'existent pas
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS clients (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            sector ENUM('Hôtellerie', 'Restauration', 'Médical', 'Collectivités', 'Autre') NOT NULL,
            phone VARCHAR(20),
            email VARCHAR(255),
            address TEXT,
            notes TEXT,
            active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS production_orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            client_name VARCHAR(255) NOT NULL,
            client_sector ENUM('Hôtellerie', 'Restauration', 'Médical', 'Collectivités', 'Autre') NOT NULL,
            articles_label TEXT NOT NULL,
            poids_kg DECIMAL(8,2) NOT NULL,
            date_depot DATETIME NOT NULL,
            date_retour_prevue DATETIME NOT NULL,
            priority ENUM('Basse', 'Normale', 'Haute') DEFAULT 'Normale',
            status ENUM('A_PLANIFIER', 'PLANIFIE', 'EN_COURS', 'PRET', 'RECUPERE', 'ANNULE') DEFAULT 'A_PLANIFIER',
            notes TEXT,
            ready_at DATETIME NULL,
            picked_at DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    
    // Insérer des données de démonstration si nécessaire
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM clients");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $demo_clients = [
            ['Hôtel Le Grand', 'Hôtellerie', '01.23.45.67.89', 'contact@hotelegrand.fr', '123 Rue de la République, 75001 Paris', 'Client premium'],
            ['Restaurant La Table', 'Restauration', '01.98.76.54.32', 'info@latablerest.fr', '45 Avenue des Champs, 75008 Paris', 'Livraison urgente'],
            ['Clinique Saint-Michel', 'Médical', '01.11.22.33.44', 'linge@clinique-sm.fr', '78 Boulevard Médical, 75012 Paris', 'Protocole médical strict'],
            ['Mairie de Ville', 'Collectivités', '01.55.66.77.88', 'services@mairie-ville.fr', '1 Place de la Mairie, 75020 Paris', 'Contrat annuel'],
            ['École Primaire', 'Collectivités', '01.44.55.66.77', 'direction@ecole-primaire.fr', '12 Rue de l\'École, 75019 Paris', 'Linge scolaire']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO clients (name, sector, phone, email, address, notes) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($demo_clients as $client) {
            $stmt->execute($client);
        }
    }
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM production_orders");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $demo_orders = [
            ['Hôtel Le Grand', 'Hôtellerie', 'Draps, serviettes, peignoirs', 45.5, '2024-01-15 08:00:00', '2024-01-16 18:00:00', 'Haute', 'EN_COURS', 'Commande urgente pour rénovation'],
            ['Restaurant La Table', 'Restauration', 'Nappes, serviettes de table, torchons', 25.0, '2024-01-15 09:30:00', '2024-01-15 17:00:00', 'Normale', 'A_PLANIFIER', 'Service du soir'],
            ['Clinique Saint-Michel', 'Médical', 'Blouses, draps d\'examen, sur-chaussures', 30.2, '2024-01-15 10:00:00', '2024-01-16 12:00:00', 'Haute', 'PLANIFIE', 'Protocole stérilisation'],
            ['Mairie de Ville', 'Collectivités', 'Uniformes agents, rideaux', 18.7, '2024-01-14 14:00:00', '2024-01-17 10:00:00', 'Basse', 'PRET', NULL],
            ['École Primaire', 'Collectivités', 'Blouses école, torchons cuisine', 12.3, '2024-01-13 16:00:00', '2024-01-16 08:00:00', 'Normale', 'RECUPERE', 'Livré à 8h']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO production_orders (client_name, client_sector, articles_label, poids_kg, date_depot, date_retour_prevue, priority, status, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($demo_orders as $order) {
            $stmt->execute($order);
        }
    }
} catch (Exception $e) {
    error_log("Error creating production tables: " . $e->getMessage());
}

// Récupération des données
function getClients($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE active = TRUE ORDER BY name");
    $stmt->execute();
    return array_map(function($data) { return new Client($data); }, $stmt->fetchAll());
}

function getOrders($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM production_orders ORDER BY created_at DESC");
    $stmt->execute();
    return array_map(function($data) { return new Order($data); }, $stmt->fetchAll());
}

function calculateProductionStats($pdo) {
    $stats = [
        'total_commandes' => 0,
        'poids_total' => 0,
        'on_time_rate' => 87,
        'commandes_pretes' => 0,
        'retards' => 0,
        'trs_jour' => 78.5
    ];
    
    try {
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total_commandes,
                SUM(poids_kg) as poids_total,
                SUM(CASE WHEN status = 'PRET' THEN 1 ELSE 0 END) as commandes_pretes,
                SUM(CASE WHEN date_retour_prevue < NOW() AND status NOT IN ('PRET', 'RECUPERE') THEN 1 ELSE 0 END) as retards
            FROM production_orders 
            WHERE DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        ");
        $stmt->execute();
        $result = $stmt->fetch();
        
        $stats['total_commandes'] = $result['total_commandes'];
        $stats['poids_total'] = $result['poids_total'] ?? 0;
        $stats['commandes_pretes'] = $result['commandes_pretes'];
        $stats['retards'] = $result['retards'];
        
        return $stats;
    } catch (Exception $e) {
        error_log("Error calculating production stats: " . $e->getMessage());
        return $stats;
    }
}

$clients = getClients($pdo);
$orders = getOrders($pdo);
$stats = calculateProductionStats($pdo);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hub Gestion de Production - TRS Blanchisserie</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <div class="page-header production-header">
            <h1>🏭 Hub Gestion de Production</h1>
            <button class="back-btn" onclick="window.history.back()">← Retour</button>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?= $success_message ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-error"><?= $error_message ?></div>
        <?php endif; ?>

        <!-- Filtres globaux -->
        <div class="production-filters">
            <div class="form-row">
                <div class="form-group">
                    <label>Période</label>
                    <div class="date-range">
                        <input type="date" id="filter-date-start" value="<?= date('Y-m-d', strtotime('-7 days')) ?>">
                        <input type="date" id="filter-date-end" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Secteur</label>
                    <select id="filter-sector">
                        <option value="">Tous secteurs</option>
                        <option value="Hôtellerie">Hôtellerie</option>
                        <option value="Restauration">Restauration</option>
                        <option value="Médical">Médical</option>
                        <option value="Collectivités">Collectivités</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Statut</label>
                    <select id="filter-status">
                        <option value="">Tous statuts</option>
                        <option value="A_PLANIFIER">À planifier</option>
                        <option value="PLANIFIE">Planifié</option>
                        <option value="EN_COURS">En cours</option>
                        <option value="PRET">Prêt</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Client</label>
                    <input type="text" id="filter-client" placeholder="Recherche client...">
                </div>
                <div class="form-group">
                    <button class="btn btn-primary" onclick="applyFilters()">🔍 Filtrer</button>
                </div>
            </div>
        </div>

        <!-- KPIs de production -->
        <div class="production-kpis">
            <div class="kpi-card production">
                <div class="kpi-value"><?= $stats['total_commandes'] ?></div>
                <div class="kpi-label">Commandes</div>
            </div>
            <div class="kpi-card production">
                <div class="kpi-value"><?= number_format($stats['poids_total'], 0) ?> kg</div>
                <div class="kpi-label">Poids total</div>
            </div>
            <div class="kpi-card production">
                <div class="kpi-value"><?= $stats['on_time_rate'] ?>%</div>
                <div class="kpi-label">% On-Time</div>
            </div>
            <div class="kpi-card production">
                <div class="kpi-value"><?= $stats['commandes_pretes'] ?></div>
                <div class="kpi-label">Prêtes</div>
            </div>
            <div class="kpi-card production">
                <div class="kpi-value"><?= $stats['retards'] ?></div>
                <div class="kpi-label">Retards</div>
            </div>
            <div class="kpi-card production">
                <div class="kpi-value"><?= $stats['trs_jour'] ?>%</div>
                <div class="kpi-label">TRS du jour</div>
            </div>
        </div>

        <!-- Navigation tabs du hub -->
        <div class="hub-tabs">
            <button class="hub-tab-btn active" onclick="showHubTab('backlog')">📋 Backlog & Planification</button>
            <button class="hub-tab-btn" onclick="showHubTab('kanban')">📊 Exécution (Kanban)</button>
            <button class="hub-tab-btn" onclick="showHubTab('gantt')">📅 Gantt</button>
            <button class="hub-tab-btn" onclick="showHubTab('clients')">👥 Clients</button>
            <button class="hub-tab-btn" onclick="showHubTab('depot')">📥 Dépôt</button>
            <button class="hub-tab-btn" onclick="showHubTab('stats')">📈 Historique & Stats</button>
        </div>

        <!-- Onglet Backlog & Planification -->
        <div id="backlog" class="hub-tab-content active">
            <div class="hub-layout">
                <div class="hub-main">
                    <h3>Commandes à planifier</h3>
                    <div class="orders-to-plan">
                        <?php foreach (array_filter($orders, function($o) { return $o->status === 'A_PLANIFIER'; }) as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <h4><?= htmlspecialchars($order->client_name) ?></h4>
                                <span class="priority-badge <?= strtolower($order->priority) ?>">
                                    <?= $order->priority ?>
                                </span>
                            </div>
                            <div class="order-details">
                                <p><?= htmlspecialchars($order->articles_label) ?></p>
                                <div class="order-metrics">
                                    <span>Poids: <?= $order->poids_kg ?>kg</span>
                                    <span>Secteur: <?= $order->client_sector ?></span>
                                </div>
                                <div class="order-dates">
                                    <span>Dépôt: <?= date('d/m/Y', strtotime($order->date_depot)) ?></span>
                                    <span>Retour: <?= date('d/m/Y', strtotime($order->date_retour_prevue)) ?></span>
                                </div>
                                <?php if ($order->notes): ?>
                                    <p class="order-notes"><?= htmlspecialchars($order->notes) ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="order-actions">
                                <button class="btn btn-primary btn-sm" onclick="planOrder(<?= $order->id ?>)">
                                    📅 Planifier
                                </button>
                                <button class="btn btn-secondary btn-sm" onclick="autoPlanOrder(<?= $order->id ?>)">
                                    🤖 Auto-planifier
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="hub-sidebar">
                    <h3>Planning du jour</h3>
                    <div class="planning-summary">
                        <div class="planning-section">
                            <h4>Charge par machine</h4>
                            <div class="machine-loads">
                                <div class="machine-load">
                                    <span>Machine 20kg</span>
                                    <div class="load-bar">
                                        <div class="load-fill" style="width: 75%"></div>
                                    </div>
                                    <span class="load-value">75%</span>
                                </div>
                                <div class="machine-load">
                                    <span>Machine 70kg</span>
                                    <div class="load-bar">
                                        <div class="load-fill" style="width: 45%"></div>
                                    </div>
                                    <span class="load-value">45%</span>
                                </div>
                                <div class="machine-load">
                                    <span>Séchoir 1</span>
                                    <div class="load-bar">
                                        <div class="load-fill" style="width: 80%"></div>
                                    </div>
                                    <span class="load-value">80%</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="planning-section">
                            <h4>5 prochains jobs</h4>
                            <div class="next-jobs">
                                <div class="job-item">M20 - Coton 60°C - 15:30</div>
                                <div class="job-item">S1 - Standard 55°C - 16:00</div>
                                <div class="job-item">M70 - Délicat 45°C - 16:15</div>
                                <div class="job-item">CAL1 - Calandrage - 17:00</div>
                                <div class="job-item">M50 - Synthétique 40°C - 17:30</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglet Kanban -->
        <div id="kanban" class="hub-tab-content">
            <h3>Exécution Kanban</h3>
            <div class="kanban-board">
                <?php 
                $statuses = ['PLANIFIE' => 'Planifié', 'EN_COURS' => 'En cours', 'PRET' => 'Prêt', 'RECUPERE' => 'Récupéré'];
                foreach ($statuses as $status_key => $status_label): 
                ?>
                <div class="kanban-column">
                    <h4><?= $status_label ?></h4>
                    <div class="kanban-cards">
                        <?php foreach (array_filter($orders, function($o) use ($status_key) { return $o->status === $status_key; }) as $order): ?>
                        <div class="kanban-card" draggable="true" data-order-id="<?= $order->id ?>">
                            <div class="kanban-card-header">
                                <h5><?= htmlspecialchars($order->client_name) ?></h5>
                                <span class="weight-badge"><?= $order->poids_kg ?>kg</span>
                            </div>
                            <p class="kanban-card-content"><?= htmlspecialchars(substr($order->articles_label, 0, 30)) ?>...</p>
                            <div class="kanban-card-footer">
                                <span><?= date('d/m/Y', strtotime($order->date_retour_prevue)) ?></span>
                                <span class="priority-badge <?= strtolower($order->priority) ?>">
                                    <?= $order->priority ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Onglet Gantt -->
        <div id="gantt" class="hub-tab-content">
            <h3>Planning Gantt</h3>
            <div class="gantt-container">
                <div class="gantt-timeline">
                    <div class="gantt-header">
                        <div class="gantt-time-slots">
                            <?php for ($h = 8; $h <= 17; $h++): ?>
                                <div class="time-slot"><?= sprintf('%02d:00', $h) ?></div>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                    <div class="gantt-rows">
                        <?php 
                        $equipments = ['Machine 13', 'Machine 20', 'Machine 50', 'Machine 70', 'Séchoir 1', 'Séchoir 2', 'Séchoir 3', 'Séchoir 4', 'Calandre', 'Repassage'];
                        foreach ($equipments as $equipment): 
                        ?>
                        <div class="gantt-row">
                            <div class="gantt-label"><?= $equipment ?></div>
                            <div class="gantt-bars">
                                <?php if (in_array($equipment, ['Machine 20', 'Séchoir 1'])): ?>
                                    <div class="gantt-bar active" style="left: 30%; width: 25%;">
                                        <?= $equipment === 'Machine 20' ? 'Coton Hôtel' : 'Standard Rest.' ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglet Clients -->
        <div id="clients" class="hub-tab-content">
            <div class="clients-header">
                <h3>Gestion des clients</h3>
                <button class="btn btn-primary" onclick="showNewClientForm()">➕ Nouveau client</button>
            </div>
            
            <div class="clients-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Secteur</th>
                            <th>Contact</th>
                            <th>Commandes</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clients as $client): ?>
                        <tr>
                            <td><?= htmlspecialchars($client->name) ?></td>
                            <td><?= $client->sector ?></td>
                            <td>
                                <?php if ($client->phone): ?>
                                    <div><?= $client->phone ?></div>
                                <?php endif; ?>
                                <?php if ($client->email): ?>
                                    <div><?= $client->email ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                $client_orders = array_filter($orders, function($o) use ($client) { return $o->client_name === $client->name; });
                                echo count($client_orders);
                                ?>
                            </td>
                            <td>
                                <span class="badge <?= $client->active ? 'badge-success' : 'badge-secondary' ?>">
                                    <?= $client->active ? 'Actif' : 'Inactif' ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-icon" onclick="viewClient(<?= $client->id ?>)" title="Voir">👁️</button>
                                    <button class="btn-icon" onclick="editClient(<?= $client->id ?>)" title="Modifier">✏️</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Onglet Dépôt -->
        <div id="depot" class="hub-tab-content">
            <h3>Nouveau dépôt</h3>
            <form method="POST" class="depot-form">
                <input type="hidden" name="action" value="add_order">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="client_name">Client *</label>
                        <select id="client_name" name="client_name" required>
                            <option value="">Sélectionner un client...</option>
                            <?php foreach ($clients as $client): ?>
                                <option value="<?= htmlspecialchars($client->name) ?>"><?= htmlspecialchars($client->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="client_sector">Secteur</label>
                        <select id="client_sector" name="client_sector">
                            <option value="Hôtellerie">Hôtellerie</option>
                            <option value="Restauration">Restauration</option>
                            <option value="Médical">Médical</option>
                            <option value="Collectivités">Collectivités</option>
                            <option value="Autre">Autre</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="articles_label">Articles *</label>
                        <input type="text" id="articles_label" name="articles_label" required
                               placeholder="Draps, serviettes, nappes...">
                    </div>
                    
                    <div class="form-group">
                        <label for="poids_kg">Poids (kg) *</label>
                        <input type="number" id="poids_kg" name="poids_kg" step="0.1" min="0.1" required
                               placeholder="25.5">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="date_depot">Date dépôt *</label>
                        <input type="datetime-local" id="date_depot" name="date_depot" required
                               value="<?= date('Y-m-d\TH:i') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="date_retour_prevue">Date retour prévue *</label>
                        <input type="datetime-local" id="date_retour_prevue" name="date_retour_prevue" required
                               value="<?= date('Y-m-d\TH:i', strtotime('+1 day')) ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="priority">Priorité</label>
                        <select id="priority" name="priority">
                            <option value="Normale">Normale</option>
                            <option value="Haute">Haute</option>
                            <option value="Basse">Basse</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" rows="3" 
                              placeholder="Instructions particulières..."></textarea>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">
                        ✅ Créer la commande
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        🔄 Réinitialiser
                    </button>
                </div>
            </form>
        </div>

        <!-- Onglet Stats -->
        <div id="stats" class="hub-tab-content">
            <div class="stats-header">
                <h3>Historique et Statistiques</h3>
                <div class="stats-actions">
                    <button class="btn btn-success" onclick="exportStats('csv')">📊 Export CSV</button>
