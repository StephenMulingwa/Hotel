<?php
declare(strict_types=1);

function render(string $view, array $data = [], ?string $title = null): void {
	$viewFile = VIEW_PATH . '/' . $view . '.php';
	if (!file_exists($viewFile)) {
		http_response_code(500);
		echo 'View not found: ' . htmlspecialchars($view);
		return;
	}
	extract($data);
	$settings = getHotelSettings();
	$appName = $settings['hotel_name'];
	$pageTitle = $title ?? $appName;
	?><!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo htmlspecialchars($pageTitle); ?></title>
	<script src="https://cdn.tailwindcss.com"></script>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<style>
		body { font-family: Inter, ui-sans-serif, system-ui; }
		img { max-width: 100%; height: auto; }
		/* Improve mobile scrolling */
		html, body { -webkit-tap-highlight-color: transparent; }
	</style>
</head>
<body class="bg-gray-50 text-gray-900">
	<header class="bg-white border-b sticky top-0 z-10">
		<div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
			<a href="/" class="text-xl font-semibold"><?php echo htmlspecialchars($appName); ?></a>
			<button id="mobileMenuButton" class="md:hidden p-2 rounded hover:bg-gray-100" aria-label="Toggle navigation">
				<i class="fas fa-bars"></i>
			</button>
			<nav class="hidden md:flex items-center gap-4" id="desktopNav">
				<?php if (isAuthenticated()): $u = currentUser(); ?>
					<?php if ($u['role'] === 'customer'): ?>
						<a class="hover:underline" href="/dashboard">Dashboard</a>
						<a class="hover:underline" href="/booking/new">Book Room</a>
						<a class="hover:underline" href="/menu">Menu</a>
						<a class="hover:underline" href="/orders">Orders</a>
						<a class="hover:underline" href="/chat">Chat</a>
					<?php elseif ($u['role'] === 'receptionist'): ?>
						<a class="hover:underline" href="/reception">Reception</a>
						<a class="hover:underline" href="/chat">Chat</a>
					<?php elseif ($u['role'] === 'kitchen'): ?>
						<a class="hover:underline" href="/kitchen">Kitchen</a>
						<a class="hover:underline" href="/chat">Chat</a>
					<?php else: ?>
						<a class="hover:underline" href="/dashboard">Dashboard</a>
					<?php endif; ?>
					<form action="/logout" method="post" class="inline">
						<?php echo csrf_field(); ?>
						<button class="px-3 py-1.5 rounded bg-gray-900 text-white">Logout</button>
					</form>
				<?php else: ?>
					<a class="hover:underline" href="/login">Login</a>
					<a class="hover:underline" href="/register">Register</a>
				<?php endif; ?>
			</nav>
		</div>
		<!-- Mobile navigation -->
		<div class="md:hidden hidden border-t" id="mobileNav">
			<div class="max-w-7xl mx-auto px-4 py-3 flex flex-col gap-3">
				<?php if (isAuthenticated()): $u = currentUser(); ?>
					<?php if ($u['role'] === 'customer'): ?>
						<a class="block" href="/dashboard">Dashboard</a>
						<a class="block" href="/booking/new">Book Room</a>
						<a class="block" href="/menu">Menu</a>
						<a class="block" href="/orders">Orders</a>
						<a class="block" href="/chat">Chat</a>
					<?php elseif ($u['role'] === 'receptionist'): ?>
						<a class="block" href="/reception">Reception</a>
						<a class="block" href="/chat">Chat</a>
					<?php elseif ($u['role'] === 'kitchen'): ?>
						<a class="block" href="/kitchen">Kitchen</a>
						<a class="block" href="/chat">Chat</a>
					<?php else: ?>
						<a class="block" href="/dashboard">Dashboard</a>
					<?php endif; ?>
					<form action="/logout" method="post" class="inline">
						<?php echo csrf_field(); ?>
						<button class="px-3 py-2 rounded bg-gray-900 text-white w-full text-left">Logout</button>
					</form>
				<?php else: ?>
					<a class="block" href="/login">Login</a>
					<a class="block" href="/register">Register</a>
				<?php endif; ?>
			</div>
		</div>
	</header>

	<main class="max-w-7xl mx-auto px-4 py-8">
		<?php include $viewFile; ?>
	</main>

	<footer class="border-t bg-white mt-16">
		<div class="max-w-7xl mx-auto px-4 py-6 text-sm text-gray-600">
			&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($appName); ?>. All rights reserved.
		</div>
	</footer>
<script>
document.getElementById('mobileMenuButton')?.addEventListener('click', function() {
	var mobile = document.getElementById('mobileNav');
	if (!mobile) return;
	if (mobile.classList.contains('hidden')) {
		mobile.classList.remove('hidden');
	} else {
		mobile.classList.add('hidden');
	}
});
</script>
</body>
</html><?php
}

function money(int $amountCents, string $currency): string {
	$amount = number_format($amountCents / 100, 2);
	return $currency . ' ' . $amount;
}

function getHotelSettings(): array {
	static $settings = null;
	if ($settings === null) {
		$pdo = db();
		try {
			$settings = $pdo->query('SELECT * FROM hotel_settings ORDER BY id DESC LIMIT 1')->fetch() ?: null;
		} catch (PDOException $e) {
			$settings = null;
		}
		
		if (!$settings) {
			$settings = [
				'hotel_name' => 'Aurora Hotel',
				'hotel_info' => 'Welcome to our beautiful hotel with excellent service and amenities.',
				'room_price_per_night' => 500000,
				'currency' => 'KES',
				'usd_rate' => 0.0067
			];
		}
	}
	return $settings;
}

function convertToUSD(int $amountCents, float $rate): string {
	$amount = $amountCents / 100;
	$usdAmount = $amount * $rate;
	return '$' . number_format($usdAmount, 2);
}

function sendNotification(int $userId, string $toRole, string $title, string $message): void {
	$pdo = db();
	$stmt = $pdo->prepare('INSERT INTO notifications (user_id, to_role, title, message, created_at) VALUES (?, ?, ?, ?, ?)');
	$stmt->execute([$userId, $toRole, $title, $message, now()]);
}