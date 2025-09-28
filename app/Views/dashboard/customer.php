<?php
$title = 'Customer Dashboard - ' . ($settings['hotel_name'] ?? 'Hotel');
$user = currentUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <style>
        .dashboard-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .booking-card {
            border-left: 4px solid #3b82f6;
        }
        .booking-card.confirmed {
            border-left-color: #10b981;
        }
        .booking-card.pending {
            border-left-color: #f59e0b;
        }
        .booking-card.cancelled {
            border-left-color: #ef4444;
        }
        .fc-event {
            border-radius: 6px !important;
        }
        .fc-today {
            background-color: #eff6ff !important;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="flex items-center justify-between p-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                </div>
                <div>
                    <h1 class="font-semibold text-gray-900">Welcome, <?= htmlspecialchars($user['name']) ?></h1>
                    <p class="text-sm text-gray-500"><?= htmlspecialchars($settings['hotel_name'] ?? 'Hotel') ?> Dashboard</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <!-- Currency Selector -->
                <div class="relative">
                    <select id="currencySelector" onchange="changeCurrency(this.value)" 
                            class="appearance-none bg-white border border-gray-300 rounded-lg px-3 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="KES" <?= ($userCurrency ?? 'KES') === 'KES' ? 'selected' : '' ?>>KES</option>
                        <option value="USD" <?= ($userCurrency ?? 'KES') === 'USD' ? 'selected' : '' ?>>USD</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </div>
                </div>
                
                <a href="/chat" class="p-2 text-gray-600 hover:text-gray-800 rounded-full hover:bg-gray-100 relative">
                    <i class="fas fa-comments"></i>
                    <?php if (isset($unreadMessages) && $unreadMessages > 0): ?>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            <?= $unreadMessages ?>
                        </span>
                    <?php endif; ?>
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
        <!-- Hotel Information -->
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow-sm p-6 text-white mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-2"><?= htmlspecialchars($settings['hotel_name'] ?? 'Hotel') ?></h2>
                    <p class="text-blue-100 mb-4"><?= htmlspecialchars($settings['hotel_info'] ?? 'Welcome to our beautiful hotel') ?></p>
                    <?php if (isset($activeBooking) && $activeBooking): ?>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-wifi"></i>
                                <span class="text-sm">WiFi Password: <strong><?= htmlspecialchars($settings['hotel_password'] ?? 'HOTEL2024') ?></strong></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="text-right">
                    <div class="text-4xl mb-2">🏨</div>
                    <p class="text-blue-100">Your Home Away From Home</p>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="dashboard-card bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active Bookings</p>
                        <p class="text-2xl font-bold text-gray-900"><?= count(array_filter($bookings ?? [], fn($b) => in_array($b['status'], ['pending', 'confirmed']))) ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar-check text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="dashboard-card bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Nights</p>
                        <p class="text-2xl font-bold text-gray-900"><?= $totalNights ?></p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-moon text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="dashboard-card bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Spent</p>
                        <p class="text-2xl font-bold text-gray-900" id="totalSpent">
                            <?= number_format($totalSpent / 100, 2) ?> <?= htmlspecialchars($userCurrency ?? 'KES') ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-purple-600"></i>
                    </div>
                </div>
            </div>

            <div class="dashboard-card bg-white rounded-lg shadow-sm p-6 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Orders Placed</p>
                        <p class="text-2xl font-bold text-gray-900"><?= count($orders ?? []) ?></p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-utensils text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Calendar -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Booking Calendar</h3>
                    <div class="flex items-center space-x-2">
                        <button onclick="previousMonth()" class="p-2 text-gray-600 hover:text-gray-800 rounded-full hover:bg-gray-100">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button onclick="nextMonth()" class="p-2 text-gray-600 hover:text-gray-800 rounded-full hover:bg-gray-100">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <div id="calendar"></div>
            </div>

            <!-- Quick Actions -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="/booking/new" class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white">
                                <i class="fas fa-plus"></i>
                            </div>
                            <span class="font-medium text-gray-900">Book a Room</span>
                        </a>
                        
                        <a href="/menu" class="flex items-center space-x-3 p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                            <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white">
                                <i class="fas fa-utensils"></i>
                            </div>
                            <span class="font-medium text-gray-900">Order Food</span>
                        </a>
                        
                        <a href="/chat" class="flex items-center space-x-3 p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white">
                                <i class="fas fa-comments"></i>
                            </div>
                            <span class="font-medium text-gray-900">Contact Support</span>
                        </a>
                        
                        <a href="/reviews" class="flex items-center space-x-3 p-3 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                            <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center text-white">
                                <i class="fas fa-star"></i>
                            </div>
                            <span class="font-medium text-gray-900">Leave Review</span>
                        </a>
                    </div>
                </div>

                <!-- Active Booking -->
                <?php if (isset($activeBooking) && $activeBooking): ?>
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Stay</h3>
                        <div class="booking-card confirmed bg-white rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-900">Room <?= $activeBooking['room_number'] ?></h4>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    <?= ucfirst($activeBooking['status']) ?>
                                </span>
                            </div>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p><i class="fas fa-calendar mr-2"></i>Check-in: <?= date('M j, Y', strtotime($activeBooking['start_date'])) ?></p>
                                <p><i class="fas fa-calendar mr-2"></i>Check-out: <?= date('M j, Y', strtotime($activeBooking['end_date'])) ?></p>
                                <p><i class="fas fa-wifi mr-2"></i>WiFi: <?= htmlspecialchars($settings['hotel_password'] ?? 'HOTEL2024') ?></p>
                            </div>
                            <div class="mt-3 flex items-center space-x-2">
                                <a href="/chat" class="flex-1 bg-blue-500 text-white text-center py-2 px-3 rounded-lg text-sm hover:bg-blue-600 transition-colors">
                                    <i class="fas fa-comments mr-1"></i>Chat
                                </a>
                                <a href="/orders" class="flex-1 bg-orange-500 text-white text-center py-2 px-3 rounded-lg text-sm hover:bg-orange-600 transition-colors">
                                    <i class="fas fa-utensils mr-1"></i>Order
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="mt-8 bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Recent Bookings</h3>
                <a href="/booking/history" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
            </div>
            <div class="space-y-4">
                <?php foreach (array_slice($bookings ?? [], 0, 5) as $booking): ?>
                    <div class="booking-card <?= $booking['status'] ?> bg-white rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
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
                            <div class="flex items-center space-x-3">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    <?= $booking['status'] === 'confirmed' ? 'bg-green-100 text-green-800' : 
                                        ($booking['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>">
                                    <?= ucfirst($booking['status']) ?>
                                </span>
                                <?php if ($booking['status'] === 'confirmed' && $booking['payment_status'] === 'paid'): ?>
                                    <a href="/receipt/download?booking_id=<?= $booking['id'] ?>" 
                                       class="text-sm text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-download mr-1"></i>Receipt
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        let calendar;
        
        document.addEventListener('DOMContentLoaded', function() {
            initializeCalendar();
        });

        function initializeCalendar() {
            const calendarEl = document.getElementById('calendar');
            
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                height: 400,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: ''
                },
                events: [
                    <?php foreach ($bookings ?? [] as $booking): ?>
                        {
                            title: 'Room <?= $booking['room_number'] ?>',
                            start: '<?= $booking['start_date'] ?>',
                            end: '<?= date('Y-m-d', strtotime($booking['end_date'] . ' +1 day')) ?>',
                            backgroundColor: '<?= $booking['status'] === 'confirmed' ? '#10b981' : ($booking['status'] === 'pending' ? '#f59e0b' : '#ef4444') ?>',
                            borderColor: '<?= $booking['status'] === 'confirmed' ? '#10b981' : ($booking['status'] === 'pending' ? '#f59e0b' : '#ef4444') ?>',
                            textColor: 'white'
                        },
                    <?php endforeach; ?>
                ],
                eventClick: function(info) {
                    // Handle event click
                    console.log('Event clicked:', info.event.title);
                }
            });
            
            calendar.render();
        }

        function previousMonth() {
            calendar.prev();
        }

        function nextMonth() {
            calendar.next();
        }

        // Currency conversion functionality
        const exchangeRates = {
            'KES': 1.0,
            'USD': 0.0067 // 1 KES = 0.0067 USD (approximate rate)
        };

        function changeCurrency(newCurrency) {
            // Store user preference in localStorage
            localStorage.setItem('userCurrency', newCurrency);
            
            // Update the total spent display
            const totalSpentElement = document.getElementById('totalSpent');
            const currentText = totalSpentElement.textContent;
            const currentAmount = parseFloat(currentText.replace(/[^\d.]/g, ''));
            
            if (newCurrency === 'KES') {
                // Convert from USD to KES
                const kesAmount = currentAmount / exchangeRates.USD;
                totalSpentElement.textContent = `${kesAmount.toFixed(2)} KES`;
            } else {
                // Convert from KES to USD
                const usdAmount = currentAmount * exchangeRates.USD;
                totalSpentElement.textContent = `${usdAmount.toFixed(2)} USD`;
            }
            
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
</body>
</html>