<?php
declare(strict_types=1);

// Check if we're in production (Render)
$isProduction = isset($_ENV['RENDER']) || (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'onrender.com') !== false);

// Temporarily use SQLite for testing (remove this after setting up PostgreSQL)
$isProduction = false;

if ($isProduction) {
    // Production configuration for Render
    $databaseUrl = $_ENV['DATABASE_URL'] ?? '';
    $parsedUrl = parse_url($databaseUrl);
    
    $GLOBALS['CONFIG'] = [
        'app' => [
            'name' => 'Aurora Hotel',
            'currency' => 'KES',
        ],
        'database' => [
            'driver' => 'pgsql',
            'host' => $parsedUrl['host'] ?? 'localhost',
            'port' => $parsedUrl['port'] ?? 5432,
            'dbname' => ltrim($parsedUrl['path'] ?? '', '/'),
            'user' => $parsedUrl['user'] ?? '',
            'password' => $parsedUrl['pass'] ?? '',
        ],
        'payments' => [
            'enable_stripe' => false,
            'enable_mpesa' => false,
            'room_price_per_night' => 500000, // cents, 5000.00 KES
        ],
    ];
} else {
    // Development configuration
    $GLOBALS['CONFIG'] = [
        'app' => [
            'name' => 'Aurora Hotel',
            'currency' => 'KES',
        ],
        'database' => [
            'driver' => 'sqlite',
            'sqlite_file' => 'database.sqlite',
        ],
        'payments' => [
            'enable_stripe' => false,
            'enable_mpesa' => false,
            'room_price_per_night' => 500000, // cents, 5000.00 KES
        ],
    ];
}

// Create a function to access the config
function CONFIG() {
    return $GLOBALS['CONFIG'];
}