<div class="py-16">
    <div class="max-w-3xl mx-auto text-center">
        <h1 class="text-4xl font-bold mb-4">
            Welcome to <?php echo htmlspecialchars(CONFIG()['app']['name']); ?>
        </h1>
        <p class="text-gray-600 mb-8">
            Book rooms, order from the live menu, and chat with our team. 
            Reception and Kitchen dashboards keep everything flowing.
        </p>
        <div class="flex items-center justify-center gap-4">
            <a href="/register" 
               class="px-6 py-3 rounded bg-gray-900 text-white">
                Get Started
            </a>
            <a href="/login" 
               class="px-6 py-3 rounded border">
                Login
            </a>
        </div>
        
        <div class="mt-8 pt-8 border-t">
            <h3 class="text-lg font-semibold mb-4 text-center">Staff Access</h3>
            <div class="flex items-center justify-center gap-4">
                <a href="/reception" 
                   class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
                    Reception Dashboard
                </a>
                <a href="/kitchen" 
                   class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">
                    Kitchen Dashboard
                </a>
                <a href="/dashboard" 
                   class="px-4 py-2 rounded bg-purple-600 text-white hover:bg-purple-700">
                    Admin Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
