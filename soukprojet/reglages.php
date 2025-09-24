<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>⚙️ Réglages Avancés - TRS Blanchisserie</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚙️ Réglages Avancés - Configuration Système</h1>
            <div class="header-actions">
                <button class="btn btn-secondary" onclick="window.location.href='index.php'">← Retour à l'accueil</button>
            </div>
        </div>

        <!-- Navigation des onglets réglages -->
        <div class="settings-tabs">
            <button class="settings-tab-btn active" onclick="showSettingsTab('programmes')" id="tab-programmes">
                🔧 Programmes
            </button>
            <button class="settings-tab-btn" onclick="showSettingsTab('operateurs')" id="tab-operateurs">
                👥 Opérateurs
            </button>
            <button class="settings-tab-btn" onclick="showSettingsTab('motifs')" id="tab-motifs">
                ⚠️ Motifs d'arrêts
            </button>
            <button class="settings-tab-btn" onclick="showSettingsTab('types-nc')" id="tab-types-nc">
                ❌ Types NC
            </button>
            <button class="settings-tab-btn" onclick="showSettingsTab('causes-5m')" id="tab-causes-5m">
                🔍 Causes 5M
            </button>
        </div>

        <!-- Onglet Programmes -->
        <div id="settings-programmes" class="settings-tab-content active">
            <div class="settings-section">
                <div class="settings-header">
                    <h3>🔧 Configuration des Programmes</h3>
                    <button class="btn btn-primary" onclick="showNewProgramModal()">
                        ➕ Nouveau programme
                    </button>
                </div>

                <div class="programs-grid">
                    <!-- Programmes Machines -->
                    <div class="program-category">
                        <h4>Programmes Machines à Laver</h4>
                        <div class="programs-table-container">
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
                                <tbody id="machine-programs-tbody">
                                    <!-- Sera rempli via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Programmes Séchoirs -->
                    <div class="program-category">
                        <h4>Programmes Séchoirs</h4>
                        <div class="programs-table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Nom de programme</th>
                                        <th>Type d'article</th>
                                        <th>Durée</th>
                                        <th>Température</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="drying-programs-tbody">
                                    <!-- Sera rempli via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglet Opérateurs -->
        <div id="settings-operateurs" class="settings-tab-content">
            <div class="settings-section">
                <div class="settings-header">
                    <h3>👥 Gestion des Opérateurs</h3>
                    <button class="btn btn-primary" onclick="showNewOperatorModal()">
                        ➕ Nouvel opérateur
                    </button>
                </div>

                <div class="operators-table-container">
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
                        <tbody id="operators-tbody">
                            <tr>
                                <td><strong>Jean Dupont</strong></td>
                                <td>jdupont</td>
                                <td><span class="badge badge-info">OPERATEUR</span></td>
                                <td><span class="badge badge-success">Actif</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-icon" onclick="editOperator(1)" title="Éditer">✏️</button>
                                        <button class="btn-icon warning" onclick="resetPassword(1)" title="Reset MDP">🔑</button>
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
                                        <button class="btn-icon" onclick="editOperator(2)" title="Éditer">✏️</button>
                                        <button class="btn-icon warning" onclick="resetPassword(2)" title="Reset MDP">🔑</button>
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
                                        <button class="btn-icon" onclick="editOperator(3)" title="Éditer">✏️</button>
                                        <button class="btn-icon warning" onclick="resetPassword(3)" title="Reset MDP">🔑</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Onglet Motifs d'arrêts -->
        <div id="settings-motifs" class="settings-tab-content">
            <div class="settings-section">
                <div class="settings-header">
                    <h3>⚠️ Configuration des Motifs d'Arrêts</h3>
                    <button class="btn btn-primary" onclick="showNewMotifModal()">
                        ➕ Nouveau motif
                    </button>
                </div>

                <div class="motifs-table-container">
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
                        <tbody id="motifs-tbody">
                            <tr>
                                <td><strong class="code-highlight">AT01</strong></td>
                                <td>Attente opérateur</td>
                                <td><span class="badge badge-warning">INTER_CYCLE</span></td>
                                <td><span class="badge badge-secondary">ORGANISATION</span></td>
                                <td><span class="badge badge-success">Actif</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-icon" onclick="editMotif('AT01')" title="Éditer">✏️</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><strong class="code-highlight">NF01</strong></td>
                                <td>Nettoyage filtre</td>
                                <td><span class="badge badge-info">NETTOYAGE</span></td>
                                <td><span class="badge badge-secondary">MECANIQUE</span></td>
                                <td><span class="badge badge-success">Actif</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-icon" onclick="editMotif('NF01')" title="Éditer">✏️</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><strong class="code-highlight">MP01</strong></td>
                                <td>Maintenance préventive</td>
                                <td><span class="badge badge-success">PLANIFIE</span></td>
                                <td><span class="badge badge-secondary">MECANIQUE</span></td>
                                <td><span class="badge badge-success">Actif</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-icon" onclick="editMotif('MP01')" title="Éditer">✏️</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><strong class="code-highlight">PE01</strong></td>
                                <td>Panne électrique</td>
                                <td><span class="badge badge-danger">NON_PLANIFIE</span></td>
                                <td><span class="badge badge-secondary">ELECTRIQUE</span></td>
                                <td><span class="badge badge-success">Actif</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-icon" onclick="editMotif('PE01')" title="Éditer">✏️</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Onglet Types de non-conformités -->
        <div id="settings-types-nc" class="settings-tab-content">
            <div class="settings-section">
                <div class="settings-header">
                    <h3>❌ Configuration des Types de Non-conformités</h3>
                    <button class="btn btn-primary" onclick="showNewTypeNCModal()">
                        ➕ Nouveau type
                    </button>
                </div>

                <div class="types-nc-table-container">
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
                        <tbody id="types-nc-tbody">
                            <tr>
                                <td><strong class="code-highlight">QUA01</strong></td>
                                <td>Défaut qualité</td>
                                <td><span class="badge badge-info">MINEURE</span></td>
                                <td><span class="badge badge-success">Actif</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-icon" onclick="editTypeNC('QUA01')" title="Éditer">✏️</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><strong class="code-highlight">SEC01</strong></td>
                                <td>Problème sécurité</td>
                                <td><span class="badge badge-warning">MAJEURE</span></td>
                                <td><span class="badge badge-success">Actif</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-icon" onclick="editTypeNC('SEC01')" title="Éditer">✏️</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><strong class="code-highlight">ENV01</strong></td>
                                <td>Impact environnemental</td>
                                <td><span class="badge badge-info">MINEURE</span></td>
                                <td><span class="badge badge-success">Actif</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-icon" onclick="editTypeNC('ENV01')" title="Éditer">✏️</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><strong class="code-highlight">PROD01</strong></td>
                                <td>Défaut production</td>
                                <td><span class="badge badge-warning">MAJEURE</span></td>
                                <td><span class="badge badge-success">Actif</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-icon" onclick="editTypeNC('PROD01')" title="Éditer">✏️</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Onglet Causes 5M -->
        <div id="settings-causes-5m" class="settings-tab-content">
            <div class="settings-section">
                <div class="settings-header">
                    <h3>🔍 Configuration des Causes 5M</h3>
                    <p class="settings-description">
                        Configurez les causes racines selon la méthode 5M (Main-d'œuvre, Méthode, Matière, Milieu, Machine)
                    </p>
                </div>

                <div class="causes-5m-grid">
                    <!-- Main-d'œuvre -->
                    <div class="cause-5m-category">
                        <div class="cause-category-header">
                            <h4>👥 Main-d'œuvre</h4>
                            <button class="btn btn-sm btn-primary" onclick="showNewCause5MModal('mainOeuvre')">
                                ➕ Ajouter
                            </button>
                        </div>
                        <div class="causes-list" id="causes-mainOeuvre">
                            <div class="cause-item">
                                <span class="cause-text">Formation insuffisante</span>
                                <div class="cause-actions">
                                    <button class="btn-icon" onclick="editCause5M('mainOeuvre', 'MO001')" title="Éditer">✏️</button>
                                    <button class="btn-icon danger" onclick="deleteCause5M('mainOeuvre', 'MO001')" title="Supprimer">🗑️</button>
                                </div>
                            </div>
                            <div class="cause-item">
                                <span class="cause-text">Fatigue opérateur</span>
                                <div class="cause-actions">
                                    <button class="btn-icon" onclick="editCause5M('mainOeuvre', 'MO002')" title="Éditer">✏️</button>
                                    <button class="btn-icon danger" onclick="deleteCause5M('mainOeuvre', 'MO002')" title="Supprimer">🗑️</button>
                                </div>
                            </div>
                            <div class="cause-item">
                                <span class="cause-text">Erreur humaine</span>
                                <div class="cause-actions">
                                    <button class="btn-icon" onclick="editCause5M('mainOeuvre', 'MO003')" title="Éditer">✏️</button>
                                    <button class="btn-icon danger" onclick="deleteCause5M('mainOeuvre', 'MO003')" title="Supprimer">🗑️</button>
                                </div>
                            </div>
                            <div class="cause-item">
                                <span class="cause-text">Absence personnel</span>
                                <div class="cause-actions">
                                    <button class="btn-icon" onclick="editCause5M('mainOeuvre', 'MO004')" title="Éditer">✏️</button>
                                    <button class="btn-icon danger" onclick="deleteCause5M('mainOeuvre', 'MO004')" title="Supprimer">🗑️</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Méthode -->
                    <div class="cause-5m-category">
                        <div class="cause-category-header">
                            <h4>📋 Méthode</h4>
                            <button class="btn btn-sm btn-primary" onclick="showNewCause5MModal('methode')">
                                ➕ Ajouter
                            </button>
                        </div>
                        <div class="causes-list" id="causes-methode">
                            <div class="cause-item">
                                <span class="cause-text">Procédure incorrecte</span>
                                <div class="cause-actions">
                                    <button class="btn-icon" onclick="editCause5M('methode', 'ME001')" title="Éditer">✏️</button>
                                    <button class="btn-icon danger" onclick="deleteCause5M('methode', 'ME001')" title="Supprimer">🗑️</button>
                                </div>
                            </div>
                            <div class="cause-item">
                                <span class="cause-text">Consignes peu claires</span>
                                <div class="cause-actions">
                                    <button class="btn-icon" onclick="editCause5M('methode', 'ME002')" title="Éditer">✏️</button>
                                    <button class="btn-icon danger" onclick="deleteCause5M('methode', 'ME002')" title="Supprimer">🗑️</button>
                                </div>
                            </div>
                            <div class="cause-item">
                                <span class="cause-text">Méthode inadaptée</span>
                                <div class="cause-actions">
                                    <button class="btn-icon" onclick="editCause5M('methode', 'ME003')" title="Éditer">✏️</button>
                                    <button class="btn-icon danger" onclick="deleteCause5M('methode', 'ME003')" title="Supprimer">🗑️</button>
                                </div>
                            </div>
                            <div class="cause-item">
                                <span class="cause-text">Documentation manquante</span>
                                <div class="cause-actions">
                                    <button class="btn-icon" onclick="editCause5M('methode', 'ME004')" title="Éditer">✏️</button>
                                    <button class="btn-icon danger" onclick="deleteCause5M('methode', 'ME004')" title="Supprimer">🗑️</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Matière -->
                    <div class="cause-5m-category">
                        <div class="cause-category-header">
                            <h4>📦 Matière</h4>
                            <button class="btn btn-sm btn-primary" onclick="showNewCause5MModal('matiere')">
                                ➕ Ajouter
                            </button>
                        </div>
                        <div class="causes-list" id="causes-matiere">
                            <div class="cause-item">
                                <span class="cause-text">Qualité matière</span>
                                <div class="cause-actions">
                                    <button class="btn-icon" onclick="editCause5M('matiere', 'MA001')" title="Éditer">✏️</button>
                                    <button class="btn-icon danger" onclick="deleteCause5M('matiere', 'MA001')" title="Supprimer">🗑️</button>
                                </div>
                            </div>
                            <div class="cause-item">
                                <span class="cause-text">Approvisionnement</span>
                                <div class="cause-actions">
                                    <button class="btn-icon" onclick="editCause5M('matiere', 'MA002')" title="Éditer">✏️</button>
                                    <button class="btn-icon danger" onclick="deleteCause5M('matiere', 'MA002')" title="Supprimer">🗑️</button>
                                </div>
                            </div>
                            <div class="cause-item">
                                <span class="cause-text">Stock défaillant</span>
                                <div class="cause-actions">
                                    <button class="btn-icon" onclick="editCause5M('matiere', 'MA003')" title="Éditer">✏️</button>
                                    <button class="btn-icon danger" onclick="deleteCause5M('matiere', 'MA003')" title="Supprimer">🗑️</button>
                                </div>
                            </div>
                            <div class="cause-item">
                                <span class="cause-text">Produit non conforme</span>
                                <div class="cause-actions">
                                    <button class="btn-icon" onclick="editCause5M('matiere', 'MA004')" title="Éditer">✏️</button>
                                    <button class="btn-icon danger" onclick="deleteCause5M('matiere', 'MA004')" title="Supprimer">🗑️</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Milieu -->
                    <div class="cause-5m-category">
                        <div class="cause-category-header">
                            <h4>🌡️ Milieu</h4>
                            <button class="btn btn-sm btn-primary" onclick="showNewCause5MModal('milieu')">
                                ➕ Ajouter
                            </button>
                        </div>
                        <div class="causes-list" id="causes-milieu">
                            <div class="cause-item">
                                <span class="cause-text">Température ambiante</span>
                                <div class="cause-actions">
                                    <button class="btn-icon" onclick="editCause5M('milieu', 'MI001')" title="Éditer">✏️</button>
                                    <button class="btn-icon danger" onclick="deleteCause5M('milieu', 'MI001')" title="Supprimer">🗑️</button>
                                </div>
                            </div>
                            <div class="cause-item">
                                <span class="cause-text">Humidité</span>
                                <div class="cause-actions">
                                    <button class="btn-icon" onclick="editCause5M('milieu', 'MI002')" title="Éditer">✏️</button>
                                    <button class="btn-icon danger" onclick="deleteCause5M('milieu', 'MI002')" title="Supprimer">🗑️</button>
                                </div>
                            </div>
                            <div class="cause-item">
                                <span class="cause-text">Éclairage insuffisant</span>
                                <div class="cause-actions">
                                    <button class="btn-icon" onclick="editCause5M('milieu', 'MI003')" title="Éditer">✏️</button>
                                    <button class="btn-icon danger" onclick="deleteCause5M('milieu', 'MI003')" title="Supprimer">🗑️</button>
                                </div>
                            </div>
                            <div class="cause-item">
                                <span class="cause-text">Bruit excessif</span>
                                <div class="cause-actions">
                                    <button class="btn-icon" onclick="editCause5M('milieu', 'MI004')" title="Éditer">✏️</button>
                                    <button class="btn-icon danger" onclick="deleteCause5M('milieu', 'MI004')" title="Supprimer">🗑️</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Machine -->
                    <div class="cause-5m-category">
                        <div class="cause-category-header">
                            <h4>🔧 Machine</h4>
                            <button class="btn btn-sm btn-primary" onclick="showNewCause5MModal('machine')">
                                ➕ Ajouter
                            </button>
                        </div>
                        <div class="causes-list" id="causes-machine">
                            <div class="cause-item">
                                <span class="cause-text">Panne équipement</span>
                                <div class="cause-actions">
                                    <button class="btn-icon" onclick="editCause5M('machine', 'MC001')" title="Éditer">✏️</button>
                                    <button class="btn-icon danger" onclick="deleteCause5M('machine', 'MC001')" title="Supprimer">🗑️</button>
                                </div>
                            </div>
                            <div class="cause-item">
                                <span class="cause-text">Usure normale</span>
                                <div class="cause-actions">
                                    <button class="btn-icon" onclick="editCause5M('machine', 'MC002')" title="Éditer">✏️</button>
                                    <button class="btn-icon danger" onclick="deleteCause5M('machine', 'MC002')" title="Supprimer">🗑️</button>
                                </div>
                            </div>
                            <div class="cause-item">
                                <span class="cause-text">Réglage incorrect</span>
                                <div class="cause-actions">
                                    <button class="btn-icon" onclick="editCause5M('machine', 'MC003')" title="Éditer">✏️</button>
                                    <button class="btn-icon danger" onclick="deleteCause5M('machine', 'MC003')" title="Supprimer">🗑️</button>
                                </div>
                            </div>
                            <div class="cause-item">
                                <span class="cause-text">Maintenance retardée</span>
                                <div class="cause-actions">
                                    <button class="btn-icon" onclick="editCause5M('machine', 'MC004')" title="Éditer">✏️</button>
                                    <button class="btn-icon danger" onclick="deleteCause5M('machine', 'MC004')" title="Supprimer">🗑️</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modales -->
        <!-- Modal Nouveau Programme -->
        <div id="modal-new-program" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 id="modal-program-title">Nouveau programme</h3>
                    <button class="modal-close" onclick="closeModal('modal-new-program')">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="new-program-form" onsubmit="saveNewProgram(event)">
                        <div class="form-group">
                            <label for="modal-program-name">Nom du programme</label>
                            <input type="text" id="modal-program-name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="modal-equipment-type">Type d'équipement</label>
                            <select id="modal-equipment-type" name="equipment_type" required>
                                <option value="LAVAGE">Lavage</option>
                                <option value="SECHAGE">Séchage</option>
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="modal-duration">Durée (min)</label>
                                <input type="number" id="modal-duration" name="duration" min="1" required>
                            </div>
                            <div class="form-group">
                                <label for="modal-temperature">Température (°C)</label>
                                <input type="number" id="modal-temperature" name="temperature" min="20" max="90">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="modal-nominal-rate">Cadence nominale (kg/h)</label>
                            <input type="number" id="modal-nominal-rate" name="nominal_rate" min="1" step="0.1">
                        </div>
                        <div class="modal-actions">
                            <button type="submit" class="btn btn-primary">Créer</button>
                            <button type="button" class="btn btn-secondary" onclick="closeModal('modal-new-program')">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Nouvel Opérateur -->
        <div id="modal-new-operator" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Nouvel opérateur</h3>
                    <button class="modal-close" onclick="closeModal('modal-new-operator')">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="new-operator-form" onsubmit="saveNewOperator(event)">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="modal-operator-firstname">Prénom</label>
                                <input type="text" id="modal-operator-firstname" name="firstname" required>
                            </div>
                            <div class="form-group">
                                <label for="modal-operator-lastname">Nom</label>
                                <input type="text" id="modal-operator-lastname" name="lastname" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="modal-operator-login">Login</label>
                            <input type="text" id="modal-operator-login" name="login" required>
                        </div>
                        <div class="form-group">
                            <label for="modal-operator-role">Rôle</label>
                            <select id="modal-operator-role" name="role" required>
                                <option value="OPERATEUR">Opérateur</option>
                                <option value="CHEF_ATELIER">Chef d'atelier</option>
                                <option value="ADMIN">Administrateur</option>
                            </select>
                        </div>
                        <div class="modal-actions">
                            <button type="submit" class="btn btn-primary">Créer</button>
                            <button type="button" class="btn btn-secondary" onclick="closeModal('modal-new-operator')">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Nouveau Motif -->
        <div id="modal-new-motif" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Nouveau motif d'arrêt</h3>
                    <button class="modal-close" onclick="closeModal('modal-new-motif')">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="new-motif-form" onsubmit="saveNewMotif(event)">
                        <div class="form-group">
                            <label for="modal-motif-code">Code</label>
                            <input type="text" id="modal-motif-code" name="code" placeholder="AT02" required>
                        </div>
                        <div class="form-group">
                            <label for="modal-motif-description">Description</label>
                            <input type="text" id="modal-motif-description" name="description" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="modal-motif-type">Type</label>
                                <select id="modal-motif-type" name="type" required>
                                    <option value="NON_PLANIFIE">Non planifié</option>
                                    <option value="PLANIFIE">Planifié</option>
                                    <option value="INTER_CYCLE">Inter-cycle</option>
                                    <option value="NETTOYAGE">Nettoyage</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="modal-motif-category">Catégorie</label>
                                <select id="modal-motif-category" name="category" required>
                                    <option value="MECANIQUE">Mécanique</option>
                                    <option value="ELECTRIQUE">Électrique</option>
                                    <option value="ORGANISATION">Organisation</option>
                                    <option value="QUALITE">Qualité</option>
                                    <option value="SECURITE">Sécurité</option>
                                    <option value="AUTRE">Autre</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-actions">
                            <button type="submit" class="btn btn-primary">Créer</button>
                            <button type="button" class="btn btn-secondary" onclick="closeModal('modal-new-motif')">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Nouveau Type NC -->
        <div id="modal-new-type-nc" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Nouveau type de non-conformité</h3>
                    <button class="modal-close" onclick="closeModal('modal-new-type-nc')">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="new-type-nc-form" onsubmit="saveNewTypeNC(event)">
                        <div class="form-group">
                            <label for="modal-nc-code">Code</label>
                            <input type="text" id="modal-nc-code" name="code" placeholder="QUA02" required>
                        </div>
                        <div class="form-group">
                            <label for="modal-nc-description">Description</label>
                            <input type="text" id="modal-nc-description" name="description" required>
                        </div>
                        <div class="form-group">
                            <label for="modal-nc-severity">Gravité par défaut</label>
                            <select id="modal-nc-severity" name="severity" required>
                                <option value="MINEURE">Mineure</option>
                                <option value="MAJEURE">Majeure</option>
                                <option value="CRITIQUE">Critique</option>
                            </select>
                        </div>
                        <div class="modal-actions">
                            <button type="submit" class="btn btn-primary">Créer</button>
                            <button type="button" class="btn btn-secondary" onclick="closeModal('modal-new-type-nc')">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Nouvelle Cause 5M -->
        <div id="modal-new-cause-5m" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 id="modal-cause-5m-title">Nouvelle cause - Main-d'œuvre</h3>
                    <button class="modal-close" onclick="closeModal('modal-new-cause-5m')">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="new-cause-5m-form" onsubmit="saveNewCause5M(event)">
                        <input type="hidden" id="modal-cause-5m-axe" name="axe" value="">
                        <div class="form-group">
                            <label for="modal-cause-5m-description">Description de la cause</label>
                            <input type="text" id="modal-cause-5m-description" name="description" required>
                        </div>
                        <div class="modal-actions">
                            <button type="submit" class="btn btn-primary">Créer</button>
                            <button type="button" class="btn btn-secondary" onclick="closeModal('modal-new-cause-5m')">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        // Gestion des onglets de réglages
        function showSettingsTab(tabName) {
            // Masquer tous les contenus d'onglets
            document.querySelectorAll('.settings-tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Désactiver tous les boutons d'onglets
            document.querySelectorAll('.settings-tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Activer l'onglet sélectionné
            document.getElementById(`settings-${tabName}`).classList.add('active');
            document.getElementById(`tab-${tabName}`).classList.add('active');
            
            // Charger les données spécifiques à l'onglet
            if (tabName === 'programmes') {
                loadProgramsData();
            } else if (tabName === 'operateurs') {
                loadOperatorsData();
            }
        }

        // Fonctions pour les modales
        function showNewProgramModal() {
            document.getElementById('modal-new-program').style.display = 'flex';
        }

        function showNewOperatorModal() {
            document.getElementById('modal-new-operator').style.display = 'flex';
        }

        function showNewMotifModal() {
            document.getElementById('modal-new-motif').style.display = 'flex';
        }

        function showNewTypeNCModal() {
            document.getElementById('modal-new-type-nc').style.display = 'flex';
        }

        function showNewCause5MModal(axe) {
            const titles = {
                'mainOeuvre': 'Main-d\'œuvre',
                'methode': 'Méthode',
                'matiere': 'Matière',
                'milieu': 'Milieu',
                'machine': 'Machine'
            };
            
            document.getElementById('modal-cause-5m-title').textContent = `Nouvelle cause - ${titles[axe]}`;
            document.getElementById('modal-cause-5m-axe').value = axe;
            document.getElementById('modal-new-cause-5m').style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Fonctions de sauvegarde
        async function saveNewProgram(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            formData.append('action', 'add_program');
            
            try {
                const response = await fetch('ajax.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Programme créé avec succès !');
                    closeModal('modal-new-program');
                    event.target.reset();
                    loadProgramsData();
                } else {
                    alert('Erreur : ' + result.message);
                }
            } catch (error) {
                console.error('Erreur :', error);
                alert('Erreur de sauvegarde');
            }
        }

        async function saveNewOperator(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            formData.append('action', 'add_operator');
            
            try {
                const response = await fetch('ajax.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Opérateur créé avec succès !');
                    closeModal('modal-new-operator');
                    event.target.reset();
                    loadOperatorsData();
                } else {
                    alert('Erreur : ' + result.message);
                }
            } catch (error) {
                console.error('Erreur :', error);
                alert('Erreur de sauvegarde');
            }
        }

        async function saveNewMotif(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            formData.append('action', 'add_motif');
            
            try {
                const response = await fetch('ajax.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Motif d\'arrêt créé avec succès !');
                    closeModal('modal-new-motif');
                    event.target.reset();
                } else {
                    alert('Erreur : ' + result.message);
                }
            } catch (error) {
                console.error('Erreur :', error);
                alert('Erreur de sauvegarde');
            }
        }

        async function saveNewTypeNC(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            formData.append('action', 'add_type_nc');
            
            try {
                const response = await fetch('ajax.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Type de non-conformité créé avec succès !');
                    closeModal('modal-new-type-nc');
                    event.target.reset();
                } else {
                    alert('Erreur : ' + result.message);
                }
            } catch (error) {
                console.error('Erreur :', error);
                alert('Erreur de sauvegarde');
            }
        }

        async function saveNewCause5M(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            formData.append('action', 'add_cause_5m');
            
            try {
                const response = await fetch('ajax.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Cause 5M créée avec succès !');
                    closeModal('modal-new-cause-5m');
                    event.target.reset();
                } else {
                    alert('Erreur : ' + result.message);
                }
            } catch (error) {
                console.error('Erreur :', error);
                alert('Erreur de sauvegarde');
            }
        }

        // Fonctions de chargement des données
        async function loadProgramsData() {
            try {
                const response = await fetch('ajax.php?action=get_programs_settings');
                const result = await response.json();
                
                if (result.success) {
                    updateProgramsTable(result.machine_programs, result.drying_programs);
                }
            } catch (error) {
                console.error('Erreur de chargement des programmes :', error);
            }
        }

        async function loadOperatorsData() {
            try {
                const response = await fetch('ajax.php?action=get_operators');
                const result = await response.json();
                
                if (result.success) {
                    updateOperatorsTable(result.operators);
                }
            } catch (error) {
                console.error('Erreur de chargement des opérateurs :', error);
            }
        }

        function updateProgramsTable(machinePrograms, dryingPrograms) {
            // Mise à jour du tableau des programmes machines
            const machineTbody = document.getElementById('machine-programs-tbody');
            if (machineTbody && machinePrograms) {
                let html = '';
                Object.values(machinePrograms).forEach(program => {
                    html += `
                        <tr>
                            <td><strong class="program-name">${program.name}</strong></td>
                            <td>${program.duration_minutes} min</td>
                            <td>${program.temperature || '-'}°C</td>
                            <td>${program.nominal_rate || '-'} kg/h</td>
                            <td>${program.options ? program.options.join(', ') : '-'}</td>
                            <td><span class="badge badge-success">Actif</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-icon" onclick="editProgram(${program.id})" title="Éditer">✏️</button>
                                    <button class="btn-icon danger" onclick="deleteProgram(${program.id})" title="Supprimer">🗑️</button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
                machineTbody.innerHTML = html;
            }

            // Mise à jour du tableau des programmes séchoirs
            const dryingTbody = document.getElementById('drying-programs-tbody');
            if (dryingTbody && dryingPrograms) {
                let html = '';
                Object.values(dryingPrograms).forEach(program => {
                    html += `
                        <tr>
                            <td><strong class="program-name">${program.name}</strong></td>
                            <td>${program.article_type || '-'}</td>
                            <td>${program.duration_minutes} min</td>
                            <td>${program.temperature || '-'}°C</td>
                            <td><span class="badge badge-success">Actif</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-icon" onclick="editDryingProgram(${program.id})" title="Éditer">✏️</button>
                                    <button class="btn-icon danger" onclick="deleteDryingProgram(${program.id})" title="Supprimer">🗑️</button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
                dryingTbody.innerHTML = html;
            }
        }

        function updateOperatorsTable(operators) {
            const tbody = document.getElementById('operators-tbody');
            if (tbody && operators) {
                let html = '';
                operators.forEach(operator => {
                    const roleClass = operator.role === 'ADMIN' ? 'danger' : 
                                     operator.role === 'CHEF_ATELIER' ? 'warning' : 'info';
                    html += `
                        <tr>
                            <td><strong>${operator.firstname} ${operator.lastname}</strong></td>
                            <td>${operator.login}</td>
                            <td><span class="badge badge-${roleClass}">${operator.role}</span></td>
                            <td><span class="badge badge-success">Actif</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-icon" onclick="editOperator(${operator.id})" title="Éditer">✏️</button>
                                    <button class="btn-icon warning" onclick="resetPassword(${operator.id})" title="Reset MDP">🔑</button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;
            }
        }

        // Fonctions d'édition et de suppression
        function editProgram(id) {
            console.log('Édition du programme:', id);
            alert('Fonctionnalité d\'édition à développer');
        }

        function deleteProgram(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce programme ?')) {
                console.log('Suppression du programme:', id);
                alert('Suppression réussie !');
                loadProgramsData();
            }
        }

        function editDryingProgram(id) {
            console.log('Édition du programme séchoir:', id);
            alert('Fonctionnalité d\'édition à développer');
        }

        function deleteDryingProgram(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce programme séchoir ?')) {
                console.log('Suppression du programme séchoir:', id);
                alert('Suppression réussie !');
                loadProgramsData();
            }
        }

        function editOperator(id) {
            console.log('Édition de l\'opérateur:', id);
            alert('Fonctionnalité d\'édition à développer');
        }

        function resetPassword(id) {
            if (confirm('Réinitialiser le mot de passe de cet opérateur ?')) {
                console.log('Reset MDP pour opérateur:', id);
                alert('Mot de passe réinitialisé : temp123');
            }
        }

        function editMotif(code) {
            console.log('Édition du motif:', code);
            alert('Fonctionnalité d\'édition à développer');
        }

        function editTypeNC(code) {
            console.log('Édition du type NC:', code);
            alert('Fonctionnalité d\'édition à développer');
        }

        function editCause5M(axe, id) {
            console.log('Édition de la cause 5M:', axe, id);
            alert('Fonctionnalité d\'édition à développer');
        }

        function deleteCause5M(axe, id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette cause ?')) {
                console.log('Suppression de la cause 5M:', axe, id);
                alert('Cause supprimée !');
            }
        }

        // Fermeture des modales en cliquant à l'extérieur
        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        };

        // Initialisation de la page
        document.addEventListener('DOMContentLoaded', function() {
            // Charger les données par défaut
            loadProgramsData();
        });
    </script>
</body>
</html>
