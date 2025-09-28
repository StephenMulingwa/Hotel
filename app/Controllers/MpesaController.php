<?php
namespace App\Controllers;

class MpesaController {
	private $consumerKey = 'SUaxYI8Ip1GEiXMG4nz1fIWjZwdN8aMqPKGGL7qLReJTFTZA';
	private $consumerSecret = 'gSwOYjLiQUkLbMq4yZ9JVSDYckQa2eGeWTzAKPNgjdVUXTUGXyNJ8x1kQZh1cA4k';
	private $baseUrl = 'https://sandbox.safaricom.co.ke'; // Change to production URL when ready
	private $businessShortCode = '174379';
	private $passKey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
	private $tillNumber = '3026432';
	private $storeNumber = '9254436';
	private $tillMsisdn = '254111514584';

	public function initiatePayment(): void {
		requireAuth();
		verify_csrf();
		
		$bookingId = (int)(input('booking_id', '0') ?? '0');
		$phone = input('phone', '');
		$amount = (int)(input('amount', '0') ?? '0');

		if (!$bookingId || !$phone || !$amount) {
			http_response_code(400);
			echo json_encode(['error' => 'Missing required parameters']);
			return;
		}

		// Get access token
		$accessToken = $this->getAccessToken();
		if (!$accessToken) {
			http_response_code(500);
			echo json_encode(['error' => 'Failed to get access token']);
			return;
		}

		// Initiate STK Push
		$result = $this->stkPush($accessToken, $phone, $amount, $bookingId);
		
		header('Content-Type: application/json');
		echo json_encode($result);
	}

	private function getAccessToken(): ?string {
		$url = $this->baseUrl . '/oauth/v1/generate?grant_type=client_credentials';
		$credentials = base64_encode($this->consumerKey . ':' . $this->consumerSecret);
		
		$headers = [
			'Authorization: Basic ' . $credentials,
			'Content-Type: application/json'
		];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($httpCode === 200) {
			$data = json_decode($response, true);
			return $data['access_token'] ?? null;
		}
		
		return null;
	}

	private function stkPush(string $accessToken, string $phone, int $amount, int $bookingId): array {
		$url = $this->baseUrl . '/mpesa/stkpush/v1/processrequest';
		
		// Format phone number (remove + and ensure it starts with 254)
		$phone = preg_replace('/[^0-9]/', '', $phone);
		if (str_starts_with($phone, '0')) {
			$phone = '254' . substr($phone, 1);
		} elseif (!str_starts_with($phone, '254')) {
			$phone = '254' . $phone;
		}

		$timestamp = date('YmdHis');
		$password = base64_encode($this->businessShortCode . $this->passKey . $timestamp);
		
		$payload = [
			'BusinessShortCode' => $this->businessShortCode,
			'Password' => $password,
			'Timestamp' => $timestamp,
			'TransactionType' => 'CustomerPayBillOnline',
			'Amount' => $amount,
			'PartyA' => $phone,
			'PartyB' => $this->businessShortCode,
			'PhoneNumber' => $phone,
			'CallBackURL' => 'https://your-domain.com/mpesa/callback',
			'AccountReference' => 'HOTEL' . $bookingId,
			'TransactionDesc' => 'Hotel Booking Payment'
		];

		$headers = [
			'Authorization: Bearer ' . $accessToken,
			'Content-Type: application/json'
		];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($httpCode === 200) {
			$data = json_decode($response, true);
			if ($data['ResponseCode'] === '0') {
				// Update payment record
				$pdo = db();
				$stmt = $pdo->prepare('UPDATE payments SET reference=?, status=? WHERE booking_id=?');
				$stmt->execute([$data['CheckoutRequestID'], 'pending', $bookingId]);
				
				return ['success' => true, 'message' => 'Payment initiated successfully', 'checkout_id' => $data['CheckoutRequestID']];
			}
		}

		return ['success' => false, 'message' => 'Failed to initiate payment'];
	}

	public function callback(): void {
		// Handle M-Pesa callback
		$input = file_get_contents('php://input');
		$data = json_decode($input, true);
		
		if (isset($data['Body']['stkCallback']['ResultCode'])) {
			$resultCode = $data['Body']['stkCallback']['ResultCode'];
			$checkoutId = $data['Body']['stkCallback']['CheckoutRequestID'];
			
			if ($resultCode === 0) {
				// Payment successful
				$pdo = db();
				$stmt = $pdo->prepare('UPDATE payments SET status=?, paid_at=? WHERE reference=?');
				$stmt->execute(['paid', now(), $checkoutId]);
				
				// Update booking status
				$stmt = $pdo->prepare('UPDATE bookings SET status=? WHERE id=(SELECT booking_id FROM payments WHERE reference=?)');
				$stmt->execute(['confirmed', $checkoutId]);
			}
		}
		
		echo json_encode(['success' => true]);
	}
}
