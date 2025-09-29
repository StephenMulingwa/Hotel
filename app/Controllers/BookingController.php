<?php
namespace App\Controllers;

class BookingController {
	private function nightsBetween(string $start, string $end): int {
		$a = new \DateTimeImmutable($start);
		$b = new \DateTimeImmutable($end);
		return max(1, (int)$a->diff($b)->format('%a'));
	}

	private function findAvailableRoom(string $start, string $end): ?int {
		$pdo = db();
		$rooms = $pdo->query('SELECT id, number FROM rooms ORDER BY number')->fetchAll();
		$q = $pdo->prepare("SELECT 1 FROM bookings WHERE room_id = ? AND status IN ('pending','confirmed') AND NOT (date(end_date) < date(?) OR date(start_date) > date(?)) LIMIT 1");
		foreach ($rooms as $r) {
			$q->execute([$r['id'], $start, $end]);
			if (!$q->fetch()) return (int)$r['id'];
		}
		return null;
	}

	public function dashboard(): void {
		requireAuth();
		$user = currentUser();
		if ($user['role'] !== 'customer') {
			if ($user['role'] === 'admin') redirect('/admin');
			if ($user['role'] === 'receptionist') redirect('/reception');
			if ($user['role'] === 'kitchen') redirect('/kitchen');
		}
		$pdo = db();
		
		// Get hotel settings
		$settings = getHotelSettings();
		
		// Active booking
		$act = $pdo->prepare("SELECT b.*, r.number AS room_number, p.status as payment_status FROM bookings b JOIN rooms r ON r.id=b.room_id LEFT JOIN payments p ON p.booking_id=b.id WHERE b.user_id=? AND b.status IN ('pending','confirmed') AND date(?) BETWEEN date(b.start_date) AND date(b.end_date) ORDER BY b.id DESC LIMIT 1");
		$act->execute([$user['id'], today()]);
		$activeBooking = $act->fetch() ?: null;

		// All bookings for customer
		$hist = $pdo->prepare("SELECT b.*, r.number AS room_number, p.status as payment_status FROM bookings b JOIN rooms r ON r.id=b.room_id LEFT JOIN payments p ON p.booking_id=b.id WHERE b.user_id=? ORDER BY b.id DESC");
		$hist->execute([$user['id']]);
		$bookings = $hist->fetchAll();

		// Orders
		$orders = $pdo->prepare("SELECT o.*, r.number as room_number FROM orders o LEFT JOIN rooms r ON r.id=o.room_id WHERE o.user_id=? ORDER BY o.created_at DESC");
		$orders->execute([$user['id']]);
		$orders = $orders->fetchAll();

		// Calculate total nights
		$totalNights = 0;
		foreach ($bookings as $booking) {
			$totalNights += $this->nightsBetween($booking['start_date'], $booking['end_date']);
		}

		// Calculate total spent
		$totalSpent = $pdo->prepare("SELECT COALESCE(SUM(p.amount_cents), 0) as total FROM payments p WHERE p.booking_id IN (SELECT id FROM bookings WHERE user_id=?) AND p.status='paid'");
		$totalSpent->execute([$user['id']]);
		$totalSpent = (int)$totalSpent->fetch()['total'];

		// Unread messages count - for customers, count unread messages in their conversations
		$unreadMessages = 0;
		if ($user['role'] === 'customer') {
			$unreadStmt = $pdo->prepare("
				SELECT COUNT(*) as count 
				FROM chat_messages cm 
				JOIN chat_conversations cc ON cc.id = cm.conversation_id 
				WHERE cc.customer_id = ? AND cm.from_user_id != ? AND cm.is_read = 0
			");
			$unreadStmt->execute([$user['id'], $user['id']]);
			$unreadMessages = (int)$unreadStmt->fetch()['count'];
		}

		// Get user currency preference (default to KES)
		$userCurrency = $settings['currency'] ?? 'KES';
		
		render('dashboard/customer', [
			'user' => $user,
			'settings' => $settings,
			'activeBooking' => $activeBooking,
			'bookings' => $bookings,
			'orders' => $orders,
			'totalNights' => $totalNights,
			'totalSpent' => $totalSpent,
			'unreadMessages' => $unreadMessages,
			'currency' => $settings['currency'] ?? 'KES',
			'userCurrency' => $userCurrency,
		], 'Customer Dashboard');
	}

	public function create(): void {
		requireAuth();
		$user = currentUser();
		
		// Admin cannot book rooms
		if ($user['role'] === 'admin') {
			redirect('/admin');
			return;
		}
		
		$pdo = db();
		$settings = getHotelSettings();
		
		// Get all rooms with their images
		$rooms = $pdo->query("
			SELECT r.*, 
			       COALESCE(hi.image_url, '') as image_url
			FROM rooms r 
			LEFT JOIN hotel_images hi ON hi.room_id = r.id AND hi.is_primary = 1
			ORDER BY r.number
		")->fetchAll();
		
		// Add price from hotel settings to each room
		foreach ($rooms as &$room) {
			$room['price_cents'] = $settings['room_price_per_night'];
		}
		
		// Get hotel images (non-room specific)
		$hotelImages = $pdo->query("
			SELECT image_url 
			FROM hotel_images 
			WHERE image_type = 'hotel' 
			ORDER BY is_primary DESC, created_at DESC
			LIMIT 6
		")->fetchAll();
		
		render('booking/create', [
			'currency' => $settings['currency'],
			'pricePerNight' => $settings['room_price_per_night'],
			'rooms' => $rooms,
			'hotelImages' => $hotelImages,
			'settings' => $settings,
		], 'Book Room');
	}

	public function store(): void {
		requireAuth();
		verify_csrf();
		$user = currentUser();
		
		// Admin cannot book rooms
		if ($user['role'] === 'admin') {
			redirect('/admin');
			return;
		}
		
		$pdo = db();
		$settings = getHotelSettings();
		
		$name = input('name', $user['name']);
		$phone = input('phone', $user['phone']);
		$start = input('start_date', '');
		$end = input('end_date', '');
		$roomId = (int)(input('room_id', '0') ?? '0');
		
		if (!$start || !$end) {
			render('booking/create', ['error' => 'Select start and end dates', 'currency' => $settings['currency'], 'pricePerNight' => $settings['room_price_per_night']], 'Book Room');
			return;
		}
		
		if (!$roomId) {
			render('booking/create', ['error' => 'Please select a room', 'currency' => $settings['currency'], 'pricePerNight' => $settings['room_price_per_night']], 'Book Room');
			return;
		}

		// Check if room is available for selected dates
		$checkRoom = $pdo->prepare("SELECT 1 FROM bookings WHERE room_id = ? AND status IN ('pending','confirmed') AND NOT (date(end_date) < date(?) OR date(start_date) > date(?)) LIMIT 1");
		$checkRoom->execute([$roomId, $start, $end]);
		if ($checkRoom->fetch()) {
			render('booking/create', ['error' => 'Selected room is not available for the chosen dates', 'currency' => $settings['currency'], 'pricePerNight' => $settings['room_price_per_night']], 'Book Room');
			return;
		}

		// Update user profile (optional)
		$upd = $pdo->prepare('UPDATE users SET name=?, phone=? WHERE id=?');
		$upd->execute([$name, $phone, $user['id']]);
		$_SESSION['user']['name'] = $name;
		$_SESSION['user']['phone'] = $phone;

		$nights = $this->nightsBetween($start, $end);
		$amount = $nights * $settings['room_price_per_night'];

		$insB = $pdo->prepare('INSERT INTO bookings (user_id, room_id, start_date, end_date, status, source, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)');
		$insB->execute([$user['id'], $roomId, $start, $end, 'pending', 'online', now()]);
		$bookingId = (int) $pdo->lastInsertId();

		$insP = $pdo->prepare('INSERT INTO payments (booking_id, amount_cents, currency, method, status, created_at) VALUES (?, ?, ?, ?, ?, ?)');
		$insP->execute([$bookingId, $amount, $settings['currency'], 'online', 'pending', now()]);

		redirect('/booking/pay?booking_id=' . $bookingId);
	}

	public function showPay(): void {
		requireAuth();
		$bookingId = (int)(input('booking_id', '0') ?? '0');
		$stmt = db()->prepare("SELECT b.*, r.number AS room_number, p.amount_cents, p.status AS pay_status FROM bookings b JOIN rooms r ON r.id=b.room_id JOIN payments p ON p.booking_id=b.id WHERE b.id=? AND b.user_id=? LIMIT 1");
		$stmt->execute([$bookingId, currentUser()['id']]);
		$data = $stmt->fetch();
		if (!$data) { http_response_code(404); echo 'Booking not found'; return; }
		render('booking/pay', ['b' => $data, 'currency' => CONFIG()['app']['currency']], 'Payment');
	}

	public function pay(): void {
		requireAuth();
		verify_csrf();
		$bookingId = (int)(input('booking_id', '0') ?? '0');
		$method = input('method', 'online');
		$pdo = db();
		$updP = $pdo->prepare("UPDATE payments SET method=?, status='paid', paid_at=? WHERE booking_id=?");
		$updP->execute([$method, now(), $bookingId]);

		$updB = $pdo->prepare("UPDATE bookings SET status='confirmed' WHERE id=?");
		$updB->execute([$bookingId]);

		redirect('/dashboard');
	}

	public function history(): void {
		requireAuth();
		$pdo = db();
		$hist = $pdo->prepare("SELECT b.*, r.number AS room_number FROM bookings b JOIN rooms r ON r.id=b.room_id WHERE b.user_id=? ORDER BY b.id DESC");
		$hist->execute([currentUser()['id']]);
		render('booking/history', ['history' => $hist->fetchAll()], 'Booking History');
	}

	public function menu(): void {
		requireAuth();
		$items = db()->query('SELECT * FROM menu_items ORDER BY category, id DESC')->fetchAll();
		$settings = getHotelSettings();
		
		// Get user currency preference (default to KES)
		$userCurrency = $settings['currency'] ?? 'KES';
		
		render('menu/enhanced', [
			'items' => $items, 
			'currency' => $userCurrency, 
			'settings' => $settings
		], 'Room Service Menu');
	}
}