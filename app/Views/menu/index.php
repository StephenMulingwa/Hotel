<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
	<h2 class="text-2xl font-semibold mb-4">Live Menu</h2>
	<div class="space-y-3">
		<?php foreach ($items as $m): ?>
			<div class="flex items-center justify-between">
				<div>
					<div class="font-medium"><?php echo htmlspecialchars($m['name']); ?></div>
					<div class="text-sm text-gray-600"><?php echo htmlspecialchars($m['description'] ?? ''); ?></div>
				</div>
				<div class="font-semibold"><?php echo money((int)$m['price_cents'], $currency); ?></div>
			</div>
		<?php endforeach; ?>
	</div>
	<a href="/orders" class="mt-4 inline-block px-4 py-2 rounded bg-gray-900 text-white">Place Order</a>
</div>