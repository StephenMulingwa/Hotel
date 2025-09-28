<?php
namespace App\Controllers;

class OrderController {
	public function index(): void {
		requireAuth();
		$pdo = db();
		$user = currentUser();
		$settings = getHotelSettings();

		if ($user['role'] === 'customer') {
			$book = $pdo->prepare("SELECT b.*, r.number AS room_number FROM bookings b JOIN rooms r ON r.id=b.room_id WHERE b.user_id=? AND b.status IN ('pending','confirmed') ORDER BY b.id DESC LIMIT 1");
			$book->execute([$user['id']]);
			$active = $book->fetch() ?: null;

			$orders = $pdo->prepare('SELECT * FROM orders WHERE user_id=? ORDER BY id DESC');
			$orders->execute([$user['id']]);
			$orderList = $orders->fetchAll();
		} else {
			$active = null;
			$orderList = $pdo->query('SELECT o.*, u.name AS customer_name FROM orders o LEFT JOIN users u ON u.id=o.user_id ORDER BY o.id DESC LIMIT 100')->fetchAll();
		}

		$items = $pdo->query('SELECT * FROM menu_items WHERE in_stock = 1 ORDER BY id DESC')->fetchAll();

		$oi = $pdo->query('SELECT oi.*, m.name, m.image_url FROM order_items oi JOIN menu_items m ON m.id=oi.menu_item_id')->fetchAll();
		$orderItems = [];
		foreach ($oi as $row) { $orderItems[$row['order_id']][] = $row; }

		render('orders/index', [
			'active' => $active, 
			'items' => $items, 
			'orders' => $orderList, 
			'orderItems' => $orderItems, 
			'currency' => $settings['currency'],
			'settings' => $settings
		], 'Orders');
	}

	public function create(): void {
		requireAuth();
		verify_csrf();
		$pdo = db();
		$user = currentUser();
		$itemIds = $_POST['item_id'] ?? [];
		$qtys = $_POST['qty'] ?? [];
		$paymentMethod = input('payment_method', 'cash');
		
		if (!is_array($itemIds) || !is_array($qtys)) redirect('/orders');

		// Attach to user's latest booking if any
		$book = $pdo->prepare("SELECT * FROM bookings WHERE user_id=? AND status IN ('pending','confirmed') ORDER BY id DESC LIMIT 1");
		$book->execute([$user['id']]);
		$booking = $book->fetch() ?: null;

		$order = $pdo->prepare('INSERT INTO orders (user_id, booking_id, room_id, total_cents, status, payment_method, created_at) VALUES (?, ?, ?, 0, ?, ?, ?)');
		$order->execute([$user['id'], $booking['id'] ?? null, $booking['room_id'] ?? null, 'pending', $paymentMethod, now()]);
		$orderId = (int)$pdo->lastInsertId();

		$total = 0;
		$sel = $pdo->prepare('SELECT id, price_cents FROM menu_items WHERE id=?');
		$ins = $pdo->prepare('INSERT INTO order_items (order_id, menu_item_id, quantity, price_cents) VALUES (?, ?, ?, ?)');
		foreach ($itemIds as $idx => $mid) {
			$mid = (int)$mid;
			$q = max(1, (int)($qtys[$idx] ?? 1));
			$sel->execute([$mid]);
			$row = $sel->fetch();
			if (!$row) continue;
			$ins->execute([$orderId, $mid, $q, $row['price_cents']]);
			$total += $q * (int)$row['price_cents'];
		}
		$upd = $pdo->prepare('UPDATE orders SET total_cents=? WHERE id=?');
		$upd->execute([$total, $orderId]);

		// Handle payment based on method
		if ($paymentMethod === 'mpesa') {
			// For M-Pesa, we'll redirect to payment page
			redirect('/orders/pay?order_id=' . $orderId);
		} else {
			// For cash, just notify staff
			$msg = $pdo->prepare('INSERT INTO messages (from_user_id, to_role, booking_id, room_id, body, created_at) VALUES (?, ?, ?, ?, ?, ?)');
			$txt = 'New order #' . $orderId . ' total ' . money($total, CONFIG['app']['currency']) . ' (Cash on delivery)';
			$msg->execute([$user['id'], 'receptionist', $booking['id'] ?? null, $booking['room_id'] ?? null, $txt, now()]);
			$msg->execute([$user['id'], 'kitchen', $booking['id'] ?? null, $booking['room_id'] ?? null, $txt, now()]);
		}

		redirect('/orders');
	}

	public function pay(): void {
		requireAuth();
		$orderId = (int)(input('order_id', '0') ?? '0');
		$pdo = db();
		$user = currentUser();
		
		// Get order details
		$order = $pdo->prepare("SELECT o.*, b.room_id, r.number as room_number FROM orders o LEFT JOIN bookings b ON b.id = o.booking_id LEFT JOIN rooms r ON r.id = b.room_id WHERE o.id = ? AND o.user_id = ?");
		$order->execute([$orderId, $user['id']]);
		$orderData = $order->fetch();
		
		if (!$orderData) {
			redirect('/orders');
			return;
		}
		
		// Get order items
		$items = $pdo->prepare("SELECT oi.*, m.name, m.image_url FROM order_items oi JOIN menu_items m ON m.id = oi.menu_item_id WHERE oi.order_id = ?");
		$items->execute([$orderId]);
		$orderItems = $items->fetchAll();
		
		$settings = getHotelSettings();
		render('orders/pay', [
			'order' => $orderData,
			'orderItems' => $orderItems,
			'currency' => $settings['currency'],
			'settings' => $settings
		], 'Order Payment');
	}
	
	public function processPayment(): void {
		requireAuth();
		verify_csrf();
		$pdo = db();
		$user = currentUser();
		$orderId = (int)(input('order_id', '0') ?? '0');
		$paymentMethod = input('payment_method', 'mpesa');
		
		// Get order details
		$order = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
		$order->execute([$orderId, $user['id']]);
		$orderData = $order->fetch();
		
		if (!$orderData) {
			redirect('/orders');
			return;
		}
		
		// Update order status based on payment method
		if ($paymentMethod === 'mpesa') {
			// Simulate M-Pesa payment success
			$update = $pdo->prepare("UPDATE orders SET status = 'confirmed', payment_status = 'paid' WHERE id = ?");
			$update->execute([$orderId]);
			
			// Notify staff
			$msg = $pdo->prepare('INSERT INTO messages (from_user_id, to_role, booking_id, room_id, body, created_at) VALUES (?, ?, ?, ?, ?, ?)');
			$txt = 'Order #' . $orderId . ' paid via M-Pesa - ' . money($orderData['total_cents'], CONFIG['app']['currency']);
			$msg->execute([$user['id'], 'receptionist', $orderData['booking_id'], $orderData['room_id'], $txt, now()]);
			$msg->execute([$user['id'], 'kitchen', $orderData['booking_id'], $orderData['room_id'], $txt, now()]);
		}
		
		redirect('/orders?payment=success');
	}

	public function updateStatus(): void {
		requireRole(['receptionist','kitchen','admin']);
		verify_csrf();
		$id = (int)(input('order_id', '0') ?? '0');
		$status = input('status', 'pending');
		$allowed = ['pending','preparing','ready','delivered','cancelled'];
		if (!in_array($status, $allowed, true)) redirect('/orders');
		$upd = db()->prepare('UPDATE orders SET status=? WHERE id=?');
		$upd->execute([$status, $id]);
		redirect('/orders');
	}
}
