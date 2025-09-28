<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

$pdo = db();

// Check if we're using PostgreSQL (production) or SQLite (development)
$isPostgreSQL = CONFIG['database']['driver'] === 'pgsql';

if ($isPostgreSQL) {
    // PostgreSQL setup
    $sql = file_get_contents(dirname(__DIR__) . '/database_schema.sql');
    $pdo->exec($sql);
    echo '<pre>PostgreSQL database setup complete!</pre>';
} else {
    // SQLite setup (existing code)
    $sql = <<<SQL
PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS users (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	name TEXT NOT NULL,
	phone TEXT NOT NULL UNIQUE,
	email TEXT,
	password_hash TEXT NOT NULL,
	role TEXT NOT NULL CHECK(role IN ('customer','receptionist','kitchen','admin')),
	created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS rooms (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	number INTEGER NOT NULL UNIQUE,
	type TEXT NOT NULL DEFAULT 'standard',
	created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS bookings (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
	room_id INTEGER NOT NULL REFERENCES rooms(id) ON DELETE CASCADE,
	start_date TEXT NOT NULL,
	end_date TEXT NOT NULL,
	status TEXT NOT NULL CHECK(status IN ('pending','confirmed','cancelled','completed')),
	source TEXT,
	notes TEXT,
	checked_in_at TEXT,
	checked_out_at TEXT,
	created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS payments (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	booking_id INTEGER NOT NULL REFERENCES bookings(id) ON DELETE CASCADE,
	amount_cents INTEGER NOT NULL,
	currency TEXT NOT NULL,
	method TEXT NOT NULL,
	status TEXT NOT NULL CHECK(status IN ('pending','paid','failed')),
	reference TEXT,
	paid_at TEXT,
	created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS menu_items (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	name TEXT NOT NULL,
	description TEXT,
	price_cents INTEGER NOT NULL,
	category TEXT NOT NULL DEFAULT 'dish' CHECK(category IN ('dish','drink')),
	image_url TEXT,
	in_stock INTEGER NOT NULL DEFAULT 1,
	created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS orders (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
	booking_id INTEGER REFERENCES bookings(id) ON DELETE SET NULL,
	room_id INTEGER REFERENCES rooms(id) ON DELETE SET NULL,
	total_cents INTEGER NOT NULL DEFAULT 0,
	status TEXT NOT NULL CHECK(status IN ('pending','preparing','ready','delivered','cancelled')),
	created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS order_items (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	order_id INTEGER NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
	menu_item_id INTEGER NOT NULL REFERENCES menu_items(id) ON DELETE CASCADE,
	quantity INTEGER NOT NULL,
	price_cents INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS messages (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	from_user_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
	to_role TEXT NOT NULL CHECK(to_role IN ('receptionist','kitchen','customer','admin')),
	booking_id INTEGER REFERENCES bookings(id) ON DELETE SET NULL,
	room_id INTEGER REFERENCES rooms(id) ON DELETE SET NULL,
	body TEXT NOT NULL,
	is_read INTEGER NOT NULL DEFAULT 0,
	created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS hotel_settings (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	hotel_name TEXT NOT NULL DEFAULT 'Aurora Hotel',
	hotel_info TEXT NOT NULL DEFAULT 'Welcome to our beautiful hotel with excellent service and amenities.',
	room_price_per_night INTEGER NOT NULL DEFAULT 500000,
	currency TEXT NOT NULL DEFAULT 'KES',
	usd_rate REAL NOT NULL DEFAULT 0.0067,
	total_rooms INTEGER NOT NULL DEFAULT 40,
	hotel_password TEXT,
	created_at TEXT NOT NULL,
	updated_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS reviews (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
	booking_id INTEGER REFERENCES bookings(id) ON DELETE SET NULL,
	rating INTEGER NOT NULL CHECK(rating >= 1 AND rating <= 5),
	comment TEXT,
	created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS notifications (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
	to_role TEXT NOT NULL CHECK(to_role IN ('receptionist','kitchen','customer','admin')),
	title TEXT NOT NULL,
	message TEXT NOT NULL,
	is_read INTEGER NOT NULL DEFAULT 0,
	created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS chat_conversations (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	customer_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
	staff_role TEXT NOT NULL CHECK(staff_role IN ('receptionist','kitchen','admin')),
	room_id INTEGER REFERENCES rooms(id) ON DELETE SET NULL,
	booking_id INTEGER REFERENCES bookings(id) ON DELETE SET NULL,
	is_active INTEGER NOT NULL DEFAULT 1,
	created_at TEXT NOT NULL,
	updated_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS chat_messages (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	conversation_id INTEGER NOT NULL REFERENCES chat_conversations(id) ON DELETE CASCADE,
	from_user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
	to_user_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
	to_role TEXT CHECK(to_role IN ('receptionist','kitchen','customer','admin')),
	message TEXT NOT NULL,
	is_read INTEGER NOT NULL DEFAULT 0,
	created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS receipts (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	booking_id INTEGER NOT NULL REFERENCES bookings(id) ON DELETE CASCADE,
	payment_id INTEGER REFERENCES payments(id) ON DELETE CASCADE,
	receipt_number TEXT NOT NULL UNIQUE,
	amount_cents INTEGER NOT NULL,
	currency TEXT NOT NULL,
	created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS refunds (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	booking_id INTEGER NOT NULL REFERENCES bookings(id) ON DELETE CASCADE,
	amount_cents INTEGER NOT NULL,
	reason TEXT NOT NULL,
	processed_by INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
	status TEXT NOT NULL CHECK(status IN ('pending','approved','rejected','processed')),
	created_at TEXT NOT NULL,
	processed_at TEXT
);

CREATE TABLE IF NOT EXISTS analytics_data (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	date TEXT NOT NULL,
	metric_type TEXT NOT NULL,
	metric_value REAL NOT NULL,
	created_at TEXT NOT NULL
);
SQL;

    $pdo->exec($sql);

    // Add missing columns to existing tables
    try {
        $pdo->exec('ALTER TABLE menu_items ADD COLUMN category TEXT DEFAULT "dish"');
    } catch (PDOException $e) {
        // Column already exists, ignore
    }

    try {
        $pdo->exec('ALTER TABLE menu_items ADD COLUMN image_url TEXT');
    } catch (PDOException $e) {
        // Column already exists, ignore
    }

    try {
        $pdo->exec('ALTER TABLE messages ADD COLUMN is_read INTEGER DEFAULT 0');
    } catch (PDOException $e) {
        // Column already exists, ignore
    }

    try {
        $pdo->exec('ALTER TABLE hotel_settings ADD COLUMN total_rooms INTEGER DEFAULT 40');
    } catch (PDOException $e) {
        // Column already exists, ignore
    }

    try {
        $pdo->exec('ALTER TABLE hotel_settings ADD COLUMN hotel_password TEXT');
    } catch (PDOException $e) {
        // Column already exists, ignore
    }

    // Fix receipts table to allow NULL payment_id
    try {
        // SQLite doesn't support ALTER COLUMN to drop NOT NULL, so we need to recreate the table
        $pdo->exec('CREATE TABLE receipts_new (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            booking_id INTEGER NOT NULL REFERENCES bookings(id) ON DELETE CASCADE,
            payment_id INTEGER REFERENCES payments(id) ON DELETE CASCADE,
            receipt_number TEXT NOT NULL UNIQUE,
            amount_cents INTEGER NOT NULL,
            currency TEXT NOT NULL,
            created_at TEXT NOT NULL
        )');
        
        $pdo->exec('INSERT INTO receipts_new SELECT * FROM receipts');
        $pdo->exec('DROP TABLE receipts');
        $pdo->exec('ALTER TABLE receipts_new RENAME TO receipts');
    } catch (PDOException $e) {
        // Table already fixed or doesn't exist, ignore
    }

    $stmt = $pdo->prepare('INSERT OR IGNORE INTO rooms (id, number, type, created_at) VALUES (?, ?, ?, ?)');
    for ($i = 1; $i <= 40; $i++) {
        $stmt->execute([$i, $i, 'standard', now()]);
    }

    $users = [
        ['Reception', '+254700000001', 'reception@example.com', 'receptionist'],
        ['Kitchen', '+254700000002', 'kitchen@example.com', 'kitchen'],
        ['Admin', '+254700000003', 'admin@example.com', 'admin'],
    ];
    $uStmt = $pdo->prepare('INSERT OR IGNORE INTO users (name, phone, email, password_hash, role, created_at) VALUES (?, ?, ?, ?, ?, ?)');
    foreach ($users as $u) {
        [$name, $phone, $email, $role] = $u;
        $uStmt->execute([$name, $phone, $email, password_hash('password', PASSWORD_DEFAULT), $role, now()]);
    }

    // Initialize hotel settings
    $hotelStmt = $pdo->prepare('INSERT OR IGNORE INTO hotel_settings (hotel_name, hotel_info, room_price_per_night, currency, usd_rate, total_rooms, hotel_password, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $hotelStmt->execute(['Aurora Hotel', 'Welcome to our beautiful hotel with excellent service and amenities. We provide comfortable rooms, delicious food, and outstanding hospitality.', 1, 'KES', 0.0067, 40, 'HOTEL2024', now(), now()]);

    // Update existing hotel settings with new columns if they exist
    try {
        $updateStmt = $pdo->prepare('UPDATE hotel_settings SET total_rooms = ?, hotel_password = ? WHERE id = 1');
        $updateStmt->execute([40, 'HOTEL2024']);
    } catch (PDOException $e) {
        // Ignore if columns don't exist yet
    }

    $menu = [
        ['Full Breakfast', 'Eggs, sausage, toast, tea', 80000, 'dish', 'https://images.unsplash.com/photo-1551218808-94e220e084d2?w=400'],
        ['Beef Stew', 'With rice', 120000, 'dish', 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400'],
        ['Chicken Curry', 'With chapati', 150000, 'dish', 'https://images.unsplash.com/photo-1563379091339-03246963d4d0?w=400'],
        ['Fresh Juice', 'Mango/Pineapple', 30000, 'drink', 'https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?w=400'],
        ['Coffee', 'Freshly brewed coffee', 15000, 'drink', 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400'],
        ['Pizza Margherita', 'Classic Italian pizza', 180000, 'dish', 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400'],
    ];
    $mStmt = $pdo->prepare('INSERT INTO menu_items (name, description, price_cents, category, image_url, in_stock, created_at) VALUES (?, ?, ?, ?, ?, 1, ?)');
    foreach ($menu as $m) {
        [$name, $desc, $price, $category, $image] = $m;
        $mStmt->execute([$name, $desc, $price, $category, $image, now()]);
    }

    echo '<pre>SQLite database setup complete!</pre>';
}

echo '<pre>Setup complete. Database initialized with sample data.</pre>';