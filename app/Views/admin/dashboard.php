<style>
	.chart-container { position: relative; height: 300px; }
	.stat-card { transition: transform 0.2s ease-in-out; }
	.stat-card:hover { transform: translateY(-2px); }
</style>
<div class="p-6">
        <!-- Quick Access Menu -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Admin Quick Access</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <a href="/admin/customers" class="flex items-center space-x-3 p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Customers</p>
                        <p class="text-sm text-gray-500">Manage customers</p>
                    </div>
                </a>
                <a href="/admin/chat" class="flex items-center space-x-3 p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Chat Overview</p>
                        <p class="text-sm text-gray-500">Monitor chats</p>
                    </div>
                </a>
                <a href="/admin/kitchen" class="flex items-center space-x-3 p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                    <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center text-white">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Kitchen Overview</p>
                        <p class="text-sm text-gray-500">Live kitchen monitoring</p>
                    </div>
                </a>
                <a href="/admin/settings" class="flex items-center space-x-3 p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                    <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center text-white">
                        <i class="fas fa-cog"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Hotel Settings</p>
                        <p class="text-sm text-gray-500">Configure hotel</p>
                    </div>
                </a>
                <a href="/admin/users" class="flex items-center space-x-3 p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                    <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center text-white">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Staff Management</p>
                        <p class="text-sm text-gray-500">Manage staff</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Revenue -->
            <div class="stat-card bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?= number_format(($roomStats['revenue_month'] + $foodStats['food_revenue_today']) / 100, 2) ?>
                            <?= htmlspecialchars($currency) ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-green-600"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-green-600">
                        <i class="fas fa-arrow-up"></i> 12.5%
                    </span>
                    <span class="text-sm text-gray-500 ml-2">vs last month</span>
                </div>
            </div>

            <!-- Occupied Rooms -->
            <div class="stat-card bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Occupied Rooms</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?= $roomStats['occupied_today'] ?> / <?= $roomStats['total_rooms'] ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-bed text-blue-600"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: <?= ($roomStats['occupied_today'] / $roomStats['total_rooms']) * 100 ?>%"></div>
                    </div>
                </div>
            </div>

            <!-- Total Bookings -->
            <div class="stat-card bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Bookings</p>
                        <p class="text-2xl font-bold text-gray-900"><?= $roomStats['total_bookings'] ?></p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar-check text-purple-600"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-purple-600">
                        <i class="fas fa-arrow-up"></i> 8.2%
                    </span>
                    <span class="text-sm text-gray-500 ml-2">vs last month</span>
                </div>
            </div>

            <!-- Food Orders -->
            <div class="stat-card bg-white rounded-lg shadow-sm p-6 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Food Orders Today</p>
                        <p class="text-2xl font-bold text-gray-900"><?= $foodStats['orders_today'] ?></p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-utensils text-orange-600"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-orange-600">
                        <i class="fas fa-arrow-up"></i> 15.3%
                    </span>
                    <span class="text-sm text-gray-500 ml-2">vs yesterday</span>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Revenue Chart -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Revenue Trends</h3>
                    <div class="flex space-x-2">
                        <button class="period-btn px-3 py-1 text-sm bg-blue-100 text-blue-800 rounded-full" data-period="week">Week</button>
                        <button class="period-btn px-3 py-1 text-sm text-gray-600 hover:bg-gray-100 rounded-full" data-period="month">Month</button>
                        <button class="period-btn px-3 py-1 text-sm text-gray-600 hover:bg-gray-100 rounded-full" data-period="year">Year</button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Occupancy Chart -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Room Occupancy</h3>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">Occupied</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                            <span class="text-sm text-gray-600">Available</span>
                        </div>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="occupancyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Bookings -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Bookings</h3>
                    <a href="/reception" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                </div>
                <div class="space-y-3">
                    <?php foreach (array_slice($recentBookings, 0, 5) as $booking): ?>
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-bed text-blue-600 text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    <?= htmlspecialchars($booking['customer_name']) ?>
                                </p>
                                <p class="text-xs text-gray-500">
                                    Room <?= $booking['room_number'] ?> • 
                                    <?= date('M j, Y', strtotime($booking['start_date'])) ?>
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    <?= $booking['status'] === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                    <?= ucfirst($booking['status']) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Orders</h3>
                    <a href="/kitchen" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                </div>
                <div class="space-y-3">
                    <?php foreach (array_slice($recentOrders, 0, 5) as $order): ?>
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-utensils text-orange-600 text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    <?= htmlspecialchars($order['customer_name']) ?>
                                </p>
                                <p class="text-xs text-gray-500">
                                    Room <?= $order['room_number'] ?> • 
                                    <?= number_format($order['total_cents'] / 100, 2) ?> <?= htmlspecialchars($currency) ?>
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    <?= $order['status'] === 'delivered' ? 'bg-green-100 text-green-800' : 
                                        ($order['status'] === 'preparing' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Recent Reviews -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Reviews</h3>
                    <a href="/reviews" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                </div>
                <div class="space-y-3">
                    <?php foreach (array_slice($recentReviews, 0, 5) as $review): ?>
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-star text-yellow-600 text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-1 mb-1">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star text-xs <?= $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    <?= htmlspecialchars($review['customer_name']) ?>
                                </p>
                                <p class="text-xs text-gray-500 truncate">
                                    <?= htmlspecialchars($review['comment'] ?: 'No comment') ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart.js configuration
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                }
            }
        };

        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Revenue',
                    data: [12000, 19000, 15000, 25000, 22000, 30000, 28000],
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: chartOptions
        });

        // Occupancy Chart
        const occupancyCtx = document.getElementById('occupancyChart').getContext('2d');
        const occupancyChart = new Chart(occupancyCtx, {
            type: 'doughnut',
            data: {
                labels: ['Occupied', 'Available'],
                datasets: [{
                    data: [<?= $roomStats['occupied_today'] ?>, <?= $roomStats['total_rooms'] - $roomStats['occupied_today'] ?>],
                    backgroundColor: ['rgb(59, 130, 246)', 'rgb(209, 213, 219)'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Period buttons
        document.querySelectorAll('.period-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.period-btn').forEach(b => {
                    b.classList.remove('bg-blue-100', 'text-blue-800');
                    b.classList.add('text-gray-600', 'hover:bg-gray-100');
                });
                this.classList.add('bg-blue-100', 'text-blue-800');
                this.classList.remove('text-gray-600', 'hover:bg-gray-100');
                
                // Update chart data based on period
                updateChartData(this.dataset.period);
            });
        });

        function updateChartData(period) {
            // This would typically fetch new data from the server
            console.log('Updating chart for period:', period);
        }
    </script>