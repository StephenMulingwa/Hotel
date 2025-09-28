<?php
$title = 'Kitchen Management - ' . ($settings['hotel_name'] ?? 'Hotel');
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
        .menu-item-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .menu-item-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .out-of-stock {
            opacity: 0.6;
            position: relative;
        }
        .out-of-stock::after {
            content: 'OUT OF STOCK';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-15deg);
            background: rgba(239, 68, 68, 0.9);
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 14px;
        }
        .image-upload-area {
            border: 2px dashed #d1d5db;
            transition: border-color 0.2s ease-in-out;
        }
        .image-upload-area:hover {
            border-color: #3b82f6;
        }
        .image-upload-area.dragover {
            border-color: #3b82f6;
            background-color: #eff6ff;
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
                <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center text-white font-semibold">
                    <i class="fas fa-utensils"></i>
                </div>
                <div>
                    <h1 class="font-semibold text-gray-900">Kitchen Management</h1>
                    <p class="text-sm text-gray-500">Room Service Menu Management</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button id="addItemBtn" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add Item
                </button>
                <a href="/kitchen" class="p-2 text-gray-600 hover:text-gray-800 rounded-full hover:bg-gray-100">
                    <i class="fas fa-list"></i>
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
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Menu Items</p>
                        <p class="text-2xl font-bold text-gray-900"><?= count($menuItems) ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-list text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">In Stock</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?= count(array_filter($menuItems, fn($item) => $item['in_stock'])) ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Out of Stock</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?= count(array_filter($menuItems, fn($item) => !$item['in_stock'])) ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?= number_format(array_sum(array_column($menuItems, 'price_cents')) / 100, 0) ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Management -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Active Orders</h2>
                <div class="flex items-center space-x-2">
                    <button onclick="refreshOrders()" class="p-2 text-gray-600 hover:text-gray-800 rounded-full hover:bg-gray-100">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
            
            <div id="ordersList" class="space-y-4">
                <?php if (empty($orders)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No Active Orders</h3>
                        <p class="text-gray-600">Orders will appear here when customers place them</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card border border-gray-200 rounded-lg p-4">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-receipt text-orange-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">Order #<?= $order['id'] ?></h4>
                                        <p class="text-sm text-gray-600">
                                            <?= date('M j, Y \a\t g:i A', strtotime($order['created_at'])) ?>
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Customer: <?= htmlspecialchars($order['customer_name']) ?>
                                            <?php if ($order['room_number']): ?>
                                                • Room <?= $order['room_number'] ?>
                                            <?php endif; ?>
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
                            
                            <!-- Order Items -->
                            <div class="space-y-2 mb-4">
                                <?php foreach (($orderItems[$order['id']] ?? []) as $item): ?>
                                    <div class="flex items-center space-x-3 p-2 bg-gray-50 rounded">
                                        <div class="w-8 h-8 bg-gray-200 rounded overflow-hidden flex-shrink-0">
                                            <?php if (!empty($item['image_url'])): ?>
                                                <img src="<?= htmlspecialchars($item['image_url']) ?>" 
                                                     alt="<?= htmlspecialchars($item['name']) ?>" 
                                                     class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <i class="fas fa-utensils text-gray-400 text-xs"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-1">
                                            <span class="font-medium text-gray-900"><?= htmlspecialchars($item['name']) ?></span>
                                            <span class="text-sm text-gray-600">(Qty: <?= $item['quantity'] ?>)</span>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-900">
                                            <?= money($item['price_cents'] * $item['quantity'], $currency) ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Order Actions -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-600">Payment:</span>
                                    <span class="text-sm font-medium text-gray-900"><?= ucfirst($order['payment_method']) ?></span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <?php if ($order['status'] === 'pending'): ?>
                                        <button onclick="updateOrderStatus(<?= $order['id'] ?>, 'preparing')" 
                                                class="px-3 py-1 text-sm bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                                            <i class="fas fa-utensils mr-1"></i>Start Preparing
                                        </button>
                                    <?php elseif ($order['status'] === 'preparing'): ?>
                                        <button onclick="updateOrderStatus(<?= $order['id'] ?>, 'ready')" 
                                                class="px-3 py-1 text-sm bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                                            <i class="fas fa-check mr-1"></i>Mark Ready
                                        </button>
                                    <?php elseif ($order['status'] === 'ready'): ?>
                                        <button onclick="updateOrderStatus(<?= $order['id'] ?>, 'delivered')" 
                                                class="px-3 py-1 text-sm bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors">
                                            <i class="fas fa-truck mr-1"></i>Mark Delivered
                                        </button>
                                    <?php endif; ?>
                                    
                                    <?php if (in_array($order['status'], ['pending', 'preparing'])): ?>
                                        <button onclick="updateOrderStatus(<?= $order['id'] ?>, 'cancelled')" 
                                                class="px-3 py-1 text-sm bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                            <i class="fas fa-times mr-1"></i>Cancel
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Menu Items Grid -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Room Service Menu</h2>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Search menu items..." 
                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                    <select id="categoryFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="">All Categories</option>
                        <option value="dish">Dishes</option>
                        <option value="drink">Drinks</option>
                    </select>
                </div>
            </div>

            <div id="menuItemsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($menuItems as $item): ?>
                    <div class="menu-item-card bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden <?= !$item['in_stock'] ? 'out-of-stock' : '' ?>">
                        <div class="relative">
                            <img src="<?= htmlspecialchars($item['image_url'] ?: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400') ?>" 
                                 alt="<?= htmlspecialchars($item['name']) ?>" 
                                 class="w-full h-48 object-cover">
                            <div class="absolute top-2 right-2">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    <?= $item['in_stock'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= $item['in_stock'] ? 'In Stock' : 'Out of Stock' ?>
                                </span>
                            </div>
                            <div class="absolute top-2 left-2">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <?= ucfirst($item['category']) ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 mb-2"><?= htmlspecialchars($item['name']) ?></h3>
                            <p class="text-sm text-gray-600 mb-3"><?= htmlspecialchars($item['description']) ?></p>
                            
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <p class="text-lg font-bold text-gray-900">
                                        <?= number_format($item['price_cents'] / 100, 2) ?> KES
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        $<?= number_format(($item['price_cents'] / 100) * 0.0067, 2) ?> USD
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <button onclick="toggleStock(<?= $item['id'] ?>, <?= $item['in_stock'] ? 0 : 1 ?>)"
                                        class="flex-1 px-3 py-2 text-sm font-medium rounded-lg transition-colors
                                        <?= $item['in_stock'] ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' ?>">
                                    <i class="fas <?= $item['in_stock'] ? 'fa-times' : 'fa-check' ?> mr-1"></i>
                                    <?= $item['in_stock'] ? 'Mark Out of Stock' : 'Mark In Stock' ?>
                                </button>
                                <button onclick="editItem(<?= $item['id'] ?>)"
                                        class="px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="removeItem(<?= $item['id'] ?>, '<?= htmlspecialchars($item['name']) ?>')"
                                        class="px-3 py-2 text-sm font-medium text-red-700 bg-red-100 rounded-lg hover:bg-red-200 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Add/Edit Item Modal -->
    <div id="itemModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">Add Menu Item</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form id="itemForm" class="space-y-4">
                        <input type="hidden" id="itemId" name="item_id">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Item Name</label>
                                <input type="text" id="itemName" name="name" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                <select id="itemCategory" name="category" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                    <option value="dish">Dish</option>
                                    <option value="drink">Drink</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea id="itemDescription" name="description" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Price (KES)</label>
                                <input type="number" id="itemPrice" name="price_cents" step="0.01" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">USD Equivalent</label>
                                <input type="text" id="usdPrice" readonly
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Item Image</label>
                            <div id="imageUploadArea" class="image-upload-area rounded-lg p-6 text-center cursor-pointer" ondrop="handleDrop(event)" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)">
                                <div id="imagePreview" class="hidden mb-4">
                                    <img id="previewImg" src="" alt="Preview" class="w-32 h-32 object-cover rounded-lg mx-auto">
                                    <button type="button" onclick="clearImage()" class="mt-2 text-red-500 hover:text-red-700 text-sm">
                                        <i class="fas fa-times"></i> Remove Image
                                    </button>
                                </div>
                                <div id="uploadPrompt">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                    <p class="text-gray-600">Click to upload or drag and drop</p>
                                    <p class="text-sm text-gray-500">PNG, JPG up to 10MB</p>
                                    <p class="text-xs text-blue-500 mt-2">Or paste image URL below</p>
                                </div>
                                <input type="file" id="imageInput" name="image" accept="image/*" class="hidden">
                            </div>
                            
                            <!-- Image URL Input -->
                            <div class="mt-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Or Image URL</label>
                                <input type="url" id="imageUrlInput" name="image_url" placeholder="https://example.com/image.jpg" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="itemInStock" name="in_stock" checked
                                       class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                <label for="itemInStock" class="ml-2 text-sm text-gray-700">In Stock</label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                            <button type="button" onclick="closeModal()" 
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 text-sm font-medium text-white bg-orange-500 rounded-lg hover:bg-orange-600 transition-colors">
                                Save Item
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            setupEventListeners();
            updateUSDPrice();
        });

        function setupEventListeners() {
            // Add item button
            document.getElementById('addItemBtn').addEventListener('click', function() {
                openModal();
            });

            // Form submission
            document.getElementById('itemForm').addEventListener('submit', function(e) {
                e.preventDefault();
                saveItem();
            });

            // Price input for USD conversion
            document.getElementById('itemPrice').addEventListener('input', updateUSDPrice);

            // Image upload
            const uploadArea = document.getElementById('imageUploadArea');
            const imageInput = document.getElementById('imageInput');

            uploadArea.addEventListener('click', () => imageInput.click());
            imageInput.addEventListener('change', handleImageUpload);

            // Drag and drop
            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            });

            uploadArea.addEventListener('dragleave', () => {
                uploadArea.classList.remove('dragover');
            });

            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    imageInput.files = files;
                    handleImageUpload();
                }
            });

            // Search and filter
            document.getElementById('searchInput').addEventListener('input', filterItems);
            document.getElementById('categoryFilter').addEventListener('change', filterItems);
        }

        function openModal(itemId = null) {
            const modal = document.getElementById('itemModal');
            const title = document.getElementById('modalTitle');
            const form = document.getElementById('itemForm');
            
            if (itemId) {
                title.textContent = 'Edit Menu Item';
                // Load item data for editing
                loadItemData(itemId);
            } else {
                title.textContent = 'Add Menu Item';
                form.reset();
                document.getElementById('imagePreview').classList.add('hidden');
                document.getElementById('uploadPrompt').classList.remove('hidden');
            }
            
            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('itemModal').classList.add('hidden');
        }

        function loadItemData(itemId) {
            // This would typically fetch item data from the server
            console.log('Loading item data for ID:', itemId);
        }

        function saveItem() {
            const formData = new FormData(document.getElementById('itemForm'));
            
            fetch('/kitchen/menu/create', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    location.reload();
                } else {
                    alert('Error saving item: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving item');
            });
        }

        function toggleStock(itemId, newStatus) {
            fetch('/kitchen/menu/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    item_id: itemId,
                    in_stock: newStatus,
                    _token: '<?= csrf_token() ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error updating stock status');
                }
            });
        }

        function editItem(itemId) {
            openModal(itemId);
        }

        function updateUSDPrice() {
            const kesPrice = parseFloat(document.getElementById('itemPrice').value) || 0;
            const usdPrice = (kesPrice * 0.0067).toFixed(2);
            document.getElementById('usdPrice').value = '$' + usdPrice;
        }

        function handleImageUpload() {
            const file = document.getElementById('imageInput').files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('imagePreview').classList.remove('hidden');
                    document.getElementById('uploadPrompt').classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        function filterItems() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const categoryFilter = document.getElementById('categoryFilter').value;
            const items = document.querySelectorAll('.menu-item-card');
            
            items.forEach(item => {
                const name = item.querySelector('h3').textContent.toLowerCase();
                const category = item.querySelector('.bg-blue-100').textContent.toLowerCase();
                
                const matchesSearch = name.includes(searchTerm);
                const matchesCategory = !categoryFilter || category.includes(categoryFilter);
                
                if (matchesSearch && matchesCategory) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // Enhanced image upload functionality
        function handleDrop(e) {
            e.preventDefault();
            e.stopPropagation();
            document.getElementById('imageUploadArea').classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleImageUpload({ files: files });
            }
        }

        function handleDragOver(e) {
            e.preventDefault();
            e.stopPropagation();
            document.getElementById('imageUploadArea').classList.add('dragover');
        }

        function handleDragLeave(e) {
            e.preventDefault();
            e.stopPropagation();
            document.getElementById('imageUploadArea').classList.remove('dragover');
        }

        function clearImage() {
            document.getElementById('imagePreview').classList.add('hidden');
            document.getElementById('uploadPrompt').classList.remove('hidden');
            document.getElementById('imageInput').value = '';
            document.getElementById('imageUrlInput').value = '';
        }

        function removeItem(itemId, itemName) {
            if (confirm(`Are you sure you want to remove "${itemName}" from the menu?`)) {
                const formData = new FormData();
                formData.append('item_id', itemId);
                formData.append('_token', '<?= csrf_token() ?>');
                
                fetch('/kitchen/menu-item/remove', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(() => {
                    location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error removing item');
                });
            }
        }

        // Order management functions
        function refreshOrders() {
            location.reload();
        }

        function updateOrderStatus(orderId, newStatus) {
            const formData = new FormData();
            formData.append('order_id', orderId);
            formData.append('status', newStatus);
            formData.append('_token', '<?= csrf_token() ?>');
            
            fetch('/orders/update-status', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Error updating order status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating order status');
            });
        }

        // Auto-refresh orders every 30 seconds
        setInterval(refreshOrders, 30000);
    </script>
</body>
</html>
