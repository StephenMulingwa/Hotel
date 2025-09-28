<?php
$title = 'Refund Management - ' . ($settings['hotel_name'] ?? 'Hotel');
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
        .refund-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .refund-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .status-pending {
            border-left: 4px solid #f59e0b;
        }
        .status-approved {
            border-left: 4px solid #10b981;
        }
        .status-rejected {
            border-left: 4px solid #ef4444;
        }
        .status-processed {
            border-left: 4px solid #3b82f6;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="flex items-center justify-between p-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center text-white font-semibold">
                    <i class="fas fa-undo"></i>
                </div>
                <div>
                    <h1 class="font-semibold text-gray-900">Refund Management</h1>
                    <p class="text-sm text-gray-500">Process customer refunds and cancellations</p>
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
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pending Refunds</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?= count(array_filter($refunds ?? [], fn($r) => $r['status'] === 'pending')) ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Approved</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?= count(array_filter($refunds ?? [], fn($r) => $r['status'] === 'approved')) ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Rejected</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?= count(array_filter($refunds ?? [], fn($r) => $r['status'] === 'rejected')) ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-times text-red-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Amount</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?= number_format(array_sum(array_column($refunds, 'amount_cents')) / 100, 2) ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-blue-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Options -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Status:</label>
                    <select id="statusFilter" class="px-3 py-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="all">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="processed">Processed</option>
                    </select>
                </div>
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Date Range:</label>
                    <input type="date" id="dateFrom" class="px-3 py-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <span class="text-gray-500">to</span>
                    <input type="date" id="dateTo" class="px-3 py-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <button onclick="applyFilters()" class="px-4 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-filter mr-1"></i>Apply
                </button>
            </div>
        </div>

        <!-- Refunds List -->
        <div id="refundsList" class="space-y-4">
            <?php if (empty($refunds)): ?>
                <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                    <i class="fas fa-undo text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Refunds</h3>
                    <p class="text-gray-600">No refund requests at the moment.</p>
                </div>
            <?php else: ?>
                <?php foreach ($refunds as $refund): ?>
                    <div class="refund-card bg-white rounded-lg shadow-sm p-6 border border-gray-200 status-<?= $refund['status'] ?>"
                         data-id="<?= $refund['id'] ?>" data-status="<?= $refund['status'] ?>">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-undo text-red-600"></i>
                                </div>
                                
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            Refund Request #<?= $refund['id'] ?>
                                        </h3>
                                        <div class="flex items-center space-x-3">
                                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                                <?= $refund['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                    ($refund['status'] === 'approved' ? 'bg-green-100 text-green-800' : 
                                                    ($refund['status'] === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) ?>">
                                                <?= ucfirst($refund['status']) ?>
                                            </span>
                                            <span class="text-sm text-gray-500">
                                                <?= date('M j, Y H:i', strtotime($refund['created_at'])) ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <p class="text-sm text-gray-600">Customer</p>
                                            <p class="font-medium text-gray-900"><?= htmlspecialchars($refund['customer_name']) ?></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Booking</p>
                                            <p class="font-medium text-gray-900">Room <?= $refund['room_number'] ?></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Amount</p>
                                            <p class="font-medium text-gray-900">
                                                <?= number_format($refund['amount_cents'] / 100, 2) ?> <?= htmlspecialchars($settings['currency'] ?? 'KES') ?>
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Reason</p>
                                            <p class="font-medium text-gray-900"><?= htmlspecialchars($refund['reason']) ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600 mb-1">Additional Details</p>
                                        <p class="text-gray-900"><?= htmlspecialchars($refund['reason']) ?></p>
                                    </div>
                                    
                                    <?php if ($refund['status'] === 'pending'): ?>
                                        <div class="flex items-center space-x-3">
                                            <button onclick="processRefund(<?= $refund['id'] ?>, 'approved')"
                                                    class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                                                <i class="fas fa-check mr-2"></i>Approve
                                            </button>
                                            <button onclick="processRefund(<?= $refund['id'] ?>, 'rejected')"
                                                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                                <i class="fas fa-times mr-2"></i>Reject
                                            </button>
                                            <button onclick="viewBookingDetails(<?= $refund['booking_id'] ?>)"
                                                    class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                                                <i class="fas fa-eye mr-2"></i>View Booking
                                            </button>
                                        </div>
                                    <?php elseif ($refund['status'] === 'approved'): ?>
                                        <div class="flex items-center space-x-3">
                                            <button onclick="markAsProcessed(<?= $refund['id'] ?>)"
                                                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                                                <i class="fas fa-check-double mr-2"></i>Mark as Processed
                                            </button>
                                            <span class="text-sm text-green-600">
                                                <i class="fas fa-check-circle mr-1"></i>Approved by <?= htmlspecialchars($refund['processed_by_name']) ?>
                                            </span>
                                        </div>
                                    <?php elseif ($refund['status'] === 'rejected'): ?>
                                        <div class="flex items-center space-x-3">
                                            <span class="text-sm text-red-600">
                                                <i class="fas fa-times-circle mr-1"></i>Rejected by <?= htmlspecialchars($refund['processed_by_name']) ?>
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <div class="flex items-center space-x-3">
                                            <span class="text-sm text-blue-600">
                                                <i class="fas fa-check-double mr-1"></i>Processed on <?= date('M j, Y', strtotime($refund['processed_at'])) ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Process Refund Modal -->
    <div id="processModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Process Refund</h3>
                        <button onclick="closeProcessModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form id="processForm" class="space-y-4">
                        <input type="hidden" id="refundId" name="refund_id">
                        <input type="hidden" id="refundAction" name="action">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Action</label>
                            <p id="actionText" class="text-lg font-semibold text-gray-900"></p>
                        </div>
                        
                        <div>
                            <label for="adminNotes" class="block text-sm font-medium text-gray-700 mb-1">Admin Notes (Optional)</label>
                            <textarea id="adminNotes" name="admin_notes" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Add any additional notes..."></textarea>
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                            <button type="button" onclick="closeProcessModal()" 
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors">
                                Confirm
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function processRefund(refundId, action) {
            document.getElementById('refundId').value = refundId;
            document.getElementById('refundAction').value = action;
            document.getElementById('actionText').textContent = action === 'approved' ? 'Approve Refund' : 'Reject Refund';
            document.getElementById('processModal').classList.remove('hidden');
        }

        function closeProcessModal() {
            document.getElementById('processModal').classList.add('hidden');
        }

        function markAsProcessed(refundId) {
            if (confirm('Mark this refund as processed?')) {
                fetch('/admin/refund/process', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        refund_id: refundId,
                        action: 'processed',
                        _token: '<?= csrf_token() ?>'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error processing refund: ' + data.message);
                    }
                });
            }
        }

        function viewBookingDetails(bookingId) {
            // This would typically open a modal or navigate to booking details
            alert('Viewing booking details for ID: ' + bookingId);
        }

        function applyFilters() {
            const statusFilter = document.getElementById('statusFilter').value;
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;
            const refunds = document.querySelectorAll('.refund-card');
            
            refunds.forEach(refund => {
                const refundStatus = refund.dataset.status;
                const refundDate = new Date(refund.querySelector('.text-sm.text-gray-500').textContent);
                
                let show = true;
                
                if (statusFilter !== 'all' && refundStatus !== statusFilter) {
                    show = false;
                }
                
                if (dateFrom && refundDate < new Date(dateFrom)) {
                    show = false;
                }
                
                if (dateTo && refundDate > new Date(dateTo)) {
                    show = false;
                }
                
                refund.style.display = show ? 'block' : 'none';
            });
        }

        // Form submission
        document.getElementById('processForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('/admin/refund/process', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeProcessModal();
                    location.reload();
                } else {
                    alert('Error processing refund: ' + data.message);
                }
            });
        });
    </script>
</body>
</html>