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
