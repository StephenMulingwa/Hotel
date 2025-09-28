<?php
namespace App\Controllers;

class AuthController {
	public function showLogin(): void {
		if (isAuthenticated()) redirect('/dashboard');
		render('auth/login', [], 'Login');
	}

	public function login(): void {
		verify_csrf();
		$phone = input('phone', '');
		$password = input('password', '');
		$stmt = db()->prepare('SELECT * FROM users WHERE phone = ? LIMIT 1');
		$stmt->execute([$phone]);
		$user = $stmt->fetch();
		if (!$user || !password_verify($password, $user['password_hash'])) {
			render('auth/login', ['error' => 'Invalid credentials'], 'Login');
			return;
		}
		$_SESSION['user'] = [
			'id' => (int)$user['id'],
			'name' => $user['name'],
			'phone' => $user['phone'],
			'role' => $user['role'],
		];
		if ($user['role'] === 'receptionist') redirect('/reception');
		if ($user['role'] === 'kitchen') redirect('/kitchen');
		redirect('/dashboard');
	}

	public function showRegister(): void {
		if (isAuthenticated()) redirect('/dashboard');
		render('auth/register', [], 'Register');
	}

	public function register(): void {
		verify_csrf();
		$name = input('name', '');
		$phone = input('phone', '');
		$password = input('password', '');
		if (!$name || !$phone || !$password) {
			render('auth/register', ['error' => 'All fields are required'], 'Register');
			return;
		}
		$stmt = db()->prepare('SELECT id FROM users WHERE phone = ?');
		$stmt->execute([$phone]);
		if ($stmt->fetch()) {
			render('auth/register', ['error' => 'Phone already registered'], 'Register');
			return;
		}
		$ins = db()->prepare('INSERT INTO users (name, phone, password_hash, role, created_at) VALUES (?, ?, ?, ?, ?)');
		$ins->execute([$name, $phone, password_hash($password, PASSWORD_DEFAULT), 'customer', now()]);
		$userId = (int) db()->lastInsertId();
		$_SESSION['user'] = ['id' => $userId, 'name' => $name, 'phone' => $phone, 'role' => 'customer'];
		redirect('/dashboard');
	}

	public function logout(): void {
		verify_csrf();
		session_destroy();
		redirect('/');
	}
}