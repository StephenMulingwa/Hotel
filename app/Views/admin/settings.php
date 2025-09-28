<?php
$title = 'Hotel Settings - ' . ($settings['hotel_name'] ?? 'Hotel');
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
        .settings-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .settings-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .form-section {
            border-left: 4px solid #3b82f6;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="flex items-center justify-between p-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                    <i class="fas fa-cog"></i>
                </div>
                <div>
                    <h1 class="font-semibold text-gray-900">Hotel Settings</h1>
                    <p class="text-sm text-gray-500">Manage hotel configuration and preferences</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <a href="/admin" class="p-2 text-gray-600 hover:text-gray-800 rounded-full hover:bg-gray-100">
                    <i class="fas fa-arrow-left"></i>
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
        <form id="settingsForm" method="POST" action="/admin/settings/update" class="space-y-8">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            
            <!-- Hotel Information -->
            <div class="settings-card bg-white rounded-lg shadow-sm p-6">
                <div class="form-section pl-4 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Hotel Information</h3>
                    <p class="text-sm text-gray-600">Basic hotel details and branding</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="hotel_name" class="block text-sm font-medium text-gray-700 mb-2">Hotel Name</label>
                        <input type="text" id="hotel_name" name="hotel_name" value="<?= htmlspecialchars($settings['hotel_name'] ?? '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Enter hotel name">
                    </div>
                    
                    <div>
                        <label for="total_rooms" class="block text-sm font-medium text-gray-700 mb-2">Total Rooms</label>
                        <input type="number" id="total_rooms" name="total_rooms" value="<?= $settings['total_rooms'] ?? 40 ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               min="1" max="1000">
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="hotel_info" class="block text-sm font-medium text-gray-700 mb-2">Hotel Description</label>
                    <textarea id="hotel_info" name="hotel_info" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Enter hotel description (200 words max)"><?= htmlspecialchars($settings['hotel_info'] ?? '') ?></textarea>
                    <p class="text-sm text-gray-500 mt-1">This will be displayed to customers on their dashboard</p>
                </div>
            </div>

            <!-- Pricing & Currency -->
            <div class="settings-card bg-white rounded-lg shadow-sm p-6">
                <div class="form-section pl-4 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Pricing & Currency</h3>
                    <p class="text-sm text-gray-600">Set room prices and currency settings</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="room_price_per_night" class="block text-sm font-medium text-gray-700 mb-2">Room Price per Night (KES)</label>
                        <input type="number" id="room_price_per_night" name="room_price_per_night" 
                               value="<?= ($settings['room_price_per_night'] ?? 500000) / 100 ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               min="1" step="0.01">
                    </div>
                    
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                        <select id="currency" name="currency"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="KES" <?= ($settings['currency'] ?? 'KES') === 'KES' ? 'selected' : '' ?>>Kenyan Shilling (KES)</option>
                            <option value="USD" <?= ($settings['currency'] ?? 'KES') === 'USD' ? 'selected' : '' ?>>US Dollar (USD)</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="usd_rate" class="block text-sm font-medium text-gray-700 mb-2">USD Exchange Rate</label>
                        <input type="number" id="usd_rate" name="usd_rate" 
                               value="<?= $settings['usd_rate'] ?? 0.0067 ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               step="0.0001" min="0">
                        <p class="text-sm text-gray-500 mt-1">1 KES = $<?= $settings['usd_rate'] ?? 0.0067 ?> USD</p>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="settings-card bg-white rounded-lg shadow-sm p-6">
                <div class="form-section pl-4 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Security Settings</h3>
                    <p class="text-sm text-gray-600">WiFi password and security configurations</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="hotel_password" class="block text-sm font-medium text-gray-700 mb-2">Hotel WiFi Password</label>
                        <div class="relative">
                            <input type="password" id="hotel_password" name="hotel_password" 
                                   value="<?= htmlspecialchars($settings['hotel_password'] ?? '') ?>"
                                   class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <button type="button" onclick="togglePassword()" 
                                    class="absolute right-3 top-2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye" id="passwordToggleIcon"></i>
                            </button>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">This will be shown to checked-in customers</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password Requirements</label>
                        <div class="space-y-2 text-sm text-gray-600">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-check text-green-500"></i>
                                <span>At least 8 characters</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-check text-green-500"></i>
                                <span>Mix of letters and numbers</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hotel Images Management -->
            <div class="settings-card bg-white rounded-lg shadow-sm p-6">
                <div class="form-section pl-4 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Hotel Images Management</h3>
                    <p class="text-sm text-gray-600">Manage hotel and room images</p>
                </div>
                
                <!-- Hotel Images -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4">Hotel Images</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <?php foreach ($hotelImages as $image): ?>
                            <div class="relative group">
                                <img src="<?= htmlspecialchars($image['image_url']) ?>" 
                                     alt="Hotel Image" 
                                     class="w-full h-32 object-cover rounded-lg">
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 rounded-lg flex items-center justify-center">
                                    <button onclick="deleteImage(<?= $image['id'] ?>, 'hotel')" 
                                            class="opacity-0 group-hover:opacity-100 bg-red-500 text-white px-3 py-1 rounded text-sm">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                </div>
                                <?php if ($image['is_primary']): ?>
                                    <div class="absolute top-2 left-2 bg-blue-500 text-white text-xs px-2 py-1 rounded">Primary</div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button onclick="openImageUpload('hotel')" 
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Add Hotel Image
                    </button>
                </div>

                <!-- Room Images -->
                <div>
                    <h4 class="text-md font-semibold text-gray-800 mb-4">Room Images</h4>
                    <div class="space-y-4">
                        <?php foreach ($rooms as $room): ?>
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h5 class="font-medium text-gray-900 mb-2">Room <?= $room['number'] ?> (<?= ucfirst($room['type']) ?>)</h5>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <?php foreach ($roomImages[$room['id']] ?? [] as $image): ?>
                                        <div class="relative group">
                                            <img src="<?= htmlspecialchars($image['image_url']) ?>" 
                                                 alt="Room Image" 
                                                 class="w-full h-24 object-cover rounded">
                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 rounded flex items-center justify-center">
                                                <button onclick="deleteImage(<?= $image['id'] ?>, 'room')" 
                                                        class="opacity-0 group-hover:opacity-100 bg-red-500 text-white px-2 py-1 rounded text-xs">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            <?php if ($image['is_primary']): ?>
                                                <div class="absolute top-1 left-1 bg-blue-500 text-white text-xs px-1 py-0.5 rounded">P</div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                    <button onclick="openImageUpload('room', <?= $room['id'] ?>)" 
                                            class="w-full h-24 border-2 border-dashed border-gray-300 rounded flex items-center justify-center text-gray-400 hover:border-blue-500 hover:text-blue-500 transition-colors">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Staff Management -->
            <div class="settings-card bg-white rounded-lg shadow-sm p-6">
                <div class="form-section pl-4 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Staff Management</h3>
                    <p class="text-sm text-gray-600">Manage login credentials for staff members</p>
                </div>
                
                <div class="space-y-4">
                    <?php foreach ($staff as $member): ?>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900"><?= htmlspecialchars($member['name']) ?></h4>
                                    <p class="text-sm text-gray-600"><?= ucfirst($member['role']) ?> • <?= htmlspecialchars($member['phone']) ?></p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button type="button" onclick="editStaff(<?= $member['id'] ?>)"
                                        class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </button>
                                <button type="button" onclick="resetPassword(<?= $member['id'] ?>)"
                                        class="px-3 py-1 text-sm bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors">
                                    <i class="fas fa-key mr-1"></i>Reset Password
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- System Status -->
            <div class="settings-card bg-white rounded-lg shadow-sm p-6">
                <div class="form-section pl-4 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">System Status</h3>
                    <p class="text-sm text-gray-600">Current system information and health</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <i class="fas fa-database text-green-500 text-2xl mb-2"></i>
                        <h4 class="font-semibold text-gray-900">Database</h4>
                        <p class="text-sm text-green-600">Connected</p>
                    </div>
                    
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <i class="fas fa-server text-blue-500 text-2xl mb-2"></i>
                        <h4 class="font-semibold text-gray-900">Server</h4>
                        <p class="text-sm text-blue-600">Online</p>
                    </div>
                    
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <i class="fas fa-mobile-alt text-purple-500 text-2xl mb-2"></i>
                        <h4 class="font-semibold text-gray-900">M-Pesa</h4>
                        <p class="text-sm text-purple-600">Active</p>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex items-center justify-end space-x-4">
                <button type="button" onclick="resetForm()" 
                        class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Reset
                </button>
                <button type="submit" 
                        class="px-6 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-save mr-2"></i>Save Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Image Upload Modal -->
    <div id="imageUploadModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Upload Image</h3>
                        <button onclick="closeImageUpload()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form id="imageUploadForm" class="space-y-4" enctype="multipart/form-data">
                        <input type="hidden" id="imageType" name="image_type">
                        <input type="hidden" id="roomId" name="room_id">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Image URL</label>
                            <input type="url" id="imageUrl" name="image_url" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="https://example.com/image.jpg">
                            <p class="text-sm text-gray-500 mt-1">Enter the URL of the image you want to upload</p>
                        </div>
                        
                        <div>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" id="isPrimary" name="is_primary" value="1">
                                <span class="text-sm text-gray-700">Set as primary image</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                            <button type="button" onclick="closeImageUpload()" 
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors">
                                Upload Image
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Edit Modal -->
    <div id="staffModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Edit Staff Member</h3>
                        <button onclick="closeStaffModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form id="staffForm" class="space-y-4">
                        <input type="hidden" id="staffId" name="staff_id">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" id="staffName" name="name" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="tel" id="staffPhone" name="phone" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                            <select id="staffRole" name="role" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="receptionist">Receptionist</option>
                                <option value="kitchen">Kitchen Staff</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">New Password (optional)</label>
                            <input type="password" id="staffPassword" name="new_password"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                            <button type="button" onclick="closeStaffModal()" 
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('hotel_password');
            const toggleIcon = document.getElementById('passwordToggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        function editStaff(staffId) {
            // This would typically fetch staff data from the server
            document.getElementById('staffId').value = staffId;
            document.getElementById('staffModal').classList.remove('hidden');
        }

        function closeStaffModal() {
            document.getElementById('staffModal').classList.add('hidden');
        }

        function resetPassword(staffId) {
            if (confirm('Are you sure you want to reset this staff member\'s password?')) {
                // This would typically make an API call to reset the password
                alert('Password reset email sent to staff member');
            }
        }

        function resetForm() {
            if (confirm('Are you sure you want to reset all changes?')) {
                document.getElementById('settingsForm').reset();
            }
        }

        function openImageUpload(type, roomId = null) {
            document.getElementById('imageType').value = type;
            document.getElementById('roomId').value = roomId || '';
            document.getElementById('imageUploadModal').classList.remove('hidden');
        }

        function closeImageUpload() {
            document.getElementById('imageUploadModal').classList.add('hidden');
            document.getElementById('imageUploadForm').reset();
        }

        function deleteImage(imageId, type) {
            if (confirm('Are you sure you want to delete this image?')) {
                fetch('/admin/settings/delete-image', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        image_id: imageId,
                        _token: '<?= csrf_token() ?>'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting image: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting image');
                });
            }
        }

        // Form submission
        document.getElementById('settingsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('/admin/settings/update', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Settings saved successfully!');
                    location.reload();
                } else {
                    alert('Error saving settings: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving settings');
            });
        });

        // Staff form submission
        document.getElementById('staffForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('/admin/users/update', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Staff member updated successfully!');
                    closeStaffModal();
                    location.reload();
                } else {
                    alert('Error updating staff member: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating staff member');
            });
        });

        // Image upload form submission
        document.getElementById('imageUploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('/admin/settings/upload-image', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Image uploaded successfully!');
                    closeImageUpload();
                    location.reload();
                } else {
                    alert('Error uploading image: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error uploading image');
            });
        });
    </script>
</body>
</html>