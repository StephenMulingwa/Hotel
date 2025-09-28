<?php
echo "PHP is working!<br>";
echo "Current directory: " . __DIR__ . "<br>";
echo "Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'Not set') . "<br>";
echo "Script name: " . ($_SERVER['SCRIPT_NAME'] ?? 'Not set') . "<br>";
echo "Document root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Not set') . "<br>";

// Test database connection
try {
    $pdo = new PDO('sqlite:' . __DIR__ . '/storage/database.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection: SUCCESS<br>";
    
    // Check if tables exist
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables found: " . implode(', ', $tables) . "<br>";
} catch (Exception $e) {
    echo "Database connection: FAILED - " . $e->getMessage() . "<br>";
}

// Test autoloader
if (file_exists(__DIR__ . '/bootstrap.php')) {
    echo "Bootstrap file: EXISTS<br>";
} else {
    echo "Bootstrap file: MISSING<br>";
}
?>
