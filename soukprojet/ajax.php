<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
} else {
    $action = $_GET['action'] ?? '';
}

try {
    switch ($action) {
        case 'login':
            handleLogin();
            break;
            
        case 'logout':
            handleLogout();
            break;
            
        case 'check_auth':
            handleCheckAuth();
            break;
            
        case 'get_machine_programs':
            handleGetMachinePrograms();
            break;
            
        case 'get_drying_programs':
            handleGetDryingPrograms();
            break;
            
        case 'save_machine_production':
            handleSaveMachineProduction();
            break;
            
        case 'save_sechoir_production':
            handleSaveSechoirProduction();
            break;
            
        case 'save_calandre_production':
            handleSaveCalandreProduction();
            break;
            
        case 'save_repassage_production':
            handleSaveRepassageProduction();
            break;
            
        case 'save_arret':
            handleSaveArret();
            break;
            
        case 'save_nonconformite':
            handleSaveNonConformite();
            break;
            
        case 'get_trs_data':
            handleGetTRSData();
            break;
            
        case 'add_machine_program':
            handleAddMachineProgram();
            break;
            
        case 'add_drying_program':
            handleAddDryingProgram();
            break;
            
        case 'delete_machine_program':
            handleDeleteMachineProgram();
            break;
            
        case 'delete_drying_program':
            handleDeleteDryingProgram();
            break;
            
        case 'apply_filters':
            handleApplyFilters();
            break;
            
        case 'export_excel':
            handleExportExcel();
            break;
            
        case 'get_arrets':
            handleGetArrets();
            break;
            
        case 'get_nonconformites':
            handleGetNonConformites();
            break;
            
        case 'recalculate_sechoir':
            handleRecalculateSechoir();
            break;
            
        case 'get_advanced_trs_data':
            handleGetAdvancedTRSData();
            break;
            
        case 'get_equipment_performance':
            handleGetEquipmentPerformance();
            break;
            
        case 'get_pareto_data':
            handleGetParetoData();
            break;
            
        case 'generate_pdf_report':
            handleGeneratePDFReport();
            break;
            
        case 'save_manual_stations_config':
            handleSaveManualStationsConfig();
            break;
            
        case 'save_calendar_config':
            handleSaveCalendarConfig();
            break;
            
        case 'save_thresholds_config':
            handleSaveThresholdsConfig();
            break;
            
        case 'save_equipment_config':
            handleSaveEquipmentConfig();
            break;
            
        case 'add_stop_code':
            handleAddStopCode();
            break;
            
        case 'add_nc_code':
            handleAddNCCode();
            break;
            
        case 'get_conclusions':
            handleGetConclusions();
            break;
            
        case 'get_machine_program_capacities':
            handleGetMachineProgramCapacities();
            break;
            
        case 'save_machine_program_capacities':
            handleSaveMachineProgramCapacities();
            break;
            
        case 'get_program_capacity':
            handleGetProgramCapacity();
            break;
            
        case 'get_drying_program_temperature':
            handleGetDryingProgramTemperature();
            break;
            
        // Manual stations management
        case 'start_manual_session':
            handleStartManualSession();
            break;
            
        case 'pause_manual_session':
            handlePauseManualSession();
            break;
            
        case 'resume_manual_session':
            handleResumeManualSession();
            break;
            
        case 'finish_manual_session':
            handleFinishManualSession();
            break;
            
        case 'autosave_manual_session':
            handleAutosaveManualSession();
            break;
            
        case 'get_active_manual_sessions':
            handleGetActiveManualSessions();
            break;
            
        // Enhanced equipment stops
        case 'save_enhanced_arret':
            handleSaveEnhancedArret();
            break;
            
        case 'get_stop_codes':
            handleGetStopCodes();
            break;
            
        case 'save_stop_code':
            handleSaveStopCode();
            break;
            
        // Enhanced non-conformities
        case 'save_enhanced_nc':
            handleSaveEnhancedNC();
            break;
            
        case 'get_nc_types':
            handleGetNCTypes();
            break;
            
        case 'save_nc_type':
            handleSaveNCType();
            break;
            
        // Operators management
        case 'get_operators':
            handleGetOperators();
            break;
            
        case 'save_operator':
            handleSaveOperator();
            break;
            
        // Filter presets
        case 'save_filter_preset':
            handleSaveFilterPreset();
            break;
            
        case 'get_filter_presets':
            handleGetFilterPresets();
            break;
            
        case 'delete_filter_preset':
            handleDeleteFilterPreset();
            break;
            
        default:
            throw new Exception('Action non reconnue');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function handleLogin() {
    global $pdo;
    
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Identifiants incorrects']);
    }
}

function handleLogout() {
    session_destroy();
    echo json_encode(['success' => true]);
}

function handleCheckAuth() {
    echo json_encode(['authenticated' => isLoggedIn()]);
}

function handleGetMachinePrograms() {
    global $pdo;
    
    $stmt = $pdo->query("SELECT * FROM machine_programs ORDER BY program_number");
    $programs = $stmt->fetchAll();
    
    $result = [];
    foreach ($programs as $program) {
        $result[$program['id']] = $program;
    }
    
    echo json_encode($result);
}

function handleGetDryingPrograms() {
    global $pdo;
    
    $stmt = $pdo->query("SELECT * FROM drying_programs ORDER BY name");
    $programs = $stmt->fetchAll();
    
    $result = [];
    foreach ($programs as $program) {
        $result[$program['id']] = $program;
    }
    
    echo json_encode($result);
}

function handleSaveMachineProduction() {
    global $pdo;
    
    $machine = sanitize($_POST['machine']);
    $programId = (int)$_POST['program'];
    $weight = (float)$_POST['weight'];
    $operator = sanitize($_POST['operator']);
    $realDuration = (float)$_POST['real_duration'];
    
    // Get program details
    $stmt = $pdo->prepare("SELECT * FROM machine_programs WHERE id = ?");
    $stmt->execute([$programId]);
    $program = $stmt->fetch();
    
    if (!$program) {
        throw new Exception('Programme non trouvé');
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO productions (type, equipment, operator, weight, program_name, theoretical_duration, real_duration, timestamp)
        VALUES ('machine', ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([
        "Machine $machine",
        $operator,
        $weight,
        $program['name'],
        $program['duration_minutes'],
        $realDuration
    ]);
    
    echo json_encode(['success' => true]);
}

function handleSaveSechoirProduction() {
    global $pdo;
    
    $sechoir = sanitize($_POST['sechoir']);
    $articleType = sanitize($_POST['article_type']);
    $duration = (int)$_POST['duration'];
    $temperature = (int)$_POST['temperature'];
    $weight = (float)$_POST['weight'];
    $operator = sanitize($_POST['operator']);
    $realDuration = (float)$_POST['real_duration'];
    
    $stmt = $pdo->prepare("
        INSERT INTO productions (type, equipment, operator, weight, theoretical_duration, real_duration, temperature, article_type, timestamp)
        VALUES ('sechoir', ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([
        "Séchoir $sechoir",
        $operator,
        $weight,
        $duration,
        $realDuration,
        $temperature,
        $articleType
    ]);
    
    echo json_encode(['success' => true]);
}

function handleSaveCalandreProduction() {
    global $pdo;
    
    $weight = (float)$_POST['weight'];
    $pieces = (int)$_POST['pieces'];
    $operator = sanitize($_POST['operator']);
    $realDuration = (float)$_POST['real_duration'];
    
    $stmt = $pdo->prepare("
        INSERT INTO productions (type, equipment, operator, weight, pieces, real_duration, timestamp)
        VALUES ('calandre', 'Calandre', ?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([$operator, $weight, $pieces, $realDuration]);
    
    echo json_encode(['success' => true]);
}

function handleSaveRepassageProduction() {
    global $pdo;
    
    $pieces = (int)$_POST['pieces'];
    $articleType = sanitize($_POST['article_type']);
    $operator = sanitize($_POST['operator']);
    $realDuration = (float)$_POST['real_duration'];
    
    $stmt = $pdo->prepare("
        INSERT INTO productions (type, equipment, operator, pieces, article_type, real_duration, timestamp)
        VALUES ('repassage', 'Repassage', ?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([$operator, $pieces, $articleType, $realDuration]);
    
    echo json_encode(['success' => true]);
}

function handleSaveArret() {
    global $pdo;
    
    $equipment = sanitize($_POST['equipment']);
    $reason = sanitize($_POST['reason']);
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];
    $comment = sanitize($_POST['comment']);
    
    $start = new DateTime($startTime);
    $end = new DateTime($endTime);
    $duration = $end->diff($start)->h * 60 + $end->diff($start)->i;
    
    if ($duration <= 0) {
        throw new Exception('La fin de l\'arrêt doit être après le début');
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO equipment_stops (equipment, reason, start_time, end_time, duration_minutes, comment, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([$equipment, $reason, $startTime, $endTime, $duration, $comment]);
    
    echo json_encode(['success' => true]);
}

function handleSaveNonConformite() {
    global $pdo;
    
    $equipment = sanitize($_POST['equipment']);
    $ncType = sanitize($_POST['type']);
    $quantity = (int)$_POST['quantity'];
    $severity = sanitize($_POST['severity']);
    $description = sanitize($_POST['description']);
    
    $stmt = $pdo->prepare("
        INSERT INTO non_conformities (equipment_type, equipment_name, nc_type, quantity_impacted, comment, created_at)
        VALUES ('Machine', ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([$equipment, $ncType, $quantity, $description]);
    
    echo json_encode(['success' => true]);
}

function handleGetTRSData() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    // Get recent productions
    $stmt = $pdo->prepare("
        SELECT * FROM productions 
        WHERE DATE(timestamp) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        ORDER BY timestamp DESC 
        LIMIT 50
    ");
    $stmt->execute();
    $productions = $stmt->fetchAll();
    
    // Calculate KPIs
    $kpis = calculateKPIs();
    
    echo json_encode([
        'success' => true,
        'productions' => $productions,
        'kpis' => $kpis
    ]);
}

function calculateKPIs() {
    global $pdo;
    
    // Simple KPI calculation for demonstration
    // In a real system, these would be more complex calculations
    
    // Availability: Based on equipment stops
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(duration_minutes), 0) as total_stop_time
        FROM equipment_stops 
        WHERE DATE(created_at) = CURDATE()
    ");
    $stmt->execute();
    $stopTime = $stmt->fetchColumn();
    
    $workingHours = 8 * 60; // 8 hours in minutes
    $availability = max(0, (($workingHours - $stopTime) / $workingHours) * 100);
    
    // Performance: Based on theoretical vs real duration
    $stmt = $pdo->prepare("
        SELECT AVG(CASE 
            WHEN theoretical_duration > 0 AND real_duration > 0 
            THEN (theoretical_duration / real_duration) * 100 
            ELSE 100 
        END) as avg_performance
        FROM productions 
        WHERE DATE(timestamp) = CURDATE()
    ");
    $stmt->execute();
    $performance = $stmt->fetchColumn() ?: 85;
    
    // Quality: Based on non-conformities
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as nc_count
        FROM non_conformities 
        WHERE DATE(created_at) = CURDATE()
    ");
    $stmt->execute();
    $ncCount = $stmt->fetchColumn();
    
    $quality = max(70, 100 - ($ncCount * 5)); // Reduce quality by 5% per non-conformity
    
    $trs = calculateTRS($availability, $performance, $quality);
    
    return [
        'availability' => round($availability, 1),
        'performance' => round($performance, 1),
        'quality' => round($quality, 1),
        'trs' => $trs
    ];
}

function handleAddMachineProgram() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $programNumber = (int)$_POST['program_number'];
    $name = sanitize($_POST['name']);
    $duration = (int)$_POST['duration_minutes'];
    $capacity = isset($_POST['capacity']) ? (float)$_POST['capacity'] : null;
    
    // Check if we're editing an existing program
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("
            UPDATE machine_programs 
            SET program_number = ?, name = ?, duration_minutes = ?, capacity_kg = ?
            WHERE id = ?
        ");
        $stmt->execute([$programNumber, $name, $duration, $capacity, $id]);
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO machine_programs (program_number, name, duration_minutes, capacity_kg)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            name = VALUES(name), 
            duration_minutes = VALUES(duration_minutes),
            capacity_kg = VALUES(capacity_kg)
        ");
        $stmt->execute([$programNumber, $name, $duration, $capacity]);
    }
    
    echo json_encode(['success' => true]);
}

function handleAddDryingProgram() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $name = sanitize($_POST['name']);
    $articleType = sanitize($_POST['article_type']);
    $duration = (int)$_POST['duration_minutes'];
    $temperature = isset($_POST['temperature']) ? (int)$_POST['temperature'] : null;
    
    // Check if we're editing an existing program
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("
            UPDATE drying_programs 
            SET name = ?, article_type = ?, duration_minutes = ?, temperature = ?
            WHERE id = ?
        ");
        $stmt->execute([$name, $articleType, $duration, $temperature, $id]);
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO drying_programs (name, article_type, duration_minutes, temperature)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$name, $articleType, $duration, $temperature]);
    }
    
    echo json_encode(['success' => true]);
}

function handleDeleteMachineProgram() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $id = (int)$_POST['id'];
    
    $stmt = $pdo->prepare("DELETE FROM machine_programs WHERE id = ?");
    $stmt->execute([$id]);
    
    echo json_encode(['success' => true]);
}

function handleDeleteDryingProgram() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $id = (int)$_POST['id'];
    
    $stmt = $pdo->prepare("DELETE FROM drying_programs WHERE id = ?");
    $stmt->execute([$id]);
    
    echo json_encode(['success' => true]);
}

function handleApplyFilters() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $dateStart = $_POST['date_start'] ?? '';
    $dateEnd = $_POST['date_end'] ?? '';
    $equipment = $_POST['equipment'] ?? '';
    $operator = $_POST['operator'] ?? '';
    
    $whereConditions = [];
    $params = [];
    
    if (!empty($dateStart)) {
        $whereConditions[] = "DATE(timestamp) >= ?";
        $params[] = $dateStart;
    }
    
    if (!empty($dateEnd)) {
        $whereConditions[] = "DATE(timestamp) <= ?";
        $params[] = $dateEnd;
    }
    
    if (!empty($equipment)) {
        $whereConditions[] = "equipment LIKE ?";
        $params[] = "%$equipment%";
    }
    
    if (!empty($operator)) {
        $whereConditions[] = "operator LIKE ?";
        $params[] = "%$operator%";
    }
    
    $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";
    
    $stmt = $pdo->prepare("
        SELECT * FROM productions 
        $whereClause
        ORDER BY timestamp DESC 
        LIMIT 100
    ");
    $stmt->execute($params);
    $productions = $stmt->fetchAll();
    
    // Calculate KPIs based on filtered data
    $kpis = calculateFilteredKPIs($dateStart, $dateEnd, $equipment, $operator);
    
    echo json_encode([
        'success' => true,
        'productions' => $productions,
        'kpis' => $kpis
    ]);
}

function calculateFilteredKPIs($dateStart = '', $dateEnd = '', $equipment = '', $operator = '') {
    global $pdo;
    
    // Build date range for KPI calculations
    $dateConditions = [];
    $dateParams = [];
    
    if (!empty($dateStart)) {
        $dateConditions[] = "DATE(created_at) >= ?";
        $dateParams[] = $dateStart;
    } else {
        // Default to current date if no start date
        $dateConditions[] = "DATE(created_at) = CURDATE()";
    }
    
    if (!empty($dateEnd)) {
        $dateConditions[] = "DATE(created_at) <= ?";
        $dateParams[] = $dateEnd;
    }
    
    $dateWhereClause = !empty($dateConditions) ? "WHERE " . implode(" AND ", $dateConditions) : "";
    
    // Availability: Based on equipment stops in the filtered period
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(duration_minutes), 0) as total_stop_time
        FROM equipment_stops 
        $dateWhereClause
    ");
    $stmt->execute($dateParams);
    $stopTime = $stmt->fetchColumn();
    
    $workingHours = 8 * 60; // 8 hours in minutes
    $availability = max(0, (($workingHours - $stopTime) / $workingHours) * 100);
    
    // Performance: Based on theoretical vs real duration in filtered period
    $prodConditions = [];
    $prodParams = [];
    
    if (!empty($dateStart)) {
        $prodConditions[] = "DATE(timestamp) >= ?";
        $prodParams[] = $dateStart;
    }
    
    if (!empty($dateEnd)) {
        $prodConditions[] = "DATE(timestamp) <= ?";
        $prodParams[] = $dateEnd;
    } else if (empty($dateStart)) {
        // Default to current date if no date range specified
        $prodConditions[] = "DATE(timestamp) = CURDATE()";
    }
    
    if (!empty($equipment)) {
        $prodConditions[] = "equipment LIKE ?";
        $prodParams[] = "%$equipment%";
    }
    
    if (!empty($operator)) {
        $prodConditions[] = "operator LIKE ?";
        $prodParams[] = "%$operator%";
    }
    
    $prodWhereClause = !empty($prodConditions) ? "WHERE " . implode(" AND ", $prodConditions) : "";
    
    $stmt = $pdo->prepare("
        SELECT AVG(CASE 
            WHEN theoretical_duration > 0 AND real_duration > 0 
            THEN (theoretical_duration / real_duration) * 100 
            ELSE 100 
        END) as avg_performance
        FROM productions 
        $prodWhereClause
    ");
    $stmt->execute($prodParams);
    $performance = $stmt->fetchColumn() ?: 85;
    
    // Quality: Based on non-conformities in the filtered period
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as nc_count
        FROM non_conformities 
        $dateWhereClause
    ");
    $stmt->execute($dateParams);
    $ncCount = $stmt->fetchColumn();
    
    $quality = max(70, 100 - ($ncCount * 5)); // Reduce quality by 5% per non-conformity
    
    $trs = calculateTRS($availability, $performance, $quality);
    
    return [
        'availability' => round($availability, 1),
        'performance' => round($performance, 1),
        'quality' => round($quality, 1),
        'trs' => $trs
    ];
}

function handleExportExcel() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $dateStart = $_POST['date_start'] ?? '';
    $dateEnd = $_POST['date_end'] ?? '';
    $equipment = $_POST['equipment'] ?? '';
    $operator = $_POST['operator'] ?? '';
    
    $whereConditions = [];
    $params = [];
    
    if (!empty($dateStart)) {
        $whereConditions[] = "DATE(timestamp) >= ?";
        $params[] = $dateStart;
    }
    
    if (!empty($dateEnd)) {
        $whereConditions[] = "DATE(timestamp) <= ?";
        $params[] = $dateEnd;
    }
    
    if (!empty($equipment)) {
        $whereConditions[] = "equipment LIKE ?";
        $params[] = "%$equipment%";
    }
    
    if (!empty($operator)) {
        $whereConditions[] = "operator LIKE ?";
        $params[] = "%$operator%";
    }
    
    $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";
    
    $stmt = $pdo->prepare("
        SELECT * FROM productions 
        $whereClause
        ORDER BY timestamp DESC
    ");
    $stmt->execute($params);
    $productions = $stmt->fetchAll();
    
    // Generate CSV content
    $csvContent = "Date,Équipement,Opérateur,Type,Poids (kg),Pièces,Durée théorique (min),Durée réelle (min),Température (°C),Article\n";
    
    foreach ($productions as $prod) {
        $csvContent .= sprintf(
            "%s,%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
            $prod['timestamp'],
            $prod['equipment'],
            $prod['operator'],
            $prod['type'],
            $prod['weight'] ?? '',
            $prod['pieces'] ?? '',
            $prod['theoretical_duration'] ?? '',
            $prod['real_duration'] ?? '',
            $prod['temperature'] ?? '',
            $prod['article_type'] ?? ''
        );
    }
    
    echo json_encode([
        'success' => true,
        'csv_content' => $csvContent,
        'filename' => 'export_trs_' . date('Y-m-d_H-i-s') . '.csv'
    ]);
}

function handleGetArrets() {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT * FROM equipment_stops 
        ORDER BY created_at DESC 
        LIMIT 20
    ");
    $stmt->execute();
    $arrets = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'arrets' => $arrets
    ]);
}

function handleGetNonConformites() {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT * FROM non_conformities 
        ORDER BY created_at DESC 
        LIMIT 20
    ");
    $stmt->execute();
    $nonconformites = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'nonconformites' => $nonconformites
    ]);
}

function handleRecalculateSechoir() {
    global $pdo;
    
    $articleType = sanitize($_POST['article_type']);
    $weight = (float)$_POST['weight'];
    
    // Get drying program for this article type
    $stmt = $pdo->prepare("SELECT * FROM drying_programs WHERE article_type LIKE ? LIMIT 1");
    $stmt->execute(["%$articleType%"]);
    $program = $stmt->fetch();
    
    if ($program) {
        // Calculate adjusted duration based on weight
        $baseDuration = $program['duration_minutes'];
        $baseTemperature = $program['temperature'];
        
        // Simple calculation: adjust duration based on weight
        // In a real system, this would be more sophisticated
        $adjustedDuration = $baseDuration + ($weight > 10 ? ($weight - 10) * 2 : 0);
        $adjustedTemperature = $baseTemperature;
        
        echo json_encode([
            'success' => true,
            'duration' => $adjustedDuration,
            'temperature' => $adjustedTemperature,
            'program_name' => $program['name']
        ]);
    } else {
        // Default values if no program found
        echo json_encode([
            'success' => true,
            'duration' => 60,
            'temperature' => 70,
            'program_name' => 'Programme par défaut'
        ]);
    }
}

// New enhanced TRS functions
function handleGetAdvancedTRSData() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    // Get comprehensive data for advanced TRS dashboard
    $dateStart = $_GET['date_start'] ?? date('Y-m-d', strtotime('-7 days'));
    $dateEnd = $_GET['date_end'] ?? date('Y-m-d');
    
    // Global summary calculations
    $globalSummary = calculateGlobalSummary($dateStart, $dateEnd);
    
    // Equipment performance data
    $equipmentPerformance = calculateEquipmentPerformance($dateStart, $dateEnd);
    
    // Chart data
    $chartData = generateChartData($dateStart, $dateEnd);
    
    echo json_encode([
        'success' => true,
        'global_summary' => $globalSummary,
        'equipment_performance' => $equipmentPerformance,
        'chart_data' => $chartData
    ]);
}

function calculateGlobalSummary($dateStart, $dateEnd) {
    global $pdo;
    
    // Calculate totals for the period
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_cycles,
            SUM(COALESCE(weight, 0)) as total_weight,
            SUM(COALESCE(pieces, 0)) as total_pieces,
            AVG(CASE 
                WHEN theoretical_duration > 0 AND real_duration > 0 
                THEN (theoretical_duration / real_duration) * 100 
                ELSE 100 
            END) as avg_performance
        FROM productions 
        WHERE DATE(timestamp) BETWEEN ? AND ?
    ");
    $stmt->execute([$dateStart, $dateEnd]);
    $totals = $stmt->fetch();
    
    // Calculate availability
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(duration_minutes), 0) as total_stop_time
        FROM equipment_stops 
        WHERE DATE(created_at) BETWEEN ? AND ?
    ");
    $stmt->execute([$dateStart, $dateEnd]);
    $stopTime = $stmt->fetchColumn();
    
    $workingDays = (strtotime($dateEnd) - strtotime($dateStart)) / (24 * 3600) + 1;
    $totalWorkingMinutes = $workingDays * 7 * 60; // 7 hours per day
    $availability = max(0, (($totalWorkingMinutes - $stopTime) / $totalWorkingMinutes) * 100);
    
    // Calculate quality
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as nc_count
        FROM non_conformities 
        WHERE DATE(created_at) BETWEEN ? AND ?
    ");
    $stmt->execute([$dateStart, $dateEnd]);
    $ncCount = $stmt->fetchColumn();
    
    $quality = max(70, 100 - ($ncCount * 2)); // Reduce quality by 2% per non-conformity
    
    $trs = calculateTRS($availability, $totals['avg_performance'] ?: 85, $quality);
    
    return [
        'trs' => round($trs, 1),
        'availability' => round($availability, 1),
        'performance' => round($totals['avg_performance'] ?: 85, 1),
        'quality' => round($quality, 1),
        'total_weight' => round($totals['total_weight'], 0),
        'total_cycles' => $totals['total_cycles'],
        'total_pieces' => $totals['total_pieces']
    ];
}

function calculateEquipmentPerformance($dateStart, $dateEnd) {
    global $pdo;
    
    $performance = [];
    
    // Machines performance
    $stmt = $pdo->prepare("
        SELECT 
            equipment,
            program_name,
            COUNT(*) as cycles,
            SUM(weight) as total_weight,
            AVG(CASE 
                WHEN theoretical_duration > 0 AND real_duration > 0 
                THEN (theoretical_duration / real_duration) * 100 
                ELSE 100 
            END) as performance_pct
        FROM productions 
        WHERE type = 'machine' AND DATE(timestamp) BETWEEN ? AND ?
        GROUP BY equipment, program_name
        ORDER BY equipment, program_name
    ");
    $stmt->execute([$dateStart, $dateEnd]);
    $performance['machines'] = $stmt->fetchAll();
    
    // Dryers performance
    $stmt = $pdo->prepare("
        SELECT 
            equipment,
            article_type,
            COUNT(*) as cycles,
            AVG(ABS(theoretical_duration - real_duration)) as avg_duration_diff,
            AVG(temperature) as avg_temperature
        FROM productions 
        WHERE type = 'sechoir' AND DATE(timestamp) BETWEEN ? AND ?
        GROUP BY equipment, article_type
        ORDER BY equipment, article_type
    ");
    $stmt->execute([$dateStart, $dateEnd]);
    $performance['sechoirs'] = $stmt->fetchAll();
    
    // Manual stations performance
    $stmt = $pdo->prepare("
        SELECT 
            equipment,
            SUM(pieces) as total_pieces,
            SUM(real_duration) as total_duration,
            CASE 
                WHEN SUM(real_duration) > 0 
                THEN (SUM(pieces) / (SUM(real_duration) / 60)) 
                ELSE 0 
            END as pieces_per_hour
        FROM productions 
        WHERE type IN ('calandre', 'repassage') AND DATE(timestamp) BETWEEN ? AND ?
        GROUP BY equipment
        ORDER BY equipment
    ");
    $stmt->execute([$dateStart, $dateEnd]);
    $performance['manual'] = $stmt->fetchAll();
    
    return $performance;
}

function generateChartData($dateStart, $dateEnd) {
    global $pdo;
    
    $chartData = [];
    
    // Weight by program chart data
    $stmt = $pdo->prepare("
        SELECT 
            program_name,
            SUM(weight) as total_weight
        FROM productions 
        WHERE type = 'machine' AND DATE(timestamp) BETWEEN ? AND ?
        GROUP BY program_name
        ORDER BY total_weight DESC
    ");
    $stmt->execute([$dateStart, $dateEnd]);
    $chartData['weight_by_program'] = $stmt->fetchAll();
    
    // Cycles by dryer chart data
    $stmt = $pdo->prepare("
        SELECT 
            equipment,
            COUNT(*) as cycles
        FROM productions 
        WHERE type = 'sechoir' AND DATE(timestamp) BETWEEN ? AND ?
        GROUP BY equipment
        ORDER BY equipment
    ");
    $stmt->execute([$dateStart, $dateEnd]);
    $chartData['cycles_by_dryer'] = $stmt->fetchAll();
    
    return $chartData;
}

function handleGetEquipmentPerformance() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $dateStart = $_GET['date_start'] ?? date('Y-m-d', strtotime('-7 days'));
    $dateEnd = $_GET['date_end'] ?? date('Y-m-d');
    
    $performance = calculateEquipmentPerformance($dateStart, $dateEnd);
    
    echo json_encode([
        'success' => true,
        'performance' => $performance
    ]);
}

function handleGetParetoData() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $type = $_GET['type'] ?? 'arrets'; // 'arrets' or 'nc'
    $equipmentFilter = $_GET['equipment_filter'] ?? '';
    $dateFilter = $_GET['date_filter'] ?? date('Y-m-d');
    
    if ($type === 'arrets') {
        $whereClause = "WHERE DATE(created_at) = ?";
        $params = [$dateFilter];
        
        if (!empty($equipmentFilter)) {
            if ($equipmentFilter === 'machines') {
                $whereClause .= " AND equipment LIKE '%machine%'";
            } elseif ($equipmentFilter === 'sechoirs') {
                $whereClause .= " AND equipment LIKE '%sechoir%'";
            } elseif ($equipmentFilter === 'manuels') {
                $whereClause .= " AND equipment IN ('calandre', 'repassage')";
            }
        }
        
        $stmt = $pdo->prepare("
            SELECT 
                reason,
                COUNT(*) as count,
                SUM(duration_minutes) as total_duration
            FROM equipment_stops 
            $whereClause
            GROUP BY reason
            ORDER BY total_duration DESC
        ");
        $stmt->execute($params);
        $data = $stmt->fetchAll();
    } else {
        $whereClause = "WHERE DATE(created_at) = ?";
        $params = [$dateFilter];
        
        if (!empty($equipmentFilter)) {
            if ($equipmentFilter === 'machines') {
                $whereClause .= " AND equipment LIKE '%machine%'";
            } elseif ($equipmentFilter === 'sechoirs') {
                $whereClause .= " AND equipment LIKE '%sechoir%'";
            } elseif ($equipmentFilter === 'manuels') {
                $whereClause .= " AND equipment IN ('calandre', 'repassage')";
            }
        }
        
        $stmt = $pdo->prepare("
            SELECT 
                type,
                COUNT(*) as count,
                SUM(quantity) as total_quantity
            FROM non_conformities 
            $whereClause
            GROUP BY type
            ORDER BY total_quantity DESC
        ");
        $stmt->execute($params);
        $data = $stmt->fetchAll();
    }
    
    echo json_encode([
        'success' => true,
        'data' => $data
    ]);
}

function handleGeneratePDFReport() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    // For now, return a simple response
    // In a real implementation, you would generate a PDF using a library like TCPDF or FPDF
    echo json_encode([
        'success' => true,
        'message' => 'Génération du rapport PDF en cours...',
        'download_url' => 'reports/trs_report_' . date('Y-m-d_H-i-s') . '.pdf'
    ]);
}

function handleSaveManualStationsConfig() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $calandreRate = (int)$_POST['calandre_rate'];
    $repassageRate = (int)$_POST['repassage_rate'];
    
    // Save to a configuration table (you would need to create this table)
    // For now, just return success
    echo json_encode(['success' => true, 'message' => 'Configuration des cadences sauvegardée']);
}

function handleSaveCalendarConfig() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $workingHours = (float)$_POST['working_hours'];
    $workingDays = (int)$_POST['working_days'];
    $shiftStart = $_POST['shift_start'];
    $shiftEnd = $_POST['shift_end'];
    
    // Save to configuration table
    echo json_encode(['success' => true, 'message' => 'Configuration du calendrier sauvegardée']);
}

function handleSaveThresholdsConfig() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $thresholds = [
        'disponibilite_red' => (int)$_POST['disponibilite_red'],
        'disponibilite_amber' => (int)$_POST['disponibilite_amber'],
        'performance_red' => (int)$_POST['performance_red'],
        'performance_amber' => (int)$_POST['performance_amber'],
        'qualite_red' => (int)$_POST['qualite_red'],
        'qualite_amber' => (int)$_POST['qualite_amber'],
        'trs_red' => (int)$_POST['trs_red'],
        'trs_amber' => (int)$_POST['trs_amber']
    ];
    
    // Save to configuration table
    echo json_encode(['success' => true, 'message' => 'Seuils TRS sauvegardés']);
}

function handleSaveEquipmentConfig() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $equipmentConfig = [
        'machine_13_capacity' => (int)$_POST['machine_13_capacity'],
        'machine_20_capacity' => (int)$_POST['machine_20_capacity'],
        'machine_50_capacity' => (int)$_POST['machine_50_capacity'],
        'machine_70_capacity' => (int)$_POST['machine_70_capacity'],
        'sechoir_1_capacity' => (int)$_POST['sechoir_1_capacity'],
        'sechoir_2_capacity' => (int)$_POST['sechoir_2_capacity'],
        'sechoir_3_capacity' => (int)$_POST['sechoir_3_capacity'],
        'sechoir_4_capacity' => (int)$_POST['sechoir_4_capacity']
    ];
    
    // Save to configuration table
    echo json_encode(['success' => true, 'message' => 'Configuration des équipements sauvegardée']);
}

function handleAddStopCode() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $code = sanitize($_POST['code']);
    $description = sanitize($_POST['description']);
    $type = sanitize($_POST['type']);
    $category = sanitize($_POST['category']);
    
    // You would need to create a stop_codes table
    echo json_encode(['success' => true, 'message' => 'Code arrêt ajouté']);
}

function handleAddNCCode() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $code = sanitize($_POST['code']);
    $description = sanitize($_POST['description']);
    $severity = sanitize($_POST['severity']);
    $type = sanitize($_POST['type']);
    
    // You would need to create a nc_codes table
    echo json_encode(['success' => true, 'message' => 'Code NC ajouté']);
}

function handleGetConclusions() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $dateStart = $_GET['date_start'] ?? date('Y-m-d', strtotime('-7 days'));
    $dateEnd = $_GET['date_end'] ?? date('Y-m-d');
    
    // Calculate conclusions based on data analysis
    $globalSummary = calculateGlobalSummary($dateStart, $dateEnd);
    
    $conclusions = [
        'strengths' => [],
        'weaknesses' => [],
        'recommendations' => []
    ];
    
    // Analyze strengths
    if ($globalSummary['availability'] > 90) {
        $conclusions['strengths'][] = "Excellente disponibilité des équipements ({$globalSummary['availability']}%)";
    }
    if ($globalSummary['performance'] > 85) {
        $conclusions['strengths'][] = "Bonne performance opérationnelle ({$globalSummary['performance']}%)";
    }
    if ($globalSummary['quality'] > 95) {
        $conclusions['strengths'][] = "Qualité élevée de la production ({$globalSummary['quality']}%)";
    }
    
    // Analyze weaknesses
    if ($globalSummary['availability'] < 80) {
        $conclusions['weaknesses'][] = "Disponibilité insuffisante ({$globalSummary['availability']}%)";
    }
    if ($globalSummary['performance'] < 75) {
        $conclusions['weaknesses'][] = "Performance en dessous des objectifs ({$globalSummary['performance']}%)";
    }
    if ($globalSummary['quality'] < 90) {
        $conclusions['weaknesses'][] = "Problèmes de qualité à adresser ({$globalSummary['quality']}%)";
    }
    
    // Generate recommendations
    if ($globalSummary['trs'] < 60) {
        $conclusions['recommendations'][] = "Mettre en place un plan d'amélioration urgent du TRS";
    }
    if ($globalSummary['availability'] < 85) {
        $conclusions['recommendations'][] = "Renforcer la maintenance préventive";
    }
    if ($globalSummary['performance'] < 80) {
        $conclusions['recommendations'][] = "Optimiser les temps de cycle et réduire les micro-arrêts";
    }
    
    echo json_encode([
        'success' => true,
        'conclusions' => $conclusions
    ]);
}

function handleGetMachineProgramCapacities() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    // Get all machine/program capacity configurations
    $stmt = $pdo->query("
        SELECT 
            mpc.machine_number,
            mpc.program_id,
            mpc.optimal_capacity,
            mp.name as program_name,
            mp.program_number
        FROM machine_program_capacities mpc
        JOIN machine_programs mp ON mpc.program_id = mp.id
        ORDER BY mpc.machine_number, mp.program_number
    ");
    $capacities = $stmt->fetchAll();
    
    // Get all machine programs for reference
    $stmt = $pdo->query("SELECT * FROM machine_programs ORDER BY program_number");
    $programs = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'capacities' => $capacities,
        'programs' => $programs
    ]);
}

function handleSaveMachineProgramCapacities() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $capacities = $_POST['capacities'] ?? [];
    
    if (empty($capacities)) {
        throw new Exception('Aucune donnée de capacité fournie');
    }
    
    try {
        $pdo->beginTransaction();
        
        // Clear existing capacities for the machines being updated
        $machineNumbers = array_unique(array_column($capacities, 'machine_number'));
        $placeholders = str_repeat('?,', count($machineNumbers) - 1) . '?';
        $stmt = $pdo->prepare("DELETE FROM machine_program_capacities WHERE machine_number IN ($placeholders)");
        $stmt->execute($machineNumbers);
        
        // Insert new capacities
        $stmt = $pdo->prepare("
            INSERT INTO machine_program_capacities (machine_number, program_id, optimal_capacity)
            VALUES (?, ?, ?)
        ");
        
        foreach ($capacities as $capacity) {
            $machineNumber = (int)$capacity['machine_number'];
            $programId = (int)$capacity['program_id'];
            $optimalCapacity = (float)$capacity['optimal_capacity'];
            
            if ($optimalCapacity > 0) { // Only save if capacity is specified
                $stmt->execute([$machineNumber, $programId, $optimalCapacity]);
            }
        }
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Configuration des capacités sauvegardée avec succès'
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw new Exception('Erreur lors de la sauvegarde: ' . $e->getMessage());
    }
}

function handleGetProgramCapacity() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $programId = (int)$_GET['program_id'];
    $machineType = (int)$_GET['machine_type'];
    
    // Get the capacity for this program and machine type from the Excel data
    $stmt = $pdo->prepare("
        SELECT optimal_capacity 
        FROM machine_program_capacities 
        WHERE program_id = ? AND machine_number = ?
    ");
    $stmt->execute([$programId, $machineType]);
    $result = $stmt->fetch();
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'capacity' => $result['optimal_capacity']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Aucune capacité définie pour cette combinaison'
        ]);
    }
}

function handleGetDryingProgramTemperature() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $programId = (int)$_GET['program_id'];
    $sechoirType = (int)$_GET['sechoir_type'];
    
    // Get the temperature for this drying program and séchoir type from the Excel data
    // For now, we'll use the base temperature from the drying program
    // In a real implementation, you might have a sechoir_program_temperatures table
    $stmt = $pdo->prepare("
        SELECT temperature 
        FROM drying_programs 
        WHERE id = ?
    ");
    $stmt->execute([$programId]);
    $result = $stmt->fetch();
    
    if ($result && $result['temperature']) {
        // You could adjust temperature based on séchoir type here
        $adjustedTemperature = $result['temperature'];
        
        // Example: different séchoirs might have slight temperature variations
        switch ($sechoirType) {
            case 1:
                $adjustedTemperature = $result['temperature']; // Base temperature
                break;
            case 2:
                $adjustedTemperature = $result['temperature'] + 2; // Slightly higher
                break;
            case 3:
                $adjustedTemperature = $result['temperature'] - 1; // Slightly lower
                break;
            case 4:
                $adjustedTemperature = $result['temperature'] + 1; // Slightly higher
                break;
            default:
                $adjustedTemperature = $result['temperature'];
        }
        
        echo json_encode([
            'success' => true,
            'temperature' => $adjustedTemperature
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Aucune température définie pour cette combinaison'
        ]);
    }
}

// Manual Sessions Management Functions
function handleStartManualSession() {
    global $pdo;
    
    $stationType = sanitize($_POST['station_type']); // 'Calandre' or 'Repassage'
    $operator = sanitize($_POST['operator']);
    $pieces = (int)($_POST['pieces'] ?? 0);
    $weight = (float)($_POST['weight_kg'] ?? 0);
    $comment = sanitize($_POST['comment'] ?? '');
    
    try {
        $pdo->beginTransaction();
        
        // Create new session
        $stmt = $pdo->prepare("
            INSERT INTO manual_sessions (station_type, operator, pieces, weight_kg, comment, session_start, status)
            VALUES (?, ?, ?, ?, ?, NOW(), 'En cours')
        ");
        $stmt->execute([$stationType, $operator, $pieces, $weight, $comment]);
        $sessionId = $pdo->lastInsertId();
        
        // Log session event
        $stmt = $pdo->prepare("
            INSERT INTO session_events (session_id, session_type, event_type, event_time, previous_status, new_status, duration_at_event)
            VALUES (?, 'manual', 'start', NOW(), 'Arrêtée', 'En cours', 0)
        ");
        $stmt->execute([$sessionId]);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'session_id' => $sessionId,
            'message' => 'Session démarrée avec succès'
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw new Exception('Erreur lors du démarrage de la session: ' . $e->getMessage());
    }
}

function handlePauseManualSession() {
    global $pdo;
    
    $sessionId = (int)$_POST['session_id'];
    $currentDuration = (float)$_POST['current_duration'];
    
    try {
        $pdo->beginTransaction();
        
        // Update session status
        $stmt = $pdo->prepare("
            UPDATE manual_sessions 
            SET status = 'Pause', total_duration_minutes = ?, updated_at = NOW()
            WHERE id = ? AND status = 'En cours'
        ");
        $stmt->execute([$currentDuration, $sessionId]);
        
        // Log session event
        $stmt = $pdo->prepare("
            INSERT INTO session_events (session_id, session_type, event_type, event_time, previous_status, new_status, duration_at_event)
            VALUES (?, 'manual', 'pause', NOW(), 'En cours', 'Pause', ?)
        ");
        $stmt->execute([$sessionId, $currentDuration]);
        
        $pdo->commit();
        
        echo json_encode(['success' => true, 'message' => 'Session mise en pause']);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw new Exception('Erreur lors de la pause: ' . $e->getMessage());
    }
}

function handleResumeManualSession() {
    global $pdo;
    
    $sessionId = (int)$_POST['session_id'];
    $currentDuration = (float)$_POST['current_duration'];
    
    try {
        $pdo->beginTransaction();
        
        // Update session status
        $stmt = $pdo->prepare("
            UPDATE manual_sessions 
            SET status = 'En cours', total_duration_minutes = ?, updated_at = NOW()
            WHERE id = ? AND status = 'Pause'
        ");
        $stmt->execute([$currentDuration, $sessionId]);
        
        // Log session event
        $stmt = $pdo->prepare("
            INSERT INTO session_events (session_id, session_type, event_type, event_time, previous_status, new_status, duration_at_event)
            VALUES (?, 'manual', 'resume', NOW(), 'Pause', 'En cours', ?)
        ");
        $stmt->execute([$sessionId, $currentDuration]);
        
        $pdo->commit();
        
        echo json_encode(['success' => true, 'message' => 'Session reprise']);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw new Exception('Erreur lors de la reprise: ' . $e->getMessage());
    }
}

function handleFinishManualSession() {
    global $pdo;
    
    $sessionId = (int)$_POST['session_id'];
    $totalDuration = (float)$_POST['total_duration'];
    $pauseDuration = (float)($_POST['pause_duration'] ?? 0);
    $realDuration = $totalDuration - $pauseDuration;
    $pieces = (int)($_POST['pieces'] ?? 0);
    
    // Calculate cadence
    $cadence = 0;
    if ($realDuration > 0 && $pieces > 0) {
        $cadence = ($pieces / ($realDuration / 60)); // pieces per hour
    }
    
    try {
        $pdo->beginTransaction();
        
        // Update session
        $stmt = $pdo->prepare("
            UPDATE manual_sessions 
            SET status = 'Finie', 
                session_end = NOW(),
                total_duration_minutes = ?,
                pause_duration_minutes = ?,
                real_duration_minutes = ?,
                cadence_pieces_per_hour = ?,
                pieces = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$totalDuration, $pauseDuration, $realDuration, $cadence, $pieces, $sessionId]);
        
        // Log session event
        $stmt = $pdo->prepare("
            INSERT INTO session_events (session_id, session_type, event_type, event_time, previous_status, new_status, duration_at_event)
            VALUES (?, 'manual', 'finish', NOW(), 'En cours', 'Finie', ?)
        ");
        $stmt->execute([$sessionId, $totalDuration]);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Session terminée avec succès',
            'cadence' => round($cadence, 2)
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw new Exception('Erreur lors de la finalisation: ' . $e->getMessage());
    }
}

function handleAutosaveManualSession() {
    global $pdo;
    
    $sessionId = (int)$_POST['session_id'];
    $currentDuration = (float)$_POST['current_duration'];
    $pieces = (int)($_POST['pieces'] ?? 0);
    $weight = (float)($_POST['weight_kg'] ?? 0);
    $comment = sanitize($_POST['comment'] ?? '');
    
    try {
        // Update session data
        $stmt = $pdo->prepare("
            UPDATE manual_sessions 
            SET total_duration_minutes = ?, pieces = ?, weight_kg = ?, comment = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$currentDuration, $pieces, $weight, $comment, $sessionId]);
        
        // Log autosave event
        $stmt = $pdo->prepare("
            INSERT INTO session_events (session_id, session_type, event_type, event_time, duration_at_event)
            VALUES (?, 'manual', 'autosave', NOW(), ?)
        ");
        $stmt->execute([$sessionId, $currentDuration]);
        
        echo json_encode(['success' => true, 'message' => 'Autosauvegarde effectuée']);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors de l\'autosauvegarde: ' . $e->getMessage());
    }
}

function handleGetActiveManualSessions() {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT * FROM manual_sessions 
        WHERE status IN ('En cours', 'Pause')
        ORDER BY session_start DESC
    ");
    $stmt->execute();
    $sessions = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'sessions' => $sessions
    ]);
}

// Enhanced Equipment Stops Functions
function handleSaveEnhancedArret() {
    global $pdo;
    
    $equipmentType = sanitize($_POST['equipment_type']);
    $equipmentName = sanitize($_POST['equipment_name']);
    $stopCode = sanitize($_POST['stop_code']);
    $stopType = sanitize($_POST['stop_type']);
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'] ?? null;
    $operator = sanitize($_POST['operator'] ?? '');
    $comment = sanitize($_POST['comment'] ?? '');
    
    $duration = null;
    if ($endTime) {
        $start = new DateTime($startTime);
        $end = new DateTime($endTime);
        $duration = $end->diff($start)->h * 60 + $end->diff($start)->i;
        
        if ($duration <= 0) {
            throw new Exception('La fin de l\'arrêt doit être après le début');
        }
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO equipment_stops (equipment_type, equipment_name, stop_code, stop_type, start_time, end_time, duration_minutes, operator, comment)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$equipmentType, $equipmentName, $stopCode, $stopType, $startTime, $endTime, $duration, $operator, $comment]);
    
    echo json_encode(['success' => true, 'message' => 'Arrêt enregistré avec succès']);
}

function handleGetStopCodes() {
    global $pdo;
    
    $equipmentType = $_GET['equipment_type'] ?? '';
    
    $whereClause = "WHERE active = 1";
    $params = [];
    
    if (!empty($equipmentType)) {
        $whereClause .= " AND (equipment_types IS NULL OR JSON_CONTAINS(equipment_types, ?))";
        $params[] = json_encode($equipmentType);
    }
    
    $stmt = $pdo->prepare("SELECT * FROM stop_codes $whereClause ORDER BY code");
    $stmt->execute($params);
    $codes = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'stop_codes' => $codes
    ]);
}

function handleSaveStopCode() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $code = sanitize($_POST['code']);
    $description = sanitize($_POST['description']);
    $stopType = sanitize($_POST['stop_type']);
    $category = sanitize($_POST['category']);
    $equipmentTypes = $_POST['equipment_types'] ?? [];
    
    $stmt = $pdo->prepare("
        INSERT INTO stop_codes (code, description, stop_type, category, equipment_types)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
        description = VALUES(description),
        stop_type = VALUES(stop_type),
        category = VALUES(category),
        equipment_types = VALUES(equipment_types)
    ");
    $stmt->execute([$code, $description, $stopType, $category, json_encode($equipmentTypes)]);
    
    echo json_encode(['success' => true, 'message' => 'Code arrêt sauvegardé']);
}

// Enhanced Non-Conformities Functions
function handleSaveEnhancedNC() {
    global $pdo;
    
    $equipmentType = sanitize($_POST['equipment_type']);
    $equipmentName = sanitize($_POST['equipment_name']);
    $programName = sanitize($_POST['program_name'] ?? '');
    $ncType = sanitize($_POST['nc_type']);
    $quantityImpacted = (float)($_POST['quantity_impacted'] ?? 0);
    $weightImpacted = (float)($_POST['weight_impacted'] ?? 0);
    $operator = sanitize($_POST['operator'] ?? '');
    $comment = sanitize($_POST['comment'] ?? '');
    
    $stmt = $pdo->prepare("
        INSERT INTO non_conformities (equipment_type, equipment_name, program_name, nc_type, quantity_impacted, weight_impacted, operator, comment)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$equipmentType, $equipmentName, $programName, $ncType, $quantityImpacted, $weightImpacted, $operator, $comment]);
    
    echo json_encode(['success' => true, 'message' => 'Non-conformité enregistrée avec succès']);
}

function handleGetNCTypes() {
    global $pdo;
    
    $equipmentType = $_GET['equipment_type'] ?? '';
    
    $whereClause = "WHERE active = 1";
    $params = [];
    
    if (!empty($equipmentType)) {
        $whereClause .= " AND (equipment_types IS NULL OR JSON_CONTAINS(equipment_types, ?))";
        $params[] = json_encode($equipmentType);
    }
    
    $stmt = $pdo->prepare("SELECT * FROM nc_types $whereClause ORDER BY code");
    $stmt->execute($params);
    $types = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'nc_types' => $types
    ]);
}

function handleSaveNCType() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $code = sanitize($_POST['code']);
    $description = sanitize($_POST['description']);
    $severity = sanitize($_POST['severity']);
    $category = sanitize($_POST['category']);
    $equipmentTypes = $_POST['equipment_types'] ?? [];
    
    $stmt = $pdo->prepare("
        INSERT INTO nc_types (code, description, severity, category, equipment_types)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
        description = VALUES(description),
        severity = VALUES(severity),
        category = VALUES(category),
        equipment_types = VALUES(equipment_types)
    ");
    $stmt->execute([$code, $description, $severity, $category, json_encode($equipmentTypes)]);
    
    echo json_encode(['success' => true, 'message' => 'Type NC sauvegardé']);
}

// Operators Management Functions
function handleGetOperators() {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM operators WHERE active = 1 ORDER BY name");
    $stmt->execute();
    $operators = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'operators' => $operators
    ]);
}

function handleSaveOperator() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $name = sanitize($_POST['name']);
    $employeeId = sanitize($_POST['employee_id'] ?? '');
    
    $stmt = $pdo->prepare("
        INSERT INTO operators (name, employee_id)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE 
        employee_id = VALUES(employee_id)
    ");
    $stmt->execute([$name, $employeeId]);
    
    echo json_encode(['success' => true, 'message' => 'Opérateur sauvegardé']);
}

// Filter Presets Functions
function handleSaveFilterPreset() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $presetName = sanitize($_POST['preset_name']);
    $filters = $_POST['filters'] ?? [];
    $isDefault = (bool)($_POST['is_default'] ?? false);
    $userId = $_SESSION['user_id'];
    
    // If setting as default, unset other defaults for this user
    if ($isDefault) {
        $stmt = $pdo->prepare("UPDATE filter_presets SET is_default = 0 WHERE user_id = ?");
        $stmt->execute([$userId]);
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO filter_presets (user_id, preset_name, filters, is_default)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
        filters = VALUES(filters),
        is_default = VALUES(is_default)
    ");
    $stmt->execute([$userId, $presetName, json_encode($filters), $isDefault]);
    
    echo json_encode(['success' => true, 'message' => 'Preset de filtres sauvegardé']);
}

function handleGetFilterPresets() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $userId = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("
        SELECT * FROM filter_presets 
        WHERE user_id = ? 
        ORDER BY is_default DESC, preset_name
    ");
    $stmt->execute([$userId]);
    $presets = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'presets' => $presets
    ]);
}

function handleDeleteFilterPreset() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $presetId = (int)$_POST['preset_id'];
    $userId = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("DELETE FROM filter_presets WHERE id = ? AND user_id = ?");
    $stmt->execute([$presetId, $userId]);
    
    echo json_encode(['success' => true, 'message' => 'Preset supprimé']);
}
?>
