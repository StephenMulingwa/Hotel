<div class="grid md:grid-cols-3 gap-6">
	<div class="md:col-span-2">
		<div class="bg-white p-6 rounded shadow">
			<h3 class="text-lg font-semibold mb-4">Incoming Orders</h3>
			<div class="space-y-4">
				<?php foreach ($orders as $o): ?>
					<div class="p-4 border rounded">
						<div class="flex items-center justify-between">
							<div class="font-semibold">Order #<?php echo (int)$o['id']; ?> • <?php echo htmlspecialchars($o['customer_name'] ?? 'Guest'); ?> • Room #<?php echo htmlspecialchars($o['room_number'] ?? '-'); ?></div>
							<div class="text-sm px-2 py-1 rounded bg-gray-100"><?php echo htmlspecialchars($o['status']); ?></div>
						</div>
						<div class="mt-2 text-sm">
							<ul class="list-disc ml-5">
								<?php foreach (($orderItems[$o['id']] ?? []) as $it): ?>
									<li><?php echo (int)$it['quantity']; ?> × <?php echo htmlspecialchars($it['name']); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
						<form method="post" action="/orders/update-status" class="mt-3 flex items-center gap-2">
							<?php echo csrf_field(); ?>
							<input type="hidden" name="order_id" value="<?php echo (int)$o['id']; ?>">
							<select name="status" class="border rounded px-2 py-1">
								<?php foreach (['pending','preparing','ready','delivered','cancelled'] as $s): ?>
									<option <?php echo $o['status']===$s?'selected':''; ?>><?php echo $s; ?></option>
								<?php endforeach; ?>
							</select>
							<button class="px-3 py-1.5 rounded bg-gray-900 text-white">Update</button>
						</form>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

	<div>
		<div class="bg-white p-6 rounded shadow">
			<h3 class="text-lg font-semibold mb-4">Menu Management</h3>
			<form method="post" action="/kitchen/menu/create" class="space-y-2 mb-4">
				<?php echo csrf_field(); ?>
				<input name="name" class="w-full border rounded px-3 py-2" placeholder="Item name" required>
				<input name="description" class="w-full border rounded px-3 py-2" placeholder="Description">
				<input name="price_cents" class="w-full border rounded px-3 py-2" placeholder="Price in cents e.g. 120000" required>
				<button class="px-3 py-1.5 rounded bg-gray-900 text-white">Add Item</button>
			</form>
			<div class="divide-y">
				<?php foreach ($items as $m): ?>
					<div class="py-3 flex items-center justify-between">
						<div>
							<div class="font-medium"><?php echo htmlspecialchars($m['name']); ?> <span class="text-sm text-gray-500">(<?php echo money((int)$m['price_cents'], $currency); ?>)</span></div>
							<div class="text-sm text-gray-600"><?php echo htmlspecialchars($m['description'] ?? ''); ?></div>
						</div>
						<form method="post" action="/kitchen/menu/toggle">
							<?php echo csrf_field(); ?>
							<input type="hidden" name="id" value="<?php echo (int)$m['id']; ?>">
							<button class="px-3 py-1.5 rounded <?php echo $m['in_stock'] ? 'bg-red-600' : 'bg-green-600'; ?> text-white">
								<?php echo $m['in_stock'] ? 'Mark Out of Stock' : 'Mark In Stock'; ?>
							</button>
						</form>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>