<?php
namespace App\Controllers;

class ReceiptController {
    public function generate(): void {
        requireAuth();
        $pdo = db();
        $user = currentUser();
        
        $bookingId = (int)(input('booking_id', '0') ?? '0');
        if (!$bookingId) {
            http_response_code(400);
            echo 'Booking ID required';
            return;
        }
        
        // Get booking details
        $booking = $pdo->prepare("
            SELECT b.*, u.name as customer_name, u.phone, u.email, r.number as room_number,
                   p.amount_cents, p.status as payment_status, p.paid_at, p.reference
            FROM bookings b 
            JOIN users u ON u.id = b.user_id 
            JOIN rooms r ON r.id = b.room_id 
            LEFT JOIN payments p ON p.booking_id = b.id 
            WHERE b.id = ? AND (b.user_id = ? OR ? = 'admin')
        ");
        $booking->execute([$bookingId, $user['id'], $user['role']]);
        $bookingData = $booking->fetch();
        
        if (!$bookingData) {
            http_response_code(404);
            echo 'Booking not found';
            return;
        }
        
        // Generate receipt number
        $receiptNumber = 'RCP-' . str_pad($bookingId, 6, '0', STR_PAD_LEFT) . '-' . date('Y');
        
        // Check if receipt already exists
        $existingReceipt = $pdo->prepare('SELECT * FROM receipts WHERE booking_id = ?');
        $existingReceipt->execute([$bookingId]);
        $receipt = $existingReceipt->fetch();
        
        if (!$receipt) {
            // Create receipt record
            $stmt = $pdo->prepare('INSERT INTO receipts (booking_id, payment_id, receipt_number, amount_cents, currency, created_at) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([
                $bookingId,
                $bookingData['payment_id'] ?? null,
                $receiptNumber,
                $bookingData['amount_cents'] ?? 0,
                'KES',
                now()
            ]);
        } else {
            $receiptNumber = $receipt['receipt_number'];
        }
        
        // Generate PDF receipt
        $this->generatePDFReceipt($bookingData, $receiptNumber);
    }
    
    public function download(): void {
        requireAuth();
        $pdo = db();
        $user = currentUser();
        
        $bookingId = (int)(input('booking_id', '0') ?? '0');
        $orderId = (int)(input('order_id', '0') ?? '0');
        
        if ($bookingId) {
            // Handle booking receipt
            $this->downloadBookingReceipt($bookingId, $user, $pdo);
        } elseif ($orderId) {
            // Handle food order receipt
            $this->downloadOrderReceipt($orderId, $user, $pdo);
        } else {
            http_response_code(400);
            echo 'Booking ID or Order ID required';
            return;
        }
    }
    
    private function downloadBookingReceipt(int $bookingId, array $user, $pdo): void {
        // Get booking details first
        $booking = $pdo->prepare("
            SELECT b.*, u.name as customer_name, u.phone, u.email, r.number as room_number,
                   p.amount_cents, p.status as payment_status, p.paid_at, p.reference
            FROM bookings b 
            JOIN users u ON u.id = b.user_id 
            JOIN rooms r ON r.id = b.room_id 
            LEFT JOIN payments p ON p.booking_id = b.id 
            WHERE b.id = ? AND (b.user_id = ? OR ? = 'admin')
        ");
        $booking->execute([$bookingId, $user['id'], $user['role']]);
        $bookingData = $booking->fetch();
        
        if (!$bookingData) {
            http_response_code(404);
            echo 'Booking not found';
            return;
        }
        
        // Check if receipt exists
        $existingReceipt = $pdo->prepare('SELECT * FROM receipts WHERE booking_id = ?');
        $existingReceipt->execute([$bookingId]);
        $receipt = $existingReceipt->fetch();
        
        if (!$receipt) {
            // Generate receipt number
            $receiptNumber = 'RCP-' . str_pad($bookingId, 6, '0', STR_PAD_LEFT) . '-' . date('Y');
            
            // Create receipt record
            $stmt = $pdo->prepare('INSERT INTO receipts (booking_id, payment_id, receipt_number, amount_cents, currency, created_at) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([
                $bookingId,
                $bookingData['payment_id'] ?? null,
                $receiptNumber,
                $bookingData['amount_cents'] ?? 0,
                'KES',
                now()
            ]);
        } else {
            $receiptNumber = $receipt['receipt_number'];
        }
        
        // Generate and download PDF
        $this->generateBookingReceiptHTML($bookingData, $receiptNumber);
    }
    
    private function downloadOrderReceipt(int $orderId, array $user, $pdo): void {
        // Get order details
        $order = $pdo->prepare("
            SELECT o.*, u.name as customer_name, u.phone, u.email, 
                   b.room_id, r.number as room_number
            FROM orders o 
            JOIN users u ON u.id = o.user_id 
            LEFT JOIN bookings b ON b.id = o.booking_id 
            LEFT JOIN rooms r ON r.id = b.room_id 
            WHERE o.id = ? AND (o.user_id = ? OR ? = 'admin')
        ");
        $order->execute([$orderId, $user['id'], $user['role']]);
        $orderData = $order->fetch();
        
        if (!$orderData) {
            http_response_code(404);
            echo 'Order not found';
            return;
        }
        
        // Get order items
        $items = $pdo->prepare("
            SELECT oi.*, m.name, m.image_url 
            FROM order_items oi 
            JOIN menu_items m ON m.id = oi.menu_item_id 
            WHERE oi.order_id = ?
        ");
        $items->execute([$orderId]);
        $orderItems = $items->fetchAll();
        
        // Generate receipt number
        $receiptNumber = 'ORD-' . str_pad($orderId, 6, '0', STR_PAD_LEFT) . '-' . date('Y');
        
        // Generate and download PDF
        $this->generateOrderReceiptHTML($orderData, $orderItems, $receiptNumber);
    }
    
    private function generatePDFReceipt(array $data, string $receiptNumber): void {
        // Simple HTML receipt - in production, use a proper PDF library like TCPDF or DomPDF
        $html = $this->generateBookingReceiptHTML($data, $receiptNumber);
        
        header('Content-Type: text/html');
        header('Content-Disposition: attachment; filename="receipt-' . $receiptNumber . '.html"');
        echo $html;
    }
    
    private function generateBookingReceiptHTML(array $data, string $receiptNumber): string {
        $hotelSettings = getHotelSettings();
        $amount = number_format(($data['amount_cents'] ?? 0) / 100, 2);
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <title>Receipt - {$receiptNumber}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .hotel-name { font-size: 24px; font-weight: bold; color: #2563eb; }
                .receipt-info { margin-bottom: 20px; }
                .receipt-info table { width: 100%; border-collapse: collapse; }
                .receipt-info td { padding: 8px; border-bottom: 1px solid #e5e7eb; }
                .receipt-info td:first-child { font-weight: bold; width: 30%; }
                .amount { font-size: 18px; font-weight: bold; color: #059669; }
                .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #6b7280; }
                @media print { body { margin: 0; } }
            </style>
        </head>
        <body>
            <div class='header'>
                <div class='hotel-name'>{$hotelSettings['hotel_name']}</div>
                <div>Official Receipt</div>
            </div>
            
            <div class='receipt-info'>
                <table>
                    <tr><td>Receipt Number:</td><td>{$receiptNumber}</td></tr>
                    <tr><td>Date:</td><td>" . date('F j, Y', strtotime($data['created_at'])) . "</td></tr>
                    <tr><td>Customer:</td><td>{$data['customer_name']}</td></tr>
                    <tr><td>Phone:</td><td>{$data['phone']}</td></tr>
                    <tr><td>Room:</td><td>Room {$data['room_number']}</td></tr>
                    <tr><td>Check-in:</td><td>" . date('M j, Y', strtotime($data['start_date'])) . "</td></tr>
                    <tr><td>Check-out:</td><td>" . date('M j, Y', strtotime($data['end_date'])) . "</td></tr>
                    <tr><td>Payment Status:</td><td>" . ucfirst($data['payment_status'] ?? 'pending') . "</td></tr>
                    <tr><td>Payment Reference:</td><td>{$data['reference']}</td></tr>
                    <tr><td>Amount:</td><td class='amount'>{$amount} KES</td></tr>
                </table>
            </div>
            
            <div class='footer'>
                <p>Thank you for choosing {$hotelSettings['hotel_name']}!</p>
                <p>This is an official receipt. Please keep it for your records.</p>
            </div>
        </body>
        </html>";
    }
    
    private function generateOrderReceiptHTML(array $orderData, array $orderItems, string $receiptNumber): void {
        $hotelSettings = getHotelSettings();
        $total = number_format($orderData['total_cents'] / 100, 2);
        
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <title>Order Receipt - {$receiptNumber}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .hotel-name { font-size: 24px; font-weight: bold; color: #2563eb; }
                .receipt-info { margin-bottom: 20px; }
                .receipt-info table { width: 100%; border-collapse: collapse; }
                .receipt-info td { padding: 8px; border-bottom: 1px solid #e5e7eb; }
                .receipt-info td:first-child { font-weight: bold; width: 30%; }
                .items-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                .items-table th, .items-table td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
                .items-table th { background-color: #f9fafb; font-weight: bold; }
                .amount { font-size: 18px; font-weight: bold; color: #059669; }
                .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #6b7280; }
                @media print { body { margin: 0; } }
            </style>
        </head>
        <body>
            <div class='header'>
                <div class='hotel-name'>{$hotelSettings['hotel_name']}</div>
                <div>Food Order Receipt</div>
            </div>
            
            <div class='receipt-info'>
                <table>
                    <tr><td>Receipt Number:</td><td>{$receiptNumber}</td></tr>
                    <tr><td>Date:</td><td>" . date('F j, Y \a\t g:i A', strtotime($orderData['created_at'])) . "</td></tr>
                    <tr><td>Customer:</td><td>{$orderData['customer_name']}</td></tr>
                    <tr><td>Phone:</td><td>{$orderData['phone']}</td></tr>
                    <tr><td>Room:</td><td>" . ($orderData['room_number'] ? 'Room ' . $orderData['room_number'] : 'N/A') . "</td></tr>
                    <tr><td>Payment Method:</td><td>" . ucfirst($orderData['payment_method']) . "</td></tr>
                    <tr><td>Status:</td><td>" . ucfirst($orderData['status']) . "</td></tr>
                </table>
            </div>
            
            <div class='items-section'>
                <h3>Order Items</h3>
                <table class='items-table'>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>";
        
        foreach ($orderItems as $item) {
            $unitPrice = number_format($item['price_cents'] / 100, 2);
            $itemTotal = number_format(($item['price_cents'] * $item['quantity']) / 100, 2);
            $html .= "
                        <tr>
                            <td>{$item['name']}</td>
                            <td>{$item['quantity']}</td>
                            <td>{$unitPrice} KES</td>
                            <td>{$itemTotal} KES</td>
                        </tr>";
        }
        
        $html .= "
                    </tbody>
                    <tfoot>
                        <tr style='font-weight: bold; background-color: #f9fafb;'>
                            <td colspan='3'>Total Amount</td>
                            <td class='amount'>{$total} KES</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class='footer'>
                <p>Thank you for choosing {$hotelSettings['hotel_name']}!</p>
                <p>This is an official receipt. Please keep it for your records.</p>
                <p>For any inquiries, please contact our customer service.</p>
            </div>
        </body>
        </html>";
        
        header('Content-Type: text/html');
        header('Content-Disposition: attachment; filename="order-receipt-' . $receiptNumber . '.html"');
        echo $html;
    }
}
