<?php
namespace App\Controllers;

class RefundController {
	public function index(): void {
		requireRole(['admin', 'receptionist']);
		$pdo = db();
		$search = input('search', '');
		$customer = null;
		$bookings = [];
		
		// Get all pending refunds
		$refundsStmt = $pdo->query("
			SELECT r.*, b.id as booking_id, b.start_date, b.end_date, b.status as booking_status,
			       u.name as customer_name, u.phone as customer_phone,
			       rm.number as room_number, p.amount_cents, p.status as payment_status
			FROM refunds r
			JOIN bookings b ON b.id = r.booking_id
			JOIN users u ON u.id = b.user_id
			JOIN rooms rm ON rm.id = b.room_id
			LEFT JOIN payments p ON p.booking_id = b.id
			WHERE r.status = 'pending'
			ORDER BY r.created_at DESC
		");
		$refunds = $refundsStmt->fetchAll();
		
		if ($search) {
			// Search for customer
			$customerStmt = $pdo->prepare("
				SELECT u.*, COUNT(b.id) as total_bookings, 
				       COALESCE(SUM(p.amount_cents), 0) as total_spent
				FROM users u 
				LEFT JOIN bookings b ON b.user_id = u.id 
				LEFT JOIN payments p ON p.booking_id = b.id AND p.status = 'paid'
				WHERE u.name LIKE ? OR u.phone LIKE ?
				GROUP BY u.id 
				ORDER BY u.created_at DESC
				LIMIT 1
			");
			$customerStmt->execute(["%$search%", "%$search%"]);
			$customer = $customerStmt->fetch();
			
			if ($customer) {
				// Get customer's active bookings
				$bookingsStmt = $pdo->prepare("
					SELECT b.*, r.number as room_number, p.amount_cents, p.status as payment_status
					FROM bookings b 
					JOIN rooms r ON r.id = b.room_id 
					LEFT JOIN payments p ON p.booking_id = b.id 
					WHERE b.user_id = ? AND b.status IN ('pending', 'confirmed')
					ORDER BY b.created_at DESC
				");
				$bookingsStmt->execute([$customer['id']]);
				$bookings = $bookingsStmt->fetchAll();
			}
		}
		
		render('admin/refund', [
			'customer' => $customer,
			'bookings' => $bookings,
			'refunds' => $refunds
		], 'Refund Management');
	}
	
	public function process(): void {
		requireRole(['admin', 'receptionist']);
		verify_csrf();
		$pdo = db();
		$bookingId = (int)(input('booking_id', '0') ?? '0');
		$userId = (int)(input('user_id', '0') ?? '0');
		
		if (!$bookingId || !$userId) {
			redirect('/admin/refund?error=1');
			return;
		}
		
		// Get booking details for notification
		$bookingStmt = $pdo->prepare("
			SELECT b.*, r.number as room_number, u.name as customer_name, u.phone as customer_phone
			FROM bookings b
			JOIN rooms r ON r.id = b.room_id
			JOIN users u ON u.id = b.user_id
			WHERE b.id = ?
		");
		$bookingStmt->execute([$bookingId]);
		$booking = $bookingStmt->fetch();
		
		if (!$booking) {
			redirect('/admin/refund?error=2');
			return;
		}
		
		// Create refund record
		$refundStmt = $pdo->prepare("
			INSERT INTO refunds (booking_id, amount_cents, reason, status, processed_by, created_at)
			VALUES (?, ?, ?, 'approved', ?, ?)
		");
		$refundStmt->execute([
			$bookingId,
			$booking['price_cents'] ?? 0,
			'Booking cancelled and refunded',
			currentUser()['id'],
			now()
		]);
		
		// Update booking status to cancelled
		$stmt = $pdo->prepare('UPDATE bookings SET status = ? WHERE id = ?');
		$stmt->execute(['cancelled', $bookingId]);
		
		// Update payment status to refunded
		$stmt = $pdo->prepare('UPDATE payments SET status = ? WHERE booking_id = ?');
		$stmt->execute(['refunded', $bookingId]);
		
		// Send notification to customer
		$message = "Your booking for Room {$booking['room_number']} has been refunded successfully. Amount: " . 
		          number_format(($booking['price_cents'] ?? 0) / 100, 2) . " KES";
		sendNotification($userId, 'customer', 'Booking Refunded', $message);
		
		// Send notification to admin
		$adminMessage = "Refund processed for {$booking['customer_name']} (Room {$booking['room_number']})";
		sendNotification(currentUser()['id'], 'admin', 'Refund Processed', $adminMessage);
		
		redirect('/admin/refund?success=1');
	}
}
