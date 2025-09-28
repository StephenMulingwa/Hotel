<?php
namespace App\Controllers;

class ChatController {
	public function index(): void {
		requireAuth();
		$pdo = db();
		$u = currentUser();
		
		// Get user's active booking
		$activeBooking = null;
		if ($u['role'] === 'customer') {
			$bookingStmt = $pdo->prepare("
				SELECT b.*, r.number as room_number, p.status as payment_status 
				FROM bookings b 
				JOIN rooms r ON r.id = b.room_id 
				LEFT JOIN payments p ON p.booking_id = b.id 
				WHERE b.user_id = ? AND b.status IN ('pending','confirmed') 
				ORDER BY b.id DESC LIMIT 1
			");
			$bookingStmt->execute([$u['id']]);
			$activeBooking = $bookingStmt->fetch() ?: null;
		}
		
		// Get chat conversations using the new chat system
		if ($u['role'] === 'customer') {
			// Customer sees their conversations
			$conversations = $pdo->prepare("
				SELECT c.*, 
				       CASE WHEN c.staff_role = 'receptionist' THEN 'Reception' 
				            WHEN c.staff_role = 'kitchen' THEN 'Kitchen' 
				            ELSE 'Admin' END as role_name,
				       r.number as room_number,
				       (SELECT cm.message FROM chat_messages cm WHERE cm.conversation_id = c.id ORDER BY cm.id DESC LIMIT 1) as last_message,
				       (SELECT cm.created_at FROM chat_messages cm WHERE cm.conversation_id = c.id ORDER BY cm.id DESC LIMIT 1) as last_message_time,
				       (SELECT COUNT(*) FROM chat_messages cm2 WHERE cm2.conversation_id = c.id AND cm2.from_user_id != ? AND cm2.is_read = 0) as unread_count
				FROM chat_conversations c
				LEFT JOIN rooms r ON r.id = c.room_id
				WHERE c.customer_id = ? AND c.is_active = 1
				ORDER BY last_message_time DESC
			");
			$conversations->execute([$u['id'], $u['id']]);
		} else {
			// Staff see conversations with customers
			$conversations = $pdo->prepare("
				SELECT c.*, u.name as customer_name, u.phone as customer_phone,
				       r.number as room_number,
				       (SELECT cm.message FROM chat_messages cm WHERE cm.conversation_id = c.id ORDER BY cm.id DESC LIMIT 1) as last_message,
				       (SELECT cm.created_at FROM chat_messages cm WHERE cm.conversation_id = c.id ORDER BY cm.id DESC LIMIT 1) as last_message_time,
				       (SELECT COUNT(*) FROM chat_messages cm2 WHERE cm2.conversation_id = c.id AND cm2.from_user_id != ? AND cm2.is_read = 0) as unread_count
				FROM chat_conversations c
				JOIN users u ON u.id = c.customer_id
				LEFT JOIN rooms r ON r.id = c.room_id
				WHERE c.staff_role = ? AND c.is_active = 1
				ORDER BY last_message_time DESC
			");
			$conversations->execute([$u['id'], $u['role']]);
		}
		
		$settings = getHotelSettings();
		render('chat/whatsapp', [
			'conversations' => $conversations->fetchAll(),
			'activeBooking' => $activeBooking,
			'user' => $u,
			'settings' => $settings
		], 'Chat');
	}

	public function getMessages(): void {
		requireAuth();
		header('Content-Type: application/json');
		$pdo = db();
		$user = currentUser();
		$conversationId = (int)(input('conversation_id', '0') ?? '0');
		$since = (int)(input('since', '0') ?? '0');
		
		if (!$conversationId) {
			echo json_encode(['messages' => []]);
			return;
		}
		
		// Get messages for the conversation
		$stmt = $pdo->prepare("
			SELECT cm.*, u.name as from_name, u.role as from_role
			FROM chat_messages cm
			JOIN users u ON u.id = cm.from_user_id
			WHERE cm.conversation_id = ? AND cm.id > ?
			ORDER BY cm.id ASC
		");
		$stmt->execute([$conversationId, $since]);
		
		echo json_encode(['messages' => $stmt->fetchAll()]);
	}

	public function send(): void {
		requireAuth();
		verify_csrf();
		$pdo = db();
		$user = currentUser();
		$message = input('message', '');
		$target = input('target', '');
		
		if (!$message || !$target) {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'message' => 'Missing required fields']);
			return;
		}
		
		// Find or create conversation
		$conversationId = null;
		
		if ($user['role'] === 'customer') {
			// Customer sending to staff role
			$conversationStmt = $pdo->prepare("
				SELECT id FROM chat_conversations 
				WHERE customer_id = ? AND staff_role = ? AND is_active = 1
				LIMIT 1
			");
			$conversationStmt->execute([$user['id'], $target]);
			$conversation = $conversationStmt->fetch();
			
			if (!$conversation) {
				// Create new conversation
				$createStmt = $pdo->prepare("
					INSERT INTO chat_conversations (customer_id, staff_role, room_id, booking_id, is_active, created_at, updated_at)
					VALUES (?, ?, ?, ?, 1, ?, ?)
				");
				$createStmt->execute([
					$user['id'], 
					$target, 
					null, // room_id - could be enhanced to get from active booking
					null, // booking_id - could be enhanced to get from active booking
					now(), 
					now()
				]);
				$conversationId = $pdo->lastInsertId();
			} else {
				$conversationId = $conversation['id'];
			}
			
			// Insert message
			$messageStmt = $pdo->prepare("
				INSERT INTO chat_messages (conversation_id, from_user_id, to_role, message, is_read, created_at)
				VALUES (?, ?, ?, ?, 0, ?)
			");
			$messageStmt->execute([$conversationId, $user['id'], $target, $message, now()]);
			
		} else {
			// Staff sending to customer
			$targetUserId = (int)$target;
			
			$conversationStmt = $pdo->prepare("
				SELECT id FROM chat_conversations 
				WHERE customer_id = ? AND staff_role = ? AND is_active = 1
				LIMIT 1
			");
			$conversationStmt->execute([$targetUserId, $user['role']]);
			$conversation = $conversationStmt->fetch();
			
			if (!$conversation) {
				// Create new conversation
				$createStmt = $pdo->prepare("
					INSERT INTO chat_conversations (customer_id, staff_role, room_id, booking_id, is_active, created_at, updated_at)
					VALUES (?, ?, ?, ?, 1, ?, ?)
				");
				$createStmt->execute([
					$targetUserId, 
					$user['role'], 
					null, // room_id - could be enhanced
					null, // booking_id - could be enhanced
					now(), 
					now()
				]);
				$conversationId = $pdo->lastInsertId();
			} else {
				$conversationId = $conversation['id'];
			}
			
			// Insert message
			$messageStmt = $pdo->prepare("
				INSERT INTO chat_messages (conversation_id, from_user_id, to_user_id, message, is_read, created_at)
				VALUES (?, ?, ?, ?, 0, ?)
			");
			$messageStmt->execute([$conversationId, $user['id'], $targetUserId, $message, now()]);
		}
		
		// Update conversation timestamp
		$updateStmt = $pdo->prepare("UPDATE chat_conversations SET updated_at = ? WHERE id = ?");
		$updateStmt->execute([now(), $conversationId]);
		
		header('Content-Type: application/json');
		echo json_encode(['success' => true, 'conversation_id' => $conversationId]);
	}

	public function markAsRead(): void {
		requireAuth();
		$pdo = db();
		$user = currentUser();
		$conversationId = (int)(input('conversation_id', '0') ?? '0');
		
		if ($conversationId) {
			$stmt = $pdo->prepare("
				UPDATE chat_messages 
				SET is_read = 1 
				WHERE conversation_id = ? AND from_user_id != ?
			");
			$stmt->execute([$conversationId, $user['id']]);
		}
		
		header('Content-Type: application/json');
		echo json_encode(['success' => true]);
	}
}