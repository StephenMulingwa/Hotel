<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Room - <?= htmlspecialchars($settings['hotel_name'] ?? 'Hotel') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .room-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .room-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .room-card.selected {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
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
                    <h1 class="font-semibold text-gray-900">Book a Room</h1>
                    <p class="text-sm text-gray-500"><?= htmlspecialchars($settings['hotel_name'] ?? 'Hotel') ?></p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <a href="/dashboard" class="p-2 text-gray-600 hover:text-gray-800 rounded-full hover:bg-gray-100">
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

    <div class="max-w-6xl mx-auto p-6">
        <?php if (!empty($error)): ?>
            <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6 border border-red-200">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Hotel Images Gallery -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Hotel Gallery</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <?php if (isset($hotelImages) && !empty($hotelImages)): ?>
                    <?php foreach ($hotelImages as $image): ?>
                        <div class="relative group">
                            <img src="<?= htmlspecialchars($image['image_url']) ?>" 
                                 alt="Hotel Image" 
                                 class="w-full h-48 object-cover rounded-lg">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded-lg"></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-3 text-center py-8">
                        <div class="text-gray-400">
                            <i class="fas fa-image text-4xl mb-4"></i>
                            <p>No hotel images available</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Room Selection -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Available Rooms</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php if (isset($rooms) && !empty($rooms)): ?>
                            <?php foreach ($rooms as $room): ?>
                                <div class="room-card border-2 border-gray-200 rounded-lg p-4 cursor-pointer" 
                                     data-room-id="<?= $room['id'] ?>"
                                     onclick="selectRoom(<?= $room['id'] ?>, '<?= htmlspecialchars($room['type']) ?>', <?= $room['price_cents'] ?>)">
                                <div class="relative mb-3">
                                    <?php if (!empty($room['image_url'])): ?>
                                        <img src="<?= htmlspecialchars($room['image_url']) ?>" 
                                             alt="Room <?= $room['number'] ?>" 
                                             class="w-full h-32 object-cover rounded-lg">
                                    <?php else: ?>
                                        <div class="w-full h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-bed text-gray-400 text-2xl"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="absolute top-2 right-2 bg-white rounded-full px-2 py-1 text-xs font-semibold">
                                        Room <?= $room['number'] ?>
                                    </div>
                                </div>
                                <h3 class="font-semibold text-gray-900 capitalize"><?= htmlspecialchars($room['type']) ?> Room</h3>
                                <p class="text-sm text-gray-600 mb-2">Comfortable and well-equipped</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-lg font-bold text-blue-600">
                                        <?= money($room['price_cents'], $currency) ?>/night
                                    </span>
                                    <div class="flex items-center space-x-1 text-yellow-500">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-span-2 text-center py-8">
                                <div class="text-gray-400">
                                    <i class="fas fa-bed text-4xl mb-4"></i>
                                    <p>No rooms available</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Booking Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Booking Details</h3>
                    <form method="post" action="/booking" class="space-y-4">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="room_id" id="selected_room_id" required>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input name="name" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                   value="<?php echo htmlspecialchars(currentUser()['name']); ?>" 
                                   required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input name="phone" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                   value="<?php echo htmlspecialchars(currentUser()['phone']); ?>" 
                                   required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Check-in Date</label>
                            <input type="date" 
                                   name="start_date" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                   value="<?php echo htmlspecialchars(today()); ?>" 
                                   required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Check-out Date</label>
                            <input type="date" 
                                   name="end_date" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                   required>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-gray-600">Selected Room:</span>
                                <span id="selected_room_info" class="text-sm font-medium text-gray-900">None selected</span>
                            </div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-gray-600">Price per night:</span>
                                <span id="room_price" class="text-sm font-medium text-gray-900">-</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Total nights:</span>
                                <span id="total_nights" class="text-sm font-medium text-gray-900">0</span>
                            </div>
                            <div class="border-t pt-2 mt-2">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-gray-900">Total Amount:</span>
                                    <span id="total_amount" class="font-bold text-blue-600">-</span>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" 
                                class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-medium">
                            <i class="fas fa-credit-card mr-2"></i>Continue to Payment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedRoom = null;
        let roomPrices = <?= json_encode(isset($rooms) && !empty($rooms) ? array_column($rooms, 'price_cents', 'id') : []) ?>;

        function selectRoom(roomId, roomType, priceCents) {
            // Remove previous selection
            document.querySelectorAll('.room-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selection to clicked room
            event.currentTarget.classList.add('selected');
            
            selectedRoom = { id: roomId, type: roomType, price: priceCents };
            document.getElementById('selected_room_id').value = roomId;
            document.getElementById('selected_room_info').textContent = roomType + ' Room';
            document.getElementById('room_price').textContent = formatMoney(priceCents);
            
            calculateTotal();
        }

        function calculateTotal() {
            const startDate = document.querySelector('input[name="start_date"]').value;
            const endDate = document.querySelector('input[name="end_date"]').value;
            
            if (startDate && endDate && selectedRoom) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                const nights = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
                
                if (nights > 0) {
                    document.getElementById('total_nights').textContent = nights;
                    const total = nights * selectedRoom.price;
                    document.getElementById('total_amount').textContent = formatMoney(total);
                } else {
                    document.getElementById('total_nights').textContent = '0';
                    document.getElementById('total_amount').textContent = '-';
                }
            }
        }

        function formatMoney(cents) {
            return '<?= $currency ?? 'KES' ?> ' + (cents / 100).toFixed(2);
        }

        // Add event listeners for date changes
        document.querySelector('input[name="start_date"]').addEventListener('change', calculateTotal);
        document.querySelector('input[name="end_date"]').addEventListener('change', calculateTotal);
    </script>
</body>
</html>