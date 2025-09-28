<div class="grid md:grid-cols-3 gap-6">
	<div class="md:col-span-2">
		<div class="bg-white p-6 rounded shadow">
			<div class="flex items-center justify-between mb-4">
				<h3 class="text-lg font-semibold">Rooms Occupancy</h3>
				<form method="get" class="flex items-center gap-2">
					<input type="date" name="date" value="<?php echo htmlspecialchars($date); ?>" class="border rounded px-3 py-1.5">
					<button class="px-3 py-1.5 rounded border">Go</button>
				</form>
			</div>
			<div class="grid grid-cols-5 gap-3">
				<?php foreach ($rooms as $r): $busy = $occupancy[$r['id']]; ?>
					<div class="p-3 rounded text-center <?php echo $busy ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; ?>">
						<div class="text-sm">Room</div>
						<div class="text-xl font-bold">#<?php echo (int)$r['number']; ?></div>
						<div class="text-xs mt-1"><?php echo $busy ? 'Occupied' : 'Available'; ?></div>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="mt-4 text-sm text-gray-600">Legend: <span class="px-2 py-1 rounded bg-red-100 text-red-800">Occupied</span> / <span class="px-2 py-1 rounded bg-green-100 text-green-800">Available</span></div>
		</div>
	</div>

	<div>
		<div class="bg-white p-6 rounded shadow">
			<h3 class="text-lg font-semibold mb-4">New Walk-in Booking</h3>
			<form method="post" action="/reception/book" class="space-y-3">
				<?php echo csrf_field(); ?>
				<div>
					<label class="block mb-1">Name</label>
					<input name="name" class="w-full border rounded px-3 py-2" required>
				</div>
				<div>
					<label class="block mb-1">Phone</label>
					<input name="phone" class="w-full border rounded px-3 py-2" placeholder="+254..." required>
				</div>
				<div>
					<label class="block mb-1">Room</label>
					<select name="room_id" class="w-full border rounded px-3 py-2">
						<?php foreach ($rooms as $r): ?>
							<option value="<?php echo (int)$r['id']; ?>">#<?php echo (int)$r['number']; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="grid grid-cols-2 gap-3">
					<div>
						<label class="block mb-1">Check-in</label>
						<input type="date" name="start_date" class="w-full border rounded px-3 py-2" value="<?php echo htmlspecialchars($date); ?>" required>
					</div>
					<div>
						<label class="block mb-1">Check-out</label>
						<input type="date" name="end_date" class="w-full border rounded px-3 py-2" required>
					</div>
				</div>
				<div class="text-sm text-gray-600">Price per night: <strong><?php echo money($pricePerNight, $currency); ?></strong></div>
				<button class="px-4 py-2 bg-gray-900 text-white rounded w-full">Confirm Booking</button>
			</form>
		</div>
	</div>
</div>