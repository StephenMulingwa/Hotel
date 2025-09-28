<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-6xl mx-auto space-y-8">
        <!-- Customer Header Card -->
        <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-2xl shadow-xl overflow-hidden">
            <div class="p-8 text-white">
                <div class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-6">
                    <!-- Avatar -->
                    <div class="relative">
                        <div class="w-24 h-24 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm border-4 border-white border-opacity-30">
                            <i class="fas fa-user text-white text-4xl"></i>
                        </div>
                        <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-400 rounded-full border-4 border-white flex items-center justify-center">
                            <i class="fas fa-check text-white text-sm"></i>
                        </div>
                    </div>
                    
                    <!-- Customer Info -->
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold mb-2"><?= htmlspecialchars($customerData['name']) ?></h1>
                        <div class="flex flex-wrap items-center space-x-6 text-indigo-100">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-id-card"></i>
                                <span>ID: #<?= $customerData['id'] ?></span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-calendar-plus"></i>
                                <span>Member since <?= date('M j, Y', strtotime($customerData['created_at'])) ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="bg-white bg-opacity-10 rounded-xl p-4 backdrop-blur-sm">
                        <div class="text-center">
                            <div class="text-2xl font-bold"><?= $customerData['total_bookings'] ?></div>
                            <div class="text-sm text-indigo-100">Bookings</div>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Info -->
                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white bg-opacity-10 rounded-xl p-4 backdrop-blur-sm">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-phone text-white"></i>
                            </div>
                            <div>
                                <div class="text-sm text-indigo-100">Phone</div>
                                <div class="font-semibold"><?= htmlspecialchars($customerData['phone']) ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white bg-opacity-10 rounded-xl p-4 backdrop-blur-sm">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-envelope text-white"></i>
                            </div>
                            <div>
                                <div class="text-sm text-indigo-100">Email</div>
                                <div class="font-semibold"><?= htmlspecialchars($customerData['email']) ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white bg-opacity-10 rounded-xl p-4 backdrop-blur-sm">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-dollar-sign text-white"></i>
                            </div>
                            <div>
                                <div class="text-sm text-indigo-100">Total Spent</div>
                                <div class="font-bold text-lg"><?= number_format($customerData['total_spent'] / 100, 2) ?> KES</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-2">Total Bookings</p>
                        <p class="text-4xl font-bold text-blue-600"><?= $customerData['total_bookings'] ?></p>
                        <p class="text-sm text-gray-500 mt-1">All time bookings</p>
                    </div>
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-calendar-check text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-2">Total Spent</p>
                        <p class="text-4xl font-bold text-green-600"><?= number_format($customerData['total_spent'] / 100, 2) ?></p>
                        <p class="text-sm text-gray-500 mt-1">KES total</p>
                    </div>
                    <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-2">Reviews Given</p>
                        <p class="text-4xl font-bold text-purple-600"><?= count($customerReviews) ?></p>
                        <p class="text-sm text-gray-500 mt-1">Customer reviews</p>
                    </div>
                    <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-star text-purple-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Bookings -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-calendar-check text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Recent Bookings</h3>
                    </div>
                </div>
                <div class="p-6">
                    <?php if (empty($customerBookings)): ?>
                        <div class="text-center py-12">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-calendar-times text-3xl text-gray-400"></i>
                            </div>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">No bookings found</h4>
                            <p class="text-gray-500">This customer hasn't made any bookings yet</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach (array_slice($customerBookings, 0, 5) as $booking): ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                            <i class="fas fa-bed text-blue-600"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">Room <?= $booking['room_number'] ?></h4>
                                            <p class="text-sm text-gray-600">
                                                <?= date('M j, Y', strtotime($booking['start_date'])) ?> - 
                                                <?= date('M j, Y', strtotime($booking['end_date'])) ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                            <?= $booking['status'] === 'confirmed' ? 'bg-green-100 text-green-800' : 
                                                ($booking['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') ?>">
                                            <?= ucfirst($booking['status']) ?>
                                        </span>
                                        <?php if ($booking['amount_cents']): ?>
                                            <p class="text-sm font-bold text-gray-900 mt-1">
                                                <?= number_format($booking['amount_cents'] / 100, 2) ?> KES
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-orange-50 to-red-50 px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-utensils text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Recent Orders</h3>
                    </div>
                </div>
                <div class="p-6">
                    <?php if (empty($customerOrders)): ?>
                        <div class="text-center py-12">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-utensils text-3xl text-gray-400"></i>
                            </div>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">No orders found</h4>
                            <p class="text-gray-500">This customer hasn't placed any food orders yet</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach (array_slice($customerOrders, 0, 5) as $order): ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                                            <i class="fas fa-utensils text-orange-600"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">Order #<?= $order['id'] ?></h4>
                                            <p class="text-sm text-gray-600">
                                                Room <?= $order['room_number'] ?> • 
                                                <?= date('M j, Y g:i A', strtotime($order['created_at'])) ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                            <?= $order['status'] === 'delivered' ? 'bg-green-100 text-green-800' : 
                                                ($order['status'] === 'preparing' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') ?>">
                                            <?= ucfirst($order['status']) ?>
                                        </span>
                                        <p class="text-sm font-bold text-gray-900 mt-1">
                                            <?= number_format($order['total_cents'] / 100, 2) ?> KES
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-50 to-orange-50 px-6 py-4 border-b border-gray-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-yellow-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-star text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Customer Reviews</h3>
                </div>
            </div>
            <div class="p-6">
                <?php if (empty($customerReviews)): ?>
                    <div class="text-center py-12">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-star text-3xl text-gray-400"></i>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">No reviews found</h4>
                        <p class="text-gray-500">This customer hasn't left any reviews yet</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach (array_slice($customerReviews, 0, 5) as $review): ?>
                            <div class="p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="flex items-center space-x-1">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star text-lg <?= $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-gray-900 font-medium mb-2">
                                            <?= htmlspecialchars($review['comment'] ?: 'No comment') ?>
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            <?= date('M j, Y', strtotime($review['created_at'])) ?>
                                            <?php if ($review['room_number']): ?>
                                                • Room <?= $review['room_number'] ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
            <h3 class="text-xl font-bold text-gray-900 mb-6 text-center">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <button onclick="editCustomer(<?= $customerData['id'] ?>)" 
                        class="group flex items-center justify-center space-x-4 p-6 bg-gradient-to-r from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 rounded-2xl transition-all duration-300 hover:shadow-lg border border-blue-200">
                    <div class="w-14 h-14 bg-blue-500 group-hover:bg-blue-600 rounded-2xl flex items-center justify-center transition-colors">
                        <i class="fas fa-edit text-white text-xl"></i>
                    </div>
                    <div class="text-left">
                        <h4 class="font-bold text-gray-900 text-lg">Edit Customer</h4>
                        <p class="text-sm text-gray-600">Update customer information</p>
                    </div>
                </button>
                
                <button onclick="resetPassword(<?= $customerData['id'] ?>)" 
                        class="group flex items-center justify-center space-x-4 p-6 bg-gradient-to-r from-green-50 to-green-100 hover:from-green-100 hover:to-green-200 rounded-2xl transition-all duration-300 hover:shadow-lg border border-green-200">
                    <div class="w-14 h-14 bg-green-500 group-hover:bg-green-600 rounded-2xl flex items-center justify-center transition-colors">
                        <i class="fas fa-key text-white text-xl"></i>
                    </div>
                    <div class="text-left">
                        <h4 class="font-bold text-gray-900 text-lg">Reset Password</h4>
                        <p class="text-sm text-gray-600">Generate new password</p>
                    </div>
                </button>
                
                <button onclick="viewChat(<?= $customerData['id'] ?>)" 
                        class="group flex items-center justify-center space-x-4 p-6 bg-gradient-to-r from-purple-50 to-purple-100 hover:from-purple-100 hover:to-purple-200 rounded-2xl transition-all duration-300 hover:shadow-lg border border-purple-200">
                    <div class="w-14 h-14 bg-purple-500 group-hover:bg-purple-600 rounded-2xl flex items-center justify-center transition-colors">
                        <i class="fas fa-comment text-white text-xl"></i>
                    </div>
                    <div class="text-left">
                        <h4 class="font-bold text-gray-900 text-lg">View Chat</h4>
                        <p class="text-sm text-gray-600">See conversation history</p>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>