<div class="p-6">
        <!-- CSRF token for inline actions -->
        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
        <!-- Customer Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Customers</p>
                        <p class="text-2xl font-bold text-gray-900"><?= count($customers) ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active Customers</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?= count(array_filter($customers, fn($c) => $c['total_bookings'] > 0)) ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-check text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?= number_format(array_sum(array_column($customers, 'total_spent')) / 100, 2) ?> KES
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer List -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Customer Profiles</h3>
                <p class="text-sm text-gray-500">View detailed customer information and booking history</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bookings</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Spent</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Booking</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($customers as $customer): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-gray-600"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($customer['name']) ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                ID: <?= $customer['id'] ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= htmlspecialchars($customer['phone'] ?? '') ?></div>
                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($customer['email'] ?? '') ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <?= $customer['total_bookings'] ?> bookings
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= number_format($customer['total_spent'] / 100, 2) ?> KES
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= $customer['last_booking'] ? date('M j, Y', strtotime($customer['last_booking'])) : 'Never' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="viewCustomer(<?= $customer['id'] ?>)" class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button onclick="editCustomer(<?= $customer['id'] ?>)" class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Customer Detail Modal -->
    <div id="customerModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] flex flex-col">
                <div class="p-6 border-b flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Customer Details</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div id="customerDetails" class="p-6 overflow-y-auto flex-1">
                    <!-- Customer details will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewCustomer(customerId) {
            // Load customer details via AJAX
            fetch(`/admin/customers/${customerId}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('customerDetails').innerHTML = html;
                    document.getElementById('customerModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error loading customer details:', error);
                });
        }

        function editCustomer(customerId) {
            // Redirect to edit page or show edit modal
            window.location.href = `/admin/customers/${customerId}/edit`;
        }

        function resetPassword(customerId) {
            if (confirm('Are you sure you want to reset this customer\'s password?')) {
                const newPassword = prompt('Enter new password for customer:');
                if (newPassword && newPassword.length >= 6) {
                    // Create a form and submit it
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/admin/customers/reset-password';
                    
                    const tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.name = '_token';
                    tokenInput.value = document.querySelector('input[name="_token"]').value;
                    
                    const customerIdInput = document.createElement('input');
                    customerIdInput.type = 'hidden';
                    customerIdInput.name = 'customer_id';
                    customerIdInput.value = customerId;
                    
                    const passwordInput = document.createElement('input');
                    passwordInput.type = 'hidden';
                    passwordInput.name = 'new_password';
                    passwordInput.value = newPassword;
                    
                    form.appendChild(tokenInput);
                    form.appendChild(customerIdInput);
                    form.appendChild(passwordInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                } else if (newPassword) {
                    alert('Password must be at least 6 characters long.');
                }
            }
        }

        function viewChat(customerId) {
            // Redirect to chat page with customer filter
            window.location.href = `/admin/chat?customer=${customerId}`;
        }

        function closeModal() {
            document.getElementById('customerModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('customerModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
