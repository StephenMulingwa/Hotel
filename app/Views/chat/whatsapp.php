<?php
$user = currentUser();
$isCustomer = $user['role'] === 'customer';
?>

<style>
        .chat-container {
            height: calc(100vh - 80px);
        }
        .message-bubble {
            max-width: 70%;
            word-wrap: break-word;
        }
        .typing-indicator {
            display: none;
        }
        .typing-indicator.show {
            display: block;
        }
        .unread-badge {
            background: #25D366;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            min-width: 18px;
            text-align: center;
        }
        .online-indicator {
            width: 8px;
            height: 8px;
            background: #25D366;
            border-radius: 50%;
            position: absolute;
            bottom: 0;
            right: 0;
            border: 2px solid white;
        }
    </style>

    <div class="flex chat-container">
        <!-- Sidebar - Conversations List -->
        <div class="w-1/3 bg-white border-r border-gray-200">
            <div class="p-4 border-b border-gray-200">
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Search conversations..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>
            
            <div id="conversationsList" class="overflow-y-auto">
                <?php if (empty($conversations)): ?>
                    <div class="p-4 text-center text-gray-500">
                        <i class="fas fa-comments text-4xl mb-2"></i>
                        <p>No conversations yet</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($conversations as $conv): ?>
                        <div class="conversation-item p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors" 
                             data-conversation-id="<?= $conv['id'] ?>" 
                             data-target="<?= htmlspecialchars($conv['to_role'] ?? $conv['from_user_id']) ?>">
                            <div class="flex items-center space-x-3">
                                <div class="relative">
                                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center text-white font-semibold">
                                        <?php if ($isCustomer): ?>
                                            <i class="fas fa-headset"></i>
                                        <?php else: ?>
                                            <?= strtoupper(substr($conv['customer_name'] ?? 'C', 0, 1)) ?>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (isset($conv['unread_count']) && $conv['unread_count'] > 0): ?>
                                        <span class="unread-badge absolute -top-1 -right-1"><?= $conv['unread_count'] ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-semibold text-gray-900 truncate">
                                            <?php if ($isCustomer): ?>
                                                <?= htmlspecialchars($conv['role_name'] ?? 'Staff') ?>
                                            <?php else: ?>
                                                <?= htmlspecialchars($conv['customer_name'] ?? 'Customer') ?>
                                            <?php endif; ?>
                                        </h3>
                                        <span class="text-xs text-gray-500">
                                            <?= date('H:i', strtotime($conv['last_message_time'] ?? 'now')) ?>
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm text-gray-600 truncate">
                                            <?= htmlspecialchars($conv['last_message'] ?? 'No messages yet') ?>
                                        </p>
                                        <?php if (isset($conv['room_number'])): ?>
                                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                                Room <?= $conv['room_number'] ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="flex-1 flex flex-col">
            <!-- Chat Header -->
            <div id="chatHeader" class="bg-white border-b border-gray-200 p-4 hidden">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white font-semibold">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h2 id="chatTitle" class="font-semibold text-gray-900">Select a conversation</h2>
                        <p id="chatSubtitle" class="text-sm text-gray-500">Start chatting</p>
                    </div>
                    <div class="ml-auto">
                        <span class="online-indicator"></span>
                    </div>
                </div>
            </div>

            <!-- Messages Area -->
            <div id="messagesContainer" class="flex-1 overflow-y-auto p-4 bg-gray-50">
                <div id="welcomeMessage" class="text-center text-gray-500 py-8">
                    <i class="fas fa-comments text-4xl mb-4"></i>
                    <h3 class="text-lg font-semibold mb-2">Welcome to Hotel Chat</h3>
                    <p>Select a conversation from the sidebar to start chatting</p>
                </div>
                <div id="messagesList" class="space-y-4 hidden">
                    <!-- Messages will be loaded here -->
                </div>
            </div>

            <!-- Message Input -->
            <div id="messageInput" class="bg-white border-t border-gray-200 p-4 hidden">
                <form id="messageForm" class="flex items-center space-x-3">
                    <input type="hidden" id="targetId" name="target_id">
                    <input type="hidden" id="targetType" name="target_type" value="role">
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    
                    <div class="flex-1 relative">
                        <input type="text" id="messageInput" name="body" placeholder="Type a message..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-full focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                               autocomplete="off">
                        <button type="button" id="emojiBtn" class="absolute right-3 top-2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-smile"></i>
                        </button>
                    </div>
                    <button type="submit" class="bg-green-500 text-white p-2 rounded-full hover:bg-green-600 transition-colors">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Typing Indicator -->
    <div id="typingIndicator" class="typing-indicator fixed bottom-20 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white px-4 py-2 rounded-full">
        <div class="flex items-center space-x-2">
            <div class="flex space-x-1">
                <div class="w-2 h-2 bg-white rounded-full animate-bounce"></div>
                <div class="w-2 h-2 bg-white rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-2 h-2 bg-white rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
            <span class="text-sm">Someone is typing...</span>
        </div>
    </div>

    <script>
        let currentTarget = null;
        let currentConversationId = null;
        let lastMessageId = 0;
        let isTyping = false;
        let typingTimeout = null;

        // Initialize chat
        document.addEventListener('DOMContentLoaded', function() {
            initializeChat();
            setupEventListeners();
            startPolling();
        });

        function initializeChat() {
            // Auto-select first conversation if available
            const firstConv = document.querySelector('.conversation-item');
            if (firstConv) {
                firstConv.click();
            }
        }

        function setupEventListeners() {
            // Conversation selection
            document.querySelectorAll('.conversation-item').forEach(item => {
                item.addEventListener('click', function() {
                    const target = this.dataset.target;
                    selectConversation(target, this);
                });
            });

            // Message form submission
            document.getElementById('messageForm').addEventListener('submit', function(e) {
                e.preventDefault();
                sendMessage();
            });

            // Typing indicator
            const messageInput = document.getElementById('messageInput');
            messageInput.addEventListener('input', function() {
                if (!isTyping) {
                    isTyping = true;
                    showTypingIndicator();
                }
                clearTimeout(typingTimeout);
                typingTimeout = setTimeout(() => {
                    isTyping = false;
                    hideTypingIndicator();
                }, 1000);
            });

            // Refresh button
            document.getElementById('refreshBtn').addEventListener('click', function() {
                location.reload();
            });
        }

        function selectConversation(target, element) {
            currentTarget = target;
            currentConversationId = element.dataset.conversationId;
            
            // Update UI
            document.querySelectorAll('.conversation-item').forEach(item => {
                item.classList.remove('bg-green-50', 'border-l-4', 'border-green-500');
            });
            element.classList.add('bg-green-50', 'border-l-4', 'border-green-500');

            // Show chat interface
            document.getElementById('chatHeader').classList.remove('hidden');
            document.getElementById('messageInput').classList.remove('hidden');
            document.getElementById('welcomeMessage').classList.add('hidden');
            document.getElementById('messagesList').classList.remove('hidden');

            // Update chat header
            const customerName = element.querySelector('h3').textContent;
            const roomNumber = element.querySelector('.text-blue-800')?.textContent || '';
            
            document.getElementById('chatTitle').textContent = customerName;
            document.getElementById('chatSubtitle').textContent = roomNumber || 'Active conversation';

            // Set form targets
            document.getElementById('targetId').value = target;
            document.getElementById('targetType').value = '<?= $isCustomer ? "role" : "user" ?>';

            // Load messages
            loadMessages();
        }

        function loadMessages() {
            if (!currentConversationId) return;

            fetch(`/chat/messages?conversation_id=${currentConversationId}&since=${lastMessageId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.messages && data.messages.length > 0) {
                        data.messages.forEach(message => {
                            addMessageToChat(message);
                        });
                        lastMessageId = Math.max(...data.messages.map(m => m.id));
                    }
                })
                .catch(error => console.error('Error loading messages:', error));
        }

        function addMessageToChat(message) {
            const messagesList = document.getElementById('messagesList');
            const messageDiv = document.createElement('div');
            
            const isFromCurrentUser = message.from_user_id == <?= $user['id'] ?>;
            const messageClass = isFromCurrentUser ? 'ml-auto' : 'mr-auto';
            const bubbleClass = isFromCurrentUser ? 'bg-green-500 text-white' : 'bg-white text-gray-900';
            
            messageDiv.className = `message-bubble ${messageClass} ${bubbleClass} p-3 rounded-lg shadow-sm`;
            messageDiv.innerHTML = `
                <div class="message-content">${escapeHtml(message.message)}</div>
                <div class="message-time text-xs mt-1 ${isFromCurrentUser ? 'text-green-100' : 'text-gray-500'}">
                    ${formatTime(message.created_at)}
                </div>
            `;
            
            messagesList.appendChild(messageDiv);
            messagesList.scrollTop = messagesList.scrollHeight;
        }

        function sendMessage() {
            const form = document.getElementById('messageForm');
            const formData = new FormData(form);
            const messageText = formData.get('body').trim();
            
            if (!messageText || !currentTarget) return;

            // Add message to UI immediately
            const tempMessage = {
                id: 'temp_' + Date.now(),
                from_user_id: <?= $user['id'] ?>,
                message: messageText,
                created_at: new Date().toISOString()
            };
            addMessageToChat(tempMessage);

            // Clear input
            form.reset();

            // Send to server
            fetch('/chat/send', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove temp message and add real one
                    const tempMsg = document.querySelector(`[data-temp-id="temp_${tempMessage.id}"]`);
                    if (tempMsg) tempMsg.remove();
                } else {
                    console.error('Failed to send message:', data.message);
                }
            })
            .catch(error => console.error('Error sending message:', error));
        }

        function startPolling() {
            setInterval(() => {
                if (currentTarget) {
                    loadMessages();
                }
            }, 3000);
        }

        function showTypingIndicator() {
            document.getElementById('typingIndicator').classList.add('show');
        }

        function hideTypingIndicator() {
            document.getElementById('typingIndicator').classList.remove('show');
        }

        function formatTime(timestamp) {
            const date = new Date(timestamp);
            return date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>