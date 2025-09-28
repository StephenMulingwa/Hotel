<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Payment - <?= htmlspecialchars($settings['hotel_name'] ?? 'Hotel') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .payment-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .payment-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="flex items-center justify-between p-4">
            <div class="flex items-center space-x-3">
                <a href="/orders" class="p-2 text-gray-600 hover:text-gray-800 rounded-full hover:bg-gray-100">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="font-semibold text-gray-900">Order Payment</h1>
                    <p class="text-sm text-gray-500">Complete your order payment</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Order #</span>
                        <span class="font-semibold text-gray-900"><?= $order['id'] ?></span>
                    </div>
                    
                    <?php if ($order['room_number']): ?>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Room</span>
                        <span class="font-semibold text-gray-900"><?= $order['room_number'] ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Payment Method</span>
                        <span class="font-semibold text-gray-900"><?= ucfirst($order['payment_method']) ?></span>
                    </div>
                </div>
                
                <!-- Order Items -->
                <div class="mt-6">
                    <h4 class="font-medium text-gray-900 mb-3">Order Items</h4>
                    <div class="space-y-3">
                        <?php foreach ($orderItems as $item): ?>
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                <div class="w-12 h-12 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
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
                                    <p class="text-sm text-gray-600">Qty: <?= $item['quantity'] ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900">
                                        <?= money($item['price_cents'] * $item['quantity'], $currency) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Total -->
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="text-lg font-semibold text-gray-900">Total Amount</span>
                        <span class="text-2xl font-bold text-blue-600">
                            <?= money($order['total_cents'], $currency) ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Payment Options -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Options</h3>
                
                <?php if ($order['payment_method'] === 'mpesa'): ?>
                    <!-- M-Pesa Payment -->
                    <div class="payment-card border-2 border-green-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-mobile-alt text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">M-Pesa Payment</h4>
                                <p class="text-sm text-gray-600">Pay via M-Pesa mobile money</p>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 rounded-lg p-4 mb-4">
                            <div class="text-center">
                                <p class="text-sm text-green-800 font-medium mb-2">Send Money to:</p>
                                <p class="text-2xl font-bold text-green-900">+254 700 000 000</p>
                                <p class="text-sm text-green-700">Amount: <?= money($order['total_cents'], $currency) ?></p>
                            </div>
                        </div>
                        
                        <form method="post" action="/orders/process-payment" class="space-y-4">
                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <input type="hidden" name="payment_method" value="mpesa">
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">M-Pesa Transaction Code</label>
                                <input type="text" 
                                       name="mpesa_code" 
                                       placeholder="Enter M-Pesa transaction code"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                       required>
                                <p class="text-xs text-gray-500 mt-1">Enter the transaction code you received via SMS</p>
                            </div>
                            
                            <button type="submit" 
                                    class="w-full px-4 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors font-medium">
                                <i class="fas fa-mobile-alt mr-2"></i>Complete M-Pesa Payment
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <!-- Cash Payment -->
                    <div class="payment-card border-2 border-blue-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-money-bill-wave text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Cash on Delivery</h4>
                                <p class="text-sm text-gray-600">Pay when your order is delivered</p>
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 rounded-lg p-4 mb-4">
                            <div class="text-center">
                                <p class="text-sm text-blue-800 font-medium mb-2">Total Amount to Pay:</p>
                                <p class="text-2xl font-bold text-blue-900"><?= money($order['total_cents'], $currency) ?></p>
                                <p class="text-sm text-blue-700">Pay when your order arrives</p>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <p class="text-sm text-gray-600 mb-4">Your order will be prepared and delivered to your room. Please have the exact amount ready.</p>
                            <a href="/orders" 
                               class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                                <i class="fas fa-check mr-2"></i>Confirm Cash Payment
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Payment Security -->
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                        <i class="fas fa-shield-alt text-green-500"></i>
                        <span>Your payment information is secure and encrypted</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-focus on M-Pesa code input
        document.addEventListener('DOMContentLoaded', function() {
            const mpesaInput = document.querySelector('input[name="mpesa_code"]');
            if (mpesaInput) {
                mpesaInput.focus();
            }
        });
    </script>
</body>
</html>
