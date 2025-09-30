<script>
	// Auto-refresh every 30 seconds
	setTimeout(function() { location.reload(); }, 30000);
</script>

<div class="p-6">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Orders</p>
                        <p class="text-3xl font-bold text-blue-600"><?= count($orders) ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-receipt text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Pending Orders</p>
                        <p class="text-3xl font-bold text-yellow-600"><?= count(array_filter($orders, fn($o) => $o['status'] === 'pending')) ?></p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Preparing</p>
                        <p class="text-3xl font-bold text-orange-600"><?= count(array_filter($orders, fn($o) => $o['status'] === 'preparing')) ?></p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-fire text-orange-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Ready</p>
                        <p class="text-3xl font-bold text-green-600"><?= count(array_filter($orders, fn($o) => $o['status'] === 'ready')) ?></p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Orders Section -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-orange-50 to-red-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-utensils text-white"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">Live Orders</h3>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <?php if (empty($orders)): ?>
                            <div class="text-center py-12">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-utensils text-3xl text-gray-400"></i>
                                </div>
                                <h4 class="text-lg font-medium text-gray-900 mb-2">No orders yet</h4>
                                <p class="text-gray-500">New food orders will appear here</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-6">
                                <?php foreach ($orders as $order): ?>
                                    <div class="border border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow">
                                        <div class="flex items-start justify-between mb-4">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                                                    <i class="fas fa-receipt text-orange-600"></i>
                                                </div>
                                                <div>
                                                    <h4 class="text-lg font-semibold text-gray-900">Order #<?= $order['id'] ?></h4>
                                                    <p class="text-sm text-gray-600">
                                                        <?= htmlspecialchars($order['customer_name'] ?? 'Guest') ?> • 
                                                        Room <?= htmlspecialchars($order['room_number'] ?? '-') ?> • 
                                                        <?= date('M j, Y g:i A', strtotime($order['created_at'])) ?>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                                    <?= $order['status'] === 'delivered' ? 'bg-green-100 text-green-800' : 
                                                        ($order['status'] === 'ready' ? 'bg-blue-100 text-blue-800' :
                                                        ($order['status'] === 'preparing' ? 'bg-orange-100 text-orange-800' : 
                                                        ($order['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'))) ?>">
                                                    <?= ucfirst($order['status']) ?>
                                                </span>
                                                <p class="text-sm font-bold text-gray-900 mt-2">
                                                    <?= number_format($order['total_cents'] / 100, 2) ?> KES
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <!-- Order Items -->
                                        <?php if (!empty($orderItems[$order['id']])): ?>
                                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                                <h5 class="font-medium text-gray-900 mb-3">Order Items:</h5>
                                                <div class="space-y-2">
                                                    <?php foreach ($orderItems[$order['id']] as $item): ?>
                                                        <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                                                            <div class="flex items-center space-x-3">
                                                                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                                                    <i class="fas fa-utensils text-orange-600 text-sm"></i>
                                                                </div>
                                                                <div>
                                                                    <p class="font-medium text-gray-900"><?= htmlspecialchars($item['name']) ?></p>
                                                                    <p class="text-sm text-gray-600">Qty: <?= $item['quantity'] ?></p>
                                                                </div>
                                                            </div>
                                                            <span class="text-sm font-semibold text-gray-900">
                                                                <?= number_format($item['price_cents'] / 100, 2) ?> KES
                                                            </span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Status Update Form -->
                                        <form method="post" action="/orders/update-status" class="flex items-center space-x-3">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                            <select name="status" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                                                <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="preparing" <?= $order['status'] === 'preparing' ? 'selected' : '' ?>>Preparing</option>
                                                <option value="ready" <?= $order['status'] === 'ready' ? 'selected' : '' ?>>Ready</option>
                                                <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                                <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                            </select>
                                            <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                                                <i class="fas fa-save mr-2"></i>Update
                                            </button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Menu Management Section -->
            <div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-50 to-blue-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-list text-white"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">Menu Management</h3>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <!-- Add New Item Form -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-semibold text-gray-900 mb-3">Add New Menu Item</h4>
                            <form method="post" action="/kitchen/menu/create" class="space-y-3">
                                <?= csrf_field() ?>
                                <input name="name" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Item name" required>
                                <input name="description" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Description">
                                <input name="price_cents" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Price in cents (e.g. 120000)" required>
                                <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>Add Item
                                </button>
                            </form>
                        </div>
                        
                        <!-- Menu Items List -->
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-3">Current Menu Items</h4>
                            <?php if (empty($items)): ?>
                                <div class="text-center py-8">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-list text-2xl text-gray-400"></i>
                                    </div>
                                    <h5 class="text-lg font-medium text-gray-900 mb-2">No menu items</h5>
                                    <p class="text-gray-500">Add your first menu item above</p>
                                </div>
                            <?php else: ?>
                                <div class="space-y-3">
                                    <?php foreach ($items as $item): ?>
                                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                                        <i class="fas fa-utensils text-green-600"></i>
                                                    </div>
                                                    <div>
                                                        <h5 class="font-semibold text-gray-900"><?= htmlspecialchars($item['name']) ?></h5>
                                                        <p class="text-sm text-gray-600"><?= htmlspecialchars($item['description'] ?? '') ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-bold text-green-600"><?= number_format($item['price_cents'] / 100, 2) ?> KES</p>
                                                <form method="post" action="/kitchen/menu/toggle" class="inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                                    <button type="submit" class="text-xs px-2 py-1 rounded-full <?= $item['in_stock'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                                        <?= $item['in_stock'] ? 'Available' : 'Unavailable' ?>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>