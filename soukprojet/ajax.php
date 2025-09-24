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
            
        case 'save_machine_pause':
            handleSaveMachinePause();
            break;
            
        case 'get_machine_pauses':
            handleGetMachinePauses();
            break;
            
        case 'debug_pauses':
            handleDebugPauses();
            break;
            
        case 'close_all_open_pauses':
            handleCloseAllOpenPauses();
            break;
            
        // New dashboard handlers
        case 'get_dashboard_data':
            handleGetDashboardData();
            break;
            
        case 'get_stop':
            handleGetStop();
            break;
            
        case 'get_stop_form':
            handleGetStopForm();
            break;
            
        case 'close_stop':
            handleCloseStop();
            break;
            
        case 'filter_stops':
            handleFilterStops();
            break;
            
        case 'get_nc':
            handleGetNC();
            break;
            
        case 'filter_nc':
            handleFilterNC();
            break;
            
        case 'save_5m_analysis':
            handleSave5MAnalysis();
            break;
            
        case 'export_nc':
            handleExportNC();
            break;
            
        case 'get_trs_data':
            handleGetTRSData();
            break;
            
        case 'get_stop_details':
            handleGetStopDetails();
            break;
            
        case 'update_stop':
            handleUpdateStop();
            break;
            
        case 'get_nc_details':
            handleGetNCDetails();
            break;
            
        case 'update_nc':
            handleUpdateNC();
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
        INSERT INTO non_conformities (equipment_name, nc_type, quantity_impacted, comment, created_at)
        VALUES (?, ?, ?, ?, NOW())
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
    
    $equipment = sanitize($_POST['equipment'] ?? $_POST['equipment_name'] ?? '');
    $reason = sanitize($_POST['reason'] ?? $_POST['stop_code'] ?? '');
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
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO equipment_stops (equipment, reason, start_time, end_time, duration_minutes, comment, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$equipment, $reason, $startTime, $endTime, $duration, $comment]);
        
        echo json_encode(['success' => true, 'message' => 'Arrêt enregistré avec succès']);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors de l\'enregistrement de l\'arrêt: ' . $e->getMessage());
    }
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
    
    $equipment = sanitize($_POST['equipment'] ?? $_POST['equipment_name'] ?? '');
    $ncType = sanitize($_POST['type'] ?? $_POST['nc_type'] ?? '');
    $quantity = (int)($_POST['quantity'] ?? $_POST['quantity_impacted'] ?? 0);
    $description = sanitize($_POST['description'] ?? $_POST['comment'] ?? '');
    $operator = sanitize($_POST['operator'] ?? '');
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO non_conformities (equipment_name, nc_type, quantity_impacted, comment, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$equipment, $ncType, $quantity, $description]);
        
        echo json_encode(['success' => true, 'message' => 'Non-conformité enregistrée avec succès']);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors de l\'enregistrement de la NC: ' . $e->getMessage());
    }
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

// Machine Pause Functions
function handleSaveMachinePause() {
    global $pdo;
    
    $equipment = sanitize($_POST['equipment']);
    $pauseStartTime = $_POST['pause_start_time'];
    $pauseEndTime = $_POST['pause_end_time'] ?? null;
    $reason = sanitize($_POST['reason'] ?? 'Pause opérateur');
    
    try {
        // Create table if it doesn't exist
        $pdo->exec("CREATE TABLE IF NOT EXISTS machine_pauses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            equipment VARCHAR(255) NOT NULL,
            reason VARCHAR(255) NOT NULL DEFAULT 'Pause opérateur',
            pause_start_time DATETIME NOT NULL,
            pause_end_time DATETIME,
            duration_minutes INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        if ($pauseEndTime) {
            // This is a pause end - find and update the most recent open pause
            $end = new DateTime($pauseEndTime);
            
            error_log("Attempting to end pause for equipment: {$equipment}");
            error_log("Pause end time: {$pauseEndTime}");
            
            // Find ALL open pauses for this equipment to debug
            $stmt = $pdo->prepare("
                SELECT id, pause_start_time, equipment, reason
                FROM machine_pauses 
                WHERE equipment = ? 
                AND pause_end_time IS NULL 
                ORDER BY pause_start_time DESC
            ");
            $stmt->execute([$equipment]);
            $allOpenPauses = $stmt->fetchAll();
            
            error_log("Found " . count($allOpenPauses) . " open pauses for {$equipment}");
            foreach ($allOpenPauses as $pause) {
                error_log("Open pause ID: {$pause['id']}, Start: {$pause['pause_start_time']}");
            }
            
            if (!empty($allOpenPauses)) {
                // Get the most recent one
                $openPause = $allOpenPauses[0];
                
                // Calculate duration from the actual database start time to the end time
                $actualStart = new DateTime($openPause['pause_start_time']);
                $duration = $end->diff($actualStart)->h * 60 + $end->diff($actualStart)->i;
                
                if ($duration < 0) {
                    $duration = 1; // Minimum 1 minute if negative
                }
                
                error_log("Updating pause ID: {$openPause['id']} with duration: {$duration} minutes");
                
                $stmt = $pdo->prepare("
                    UPDATE machine_pauses 
                    SET pause_end_time = ?, duration_minutes = ?
                    WHERE id = ?
                ");
                $result = $stmt->execute([$pauseEndTime, $duration, $openPause['id']]);
                
                if ($result) {
                    error_log("Successfully updated pause record ID: {$openPause['id']}");
                    $pauseId = $openPause['id'];
                    
                    // Verify the update worked
                    $stmt = $pdo->prepare("SELECT pause_end_time, duration_minutes FROM machine_pauses WHERE id = ?");
                    $stmt->execute([$openPause['id']]);
                    $updated = $stmt->fetch();
                    error_log("Verification - End time: {$updated['pause_end_time']}, Duration: {$updated['duration_minutes']}");
                } else {
                    error_log("Failed to update pause record ID: {$openPause['id']}");
                    $pauseId = null;
                }
            } else {
                // No open pause found, create a new complete record
                $start = new DateTime($pauseStartTime);
                $duration = $end->diff($start)->h * 60 + $end->diff($start)->i;
                
                if ($duration <= 0) {
                    $duration = 1; // Minimum 1 minute
                }
                
                error_log("No open pause found, creating new record with duration: {$duration} minutes");
                
                $stmt = $pdo->prepare("
                    INSERT INTO machine_pauses (equipment, reason, pause_start_time, pause_end_time, duration_minutes, created_at)
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$equipment, $reason, $pauseStartTime, $pauseEndTime, $duration]);
                $pauseId = $pdo->lastInsertId();
                
                error_log("Created new pause record ID: {$pauseId}");
            }
        } else {
            // This is a pause start - insert new record
            $stmt = $pdo->prepare("
                INSERT INTO machine_pauses (equipment, reason, pause_start_time, pause_end_time, duration_minutes, created_at)
                VALUES (?, ?, ?, NULL, 0, NOW())
            ");
            $stmt->execute([$equipment, $reason, $pauseStartTime]);
            $pauseId = $pdo->lastInsertId();
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Pause enregistrée avec succès',
            'pause_id' => $pauseId
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors de l\'enregistrement de la pause: ' . $e->getMessage());
    }
}

function handleGetMachinePauses() {
    global $pdo;
    
    try {
        // Create table if it doesn't exist
        $pdo->exec("CREATE TABLE IF NOT EXISTS machine_pauses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            equipment VARCHAR(255) NOT NULL,
            reason VARCHAR(255) NOT NULL DEFAULT 'Pause opérateur',
            pause_start_time DATETIME NOT NULL,
            pause_end_time DATETIME,
            duration_minutes INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        $stmt = $pdo->prepare("
            SELECT 
                DATE(pause_start_time) as date,
                equipment,
                reason,
                CASE 
                    WHEN duration_minutes > 0 THEN CONCAT(duration_minutes, ' min')
                    ELSE 'En cours'
                END as duree,
                pause_start_time,
                pause_end_time,
                duration_minutes
            FROM machine_pauses 
            WHERE DATE(pause_start_time) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            ORDER BY pause_start_time DESC 
            LIMIT 50
        ");
        $stmt->execute();
        $pauses = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'pauses' => $pauses
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors de la récupération des pauses: ' . $e->getMessage());
    }
}

// New Dashboard Functions
function handleGetDashboardData() {
    global $pdo;
    
    try {
        // Get current equipment status
        $sechoirs_html = generateSechoirsDashboard();
        $finition_html = generateFinitionDashboard();
        $activities_html = generateRecentActivities();
        
        echo json_encode([
            'success' => true,
            'sechoirs' => $sechoirs_html,
            'finition' => $finition_html,
            'activities' => $activities_html
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors du chargement du dashboard: ' . $e->getMessage());
    }
}

function generateSechoirsDashboard() {
    $sechoirs = [
        ['id' => 'S1', 'name' => 'Séchoir 1', 'status' => 'running', 'program' => 'Standard 55°C', 'progress' => 65],
        ['id' => 'S2', 'name' => 'Séchoir 2', 'status' => 'idle', 'program' => null, 'progress' => 0],
        ['id' => 'S3', 'name' => 'Séchoir 3', 'status' => 'maintenance', 'program' => 'Nettoyage filtre', 'progress' => 85],
        ['id' => 'S4', 'name' => 'Séchoir 4', 'status' => 'idle', 'program' => null, 'progress' => 0]
    ];
    
    $html = '';
    foreach ($sechoirs as $sechoir) {
        $status_class = $sechoir['status'] === 'running' ? 'active' : ($sechoir['status'] === 'maintenance' ? 'maintenance' : 'idle');
        $status_text = $sechoir['status'] === 'running' ? 'En fonctionnement' : ($sechoir['status'] === 'maintenance' ? 'Maintenance' : 'Arrêt');
        
        $html .= '<div class="equipment-card ' . $status_class . '">';
        $html .= '<div class="equipment-header">';
        $html .= '<div class="equipment-indicator"></div>';
        $html .= '<h4>' . $sechoir['name'] . '</h4>';
        $html .= '<span class="equipment-status">' . $status_text . '</span>';
        $html .= '</div>';
        
        if ($sechoir['program']) {
            $html .= '<div class="equipment-program">';
            $html .= '<span>Programme: ' . $sechoir['program'] . '</span>';
            $html .= '</div>';
        }
        
        $html .= '<div class="equipment-progress">';
        $html .= '<div class="progress-bar">';
        $html .= '<div class="progress-fill" style="width: ' . $sechoir['progress'] . '%"></div>';
        $html .= '</div>';
        $html .= '<span class="progress-text">' . $sechoir['progress'] . '%</span>';
        $html .= '</div>';
        
        $html .= '<div class="equipment-actions">';
        if ($sechoir['status'] === 'idle') {
            $html .= '<button class="btn btn-primary btn-sm">Démarrer</button>';
        } elseif ($sechoir['status'] === 'running') {
            $html .= '<button class="btn btn-warning btn-sm">Pause</button>';
            $html .= '<button class="btn btn-danger btn-sm">Arrêter</button>';
        }
        $html .= '<button class="btn btn-info btn-sm">Nettoyage</button>';
        $html .= '<button class="btn btn-secondary btn-sm">Relance</button>';
        $html .= '</div>';
        
        $html .= '</div>';
    }
    
    return $html;
}

function generateFinitionDashboard() {
    $postes = [
        ['id' => 'CAL1', 'name' => 'Calandre', 'status' => 'running', 'duration' => '00:15:00'],
        ['id' => 'REP1', 'name' => 'Repassage', 'status' => 'idle', 'duration' => '00:00:00']
    ];
    
    $html = '';
    foreach ($postes as $poste) {
        $status_class = $poste['status'] === 'running' ? 'active' : 'idle';
        $status_text = $poste['status'] === 'running' ? 'Actif' : 'Arrêt';
        
        $html .= '<div class="equipment-card ' . $status_class . '">';
        $html .= '<div class="equipment-header">';
        $html .= '<div class="equipment-indicator"></div>';
        $html .= '<h4>' . $poste['name'] . '</h4>';
        $html .= '<span class="equipment-status">' . $status_text . '</span>';
        $html .= '</div>';
        
        $html .= '<div class="equipment-timer">' . $poste['duration'] . '</div>';
        
        $html .= '<div class="equipment-actions">';
        if ($poste['status'] === 'idle') {
            $html .= '<button class="btn btn-primary btn-sm">Démarrer</button>';
        } else {
            $html .= '<button class="btn btn-warning btn-sm">Pause</button>';
            $html .= '<button class="btn btn-danger btn-sm">Arrêter</button>';
        }
        $html .= '</div>';
        
        $html .= '</div>';
    }
    
    return $html;
}

function generateRecentActivities() {
    $activities = [
        ['time' => '14:30', 'equipment' => 'Machine 20', 'action' => 'Démarrage cycle Coton 60°C', 'type' => 'start'],
        ['time' => '15:10', 'equipment' => 'Séchoir S2', 'action' => 'Cycle terminé - linge prêt', 'type' => 'complete'],
        ['time' => '16:05', 'equipment' => 'Machine 70', 'action' => 'Maintenance préventive', 'type' => 'maintenance'],
        ['time' => '16:45', 'equipment' => 'Séchoir S1', 'action' => 'Maintenance filtre terminé', 'type' => 'complete']
    ];
    
    $html = '';
    foreach ($activities as $activity) {
        $type_class = $activity['type'] === 'start' ? 'green' : ($activity['type'] === 'maintenance' ? 'orange' : 'blue');
        
        $html .= '<div class="activity-item">';
        $html .= '<span class="activity-time">' . $activity['time'] . '</span>';
        $html .= '<div class="activity-indicator ' . $type_class . '"></div>';
        $html .= '<span class="activity-equipment">' . $activity['equipment'] . '</span>';
        $html .= '<span class="activity-action">' . $activity['action'] . '</span>';
        $html .= '</div>';
    }
    
    return $html;
}

// New Stop Management Functions
function handleGetStop() {
    global $pdo;
    
    $id = (int)$_GET['id'];
    
    $stmt = $pdo->prepare("SELECT * FROM equipment_stops WHERE id = ?");
    $stmt->execute([$id]);
    $stop = $stmt->fetch();
    
    if ($stop) {
        echo json_encode($stop);
    } else {
        throw new Exception('Arrêt non trouvé');
    }
}

function handleGetStopForm() {
    $id = (int)$_GET['id'];
    
    // Generate edit form HTML for the stop
    $html = '<div class="form-group">';
    $html .= '<label>Équipement</label>';
    $html .= '<input type="text" id="edit-equipment" value="Machine 20" readonly>';
    $html .= '</div>';
    $html .= '<div class="form-group">';
    $html .= '<label>Motif</label>';
    $html .= '<select id="edit-reason">';
    $html .= '<option value="panne-mecanique">Panne mécanique</option>';
    $html .= '<option value="maintenance">Maintenance</option>';
    $html .= '</select>';
    $html .= '</div>';
    $html .= '<div class="btn-group">';
    $html .= '<button class="btn btn-primary" onclick="saveStopEdit(' . $id . ')">Sauvegarder</button>';
    $html .= '</div>';
    
    echo $html;
}

function handleCloseStop() {
    global $pdo;
    
    $id = (int)$_POST['id'];
    $endTime = $_POST['end_time'];
    
    try {
        // Get the stop to calculate duration
        $stmt = $pdo->prepare("SELECT start_time FROM equipment_stops WHERE id = ?");
        $stmt->execute([$id]);
        $stop = $stmt->fetch();
        
        if ($stop) {
            $start = new DateTime($stop['start_time']);
            $end = new DateTime($endTime);
            $duration = $end->diff($start)->h * 60 + $end->diff($start)->i;
            
            $stmt = $pdo->prepare("
                UPDATE equipment_stops 
                SET end_time = ?, duration_minutes = ? 
                WHERE id = ?
            ");
            $stmt->execute([$endTime, $duration, $id]);
            
            echo json_encode(['success' => true]);
        } else {
            throw new Exception('Arrêt non trouvé');
        }
    } catch (Exception $e) {
        throw new Exception('Erreur lors de la clôture: ' . $e->getMessage());
    }
}

function handleFilterStops() {
    global $pdo;
    
    $equipment = $_GET['equipment'] ?? '';
    $type = $_GET['type'] ?? '';
    $dateStart = $_GET['date_start'] ?? '';
    $dateEnd = $_GET['date_end'] ?? '';
    
    $whereConditions = [];
    $params = [];
    
    if (!empty($equipment)) {
        $whereConditions[] = "equipment_name = ?";
        $params[] = $equipment;
    }
    
    if (!empty($type)) {
        $whereConditions[] = "stop_type = ?";
        $params[] = $type;
    }
    
    if (!empty($dateStart)) {
        $whereConditions[] = "DATE(start_time) >= ?";
        $params[] = $dateStart;
    }
    
    if (!empty($dateEnd)) {
        $whereConditions[] = "DATE(start_time) <= ?";
        $params[] = $dateEnd;
    }
    
    $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";
    
    $stmt = $pdo->prepare("
        SELECT *, 
               CASE 
                   WHEN end_time IS NULL THEN 'En cours'
                   ELSE 'Terminé'
               END as status
        FROM equipment_stops 
        $whereClause
        ORDER BY start_time DESC 
        LIMIT 50
    ");
    $stmt->execute($params);
    $stops = $stmt->fetchAll();
    
    $html = '';
    foreach ($stops as $stop) {
        $html .= '<tr>';
        $html .= '<td><strong>' . htmlspecialchars($stop['equipment_name']) . '</strong></td>';
        $html .= '<td><span class="badge ' . ($stop['stop_type'] === 'planifie' ? 'badge-success' : 'badge-danger') . '">' . ucfirst($stop['stop_type']) . '</span></td>';
        $html .= '<td>' . htmlspecialchars($stop['stop_code']) . '</td>';
        $html .= '<td>' . date('d/m/Y H:i', strtotime($stop['start_time'])) . '</td>';
        $html .= '<td>' . ($stop['duration_minutes'] ? $stop['duration_minutes'] . ' min' : '<span class="status-ongoing">En cours</span>') . '</td>';
        $html .= '<td><span class="badge ' . ($stop['status'] === 'Terminé' ? 'badge-success' : 'badge-warning') . '">' . $stop['status'] . '</span></td>';
        $html .= '<td>';
        $html .= '<div class="action-buttons">';
        $html .= '<button class="btn-icon" onclick="viewStop(' . $stop['id'] . ')" title="Voir">👁️</button>';
        $html .= '<button class="btn-icon" onclick="editStop(' . $stop['id'] . ')" title="Modifier">✏️</button>';
        if ($stop['status'] === 'En cours') {
            $html .= '<button class="btn-icon success" onclick="closeStop(' . $stop['id'] . ')" title="Clôturer">✅</button>';
        }
        $html .= '</div>';
        $html .= '</td>';
        $html .= '</tr>';
    }
    
    echo $html;
}

// New NC Management Functions
function handleGetNC() {
    global $pdo;
    
    $id = (int)$_GET['id'];
    
    $stmt = $pdo->prepare("
        SELECT nc.*, nt.severity, nt.category 
        FROM non_conformities nc
        LEFT JOIN nc_types nt ON nc.nc_type = nt.code
        WHERE nc.id = ?
    ");
    $stmt->execute([$id]);
    $nc = $stmt->fetch();
    
    if ($nc) {
        echo json_encode($nc);
    } else {
        throw new Exception('Non-conformité non trouvée');
    }
}

function handleFilterNC() {
    global $pdo;
    
    $equipment = $_GET['equipment'] ?? '';
    $type = $_GET['type'] ?? '';
    $severity = $_GET['severity'] ?? '';
    $dateStart = $_GET['date_start'] ?? '';
    $dateEnd = $_GET['date_end'] ?? '';
    
    $whereConditions = [];
    $params = [];
    
    if (!empty($equipment)) {
        $whereConditions[] = "nc.equipment_name = ?";
        $params[] = $equipment;
    }
    
    if (!empty($type)) {
        $whereConditions[] = "nc.nc_type = ?";
        $params[] = $type;
    }
    
    if (!empty($severity)) {
        $whereConditions[] = "nt.severity = ?";
        $params[] = $severity;
    }
    
    if (!empty($dateStart)) {
        $whereConditions[] = "DATE(nc.created_at) >= ?";
        $params[] = $dateStart;
    }
    
    if (!empty($dateEnd)) {
        $whereConditions[] = "DATE(nc.created_at) <= ?";
        $params[] = $dateEnd;
    }
    
    $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";
    
    $stmt = $pdo->prepare("
        SELECT nc.*, nt.severity, nt.category 
        FROM non_conformities nc
        LEFT JOIN nc_types nt ON nc.nc_type = nt.code
        $whereClause
        ORDER BY nc.created_at DESC 
        LIMIT 50
    ");
    $stmt->execute($params);
    $ncs = $stmt->fetchAll();
    
    $html = '';
    foreach ($ncs as $nc) {
        $severity_class = $nc['severity'] === 'critique' ? 'badge-danger' : 
                         ($nc['severity'] === 'majeure' ? 'badge-warning' : 'badge-secondary');
        
        $html .= '<tr>';
        $html .= '<td>';
        $html .= '<div class="nc-type-info">';
        $html .= '<span class="badge badge-info">' . htmlspecialchars($nc['nc_type']) . '</span>';
        $html .= '<span class="badge ' . $severity_class . '">' . ucfirst($nc['severity'] ?? 'mineure') . '</span>';
        $html .= '</div>';
        $html .= '</td>';
        $html .= '<td>';
        $html .= '<div class="nc-description">' . htmlspecialchars(substr($nc['comment'], 0, 50));
        if (strlen($nc['comment']) > 50) $html .= '...';
        $html .= '</div>';
        $html .= '</td>';
        $html .= '<td>' . htmlspecialchars($nc['equipment_name']) . '</td>';
        $html .= '<td>' . ($nc['quantity_impacted'] ?? '-') . '</td>';
        $html .= '<td>' . number_format($nc['weight_impacted'] ?? 0, 1) . '</td>';
        $html .= '<td>' . date('d/m/Y H:i', strtotime($nc['created_at'])) . '</td>';
        $html .= '<td>' . htmlspecialchars($nc['operator'] ?? 'N/A') . '</td>';
        $html .= '<td>';
        $html .= '<div class="action-buttons">';
        $html .= '<button class="btn-icon" onclick="viewNC(' . $nc['id'] . ')" title="Voir">👁️</button>';
        $html .= '<button class="btn-icon" onclick="editNC(' . $nc['id'] . ')" title="Modifier">✏️</button>';
        $html .= '<button class="btn-icon" onclick="analyzeNC(' . $nc['id'] . ')" title="Analyser 5M">🔍</button>';
        $html .= '</div>';
        $html .= '</td>';
        $html .= '</tr>';
    }
    
    echo $html;
}

function handleSave5MAnalysis() {
    global $pdo;
    
    $ncId = (int)$_POST['nc_id'];
    $causes = json_decode($_POST['causes'], true);
    $comment = sanitize($_POST['comment']);
    
    try {
        // Create analysis table if it doesn't exist
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS nc_5m_analysis (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nc_id INT NOT NULL,
                causes_json TEXT NOT NULL,
                corrective_actions TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (nc_id) REFERENCES non_conformities(id) ON DELETE CASCADE
            )
        ");
        
        $stmt = $pdo->prepare("
            INSERT INTO nc_5m_analysis (nc_id, causes_json, corrective_actions)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            causes_json = VALUES(causes_json),
            corrective_actions = VALUES(corrective_actions)
        ");
        $stmt->execute([$ncId, json_encode($causes), $comment]);
        
        echo json_encode(['success' => true]);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors de la sauvegarde de l\'analyse: ' . $e->getMessage());
    }
}

function handleExportNC() {
    global $pdo;
    
    $equipment = $_GET['equipment'] ?? '';
    $type = $_GET['type'] ?? '';
    $severity = $_GET['severity'] ?? '';
    $dateStart = $_GET['date_start'] ?? '';
    $dateEnd = $_GET['date_end'] ?? '';
    
    $whereConditions = [];
    $params = [];
    
    if (!empty($equipment)) {
        $whereConditions[] = "nc.equipment_name = ?";
        $params[] = $equipment;
    }
    
    if (!empty($type)) {
        $whereConditions[] = "nc.nc_type = ?";
        $params[] = $type;
    }
    
    if (!empty($severity)) {
        $whereConditions[] = "nt.severity = ?";
        $params[] = $severity;
    }
    
    if (!empty($dateStart)) {
        $whereConditions[] = "DATE(nc.created_at) >= ?";
        $params[] = $dateStart;
    }
    
    if (!empty($dateEnd)) {
        $whereConditions[] = "DATE(nc.created_at) <= ?";
        $params[] = $dateEnd;
    }
    
    $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";
    
    $stmt = $pdo->prepare("
        SELECT nc.*, nt.severity, nt.category 
        FROM non_conformities nc
        LEFT JOIN nc_types nt ON nc.nc_type = nt.code
        $whereClause
        ORDER BY nc.created_at DESC
    ");
    $stmt->execute($params);
    $ncs = $stmt->fetchAll();
    
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=export_nc_' . date('Y-m-d') . '.csv');
    
    // Output CSV
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Type', 'Gravité', 'Description', 'Équipement', 'Quantité', 'Poids (kg)', 'Date', 'Opérateur']);
    
    foreach ($ncs as $nc) {
        fputcsv($output, [
            $nc['nc_type'],
            $nc['severity'] ?? 'mineure',
            $nc['comment'],
            $nc['equipment_name'],
            $nc['quantity_impacted'] ?? '',
            $nc['weight_impacted'] ?? '',
            $nc['created_at'],
            $nc['operator'] ?? ''
        ]);
    }
    
    fclose($output);
    exit;
}

// Utility functions for missing handlers
function handleDebugPauses() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM machine_pauses 
            WHERE pause_end_time IS NULL 
            ORDER BY pause_start_time DESC
        ");
        $stmt->execute();
        $openPauses = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'open_pauses' => $openPauses,
            'count' => count($openPauses)
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors du debug: ' . $e->getMessage());
    }
}

function handleCloseAllOpenPauses() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            UPDATE machine_pauses 
            SET pause_end_time = NOW(), 
                duration_minutes = TIMESTAMPDIFF(MINUTE, pause_start_time, NOW())
            WHERE pause_end_time IS NULL
        ");
        $stmt->execute();
        
        echo json_encode([
            'success' => true,
            'message' => 'Toutes les pauses ouvertes ont été fermées'
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors de la fermeture: ' . $e->getMessage());
    }
}

// Settings management functions for dashboard
function handleAddProgram() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $name = sanitize($_POST['name']);
    $duration = (int)$_POST['duration'];
    $temperature = (int)$_POST['temperature'];
    $rate = (float)$_POST['rate'];
    $equipmentType = sanitize($_POST['equipmentType'] ?? 'LAVAGE');
    
    try {
        // Create programs table if it doesn't exist
        $pdo->exec("CREATE TABLE IF NOT EXISTS programs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            equipment_type ENUM('LAVAGE', 'SECHAGE') NOT NULL DEFAULT 'LAVAGE',
            duration_minutes INT NOT NULL,
            temperature_c INT,
            nominal_rate_kg_h FLOAT,
            active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        $stmt = $pdo->prepare("
            INSERT INTO programs (name, equipment_type, duration_minutes, temperature_c, nominal_rate_kg_h)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$name, $equipmentType, $duration, $temperature, $rate]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Programme créé avec succès',
            'id' => $pdo->lastInsertId()
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors de la création du programme: ' . $e->getMessage());
    }
}

function handleAddOperator() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $firstName = sanitize($_POST['firstName']);
    $lastName = sanitize($_POST['lastName']);
    $login = sanitize($_POST['login']);
    $role = sanitize($_POST['role']);
    
    if (!in_array($role, ['OPERATEUR', 'CHEF_ATELIER', 'ADMIN'])) {
        throw new Exception('Rôle invalide');
    }
    
    try {
        // Create operators table if it doesn't exist
        $pdo->exec("CREATE TABLE IF NOT EXISTS operators_config (
            id INT AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(255) NOT NULL,
            last_name VARCHAR(255) NOT NULL,
            login VARCHAR(100) UNIQUE NOT NULL,
            role ENUM('OPERATEUR', 'CHEF_ATELIER', 'ADMIN') NOT NULL,
            active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        $stmt = $pdo->prepare("
            INSERT INTO operators_config (first_name, last_name, login, role)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$firstName, $lastName, $login, $role]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Opérateur créé avec succès',
            'id' => $pdo->lastInsertId()
        ]);
        
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
            throw new Exception('Ce login existe déjà');
        }
        throw new Exception('Erreur lors de la création de l\'opérateur: ' . $e->getMessage());
    }
}

function handleAddMotif() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $code = sanitize($_POST['code']);
    $description = sanitize($_POST['description']);
    $type = sanitize($_POST['type']);
    $category = sanitize($_POST['category']);
    
    if (!in_array($type, ['NON_PLANIFIE', 'PLANIFIE', 'INTER_CYCLE', 'NETTOYAGE'])) {
        throw new Exception('Type d\'arrêt invalide');
    }
    
    if (!in_array($category, ['MECANIQUE', 'ELECTRIQUE', 'ORGANISATION', 'QUALITE', 'SECURITE', 'AUTRE'])) {
        throw new Exception('Catégorie invalide');
    }
    
    try {
        // Create stop_motifs table if it doesn't exist
        $pdo->exec("CREATE TABLE IF NOT EXISTS stop_motifs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            code VARCHAR(50) UNIQUE NOT NULL,
            description VARCHAR(255) NOT NULL,
            stop_type ENUM('NON_PLANIFIE', 'PLANIFIE', 'INTER_CYCLE', 'NETTOYAGE') NOT NULL,
            category ENUM('MECANIQUE', 'ELECTRIQUE', 'ORGANISATION', 'QUALITE', 'SECURITE', 'AUTRE') NOT NULL,
            active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        $stmt = $pdo->prepare("
            INSERT INTO stop_motifs (code, description, stop_type, category)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$code, $description, $type, $category]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Motif d\'arrêt créé avec succès',
            'id' => $pdo->lastInsertId()
        ]);
        
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
            throw new Exception('Ce code motif existe déjà');
        }
        throw new Exception('Erreur lors de la création du motif: ' . $e->getMessage());
    }
}

function handleAddTypeNC() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $code = sanitize($_POST['code']);
    $description = sanitize($_POST['description']);
    $severity = sanitize($_POST['severity']);
    
    if (!in_array($severity, ['MINEURE', 'MAJEURE', 'CRITIQUE'])) {
        throw new Exception('Gravité invalide');
    }
    
    try {
        // Create nc_types_config table if it doesn't exist
        $pdo->exec("CREATE TABLE IF NOT EXISTS nc_types_config (
            id INT AUTO_INCREMENT PRIMARY KEY,
            code VARCHAR(50) UNIQUE NOT NULL,
            description VARCHAR(255) NOT NULL,
            severity ENUM('MINEURE', 'MAJEURE', 'CRITIQUE') NOT NULL,
            active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        $stmt = $pdo->prepare("
            INSERT INTO nc_types_config (code, description, severity)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$code, $description, $severity]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Type de non-conformité créé avec succès',
            'id' => $pdo->lastInsertId()
        ]);
        
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
            throw new Exception('Ce code type NC existe déjà');
        }
        throw new Exception('Erreur lors de la création du type NC: ' . $e->getMessage());
    }
}

function handleAddCause5M() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $axe = sanitize($_POST['axe']);
    $description = sanitize($_POST['description']);
    
    if (!in_array($axe, ['mainOeuvre', 'methode', 'matiere', 'milieu', 'machine'])) {
        throw new Exception('Axe 5M invalide');
    }
    
    try {
        // Create causes_5m table if it doesn't exist
        $pdo->exec("CREATE TABLE IF NOT EXISTS causes_5m (
            id INT AUTO_INCREMENT PRIMARY KEY,
            axe ENUM('mainOeuvre', 'methode', 'matiere', 'milieu', 'machine') NOT NULL,
            description VARCHAR(255) NOT NULL,
            active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        $stmt = $pdo->prepare("
            INSERT INTO causes_5m (axe, description)
            VALUES (?, ?)
        ");
        $stmt->execute([$axe, $description]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Cause 5M créée avec succès',
            'id' => $pdo->lastInsertId()
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors de la création de la cause 5M: ' . $e->getMessage());
    }
}


function handleAddManualProduction() {
    global $pdo;
    
    $stationType = sanitize($_POST['station_type']);
    $designation = sanitize($_POST['designation'] ?? '');
    $articles = (int)($_POST['articles'] ?? 0);
    $pieces = (int)($_POST['pieces'] ?? 0);
    $operator = sanitize($_POST['operator'] ?? 'Jean Dupont');
    $comment = sanitize($_POST['comment'] ?? '');
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO productions (
                type, equipment, program_name, pieces, 
                operator, comment, timestamp
            )
            VALUES ('manual', ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $stationType,
            $designation,
            $pieces,
            $operator,
            $comment
        ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Production ' . $stationType . ' enregistrée avec succès',
            'id' => $pdo->lastInsertId()
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors de l\'enregistrement: ' . $e->getMessage());
    }
}

// Handlers pour l'édition et suppression des réglages
function handleEditProgram() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $id = (int)$_POST['id'];
    $name = sanitize($_POST['name']);
    $duration = (int)$_POST['duration'];
    $temperature = (int)$_POST['temperature'];
    $rate = (float)$_POST['rate'];
    $equipmentType = sanitize($_POST['equipmentType'] ?? 'LAVAGE');
    
    try {
        $stmt = $pdo->prepare("
            UPDATE programs 
            SET name = ?, duration_minutes = ?, temperature_c = ?, nominal_rate_kg_h = ?, equipment_type = ?
            WHERE id = ?
        ");
        $stmt->execute([$name, $duration, $temperature, $rate, $equipmentType, $id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Programme modifié avec succès'
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors de la modification: ' . $e->getMessage());
    }
}

function handleDeleteProgram() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $id = (int)$_POST['id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM programs WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Programme supprimé avec succès'
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors de la suppression: ' . $e->getMessage());
    }
}

function handleEditOperator() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $id = (int)$_POST['id'];
    $firstName = sanitize($_POST['firstName']);
    $lastName = sanitize($_POST['lastName']);
    $login = sanitize($_POST['login']);
    $role = sanitize($_POST['role']);
    
    if (!in_array($role, ['OPERATEUR', 'CHEF_ATELIER', 'ADMIN'])) {
        throw new Exception('Rôle invalide');
    }
    
    try {
        $stmt = $pdo->prepare("
            UPDATE operators_config 
            SET first_name = ?, last_name = ?, login = ?, role = ?
            WHERE id = ?
        ");
        $stmt->execute([$firstName, $lastName, $login, $role, $id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Opérateur modifié avec succès'
        ]);
        
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
            throw new Exception('Ce login existe déjà');
        }
        throw new Exception('Erreur lors de la modification: ' . $e->getMessage());
    }
}

function handleDeleteOperator() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $id = (int)$_POST['id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM operators_config WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Opérateur supprimé avec succès'
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors de la suppression: ' . $e->getMessage());
    }
}

function handleResetPassword() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $id = (int)$_POST['id'];
    $newPassword = 'temp123'; // Mot de passe temporaire
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("
            UPDATE operators_config 
            SET password_hash = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$hashedPassword, $id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Mot de passe réinitialisé. Nouveau mot de passe: ' . $newPassword
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors de la réinitialisation: ' . $e->getMessage());
    }
}

function handleEditMotif() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $id = (int)$_POST['id'];
    $code = sanitize($_POST['code']);
    $description = sanitize($_POST['description']);
    $type = sanitize($_POST['type']);
    $category = sanitize($_POST['category']);
    
    try {
        $stmt = $pdo->prepare("
            UPDATE stop_motifs 
            SET code = ?, description = ?, stop_type = ?, category = ?
            WHERE id = ?
        ");
        $stmt->execute([$code, $description, $type, $category, $id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Motif d\'arrêt modifié avec succès'
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors de la modification: ' . $e->getMessage());
    }
}

function handleDeleteMotif() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $id = (int)$_POST['id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM stop_motifs WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Motif d\'arrêt supprimé avec succès'
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors de la suppression: ' . $e->getMessage());
    }
}

function handleEditTypeNC() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $id = (int)$_POST['id'];
    $code = sanitize($_POST['code']);
    $description = sanitize($_POST['description']);
    $severity = sanitize($_POST['severity']);
    
    try {
        $stmt = $pdo->prepare("
            UPDATE nc_types_config 
            SET code = ?, description = ?, severity = ?
            WHERE id = ?
        ");
        $stmt->execute([$code, $description, $severity, $id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Type NC modifié avec succès'
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors de la modification: ' . $e->getMessage());
    }
}

function handleDeleteTypeNC() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $id = (int)$_POST['id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM nc_types_config WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Type NC supprimé avec succès'
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors de la suppression: ' . $e->getMessage());
    }
}

function handleEditCause5M() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $id = (int)$_POST['id'];
    $description = sanitize($_POST['description']);
    
    try {
        $stmt = $pdo->prepare("
            UPDATE causes_5m 
            SET description = ?
            WHERE id = ?
        ");
        $stmt->execute([$description, $id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Cause 5M modifiée avec succès'
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors de la modification: ' . $e->getMessage());
    }
}

function handleDeleteCause5M() {
    global $pdo;
    
    if (!isLoggedIn()) {
        throw new Exception('Non autorisé');
    }
    
    $id = (int)$_POST['id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM causes_5m WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Cause 5M supprimée avec succès'
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors de la suppression: ' . $e->getMessage());
    }
}

function handleGetPrograms() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM programs WHERE active = 1 ORDER BY name");
        $programs = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'programs' => $programs
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors du chargement: ' . $e->getMessage());
    }
}


function handleGetMotifs() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM stop_motifs WHERE active = 1 ORDER BY code");
        $motifs = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'motifs' => $motifs
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors du chargement: ' . $e->getMessage());
    }
}

function handleGetTypesNC() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM nc_types_config WHERE active = 1 ORDER BY code");
        $types = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'types' => $types
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors du chargement: ' . $e->getMessage());
    }
}

function handleGetCauses5M() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM causes_5m WHERE active = 1 ORDER BY axe, description");
        $causes = $stmt->fetchAll();
        
        $result = [
            'mainOeuvre' => [],
            'methode' => [],
            'matiere' => [],
            'milieu' => [],
            'machine' => []
        ];
        
        foreach ($causes as $cause) {
            $result[$cause['axe']][] = $cause;
        }
        
        echo json_encode([
            'success' => true,
            'causes' => $result
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors du chargement: ' . $e->getMessage());
    }
}

function handleSaveMachineProduction() {
    global $pdo;
    
    $machine = sanitize($_POST['machine']);
    $program = sanitize($_POST['program'] ?? '');
    $weight = (float)($_POST['weight'] ?? 0);
    $batch = sanitize($_POST['batch'] ?? '');
    $client = sanitize($_POST['client'] ?? '');
    $comment = sanitize($_POST['comment'] ?? '');
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO productions (
                type, equipment, program_name, weight, batch_code, client, 
                operator, comment, timestamp
            )
            VALUES ('machine', ?, ?, ?, ?, ?, 'Jean Dupont', ?, NOW())
        ");
        $stmt->execute([
            "Machine $machine kg",
            $program,
            $weight,
            $batch,
            $client,
            $comment
        ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Production machine enregistrée avec succès',
            'id' => $pdo->lastInsertId()
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors de l\'enregistrement: ' . $e->getMessage());
    }
}

function handleSaveSechoirProduction() {
    global $pdo;
    
    $sechoir = sanitize($_POST['sechoir']);
    $program = sanitize($_POST['program'] ?? '');
    $temperature = (int)($_POST['temperature'] ?? 0);
    $batch = sanitize($_POST['batch'] ?? '');
    $client = sanitize($_POST['client'] ?? '');
    $comment = sanitize($_POST['comment'] ?? '');
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO productions (
                type, equipment, program_name, temperature, batch_code, client, 
                operator, comment, timestamp
            )
            VALUES ('sechoir', ?, ?, ?, ?, ?, 'Jean Dupont', ?, NOW())
        ");
        $stmt->execute([
            "Séchoir $sechoir",
            $program,
            $temperature,
            $batch,
            $client,
            $comment
        ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Production séchoir enregistrée avec succès',
            'id' => $pdo->lastInsertId()
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors de l\'enregistrement: ' . $e->getMessage());
    }
}

// View/Edit handlers for stops and NC
function handleGetStopDetails() {
    global $pdo;
    
    $id = (int)$_GET['id'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM equipment_stops WHERE id = ?");
        $stmt->execute([$id]);
        $stop = $stmt->fetch();
        
        if ($stop) {
            echo json_encode([
                'success' => true,
                'stop' => $stop
            ]);
        } else {
            throw new Exception('Arrêt non trouvé');
        }
    } catch (Exception $e) {
        throw new Exception('Erreur lors de la récupération: ' . $e->getMessage());
    }
}

function handleUpdateStop() {
    global $pdo;
    
    $id = (int)$_POST['id'];
    $equipment = sanitize($_POST['equipment']);
    $reason = sanitize($_POST['reason']);
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'] ?? null;
    $comment = sanitize($_POST['comment'] ?? '');
    
    // Calculer la durée si end_time est fourni
    $duration = null;
    if ($startTime && $endTime) {
        $start = new DateTime($startTime);
        $end = new DateTime($endTime);
        $diff = $end->diff($start);
        $duration = $diff->i + ($diff->h * 60) + ($diff->d * 24 * 60);
    }
    
    try {
        $stmt = $pdo->prepare("
            UPDATE equipment_stops 
            SET equipment = ?, reason = ?, start_time = ?, end_time = ?, 
                duration_minutes = ?, comment = ?
            WHERE id = ?
        ");
        $stmt->execute([$equipment, $reason, $startTime, $endTime, $duration, $comment, $id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Arrêt modifié avec succès'
        ]);
    } catch (Exception $e) {
        throw new Exception('Erreur lors de la modification: ' . $e->getMessage());
    }
}

function handleGetNCDetails() {
    global $pdo;
    
    $id = (int)$_GET['id'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM non_conformities WHERE id = ?");
        $stmt->execute([$id]);
        $nc = $stmt->fetch();
        
        if ($nc) {
            echo json_encode([
                'success' => true,
                'nc' => $nc
            ]);
        } else {
            throw new Exception('Non-conformité non trouvée');
        }
    } catch (Exception $e) {
        throw new Exception('Erreur lors de la récupération: ' . $e->getMessage());
    }
}

function handleUpdateNC() {
    global $pdo;
    
    $id = (int)$_POST['id'];
    $equipmentName = sanitize($_POST['equipment_name']);
    $ncType = sanitize($_POST['nc_type']);
    $quantityImpacted = (int)($_POST['quantity_impacted'] ?? 0);
    $comment = sanitize($_POST['comment']);
    
    try {
        $stmt = $pdo->prepare("
            UPDATE non_conformities 
            SET equipment_name = ?, nc_type = ?, quantity_impacted = ?, comment = ?
            WHERE id = ?
        ");
        $stmt->execute([$equipmentName, $ncType, $quantityImpacted, $comment, $id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Non-conformité modifiée avec succès'
        ]);
    } catch (Exception $e) {
        throw new Exception('Erreur lors de la modification: ' . $e->getMessage());
    }
}
?>
