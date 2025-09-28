<div class="max-w-6xl mx-auto">
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Room Service Menu</h1>
        <p class="text-lg text-gray-600">Delicious meals delivered to your room</p>
        
        <!-- Currency Selector -->
        <div class="flex justify-center mt-4">
            <div class="relative">
                <select id="currencySelector" onchange="changeCurrency(this.value)" 
                        class="appearance-none bg-white border border-gray-300 rounded-lg px-4 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="KES" <?= ($currency ?? 'KES') === 'KES' ? 'selected' : '' ?>>KES</option>
                    <option value="USD" <?= ($currency ?? 'KES') === 'USD' ? 'selected' : '' ?>>USD</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                    <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Categories -->
    <div class="flex justify-center mb-8">
        <div class="bg-white rounded-lg shadow-sm border p-1">
            <button class="px-4 py-2 rounded-md text-sm font-medium bg-blue-600 text-white category-btn" data-category="all">All Items</button>
            <button class="px-4 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900 category-btn" data-category="dish">Dishes</button>
            <button class="px-4 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900 category-btn" data-category="drink">Drinks</button>
        </div>
    </div>

    <!-- Menu Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="menu-grid">
        <?php foreach ($items as $item): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden menu-item" data-category="<?php echo htmlspecialchars($item['category']); ?>">
                <!-- Item Image -->
                <div class="h-48 bg-gray-200 relative overflow-hidden">
                    <?php if ($item['image_url']): ?>
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                             class="w-full h-full object-cover">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center bg-gray-100">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Stock Status -->
                    <div class="absolute top-2 right-2">
                        <?php if ($item['in_stock']): ?>
                            <span class="px-2 py-1 bg-green-500 text-white text-xs rounded-full">Available</span>
                        <?php else: ?>
                            <span class="px-2 py-1 bg-red-500 text-white text-xs rounded-full">Out of Stock</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Item Details -->
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2"><?php echo htmlspecialchars($item['name']); ?></h3>
                    <p class="text-gray-600 text-sm mb-3"><?php echo htmlspecialchars($item['description'] ?? ''); ?></p>
                    
                    <!-- Price -->
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-2xl font-bold text-gray-900"><?php echo money($item['price_cents'], $currency); ?></span>
                            <span class="text-sm text-gray-500 ml-2">(<?php echo convertToUSD($item['price_cents'], $settings['usd_rate']); ?>)</span>
                        </div>
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                            <?php echo ucfirst($item['category']); ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Order Button -->
    <div class="text-center mt-8">
        <a href="/orders" class="inline-block px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
            Place Your Order
        </a>
    </div>
</div>

<script>
// Category filtering
document.querySelectorAll('.category-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const category = this.dataset.category;
        
        // Update button states
        document.querySelectorAll('.category-btn').forEach(b => {
            b.classList.remove('bg-blue-600', 'text-white');
            b.classList.add('text-gray-600', 'hover:text-gray-900');
        });
        this.classList.add('bg-blue-600', 'text-white');
        this.classList.remove('text-gray-600', 'hover:text-gray-900');
        
        // Filter items
        document.querySelectorAll('.menu-item').forEach(item => {
            if (category === 'all' || item.dataset.category === category) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
});

// Currency conversion functionality
const exchangeRates = {
    'KES': 1.0,
    'USD': 0.0067 // 1 KES = 0.0067 USD (approximate rate)
};

function changeCurrency(newCurrency) {
    // Store user preference in localStorage
    localStorage.setItem('userCurrency', newCurrency);
    
    // Reload the page to update all currency displays
    setTimeout(() => {
        window.location.reload();
    }, 500);
}

// Load user currency preference on page load
document.addEventListener('DOMContentLoaded', function() {
    const savedCurrency = localStorage.getItem('userCurrency');
    if (savedCurrency) {
        const selector = document.getElementById('currencySelector');
        if (selector) {
            selector.value = savedCurrency;
        }
    }
});
</script>
