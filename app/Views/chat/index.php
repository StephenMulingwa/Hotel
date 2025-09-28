<div class="grid md:grid-cols-3 gap-6">
	<div class="md:col-span-2">
		<div class="bg-white p-6 rounded shadow">
			<h3 class="text-lg font-semibold mb-4">Messages</h3>
			<div id="messages" class="space-y-3 max-h-[60vh] overflow-auto">
				<?php $maxId = 0; foreach ($messages as $m): $maxId = max($maxId, (int)$m['id']); ?>
					<div class="p-3 border rounded">
						<div class="text-sm text-gray-600"><?php echo htmlspecialchars($m['created_at']); ?></div>
						<div class="font-medium"><?php echo htmlspecialchars($m['from_name'] ?? 'You'); ?> → <?php echo htmlspecialchars($m['to_role']); ?></div>
						<div><?php echo nl2br(htmlspecialchars($m['body'])); ?></div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

	<div>
		<div class="bg-white p-6 rounded shadow">
			<h3 class="text-lg font-semibold mb-4">Send Message</h3>
			<form method="post" action="/chat/send" class="space-y-3">
				<?php echo csrf_field(); ?>
				<label class="block">To
					<select name="to_role" class="w-full border rounded px-3 py-2 mt-1">
						<option value="receptionist">Reception</option>
						<option value="kitchen">Kitchen</option>
					</select>
				</label>
				<label class="block">Message
					<textarea name="body" rows="3" class="w-full border rounded px-3 py-2 mt-1" required></textarea>
				</label>
				<button class="px-4 py-2 bg-gray-900 text-white rounded w-full">Send</button>
			</form>
		</div>
	</div>
</div>

<script>
let lastId = <?php echo (int)($maxId ?? 0); ?>;
async function poll() {
	try {
		const res = await fetch('/chat/fetch?since=' + lastId);
		const data = await res.json();
		if (data.messages && data.messages.length) {
			const wrap = document.getElementById('messages');
			data.messages.forEach(m => {
				const d = document.createElement('div');
				d.className = 'p-3 border rounded';
				d.innerHTML = `
					<div class="text-sm text-gray-600">${m.created_at}</div>
					<div class="font-medium">${m.from_name || 'You'} → ${m.to_role}</div>
					<div>${(m.body+'').replace(/\\n/g,'<br>')}</div>`;
				wrap.prepend(d);
				lastId = Math.max(lastId, parseInt(m.id));
			});
		}
	} catch (e) {}
	setTimeout(poll, 3000);
}
poll();
</script>