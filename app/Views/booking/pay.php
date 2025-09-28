<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
	<h2 class="text-2xl font-semibold mb-4">Payment</h2>
	<div class="space-y-2 mb-4">
		<div><span class="text-gray-600">Room:</span> <strong>#<?php echo (int)$b['room_number']; ?></strong></div>
		<div><span class="text-gray-600">Dates:</span> <strong><?php echo htmlspecialchars($b['start_date']); ?> → <?php echo htmlspecialchars($b['end_date']); ?></strong></div>
		<div><span class="text-gray-600">Payment:</span> <strong><?php echo strtoupper($b['pay_status']); ?></strong></div>
		<div><span class="text-gray-600">Total:</span> <strong><?php echo money((int)$b['amount_cents'], $currency); ?></strong></div>
	</div>
	<form method="post" action="/booking/pay" class="space-y-3">
		<?php echo csrf_field(); ?>
		<input type="hidden" name="booking_id" value="<?php echo (int)$b['id']; ?>">
		<label class="block mb-1">Method</label>
		<select name="method" class="w-full border rounded px-3 py-2">
			<option value="card">Card</option>
			<option value="mpesa">M-Pesa</option>
			<option value="online">Online</option>
		</select>
		<button class="px-4 py-2 bg-gray-900 text-white rounded">Pay Now</button>
	</form>
	<p class="text-sm text-gray-500 mt-3">Payments are simulated for demo.</p>
</div>