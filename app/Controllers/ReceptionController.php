<?php
namespace App\Controllers;

class ReceptionController {
	public function index(): void {
		requireRole(['receptionist','admin']);
		$date = input('date', today());
		$pdo = db();
		$rooms = $pdo->query('SELECT * FROM rooms ORDER BY number')->fetchAll();
		$q = $pdo->prepare("SELECT 1 FROM bookings WHERE room_id=? AND status IN ('pending','confirmed') AND NOT (date(end_date) < date(?) OR date(start_date) > date(?)) LIMIT 1");

		$occupancy = [];
		foreach ($rooms as $r) {
			$q->execute([$r['id'], $date, $date]);
			$occupancy[$r['id']] = (bool)$q->fetch();
		}

		render('reception/index', [
			'rooms' => $rooms,
			'date' => $date,
			'occupancy' => $occupancy,
			'currency' => CONFIG()['app']['currency'],
			'pricePerNight' => (int)CONFIG()['payments']['room_price_per_night'],
		], 'Reception');
	}

	public function createBooking(): void {
		requireRole(['receptionist','admin']);
		verify_csrf();
		$name = input('name', '');
		$phone = input('phone', '');
		$roomId = (int)(input('room_id', '0') ?? '0');
		$start = input('start_date', '');
		$end = input('end_date', '');
		if (!$name || !$phone || !$roomId || !$start || !$end) {
			redirect('/reception?err=missing');
		}
		$pdo = db();
		$u = $pdo->prepare('SELECT * FROM users WHERE phone=? LIMIT 1');
		$u->execute([$phone]);
		$user = $u->fetch();
		if (!$user) {
			$ins = $pdo->prepare('INSERT INTO users (name, phone, password_hash, role, created_at) VALUES (?, ?, ?, ?, ?)');
			$ins->execute([$name, $phone, password_hash('password', PASSWORD_DEFAULT), 'customer', now()]);
			$userId = (int)$pdo->lastInsertId();
		} else {
			$userId = (int)$user['id'];
			$upd = $pdo->prepare('UPDATE users SET name=? WHERE id=?');
			$upd->execute([$name, $userId]);
		}

		// Check availability
		$q = $pdo->prepare("SELECT 1 FROM bookings WHERE room_id=? AND status IN ('pending','confirmed') AND NOT (date(end_date) < date(?) OR date(start_date) > date(?)) LIMIT 1");
		$q->execute([$roomId, $start, $end]);
		if ($q->fetch()) {
			redirect('/reception?err=occupied');
		}

		$insB = $pdo->prepare('INSERT INTO bookings (user_id, room_id, start_date, end_date, status, source, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)');
		$insB->execute([$userId, $roomId, $start, $end, 'confirmed', 'reception', now()]);
		$bookingId = (int)$pdo->lastInsertId();

		$nights = max(1, (int)(new \DateTimeImmutable($start))->diff(new \DateTimeImmutable($end))->format('%a'));
		$amount = $nights * (int)CONFIG()['payments']['room_price_per_night'];
		$insP = $pdo->prepare("INSERT INTO payments (booking_id, amount_cents, currency, method, status, paid_at, created_at) VALUES (?, ?, ?, 'card', 'paid', ?, ?)");
		$insP->execute([$bookingId, $amount, CONFIG()['app']['currency'], now(), now()]);

		redirect('/reception?ok=1');
	}
}