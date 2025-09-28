<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-semibold mb-4">Login</h2>
    <?php if (!empty($error)): ?>
        <div class="bg-red-50 text-red-700 p-3 rounded mb-4">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <form method="post" action="/login" class="space-y-4">
        <?php echo csrf_field(); ?>
        <div>
            <label class="block mb-1">Phone</label>
            <input name="phone" class="w-full border rounded px-3 py-2" placeholder="+254..." required>
        </div>
        <div>
            <label class="block mb-1">Password</label>
            <input type="password" name="password" class="w-full border rounded px-3 py-2" required>
        </div>
        <button class="px-4 py-2 bg-gray-900 text-white rounded">Login</button>
    </form>

    <p class="mt-4 text-sm">
        Don’t have an account?
        <a href="/register" class="text-blue-600">Register</a>
    </p>
</div>
