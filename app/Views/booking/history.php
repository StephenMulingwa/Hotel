<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
	<h2 class="text-2xl font-semibold mb-4">Your Bookings</h2>
	<div class="divide-y">
		<?php foreach ($history as $h): ?>
			<div class="py-3 flex items-center justify-between">
				<div>
					<div class="font-medium">Room #<?php echo (int)$h['room_number']; ?></div>
					<div class="text-sm text-gray-600"><?php echo htmlspecialchars($h['start_date']); ?> → <?php echo htmlspecialchars($h['end_date']); ?></div>
				</div>
				<div class="text-sm px-2 py-1 rounded bg-gray-100"><?php echo htmlspecialchars($h['status']); ?></div>
			</div>
		<?php endforeach; ?>
	</div>
</div>