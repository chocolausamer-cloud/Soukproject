<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'trs_blanchisserie');

// Start session
session_start();

// Database connection - use SQLite for testing
try {
    $pdo = new PDO("sqlite:" . __DIR__ . "/database.sqlite");
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
        // Create simple table for basic functionality
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username VARCHAR(50) UNIQUE NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                role VARCHAR(20) DEFAULT 'user'
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
        
        return true;
    } catch (Exception $e) {
        error_log("Database setup error: " . $e->getMessage());
        return false;
    }
}

// Run database setup
setupDatabase();
?>
