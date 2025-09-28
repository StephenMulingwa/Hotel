<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - <?= htmlspecialchars($settings['hotel_name'] ?? 'Hotel') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .order-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-preparing { background-color: #dbeafe; color: #1e40af; }
        .status-ready { background-color: #dcfce7; color: #166534; }
        .status-delivered { background-color: #f0fdf4; color: #15803d; }
        .status-cancelled { background-color: #fee2e2; color: #dc2626; }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="flex items-center justify-between p-4">
            <div class="flex items-center space-x-3">
                <a href="/dashboard" class="p-2 text-gray-600 hover:text-gray-800 rounded-full hover:bg-gray-100">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="font-semibold text-gray-900">Food Orders</h1>
                    <p class="text-sm text-gray-500"><?= htmlspecialchars($settings['hotel_name'] ?? 'Hotel') ?> Room Service</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <a href="/menu" class="p-2 text-gray-600 hover:text-gray-800 rounded-full hover:bg-gray-100">
                    <i class="fas fa-utensils"></i>
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

    <div class="max-w-7xl mx-auto p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Your Orders Section -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-semibold text-gray-900">Your Orders</h3>
                        <div class="flex items-center space-x-2">
                            <button onclick="refreshOrders()" class="p-2 text-gray-600 hover:text-gray-800 rounded-full hover:bg-gray-100">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    
                    <?php if (empty($orders)): ?>
                        <div class="text-center py-12">
                            <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-4"></i>
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">No Orders Yet</h4>
                            <p class="text-gray-600 mb-4">Start by placing your first order from our delicious menu</p>
                            <a href="/menu" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                                <i class="fas fa-utensils mr-2"></i>View Menu
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($orders as $order): ?>
                                <div class="order-card border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-receipt text-blue-600"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-gray-900">Order #<?= $order['id'] ?></h4>
                                                <p class="text-sm text-gray-600">
                                                    <?= date('M j, Y \a\t g:i A', strtotime($order['created_at'])) ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="px-3 py-1 rounded-full text-sm font-medium status-<?= $order['status'] ?>">
                                                <?= ucfirst($order['status']) ?>
                                            </span>
                                            <p class="text-lg font-bold text-gray-900 mt-1">
                                                <?= money($order['total_cents'], $currency) ?>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Order Items with Images -->
                                    <div class="space-y-3">
                                        <?php foreach (($orderItems[$order['id']] ?? []) as $item): ?>
                                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                                <div class="w-16 h-16 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                                    <?php if (!empty($item['image_url'])): ?>
                                                        <img src="<?= htmlspecialchars($item['image_url']) ?>" 
                                                             alt="<?= htmlspecialchars($item['name']) ?>" 
                                                             class="w-full h-full object-cover">
                                                    <?php else: ?>
                                                        <div class="w-full h-full flex items-center justify-center">
                                                            <i class="fas fa-utensils text-gray-400"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex-1">
                                                    <h5 class="font-medium text-gray-900"><?= htmlspecialchars($item['name']) ?></h5>
                                                    <p class="text-sm text-gray-600">Quantity: <?= $item['quantity'] ?></p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="font-semibold text-gray-900">
                                                        <?= money($item['price_cents'] * $item['quantity'], $currency) ?>
                                                    </p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <!-- Order Actions -->
                                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200">
                                        <div class="flex items-center space-x-2">
                                            <?php if ($order['status'] === 'delivered'): ?>
                                                <button onclick="downloadReceipt(<?= $order['id'] ?>)" 
                                                        class="text-sm text-blue-600 hover:text-blue-800">
                                                    <i class="fas fa-download mr-1"></i>Download Receipt
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?php
                                            $statusIcons = [
                                                'pending' => 'fas fa-clock',
                                                'preparing' => 'fas fa-utensils',
                                                'ready' => 'fas fa-check-circle',
                                                'delivered' => 'fas fa-truck',
                                                'cancelled' => 'fas fa-times-circle'
                                            ];
                                            ?>
                                            <i class="<?= $statusIcons[$order['status']] ?? 'fas fa-circle' ?> mr-1"></i>
                                            <?= ucfirst($order['status']) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Place New Order Section -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Place New Order</h3>
                    
                    <?php if (!$active): ?>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                                <div>
                                    <p class="text-sm text-yellow-800 font-medium">No Active Booking</p>
                                    <p class="text-xs text-yellow-700">You need an active booking to place orders</p>
                                </div>
                            </div>
                            <a href="/booking/new" class="mt-3 inline-block text-sm text-yellow-800 underline hover:text-yellow-900">
                                Book a room first
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="/orders/create" class="space-y-4">
                        <?php echo csrf_field(); ?>
                        
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            <?php foreach ($items as $item): ?>
                                <div class="border border-gray-200 rounded-lg p-3 hover:border-blue-300 transition-colors">
                                    <div class="flex items-start space-x-3">
                                        <input type="checkbox" 
                                               name="item_id[]" 
                                               value="<?= $item['id'] ?>" 
                                               class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        
                                        <div class="w-16 h-16 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                            <?php if (!empty($item['image_url'])): ?>
                                                <img src="<?= htmlspecialchars($item['image_url']) ?>" 
                                                     alt="<?= htmlspecialchars($item['name']) ?>" 
                                                     class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <i class="fas fa-utensils text-gray-400"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-medium text-gray-900"><?= htmlspecialchars($item['name']) ?></h4>
                                            <p class="text-sm text-gray-600"><?= htmlspecialchars($item['description'] ?? '') ?></p>
                                            <div class="flex items-center justify-between mt-2">
                                                <span class="text-lg font-bold text-blue-600">
                                                    <?= money($item['price_cents'], $currency) ?>
                                                </span>
                                                <div class="flex items-center space-x-2">
                                                    <label class="text-sm text-gray-600">Qty:</label>
                                                    <input type="number" 
                                                           name="qty[]" 
                                                           value="1" 
                                                           min="1" 
                                                           max="10"
                                                           class="w-16 px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="pt-4 border-t border-gray-200">
                            <!-- Payment Method Selection -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                                <div class="space-y-2">
                                    <label class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:border-blue-300 cursor-pointer">
                                        <input type="radio" name="payment_method" value="cash" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500" checked>
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-money-bill-wave text-green-500"></i>
                                            <span class="text-sm font-medium text-gray-900">Pay Cash (On Delivery)</span>
                                        </div>
                                    </label>
                                    <label class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:border-blue-300 cursor-pointer">
                                        <input type="radio" name="payment_method" value="mpesa" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-mobile-alt text-green-600"></i>
                                            <span class="text-sm font-medium text-gray-900">M-Pesa (Pay Now)</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            
                            <button type="submit" 
                                    class="w-full px-4 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-medium"
                                    <?= !$active ? 'disabled' : '' ?>>
                                <i class="fas fa-shopping-cart mr-2"></i>
                                <?= $active ? 'Place Order' : 'No Active Booking' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function refreshOrders() {
            location.reload();
        }

        function downloadReceipt(orderId) {
            window.open(`/receipt/download?order_id=${orderId}`, '_blank');
        }

        // Auto-refresh orders every 30 seconds
        setInterval(refreshOrders, 30000);
    </script>
</body>
</html>