<?php
$title = 'Edit Customer - ' . ($settings['hotel_name'] ?? 'Hotel');
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
                <a href="/admin/customers" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white font-semibold">
                    <i class="fas fa-edit"></i>
                </div>
                <div>
                    <h1 class="font-semibold text-gray-900">Edit Customer</h1>
                    <p class="text-sm text-gray-500">Update customer information</p>
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
        <div class="max-w-2xl mx-auto">
            <!-- Customer Edit Form -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Customer Information</h3>
                    <p class="text-sm text-gray-500">Update the customer's details below</p>
                </div>
                
                <form method="POST" action="/admin/customers/update" class="p-6 space-y-6">
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="customer_id" value="<?= $customer['id'] ?>">
                    
                    <!-- Customer Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-50 rounded-lg">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600"><?= $customer['total_bookings'] ?></div>
                            <div class="text-sm text-gray-500">Total Bookings</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600"><?= number_format($customer['total_spent'] / 100, 2) ?> KES</div>
                            <div class="text-sm text-gray-500">Total Spent</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600"><?= $customer['last_booking'] ? date('M j', strtotime($customer['last_booking'])) : 'Never' ?></div>
                            <div class="text-sm text-gray-500">Last Booking</div>
                        </div>
                    </div>

                    <!-- Form Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input type="text" id="name" name="name" value="<?= htmlspecialchars($customer['name']) ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($customer['phone']) ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($customer['email']) ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <!-- Password Section -->
                    <div class="border-t pt-6">
                        <h4 class="text-md font-medium text-gray-900 mb-4">Change Password (Optional)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                <input type="password" id="new_password" name="new_password" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Leave blank to keep current password">
                            </div>
                            
                            <div>
                                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Confirm new password">
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Leave password fields blank to keep the current password</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                        <a href="/admin/customers" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors">
                            <i class="fas fa-save mr-2"></i>Update Customer
                        </button>
                    </div>
                </form>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <button onclick="resetPassword()" class="flex items-center space-x-3 p-4 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition-colors">
                        <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center text-white">
                            <i class="fas fa-key"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-medium text-gray-900">Reset Password</p>
                            <p class="text-sm text-gray-500">Generate new password</p>
                        </div>
                    </button>
                    
                    <a href="/admin/customers/<?= $customer['id'] ?>" class="flex items-center space-x-3 p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-medium text-gray-900">View Details</p>
                            <p class="text-sm text-gray-500">See full customer profile</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Reset Modal -->
    <div id="passwordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6 border-b">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Reset Password</h3>
                        <button onclick="closePasswordModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <form method="POST" action="/admin/customers/reset-password" class="p-6">
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="customer_id" value="<?= $customer['id'] ?>">
                    
                    <div class="mb-4">
                        <label for="reset_password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                        <input type="password" id="reset_password" name="new_password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div class="flex items-center justify-end space-x-4">
                        <button type="button" onclick="closePasswordModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 rounded-md">
                            Reset Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function resetPassword() {
            document.getElementById('passwordModal').classList.remove('hidden');
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').classList.add('hidden');
        }

        // Form validation
        document.querySelector('form[action="/admin/customers/update"]').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword && newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
        });

        // Close modal when clicking outside
        document.getElementById('passwordModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePasswordModal();
            }
        });
    </script>
</body>
</html>
