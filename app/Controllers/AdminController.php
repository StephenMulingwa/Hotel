<?php
namespace App\Controllers;

class AdminController {
	public function dashboard(): void {
		requireRole(['admin']);
		$pdo = db();
		
		// Get hotel settings
		$settings = $pdo->query('SELECT * FROM hotel_settings ORDER BY id DESC LIMIT 1')->fetch() ?: null;
		
		// Analytics data
		$today = today();
		$weekAgo = date('Y-m-d', strtotime('-7 days'));
		$monthAgo = date('Y-m-d', strtotime('-30 days'));
		
		// Room analytics
		$totalRooms = $pdo->query('SELECT COUNT(*) as count FROM rooms')->fetch()['count'];
		
		$occupiedStmt = $pdo->prepare("SELECT COUNT(*) as count FROM bookings WHERE status IN ('pending','confirmed') AND date(?) BETWEEN date(start_date) AND date(end_date)");
		$occupiedStmt->execute([$today]);
		$occupiedToday = $occupiedStmt->fetch()['count'];
		
		$totalBookings = $pdo->query('SELECT COUNT(*) as count FROM bookings')->fetch()['count'];
		
		$revenueTodayStmt = $pdo->prepare("SELECT COALESCE(SUM(amount_cents), 0) as total FROM payments WHERE date(paid_at) = ? AND status = 'paid'");
		$revenueTodayStmt->execute([$today]);
		$revenueToday = $revenueTodayStmt->fetch()['total'];
		
		$revenueWeekStmt = $pdo->prepare("SELECT COALESCE(SUM(amount_cents), 0) as total FROM payments WHERE date(paid_at) >= ? AND status = 'paid'");
		$revenueWeekStmt->execute([$weekAgo]);
		$revenueWeek = $revenueWeekStmt->fetch()['total'];
		
		$revenueMonthStmt = $pdo->prepare("SELECT COALESCE(SUM(amount_cents), 0) as total FROM payments WHERE date(paid_at) >= ? AND status = 'paid'");
		$revenueMonthStmt->execute([$monthAgo]);
		$revenueMonth = $revenueMonthStmt->fetch()['total'];
		
		$roomStats = [
			'total_rooms' => $totalRooms,
			'occupied_today' => $occupiedToday,
			'total_bookings' => $totalBookings,
			'revenue_today' => $revenueToday,
			'revenue_week' => $revenueWeek,
			'revenue_month' => $revenueMonth,
		];
		
		// Food analytics
		$totalOrders = $pdo->query('SELECT COUNT(*) as count FROM orders')->fetch()['count'];
		
		$ordersTodayStmt = $pdo->prepare("SELECT COUNT(*) as count FROM orders WHERE date(created_at) = ?");
		$ordersTodayStmt->execute([$today]);
		$ordersToday = $ordersTodayStmt->fetch()['count'];
		
		$foodRevenueStmt = $pdo->prepare("SELECT COALESCE(SUM(total_cents), 0) as total FROM orders WHERE date(created_at) = ? AND status != 'cancelled'");
		$foodRevenueStmt->execute([$today]);
		$foodRevenueToday = $foodRevenueStmt->fetch()['total'];
		
		$foodStats = [
			'total_orders' => $totalOrders,
			'orders_today' => $ordersToday,
			'food_revenue_today' => $foodRevenueToday,
		];
		
		// Recent bookings
		$recentBookings = $pdo->query("
			SELECT b.*, u.name as customer_name, r.number as room_number, p.amount_cents, p.status as payment_status
			FROM bookings b 
			JOIN users u ON u.id = b.user_id 
			JOIN rooms r ON r.id = b.room_id 
			LEFT JOIN payments p ON p.booking_id = b.id
			ORDER BY b.created_at DESC LIMIT 10
		")->fetchAll();
		
		// Recent orders
		$recentOrders = $pdo->query("
			SELECT o.*, u.name as customer_name, r.number as room_number
			FROM orders o 
			LEFT JOIN users u ON u.id = o.user_id 
			LEFT JOIN rooms r ON r.id = o.room_id 
			ORDER BY o.created_at DESC LIMIT 10
		")->fetchAll();
		
		// Recent reviews
		$recentReviews = $pdo->query("
			SELECT r.*, u.name as customer_name, b.room_id, rm.number as room_number
			FROM reviews r 
			JOIN users u ON u.id = r.user_id 
			LEFT JOIN bookings b ON b.id = r.booking_id 
			LEFT JOIN rooms rm ON rm.id = b.room_id
			ORDER BY r.created_at DESC LIMIT 5
		")->fetchAll();
		
		render('admin/dashboard', [
			'settings' => $settings,
			'roomStats' => $roomStats,
			'foodStats' => $foodStats,
			'recentBookings' => $recentBookings,
			'recentOrders' => $recentOrders,
			'recentReviews' => $recentReviews,
			'currency' => $settings['currency'] ?? 'KES'
		], 'Admin Dashboard');
	}

	public function analytics(): void {
		requireRole(['admin']);
		$pdo = db();
		$period = input('period', 'week');
		
		// Get analytics data based on period
		$startDate = match($period) {
			'day' => today(),
			'week' => date('Y-m-d', strtotime('-7 days')),
			'month' => date('Y-m-d', strtotime('-30 days')),
			'year' => date('Y-m-d', strtotime('-365 days')),
			default => date('Y-m-d', strtotime('-7 days'))
		};
		
		// Room occupancy data
		$occupancyData = $pdo->prepare("
			SELECT date(created_at) as date, COUNT(*) as bookings
			FROM bookings 
			WHERE date(created_at) >= ? 
			GROUP BY date(created_at) 
			ORDER BY date(created_at)
		");
		$occupancyData->execute([$startDate]);
		$occupancy = $occupancyData->fetchAll();
		
		// Revenue data
		$revenueData = $pdo->prepare("
			SELECT date(paid_at) as date, SUM(amount_cents) as revenue
			FROM payments 
			WHERE date(paid_at) >= ? AND status = 'paid'
			GROUP BY date(paid_at) 
			ORDER BY date(paid_at)
		");
		$revenueData->execute([$startDate]);
		$revenue = $revenueData->fetchAll();
		
		// Food sales data
		$foodData = $pdo->prepare("
			SELECT date(created_at) as date, SUM(total_cents) as sales
			FROM orders 
			WHERE date(created_at) >= ? AND status != 'cancelled'
			GROUP BY date(created_at) 
			ORDER BY date(created_at)
		");
		$foodData->execute([$startDate]);
		$foodSales = $foodData->fetchAll();
		
		header('Content-Type: application/json');
		echo json_encode([
			'occupancy' => $occupancy,
			'revenue' => $revenue,
			'foodSales' => $foodSales
		]);
	}

	public function userManagement(): void {
		requireRole(['admin']);
		$pdo = db();
		$users = $pdo->query("
			SELECT u.*, COUNT(b.id) as total_bookings, 
			       COALESCE(SUM(p.amount_cents), 0) as total_spent
			FROM users u 
			LEFT JOIN bookings b ON b.user_id = u.id 
			LEFT JOIN payments p ON p.booking_id = b.id AND p.status = 'paid'
			GROUP BY u.id 
			ORDER BY u.created_at DESC
		")->fetchAll();
		
		render('admin/users', ['users' => $users], 'User Management');
	}

	public function updateUser(): void {
		requireRole(['admin']);
		verify_csrf();
		$pdo = db();
		$userId = (int)(input('user_id', '0') ?? '0');
		$name = input('name', '');
		$phone = input('phone', '');
		$role = input('role', 'customer');
		$newPassword = input('new_password', '');
		
		if (!$userId || !$name || !$phone) {
			redirect('/admin/users?error=1');
			return;
		}
		
		$updateFields = ['name = ?', 'phone = ?', 'role = ?'];
		$params = [$name, $phone, $role];
		
		if ($newPassword) {
			$updateFields[] = 'password_hash = ?';
			$params[] = password_hash($newPassword, PASSWORD_DEFAULT);
		}
		
		$params[] = $userId;
		
		$stmt = $pdo->prepare('UPDATE users SET ' . implode(', ', $updateFields) . ' WHERE id = ?');
		$stmt->execute($params);
		
		redirect('/admin/users?success=1');
	}

	public function customers(): void {
		requireRole(['admin', 'receptionist']);
		$pdo = db();
		$customers = $pdo->query("
			SELECT u.*, COUNT(b.id) as total_bookings, 
			       COALESCE(SUM(p.amount_cents), 0) as total_spent,
			       MAX(b.created_at) as last_booking
			FROM users u 
			LEFT JOIN bookings b ON b.user_id = u.id 
			LEFT JOIN payments p ON p.booking_id = b.id AND p.status = 'paid'
			WHERE u.role = 'customer'
			GROUP BY u.id 
			ORDER BY u.created_at DESC
		")->fetchAll();
		
		render('admin/customers', ['customers' => $customers], 'Customer Management');
	}

	public function chatOverview(): void {
		requireRole(['admin']);
		$pdo = db();
		
		// Get all conversations
		$conversations = $pdo->query("
			SELECT DISTINCT 
				c.customer_id as other_user_id,
				u.name as other_user_name,
				u.role as other_user_role,
				c.staff_role,
				c.room_id,
				r.number as room_number,
				COUNT(m.id) as message_count,
				MAX(m.created_at) as last_message_at
			FROM chat_conversations c
			JOIN users u ON u.id = c.customer_id
			LEFT JOIN rooms r ON r.id = c.room_id
			LEFT JOIN chat_messages m ON m.conversation_id = c.id
			WHERE c.is_active = 1
			GROUP BY c.customer_id, u.name, u.role, c.staff_role, c.room_id, r.number
			ORDER BY last_message_at DESC
		")->fetchAll();
		
		render('admin/chat', ['conversations' => $conversations], 'Chat Overview');
	}

	public function updateCustomer(): void {
		requireRole(['admin', 'receptionist']);
		verify_csrf();
		$pdo = db();
		
		$customerId = (int)(input('customer_id', '0') ?? '0');
		$name = input('name', '');
		$phone = input('phone', '');
		$email = input('email', '');
		$newPassword = input('new_password', '');
		
		if (!$customerId || !$name || !$phone) {
			redirect('/admin/customers?error=1');
			return;
		}
		
		$updateFields = ['name = ?', 'phone = ?', 'email = ?'];
		$params = [$name, $phone, $email];
		
		if ($newPassword) {
			$updateFields[] = 'password_hash = ?';
			$params[] = password_hash($newPassword, PASSWORD_DEFAULT);
		}
		
		$params[] = $customerId;
		
		$stmt = $pdo->prepare('UPDATE users SET ' . implode(', ', $updateFields) . ' WHERE id = ? AND role = "customer"');
		$stmt->execute($params);
		
		redirect('/admin/customers?success=1');
	}

	public function resetCustomerPassword(): void {
		requireRole(['admin', 'receptionist']);
		verify_csrf();
		$pdo = db();
		
		$customerId = (int)(input('customer_id', '0') ?? '0');
		$newPassword = input('new_password', '');
		
		if (!$customerId || !$newPassword) {
			redirect('/admin/customers?error=1');
			return;
		}
		
		$stmt = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ? AND role = "customer"');
		$stmt->execute([password_hash($newPassword, PASSWORD_DEFAULT), $customerId]);
		
		redirect('/admin/customers?success=1');
	}

	public function customerDetails(): void {
		requireRole(['admin', 'receptionist']);
		$pdo = db();
		
		// Get customer ID from URL parameter
		$customerId = (int)($_GET['id'] ?? '0');
		
		if (!$customerId) {
			http_response_code(404);
			echo 'Customer not found';
			return;
		}
		
		// Get customer details
		$customer = $pdo->prepare("
			SELECT u.*, COUNT(b.id) as total_bookings, 
			       COALESCE(SUM(p.amount_cents), 0) as total_spent,
			       MAX(b.created_at) as last_booking
			FROM users u 
			LEFT JOIN bookings b ON b.user_id = u.id 
			LEFT JOIN payments p ON p.booking_id = b.id AND p.status = 'paid'
			WHERE u.id = ? AND u.role = 'customer'
			GROUP BY u.id
		");
		$customer->execute([$customerId]);
		$customerData = $customer->fetch();
		
		if (!$customerData) {
			http_response_code(404);
			echo 'Customer not found';
			return;
		}
		
		// Get customer bookings
		$bookings = $pdo->prepare("
			SELECT b.*, r.number as room_number, p.amount_cents, p.status as payment_status, p.paid_at
			FROM bookings b 
			LEFT JOIN rooms r ON r.id = b.room_id 
			LEFT JOIN payments p ON p.booking_id = b.id
			WHERE b.user_id = ?
			ORDER BY b.created_at DESC
		");
		$bookings->execute([$customerId]);
		$customerBookings = $bookings->fetchAll();
		
		// Get customer orders
		$orders = $pdo->prepare("
			SELECT o.*, r.number as room_number
			FROM orders o 
			LEFT JOIN rooms r ON r.id = o.room_id 
			WHERE o.user_id = ?
			ORDER BY o.created_at DESC
		");
		$orders->execute([$customerId]);
		$customerOrders = $orders->fetchAll();
		
		// Get customer reviews
		$reviews = $pdo->prepare("
			SELECT r.*, b.room_id, rm.number as room_number
			FROM reviews r 
			LEFT JOIN bookings b ON b.id = r.booking_id 
			LEFT JOIN rooms rm ON rm.id = b.room_id
			WHERE r.user_id = ?
			ORDER BY r.created_at DESC
		");
		$reviews->execute([$customerId]);
		$customerReviews = $reviews->fetchAll();
		
		// Return HTML for modal
		ob_start();
		include __DIR__ . '/../Views/admin/customer-details.php';
		$html = ob_get_clean();
		echo $html;
	}

	public function editCustomer(): void {
		requireRole(['admin', 'receptionist']);
		$pdo = db();
		
		// Get customer ID from URL parameter
		$customerId = (int)($_GET['id'] ?? '0');
		
		if (!$customerId) {
			http_response_code(404);
			render('errors/404', [], 'Customer Not Found');
			return;
		}
		
		// Get customer details
		$customer = $pdo->prepare("
			SELECT u.*, COUNT(b.id) as total_bookings, 
			       COALESCE(SUM(p.amount_cents), 0) as total_spent,
			       MAX(b.created_at) as last_booking
			FROM users u 
			LEFT JOIN bookings b ON b.user_id = u.id 
			LEFT JOIN payments p ON p.booking_id = b.id AND p.status = 'paid'
			WHERE u.id = ? AND u.role = 'customer'
			GROUP BY u.id
		");
		$customer->execute([$customerId]);
		$customerData = $customer->fetch();
		
		if (!$customerData) {
			http_response_code(404);
			render('errors/404', [], 'Customer Not Found');
			return;
		}
		
		render('admin/customer-edit', ['customer' => $customerData], 'Edit Customer');
	}

	public function kitchenOverview(): void {
		requireRole(['admin']);
		$pdo = db();
		
		// Get all orders with customer and room information
		$orders = $pdo->query("
			SELECT o.*, u.name as customer_name, r.number as room_number,
			       COUNT(oi.id) as item_count
			FROM orders o 
			LEFT JOIN users u ON u.id = o.user_id 
			LEFT JOIN rooms r ON r.id = o.room_id 
			LEFT JOIN order_items oi ON oi.order_id = o.id
			GROUP BY o.id
			ORDER BY o.created_at DESC
		")->fetchAll();
		
		// Get order items for each order
		$orderItems = [];
		foreach ($orders as $order) {
			$items = $pdo->prepare("
				SELECT oi.*, mi.name as item_name, mi.description, mi.image_url
				FROM order_items oi
				LEFT JOIN menu_items mi ON mi.id = oi.menu_item_id
				WHERE oi.order_id = ?
			");
			$items->execute([$order['id']]);
			$orderItems[$order['id']] = $items->fetchAll();
		}
		
		// Get menu items
		$menuItems = $pdo->query("
			SELECT * FROM menu_items 
			ORDER BY category, name
		")->fetchAll();
		
		// Get kitchen stats
		$stats = [
			'total_orders' => $pdo->query("SELECT COUNT(*) as count FROM orders")->fetch()['count'],
			'pending_orders' => $pdo->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'")->fetch()['count'],
			'preparing_orders' => $pdo->query("SELECT COUNT(*) as count FROM orders WHERE status = 'preparing'")->fetch()['count'],
			'delivered_today' => $pdo->query("SELECT COUNT(*) as count FROM orders WHERE status = 'delivered' AND DATE(created_at) = CURDATE()")->fetch()['count'],
		];
		
		render('admin/kitchen-overview', [
			'orders' => $orders,
			'orderItems' => $orderItems,
			'menuItems' => $menuItems,
			'stats' => $stats
		], 'Kitchen Overview');
	}
}
