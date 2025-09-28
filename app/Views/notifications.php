<?php
$title = 'Notifications - ' . ($settings['hotel_name'] ?? 'Hotel');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .notification-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .notification-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .notification-unread {
            border-left: 4px solid #3b82f6;
        }
        .notification-read {
            opacity: 0.7;
        }
        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="flex items-center justify-between p-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                    <i class="fas fa-bell"></i>
                </div>
                <div>
                    <h1 class="font-semibold text-gray-900">Notifications</h1>
                    <p class="text-sm text-gray-500">Stay updated with hotel activities</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="markAllAsRead()" class="px-4 py-2 text-sm bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                    <i class="fas fa-check-double mr-2"></i>Mark All Read
                </button>
                <a href="/dashboard" class="p-2 text-gray-600 hover:text-gray-800 rounded-full hover:bg-gray-100">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="p-6">
        <!-- Notification Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Notifications</p>
                        <p class="text-2xl font-bold text-gray-900"><?= count($notifications) ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-bell text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Unread</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?= count(array_filter($notifications, fn($n) => !$n['is_read'])) ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-envelope text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Today</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?= count(array_filter($notifications, fn($n) => date('Y-m-d', strtotime($n['created_at'])) === date('Y-m-d'))) ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar-day text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Options -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Filter:</label>
                    <select id="filterSelect" class="px-3 py-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="all">All Notifications</option>
                        <option value="unread">Unread Only</option>
                        <option value="read">Read Only</option>
                        <option value="today">Today</option>
                    </select>
                </div>
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Type:</label>
                    <select id="typeSelect" class="px-3 py-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="all">All Types</option>
                        <option value="booking">Bookings</option>
                        <option value="payment">Payments</option>
                        <option value="order">Orders</option>
                        <option value="chat">Messages</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div id="notificationsList" class="space-y-4">
            <?php if (empty($notifications)): ?>
                <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                    <i class="fas fa-bell-slash text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Notifications</h3>
                    <p class="text-gray-600">You're all caught up! Check back later for updates.</p>
                </div>
            <?php else: ?>
                <?php foreach ($notifications as $notification): ?>
                    <div class="notification-card bg-white rounded-lg shadow-sm p-4 border border-gray-200 <?= $notification['is_read'] ? 'notification-read' : 'notification-unread' ?>"
                         data-id="<?= $notification['id'] ?>" data-type="<?= strtolower($notification['title']) ?>">
                        <div class="flex items-start space-x-4">
                            <div class="notification-icon <?= $this->getNotificationIconClass($notification['title']) ?>">
                                <i class="<?= $this->getNotificationIcon($notification['title']) ?>"></i>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="font-semibold text-gray-900 <?= $notification['is_read'] ? '' : 'font-bold' ?>">
                                        <?= htmlspecialchars($notification['title']) ?>
                                    </h3>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs text-gray-500">
                                            <?= date('M j, Y H:i', strtotime($notification['created_at'])) ?>
                                        </span>
                                        <?php if (!$notification['is_read']): ?>
                                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <p class="text-gray-600 mb-3"><?= htmlspecialchars($notification['message']) ?></p>
                                
                                <div class="flex items-center space-x-3">
                                    <?php if (!$notification['is_read']): ?>
                                        <button onclick="markAsRead(<?= $notification['id'] ?>)"
                                                class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                            <i class="fas fa-check mr-1"></i>Mark as Read
                                        </button>
                                    <?php endif; ?>
                                    
                                    <button onclick="deleteNotification(<?= $notification['id'] ?>)"
                                            class="text-sm text-red-600 hover:text-red-800 font-medium">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Load More Button -->
        <?php if (count($notifications) > 20): ?>
            <div class="text-center mt-8">
                <button class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    Load More Notifications
                </button>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Filter functionality
        document.getElementById('filterSelect').addEventListener('change', filterNotifications);
        document.getElementById('typeSelect').addEventListener('change', filterNotifications);

        function filterNotifications() {
            const filterValue = document.getElementById('filterSelect').value;
            const typeValue = document.getElementById('typeSelect').value;
            const notifications = document.querySelectorAll('.notification-card');
            
            notifications.forEach(notification => {
                const isRead = notification.classList.contains('notification-read');
                const notificationType = notification.dataset.type;
                const isToday = isTodayNotification(notification);
                
                let show = true;
                
                // Apply filter
                if (filterValue === 'unread' && isRead) show = false;
                if (filterValue === 'read' && !isRead) show = false;
                if (filterValue === 'today' && !isToday) show = false;
                
                // Apply type filter
                if (typeValue !== 'all' && !notificationType.includes(typeValue)) show = false;
                
                notification.style.display = show ? 'block' : 'none';
            });
        }

        function isTodayNotification(notification) {
            const dateText = notification.querySelector('.text-xs').textContent;
            const today = new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            return dateText.includes(today);
        }

        function markAsRead(notificationId) {
            fetch('/notifications/mark-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    notification_id: notificationId,
                    _token: '<?= csrf_token() ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const notification = document.querySelector(`[data-id="${notificationId}"]`);
                    notification.classList.remove('notification-unread');
                    notification.classList.add('notification-read');
                    notification.querySelector('.font-bold').classList.remove('font-bold');
                    notification.querySelector('.bg-blue-500').remove();
                    location.reload();
                }
            });
        }

        function markAllAsRead() {
            if (confirm('Mark all notifications as read?')) {
                fetch('/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        _token: '<?= csrf_token() ?>'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            }
        }

        function deleteNotification(notificationId) {
            if (confirm('Delete this notification?')) {
                fetch('/notifications/delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        notification_id: notificationId,
                        _token: '<?= csrf_token() ?>'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector(`[data-id="${notificationId}"]`).remove();
                    }
                });
            }
        }
    </script>
</body>
</html>
