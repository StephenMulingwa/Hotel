<?php
namespace App\Controllers;

class HotelSettingsController {
	public function index(): void {
		requireRole(['admin']);
		$pdo = db();
		$settings = $pdo->query('SELECT * FROM hotel_settings ORDER BY id DESC LIMIT 1')->fetch() ?: null;
		
		// Get staff members
		$staff = $pdo->query("
			SELECT id, name, phone, role 
			FROM users 
			WHERE role IN ('receptionist', 'kitchen', 'admin') 
			ORDER BY role, name
		")->fetchAll();
		
		// Get hotel images
		$hotelImages = $pdo->query("
			SELECT id, image_url, is_primary 
			FROM hotel_images 
			WHERE image_type = 'hotel' 
			ORDER BY is_primary DESC, created_at DESC
		")->fetchAll();
		
		// Get rooms with their images
		$rooms = $pdo->query("
			SELECT id, number, type 
			FROM rooms 
			ORDER BY number
		")->fetchAll();
		
		$roomImages = [];
		foreach ($rooms as $room) {
			$images = $pdo->prepare("
				SELECT id, image_url, is_primary 
				FROM hotel_images 
				WHERE image_type = 'room' AND room_id = ? 
				ORDER BY is_primary DESC, created_at DESC
			");
			$images->execute([$room['id']]);
			$roomImages[$room['id']] = $images->fetchAll();
		}
		
		render('admin/settings', [
			'settings' => $settings,
			'staff' => $staff,
			'hotelImages' => $hotelImages,
			'rooms' => $rooms,
			'roomImages' => $roomImages
		], 'Hotel Settings');
	}

	public function update(): void {
		requireRole(['admin']);
		verify_csrf();
		$pdo = db();
		$name = input('hotel_name', '');
		$info = input('hotel_info', '');
		$price = (int)(input('room_price_per_night', '0') ?? '0') * 100; // Convert to cents
		$currency = input('currency', 'KES');
		$usdRate = (float)(input('usd_rate', '0.0067') ?? '0.0067');
		$totalRooms = (int)(input('total_rooms', '40') ?? '40');
		$hotelPassword = input('hotel_password', '');

		if (!$name || !$info || $price <= 0) {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'message' => 'Missing required fields']);
			return;
		}

		$stmt = $pdo->prepare('UPDATE hotel_settings SET hotel_name=?, hotel_info=?, room_price_per_night=?, currency=?, usd_rate=?, total_rooms=?, hotel_password=?, updated_at=? WHERE id=1');
		$stmt->execute([$name, $info, $price, $currency, $usdRate, $totalRooms, $hotelPassword, now()]);

		header('Content-Type: application/json');
		echo json_encode(['success' => true, 'message' => 'Settings updated successfully']);
	}

	public function uploadImage(): void {
		requireRole(['admin']);
		verify_csrf();
		$pdo = db();
		
		$imageUrl = input('image_url', '');
		$imageType = input('image_type', '');
		$roomId = (int)(input('room_id', '0') ?? '0');
		$isPrimary = (int)(input('is_primary', '0') ?? '0');
		
		if (!$imageUrl || !$imageType) {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'message' => 'Missing required fields']);
			return;
		}
		
		// If setting as primary, unset other primary images of the same type
		if ($isPrimary) {
			if ($imageType === 'hotel') {
				$pdo->query("UPDATE hotel_images SET is_primary = 0 WHERE image_type = 'hotel'");
			} else {
				$pdo->prepare("UPDATE hotel_images SET is_primary = 0 WHERE image_type = 'room' AND room_id = ?")->execute([$roomId]);
			}
		}
		
		$stmt = $pdo->prepare('INSERT INTO hotel_images (image_url, image_type, room_id, is_primary, created_at) VALUES (?, ?, ?, ?, ?)');
		$stmt->execute([$imageUrl, $imageType, $roomId ?: null, $isPrimary, now()]);
		
		header('Content-Type: application/json');
		echo json_encode(['success' => true, 'message' => 'Image uploaded successfully']);
	}
	
	public function deleteImage(): void {
		requireRole(['admin']);
		verify_csrf();
		$pdo = db();
		
		$imageId = (int)(input('image_id', '0') ?? '0');
		
		if (!$imageId) {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'message' => 'Invalid image ID']);
			return;
		}
		
		$stmt = $pdo->prepare('DELETE FROM hotel_images WHERE id = ?');
		$stmt->execute([$imageId]);
		
		header('Content-Type: application/json');
		echo json_encode(['success' => true, 'message' => 'Image deleted successfully']);
	}

	public function getSettings(): array {
		$pdo = db();
		$settings = $pdo->query('SELECT * FROM hotel_settings ORDER BY id DESC LIMIT 1')->fetch() ?: null;
		return $settings ?: [
			'hotel_name' => 'Aurora Hotel',
			'hotel_info' => 'Welcome to our beautiful hotel with excellent service and amenities.',
			'room_price_per_night' => 500000,
			'currency' => 'KES',
			'usd_rate' => 0.0067
		];
	}
}
