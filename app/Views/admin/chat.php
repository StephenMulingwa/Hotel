<?php
$title = 'Chat Overview - ' . ($settings['hotel_name'] ?? 'Hotel');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="flex items-center justify-between p-4">
            <div class="flex items-center space-x-3">
                <a href="/admin" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white font-semibold">
                    <i class="fas fa-comments"></i>
                </div>
                <div>
                    <h1 class="font-semibold text-gray-900">Chat Overview</h1>
                    <p class="text-sm text-gray-500">Monitor all hotel communications</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
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
        <!-- Chat Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Conversations</p>
                        <p class="text-2xl font-bold text-gray-900"><?= count($conversations) ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-comments text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active Today</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?= count(array_filter($conversations, fn($c) => date('Y-m-d', strtotime($c['last_message_at'])) === date('Y-m-d'))) ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-comment-dots text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Customer Chats</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?= count(array_filter($conversations, fn($c) => $c['other_user_role'] === 'customer')) ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-purple-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active Conversations</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?= count($conversations) ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-comments text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conversations List -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-900">All Conversations</h3>
                <p class="text-sm text-gray-500">Monitor all hotel communications in real-time</p>
            </div>
            
            <div class="divide-y divide-gray-200">
                <?php foreach ($conversations as $conversation): ?>
                    <div class="p-6 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
                                    <i class="fas fa-<?= $conversation['other_user_role'] === 'customer' ? 'user' : 'user-tie' ?> text-gray-600"></i>
                                </div>
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900">
                                        <?= htmlspecialchars($conversation['other_user_name']) ?>
                                    </h4>
                                    <p class="text-sm text-gray-500">
                                        <?= ucfirst($conversation['other_user_role']) ?> • 
                                        <?= $conversation['message_count'] ?> messages
                                        <?php if ($conversation['room_number']): ?>
                                            • Room <?= $conversation['room_number'] ?>
                                        <?php endif; ?>
                                        <?php if ($conversation['staff_role']): ?>
                                            • <?= ucfirst($conversation['staff_role']) ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="text-right">
                                    <p class="text-sm text-gray-500">
                                        <?= $conversation['last_message_at'] ? date('M j, Y g:i A', strtotime($conversation['last_message_at'])) : 'No messages' ?>
                                    </p>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        <?= $conversation['other_user_role'] === 'customer' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' ?>">
                                        <?= ucfirst($conversation['other_user_role']) ?>
                                    </span>
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="viewConversation(<?= $conversation['other_user_id'] ?>)" 
                                            class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                        <i class="fas fa-eye mr-2"></i>View Chat
                                    </button>
                                    <button onclick="joinConversation(<?= $conversation['other_user_id'] ?>)" 
                                            class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700">
                                        <i class="fas fa-comment mr-2"></i>Join Chat
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        function viewConversation(userId) {
            // Open chat in new window or redirect to chat page
            window.open(`/chat?user=${userId}`, '_blank');
        }

        function joinConversation(userId) {
            // Redirect to chat page with specific user
            window.location.href = `/chat?user=${userId}`;
        }
    </script>
</body>
</html>
