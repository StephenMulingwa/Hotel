<?php
namespace App\Controllers;

class KitchenController {
	public function index(): void {
		requireRole(['kitchen','admin']);
		$pdo = db();
		$items = $pdo->query('SELECT * FROM menu_items ORDER BY id DESC')->fetchAll();
		$orders = $pdo->query('SELECT o.*, u.name AS customer_name, r.number AS room_number FROM orders o LEFT JOIN users u ON u.id=o.user_id LEFT JOIN rooms r ON r.id=o.room_id ORDER BY o.id DESC LIMIT 50')->fetchAll();
		$oi = $pdo->query('SELECT oi.*, m.name FROM order_items oi JOIN menu_items m ON m.id=oi.menu_item_id')->fetchAll();
		$orderItems = [];
		foreach ($oi as $row) { $orderItems[$row['order_id']][] = $row; }
		render('kitchen/index', ['items' => $items, 'orders' => $orders, 'orderItems' => $orderItems, 'currency' => CONFIG['app']['currency']], 'Kitchen');
	}

	public function createItem(): void {
		requireRole(['kitchen','admin']);
		verify_csrf();
		$name = input('name', '');
		$desc = input('description', '');
		$price = (int)((input('price_cents', '0') ?? '0'));
		if (!$name || $price <= 0) { redirect('/kitchen?err=1'); }
		$stmt = db()->prepare('INSERT INTO menu_items (name, description, price_cents, in_stock, created_at) VALUES (?, ?, ?, 1, ?)');
		$stmt->execute([$name, $desc, $price, now()]);
		redirect('/kitchen');
	}

	public function toggleItem(): void {
		requireRole(['kitchen','admin']);
		verify_csrf();
		$id = (int)(input('id', '0') ?? '0');
		$stmt = db()->prepare('UPDATE menu_items SET in_stock = CASE in_stock WHEN 1 THEN 0 ELSE 1 END WHERE id=?');
		$stmt->execute([$id]);
		redirect('/kitchen');
	}

	public function enhanced(): void {
		requireRole(['kitchen','admin']);
		$pdo = db();
		$items = $pdo->query('SELECT * FROM menu_items ORDER BY id DESC')->fetchAll();
		$orders = $pdo->query('SELECT o.*, u.name AS customer_name, r.number AS room_number FROM orders o LEFT JOIN users u ON u.id=o.user_id LEFT JOIN rooms r ON r.id=o.room_id ORDER BY o.id DESC LIMIT 50')->fetchAll();
		$oi = $pdo->query('SELECT oi.*, m.name FROM order_items oi JOIN menu_items m ON m.id=oi.menu_item_id')->fetchAll();
		$orderItems = [];
		foreach ($oi as $row) { $orderItems[$row['order_id']][] = $row; }
		render('kitchen/enhanced', ['items' => $items, 'orders' => $orders, 'orderItems' => $orderItems, 'currency' => CONFIG['app']['currency']], 'Enhanced Kitchen');
	}

	public function uploadImage(): void {
		requireRole(['kitchen','admin']);
		verify_csrf();
		
		if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
			http_response_code(400);
			echo json_encode(['error' => 'No image uploaded']);
			return;
		}

		$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
		$maxSize = 5 * 1024 * 1024; // 5MB

		if (!in_array($_FILES['image']['type'], $allowedTypes)) {
			http_response_code(400);
			echo json_encode(['error' => 'Invalid file type']);
			return;
		}

		if ($_FILES['image']['size'] > $maxSize) {
			http_response_code(400);
			echo json_encode(['error' => 'File too large']);
			return;
		}

		// Create uploads directory if it doesn't exist
		$uploadDir = __DIR__ . '/../../public/uploads/';
		if (!is_dir($uploadDir)) {
			mkdir($uploadDir, 0755, true);
		}

		// Generate unique filename
		$extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
		$filename = 'menu_' . uniqid() . '.' . $extension;
		$filepath = $uploadDir . $filename;

		if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
			$imageUrl = '/uploads/' . $filename;
			echo json_encode(['success' => true, 'imageUrl' => $imageUrl]);
		} else {
			http_response_code(500);
			echo json_encode(['error' => 'Failed to upload image']);
		}
	}

	public function createMenuItem(): void {
		requireRole(['kitchen','admin']);
		verify_csrf();
		
		$name = input('name', '');
		$description = input('description', '');
		$priceCents = (int)(input('price_cents', '0') ?? '0');
		$category = input('category', 'dish');
		$imageUrl = input('image_url', '');
		
		if (!$name || $priceCents <= 0) {
			redirect('/kitchen/enhanced?error=1');
			return;
		}
		
		$pdo = db();
		$stmt = $pdo->prepare('INSERT INTO menu_items (name, description, price_cents, category, image_url, in_stock, created_at) VALUES (?, ?, ?, ?, ?, 1, ?)');
		$stmt->execute([$name, $description, $priceCents, $category, $imageUrl, now()]);
		
		redirect('/kitchen/enhanced?success=1');
	}

	public function removeMenuItem(): void {
		requireRole(['kitchen','admin']);
		verify_csrf();
		
		$itemId = (int)(input('item_id', '0') ?? '0');
		
		if (!$itemId) {
			redirect('/kitchen/enhanced?error=2');
			return;
		}
		
		$pdo = db();
		
		// Check if item has any orders
		$orderCheck = $pdo->prepare('SELECT COUNT(*) as count FROM order_items WHERE menu_item_id = ?');
		$orderCheck->execute([$itemId]);
		$hasOrders = $orderCheck->fetch()['count'] > 0;
		
		if ($hasOrders) {
			// If item has orders, just mark as out of stock instead of deleting
			$stmt = $pdo->prepare('UPDATE menu_items SET in_stock = 0 WHERE id = ?');
			$stmt->execute([$itemId]);
		} else {
			// If no orders, delete the item
			$stmt = $pdo->prepare('DELETE FROM menu_items WHERE id = ?');
			$stmt->execute([$itemId]);
		}
		
		redirect('/kitchen/enhanced?success=2');
	}

	public function updateOrderStatus(): void {
		requireRole(['kitchen','admin']);
		verify_csrf();
		
		$orderId = (int)(input('order_id', '0') ?? '0');
		$status = input('status', '');
		
		if (!$orderId || !$status) {
			http_response_code(400);
			echo json_encode(['error' => 'Missing order ID or status']);
			return;
		}
		
		$validStatuses = ['preparing', 'ready', 'delivered', 'cancelled'];
		if (!in_array($status, $validStatuses)) {
			http_response_code(400);
			echo json_encode(['error' => 'Invalid status']);
			return;
		}
		
		$pdo = db();
		$stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
		$stmt->execute([$status, $orderId]);
		
		echo json_encode(['success' => true, 'status' => $status]);
	}
}