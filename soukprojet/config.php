<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'trs_blanchisserie');

// Start session
session_start();

// Database connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

// Helper functions
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function formatDuration($minutes) {
    $hours = floor($minutes / 60);
    $mins = $minutes % 60;
    return sprintf("%d:%02d:00", $hours, $mins);
}

function calculateTRS($availability, $performance, $quality) {
    return round(($availability * $performance * $quality) / 10000, 1);
}

// Setup database tables
function setupDatabase() {
    global $pdo;
    
    try {
        // Create users table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                role VARCHAR(20) DEFAULT 'user',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Create machine_programs table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS machine_programs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                program_number INT UNIQUE NOT NULL,
                name VARCHAR(100) NOT NULL,
                duration_minutes INT NOT NULL,
                capacity_kg DECIMAL(5,2),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Create drying_programs table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS drying_programs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                article_type VARCHAR(100) NOT NULL,
                duration_minutes INT NOT NULL,
                temperature INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Create productions table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS productions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                type VARCHAR(20) NOT NULL,
                equipment VARCHAR(50) NOT NULL,
                operator VARCHAR(100) NOT NULL,
                weight DECIMAL(6,2),
                pieces INT,
                program_name VARCHAR(100),
                theoretical_duration INT,
                real_duration DECIMAL(8,2),
                temperature INT,
                article_type VARCHAR(100),
                timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Create equipment_stops table (enhanced)
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS equipment_stops (
                id INT AUTO_INCREMENT PRIMARY KEY,
                equipment_type VARCHAR(50) NOT NULL,
                equipment_name VARCHAR(50) NOT NULL,
                stop_code VARCHAR(100) NOT NULL,
                stop_type VARCHAR(50) NOT NULL DEFAULT 'non-planifie',
                start_time DATETIME NOT NULL,
                end_time DATETIME NULL,
                duration_minutes INT NULL,
                operator VARCHAR(100) NULL,
                comment TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
        
        // Create non_conformities table (enhanced)
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS non_conformities (
                id INT AUTO_INCREMENT PRIMARY KEY,
                equipment_type ENUM('Machine', 'Séchoir', 'Calandre', 'Repassage') NOT NULL,
                equipment_name VARCHAR(50) NOT NULL,
                program_name VARCHAR(100),
                nc_type VARCHAR(100) NOT NULL,
                quantity_impacted DECIMAL(8,2),
                weight_impacted DECIMAL(8,2),
                operator VARCHAR(100),
                comment TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Create manual_sessions table for Calandre & Repassage
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS manual_sessions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                station_type ENUM('Calandre', 'Repassage') NOT NULL,
                operator VARCHAR(100) NOT NULL,
                pieces INT DEFAULT 0,
                weight_kg DECIMAL(8,2) DEFAULT 0,
                comment TEXT,
                session_start DATETIME NOT NULL,
                session_end DATETIME NULL,
                total_duration_minutes DECIMAL(8,2) DEFAULT 0,
                pause_duration_minutes DECIMAL(8,2) DEFAULT 0,
                real_duration_minutes DECIMAL(8,2) DEFAULT 0,
                cadence_pieces_per_hour DECIMAL(8,2) DEFAULT 0,
                status ENUM('Arrêtée', 'En cours', 'Pause', 'Finie') DEFAULT 'Arrêtée',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
        
        // Create session_events table for audit trail
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS session_events (
                id INT AUTO_INCREMENT PRIMARY KEY,
                session_id INT NOT NULL,
                session_type ENUM('manual', 'machine', 'sechoir') NOT NULL,
                event_type ENUM('start', 'pause', 'resume', 'stop', 'finish', 'autosave') NOT NULL,
                event_time DATETIME NOT NULL,
                previous_status VARCHAR(20),
                new_status VARCHAR(20),
                duration_at_event DECIMAL(8,2),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (session_id) REFERENCES manual_sessions(id) ON DELETE CASCADE
            )
        ");
        
        // Create stop_codes table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS stop_codes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(20) UNIQUE NOT NULL,
                description VARCHAR(255) NOT NULL,
                stop_type ENUM('planifié', 'non-planifié') NOT NULL,
                category VARCHAR(50) NOT NULL,
                equipment_types JSON,
                active BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Create nc_types table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS nc_types (
                id INT AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(20) UNIQUE NOT NULL,
                description VARCHAR(255) NOT NULL,
                severity ENUM('mineure', 'majeure', 'critique') NOT NULL,
                category VARCHAR(50) NOT NULL,
                equipment_types JSON,
                active BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Create operators table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS operators (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) UNIQUE NOT NULL,
                employee_id VARCHAR(20),
                active BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Create filter_presets table for saved filter combinations
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS filter_presets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                preset_name VARCHAR(100) NOT NULL,
                filters JSON NOT NULL,
                is_default BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
        
        // Create system_config table for TRS thresholds and settings
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS system_config (
                id INT AUTO_INCREMENT PRIMARY KEY,
                config_key VARCHAR(100) UNIQUE NOT NULL,
                config_value JSON NOT NULL,
                description TEXT,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
        
        // Create machine_program_capacities table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS machine_program_capacities (
                id INT AUTO_INCREMENT PRIMARY KEY,
                machine_number INT NOT NULL,
                program_id INT NOT NULL,
                optimal_capacity DECIMAL(5,2) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_machine_program (machine_number, program_id),
                FOREIGN KEY (program_id) REFERENCES machine_programs(id) ON DELETE CASCADE
            )
        ");
        
        // Insert default admin user if not exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            $adminPassword = password_hash('admin', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES ('admin', ?, 'admin')");
            $stmt->execute([$adminPassword]);
        }
        
        // Insert default machine programs if not exists (from Excel data)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM machine_programs");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            $defaultPrograms = [
                // [program_number, name, duration_minutes, capacity_kg] - Based on Excel "Données" sheet
                [20, '20 Humidification', 90, 20.0],
                [21, '21: SYNTHETIQUE BLANC', 75, 15.0],
                [22, '22 :PLAT HOTELIER', 85, 18.0],
                [23, '23 :PLAT FOYER', 80, 16.0],
                [24, '24 :EPONGE COULEUR', 95, 20.0],
                [25, '25 :PLAT SALE DESINF', 100, 18.0],
                [26, '26 :VT SALE INDUSTRI', 110, 22.0],
                [27, '27 :SYNTHETIQUE BLANC', 75, 15.0],
                [28, '28 :PC PRO COUCHES', 120, 25.0],
                [29, '29 :CUISINE COULEUR', 85, 18.0],
                [30, '30: TENUES SANTE BLA', 90, 20.0],
                [31, '31 :LAINE', 60, 12.0],
                [32, '32 :COUVERTURE COUET', 70, 14.0],
                [33, '33 :ARTICLE MENAGE', 95, 22.0],
                [34, '34 :RELAVAGE CHLORE', 105, 24.0],
                [35, '35 :CUISINE BLANC', 85, 18.0],
                [36, '36 :DECATISSAGE', 75, 16.0],
                [37, '37 TENUES SDIS IMPE', 80, 18.0],
                [38, '38:DECONTAMINATION', 130, 28.0],
                [39, '39/Tenu de SKI', 70, 15.0],
                [40, '40 :THERME CURISTES', 95, 22.0],
                [41, '41 :THERMES SPA', 90, 20.0],
                [42, '42 :PANTALON JURA TR', 75, 16.0],
                [43, '43 :MAILLOT JURA TR', 70, 15.0]
            ];
            
            $stmt = $pdo->prepare("INSERT INTO machine_programs (program_number, name, duration_minutes, capacity_kg) VALUES (?, ?, ?, ?)");
            foreach ($defaultPrograms as $program) {
                $stmt->execute($program);
            }
        }
        
        // Insert machine-specific program capacities from Excel table
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM machine_program_capacities");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            // Get program IDs first
            $stmt = $pdo->prepare("SELECT id, program_number FROM machine_programs");
            $stmt->execute();
            $programs = $stmt->fetchAll();
            $programMap = [];
            foreach ($programs as $prog) {
                $programMap[$prog['program_number']] = $prog['id'];
            }
            
            // Machine-specific capacities from Excel table
            $machineCapacities = [
                // [program_number, machine_13kg, machine_20kg, machine_50kg, machine_70kg]
                [20, null, null, null, null], // 20 Humidification - no values in table
                [21, 10, 17, 38, 54], // 21: SYNTHETIQUE BLANC
                [22, 12, 23, 45, 64], // 22 :PLAT HOTELIER
                [23, 11, 21, 42, 58], // 23 :PLAT FOYER
                [24, 10, 19, 38, 54], // 24 :EPONGE COULEUR
                [25, 10, 18, 40, 50], // 25 :PLAT SALE DESINF
                [26, 9, 18, 36, 50], // 26 :VT SALE INDUSTRI
                [27, 10, 17, 38, 54], // 27 :SYNTHETIQUE BLANC
                [28, 9, 17, 38, 50], // 28 :PC PRO COUCHES
                [29, 9, 19, null, 50], // 29 :CUISINE COULEUR
                [30, 9, 18, 36, 50], // 30: TENUES SANTE BLA
                [31, 7, 13, 25, 35], // 31 :LAINE
                [32, 6, 11, null, 32], // 32 :COUVERTURE COUET
                [33, 16, 28, null, 80], // 33 :ARTICLE MENAGE
                [34, 10, 19, null, 54], // 34 :RELAVAGE CHLORE
                [35, 9, 19, null, 50], // 35 :CUISINE BLANC
                [36, 12, 23, null, 64], // 36 :DECATISSAGE
                [37, 10, 19, 38, 54], // 37 TENUES SOIS IMPE
                [38, null, null, null, null], // 38:DECONTAMINATION - no values in table
                [39, 9, 17, null, 47], // 39/Tenu de SKI
                [40, 15, 33, 65, 90], // 40 :THERME CURISTES
                [41, 11, 33, 65, 90], // 41 :THERMES SPA
                [42, 9, 17, 36, null], // 42 :PANTALON JURA TR
                [43, 9, 17, 36, null]  // 43 :MAILLOT JURA TR
            ];
            
            $stmt = $pdo->prepare("INSERT INTO machine_program_capacities (machine_number, program_id, optimal_capacity) VALUES (?, ?, ?)");
            
            foreach ($machineCapacities as $capacity) {
                $programNumber = $capacity[0];
                $capacities = array_slice($capacity, 1); // [13kg, 20kg, 50kg, 70kg]
                $machines = [13, 20, 50, 70];
                
                if (isset($programMap[$programNumber])) {
                    $programId = $programMap[$programNumber];
                    
                    for ($i = 0; $i < 4; $i++) {
                        if ($capacities[$i] !== null) {
                            $stmt->execute([$machines[$i], $programId, $capacities[$i]]);
                        }
                    }
                }
            }
        }
        
        // Insert comprehensive drying programs from Excel data if not exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM drying_programs");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            $comprehensiveDryingPrograms = [
                // Based on Excel "Séchoir" sheet and machine program data
                ['20 Humidification', 'Humidification générale', 90, 40],
                ['21: SYNTHETIQUE BLANC', 'Synthétique blanc', 75, 60],
                ['22: PLAT HOTELIER', 'Linge plat hôtelier', 85, 70],
                ['23: PLAT FOYER', 'Linge plat foyer', 80, 65],
                ['24: EPONGE COULEUR', 'Éponge couleur', 95, 75],
                ['25: PLAT SALE DESINF', 'Linge plat sale désinfection', 100, 80],
                ['26: VT SALE INDUSTRI', 'Vêtement sale industriel', 110, 85],
                ['27: SYNTHETIQUE BLANC', 'Synthétique blanc standard', 75, 60],
                ['28: PC PRO COUCHES', 'Pièces pro couches', 120, 90],
                ['29: CUISINE COULEUR', 'Linge cuisine couleur', 85, 70],
                ['30: TENUES SANTE BLA', 'Tenues santé blanc', 90, 75],
                ['31: LAINE', 'Articles en laine', 60, 45],
                ['32: COUVERTURE COUET', 'Couvertures couchettes', 70, 55],
                ['33: ARTICLE MENAGE', 'Articles ménage', 95, 80],
                ['34: RELAVAGE CHLORE', 'Relavage chloré', 105, 85],
                ['35: CUISINE BLANC', 'Linge cuisine blanc', 85, 70],
                ['36: DECATISSAGE', 'Décatissage', 75, 65],
                ['37: TENUES SDIS IMPE', 'Tenues SDIS imperméables', 80, 70],
                ['38: DECONTAMINATION', 'Décontamination', 130, 95],
                ['39: Tenu de SKI', 'Tenues de ski', 70, 60],
                ['40: THERME CURISTES', 'Linge thermes curistes', 95, 80],
                ['41: THERMES SPA', 'Linge thermes spa', 90, 75],
                ['42: PANTALON JURA TR', 'Pantalons Jura travail', 75, 65],
                ['43: MAILLOT JURA TR', 'Maillots Jura travail', 70, 60],
                // Additional séchoir-specific programs
                ['COTON EPAIS', 'Serviettes, Draps épais', 90, 75],
                ['COTON FIN', 'Taies, Nappes fines', 60, 65],
                ['SYNTHETIQUE STANDARD', 'Blouses, Chemises standard', 45, 55],
                ['DELICAT', 'Linge délicat', 30, 45],
                ['MIXTE COULEUR', 'Mélange couleurs', 80, 70],
                ['BLANC HOPITAL', 'Blanc hospitalier', 95, 80],
                ['PROFESSIONNEL', 'Vêtements professionnels', 85, 75],
                ['SPORT', 'Articles de sport', 65, 60]
            ];
            
            $stmt = $pdo->prepare("INSERT INTO drying_programs (name, article_type, duration_minutes, temperature) VALUES (?, ?, ?, ?)");
            $defaultNCTypes = [
                ['Q01', 'Linge taché', 'majeure', 'qualite', '["Machine", "Séchoir"]'],
                ['Q02', 'Linge brûlé', 'critique', 'qualite', '["Séchoir"]'],
                ['Q03', 'Mal séché', 'majeure', 'qualite', '["Séchoir"]'],
                ['Q04', 'Mal lavé', 'majeure', 'qualite', '["Machine"]'],
                ['A01', 'Déchiré', 'mineure', 'aspect', '["Machine", "Séchoir", "Calandre", "Repassage"]'],
                ['A02', 'Décoloré', 'majeure', 'aspect', '["Machine"]'],
                ['A03', 'Froissé excessivement', 'mineure', 'aspect', '["Séchoir", "Calandre"]'],
                ['F01', 'Bouton manquant', 'mineure', 'fonctionnel', '["Machine", "Repassage"]'],
                ['S01', 'Résidu chimique', 'critique', 'securite', '["Machine"]']
            ];
            
            $stmt = $pdo->prepare("INSERT INTO nc_types (code, description, severity, category, equipment_types) VALUES (?, ?, ?, ?, ?)");
            foreach ($defaultNCTypes as $type) {
                $stmt->execute($type);
            }
        }
        
        // Insert default operators if not exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM operators");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            $defaultOperators = [
                ['Marie Dupont', 'EMP001'],
                ['Jean Martin', 'EMP002'],
                ['Sophie Bernard', 'EMP003'],
                ['Pierre Durand', 'EMP004'],
                ['Claire Moreau', 'EMP005'],
                ['Michel Petit', 'EMP006'],
                ['Isabelle Roux', 'EMP007'],
                ['François Blanc', 'EMP008']
            ];
            
            $stmt = $pdo->prepare("INSERT INTO operators (name, employee_id) VALUES (?, ?)");
            foreach ($defaultOperators as $operator) {
                $stmt->execute($operator);
            }
        }
        
        // Insert default system configuration if not exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM system_config");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            $defaultConfigs = [
                ['trs_thresholds', '{"disponibilite_red": 80, "disponibilite_amber": 90, "performance_red": 75, "performance_amber": 85, "qualite_red": 85, "qualite_amber": 95, "trs_red": 60, "trs_amber": 75}', 'Seuils TRS pour les indicateurs de couleur'],
                ['working_schedule', '{"hours_per_day": 7, "days_per_week": 5, "shift_start": "08:00", "shift_end": "15:00"}', 'Configuration des horaires de travail'],
                ['manual_stations_rates', '{"calandre_target": 60, "repassage_target": 25}', 'Cadences cibles pour les postes manuels (pièces/heure)'],
                ['equipment_capacities', '{"machine_13": 13, "machine_20": 20, "machine_50": 50, "machine_70": 70, "sechoir_1": 25, "sechoir_2": 25, "sechoir_3": 25, "sechoir_4": 25, "calandre": 15, "repassage": 10}', 'Capacités maximales des équipements en kg']
            ];
            
            $stmt = $pdo->prepare("INSERT INTO system_config (config_key, config_value, description) VALUES (?, ?, ?)");
            foreach ($defaultConfigs as $config) {
                $stmt->execute($config);
            }
        }
        
        return true;
    } catch (Exception $e) {
        error_log("Database setup error: " . $e->getMessage());
        return false;
    }
}

// Run database setup
setupDatabase();
?>
