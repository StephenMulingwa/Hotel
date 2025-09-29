<?php
$title = 'Kitchen Overview - ' . ($settings['hotel_name'] ?? 'Hotel');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        // Auto-refresh every 30 seconds
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="flex items-center justify-between p-4">
            <div class="flex items-center space-x-3">
                <a href="/admin" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center text-white font-semibold">
                    <i class="fas fa-utensils"></i>
                </div>
                <div>
                    <h1 class="font-semibold text-gray-900">Kitchen Overview</h1>
                    <p class="text-sm text-gray-500">Live kitchen monitoring (Read-only)</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-sync-alt mr-1"></i>
                    Auto-refresh: 30s
                </div>
                <a href="/admin" class="p-2 text-gray-600 hover:text-gray-800 rounded-full hover:bg-gray-100">
                    <i class="fas fa-home"></i>
                </a>
                <form method="POST" action="/logout" class="inline">
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    <button type="submit" class="p-2 text-gray-600 hover:text-gray-800 rounded-full hover:bg-gray-100">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="p-6">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Orders</p>
                        <p class="text-3xl font-bold text-blue-600"><?= $stats['total_orders'] ?></p>
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
                        <p class="text-3xl font-bold text-yellow-600"><?= $stats['pending_orders'] ?></p>
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
                        <p class="text-3xl font-bold text-orange-600"><?= $stats['preparing_orders'] ?></p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-fire text-orange-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Delivered Today</p>
                        <p class="text-3xl font-bold text-green-600"><?= $stats['delivered_today'] ?></p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Live Orders -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-orange-50 to-red-50 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-utensils text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Live Kitchen Orders</h3>
                    </div>
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <i class="fas fa-eye text-green-500"></i>
                        <span>Read-only view</span>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <?php if (empty($orders)): ?>
                    <div class="text-center py-12">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-utensils text-3xl text-gray-400"></i>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">No orders found</h4>
                        <p class="text-gray-500">No food orders have been placed yet</p>
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
                                                <?= htmlspecialchars($order['customer_name']) ?> • 
                                                Room <?= $order['room_number'] ?> • 
                                                <?= date('M j, Y g:i A', strtotime($order['created_at'])) ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                            <?= $order['status'] === 'delivered' ? 'bg-green-100 text-green-800' : 
                                                ($order['status'] === 'preparing' ? 'bg-orange-100 text-orange-800' : 
                                                ($order['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) ?>">
                                            <?= ucfirst($order['status']) ?>
                                        </span>
                                        <p class="text-sm font-bold text-gray-900 mt-2">
                                            <?= number_format($order['total_cents'] / 100, 2) ?> KES
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Order Items -->
                                <?php if (!empty($orderItems[$order['id']])): ?>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h5 class="font-medium text-gray-900 mb-3">Order Items:</h5>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <?php foreach ($orderItems[$order['id']] as $item): ?>
                                                <div class="flex items-center space-x-3 p-3 bg-white rounded-lg">
                                                    <?php if ($item['image_url']): ?>
                                                        <img src="<?= htmlspecialchars($item['image_url']) ?>" 
                                                             alt="<?= htmlspecialchars($item['item_name']) ?>" 
                                                             class="w-12 h-12 object-cover rounded-lg">
                                                    <?php else: ?>
                                                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                                            <i class="fas fa-utensils text-gray-400"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="flex-1">
                                                        <p class="font-medium text-gray-900"><?= htmlspecialchars($item['item_name']) ?></p>
                                                        <p class="text-sm text-gray-600">
                                                            Qty: <?= $item['quantity'] ?> • 
                                                            <?= number_format($item['price_cents'] / 100, 2) ?> KES each
                                                        </p>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Menu Items Overview -->
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-green-50 to-blue-50 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-list text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Available Menu Items</h3>
                </div>
            </div>
            
            <div class="p-6">
                <?php if (empty($menuItems)): ?>
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-list text-2xl text-gray-400"></i>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">No menu items</h4>
                        <p class="text-gray-500">No menu items have been added yet</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach ($menuItems as $item): ?>
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start space-x-3">
                                    <?php if ($item['image_url']): ?>
                                        <img src="<?= htmlspecialchars($item['image_url']) ?>" 
                                             alt="<?= htmlspecialchars($item['name']) ?>" 
                                             class="w-16 h-16 object-cover rounded-lg">
                                    <?php else: ?>
                                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-utensils text-gray-400"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex-1">
                                        <h5 class="font-semibold text-gray-900"><?= htmlspecialchars($item['name']) ?></h5>
                                        <p class="text-sm text-gray-600 mb-2"><?= htmlspecialchars($item['description']) ?></p>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-bold text-green-600">
                                                <?= number_format($item['price_cents'] / 100, 2) ?> KES
                                            </span>
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                <?= (!empty($item['is_available']) || (!empty($item['in_stock']) && (int)$item['in_stock'] === 1)) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                                <?= (!empty($item['is_available']) || (!empty($item['in_stock']) && (int)$item['in_stock'] === 1)) ? 'Available' : 'Unavailable' ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
