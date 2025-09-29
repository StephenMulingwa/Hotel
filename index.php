<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use App\Controllers\AuthController;
use App\Controllers\BookingController;
use App\Controllers\ReceptionController;
use App\Controllers\KitchenController;
use App\Controllers\ChatController;
use App\Controllers\OrderController;
use App\Controllers\AdminController;
use App\Controllers\HotelSettingsController;
use App\Controllers\MpesaController;
use App\Controllers\RefundController;
use App\Controllers\ReceiptController;

// --- Parse Request ---
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

// --- Normalize URI (remove /Hotel prefix if in subfolder) ---
$base = '/Hotel';
if (str_starts_with($uri, $base)) {
    $uri = substr($uri, strlen($base));
}
if ($uri === '' || $uri === false) {
    $uri = '/';
}

// Ensure URI starts with /
if (!str_starts_with($uri, '/')) {
    $uri = '/' . $uri;
}

// Debug logging (remove in production)
error_log("Request URI: " . $_SERVER['REQUEST_URI']);
error_log("Normalized URI: " . $uri);
error_log("Method: " . $method);

// --- Define Routes ---
$routes = [
    'GET' => [
        '/' => function () {
            if (isAuthenticated()) {
                redirect('/dashboard');
            }
            render('welcome', [], 'Welcome');
        },
        '/login' => [AuthController::class, 'showLogin'],
        '/register' => [AuthController::class, 'showRegister'],

        '/dashboard' => [BookingController::class, 'dashboard'],
        '/booking/new' => [BookingController::class, 'create'],
        '/booking/history' => [BookingController::class, 'history'],
        '/booking/pay' => [BookingController::class, 'showPay'],

        '/reception' => [ReceptionController::class, 'index'],
        '/kitchen' => [KitchenController::class, 'index'],
        '/menu' => [BookingController::class, 'menu'],
        '/orders' => [OrderController::class, 'index'],
        '/orders/pay' => [OrderController::class, 'pay'],

        '/chat' => [ChatController::class, 'index'],
        '/chat/fetch' => [ChatController::class, 'fetch'],
        '/chat/messages' => [ChatController::class, 'getMessages'],

        // Admin routes
        '/admin' => [AdminController::class, 'dashboard'],
        '/admin/dashboard' => [AdminController::class, 'dashboard'],
        '/admin/analytics' => [AdminController::class, 'analytics'],
        '/admin/users' => [AdminController::class, 'userManagement'],
        '/admin/settings' => [HotelSettingsController::class, 'index'],
        '/admin/refund' => [RefundController::class, 'index'],
        '/admin/customers' => [AdminController::class, 'customers'],
        '/admin/customers/{id}' => [AdminController::class, 'customerDetails'],
        '/admin/customers/{id}/edit' => [AdminController::class, 'editCustomer'],
        '/admin/chat' => [AdminController::class, 'chatOverview'],
        '/admin/kitchen' => [AdminController::class, 'kitchenOverview'],
        
        // Receipt routes
        '/receipt/generate' => [ReceiptController::class, 'generate'],
        '/receipt/download' => [ReceiptController::class, 'download'],
        
        // Enhanced kitchen route
        '/kitchen/enhanced' => [KitchenController::class, 'enhanced'],
        '/kitchen/update-order-status' => [KitchenController::class, 'updateOrderStatus'],

        // Hotel info and reviews
        '/about' => function () {
            $settings = (new HotelSettingsController())->getSettings();
            render('about', ['settings' => $settings], 'About Us');
        },
        '/reviews' => function () {
            $pdo = db();
            $reviews = $pdo->query("
                SELECT r.*, u.name as customer_name, b.room_id, rm.number as room_number
                FROM reviews r 
                JOIN users u ON u.id = r.user_id 
                LEFT JOIN bookings b ON b.id = r.booking_id 
                LEFT JOIN rooms rm ON rm.id = b.room_id
                ORDER BY r.created_at DESC
            ")->fetchAll();
            $settings = (new HotelSettingsController())->getSettings();
            
            // Check if user has checked in (has completed bookings)
            $userBookings = [];
            $canReview = false;
            if (isAuthenticated()) {
                $user = currentUser();
                $userBookings = $pdo->prepare("
                    SELECT b.*, r.number as room_number 
                    FROM bookings b 
                    JOIN rooms r ON r.id = b.room_id 
                    WHERE b.user_id = ? AND b.status = 'confirmed' 
                    ORDER BY b.created_at DESC
                ");
                $userBookings->execute([$user['id']]);
                $userBookings = $userBookings->fetchAll();
                $canReview = count($userBookings) > 0;
            }
            
            render('reviews', [
                'reviews' => $reviews, 
                'settings' => $settings,
                'userBookings' => $userBookings,
                'canReview' => $canReview
            ], 'Reviews');
        },

        '/setup' => function () {
            include __DIR__ . '/scripts/setup.php';
        },
    ],
    'POST' => [
        '/login' => [AuthController::class, 'login'],
        '/register' => [AuthController::class, 'register'],
        '/logout' => [AuthController::class, 'logout'],

        '/booking' => [BookingController::class, 'store'],
        '/booking/pay' => [BookingController::class, 'pay'],

        '/reception/book' => [ReceptionController::class, 'createBooking'],

        '/kitchen/menu/create' => [KitchenController::class, 'createItem'],
        '/kitchen/menu/toggle' => [KitchenController::class, 'toggleItem'],
        '/kitchen/upload-image' => [KitchenController::class, 'uploadImage'],
        '/kitchen/menu-item/create' => [KitchenController::class, 'createMenuItem'],
        '/kitchen/menu-item/remove' => [KitchenController::class, 'removeMenuItem'],

        '/orders/create' => [OrderController::class, 'create'],
        '/orders/process-payment' => [OrderController::class, 'processPayment'],
        '/orders/update-status' => [OrderController::class, 'updateStatus'],

        '/chat/send' => [ChatController::class, 'send'],
        '/chat/mark-read' => [ChatController::class, 'markAsRead'],

        // Admin POST routes
        '/admin/settings/update' => [HotelSettingsController::class, 'update'],
        '/admin/settings/upload-image' => [HotelSettingsController::class, 'uploadImage'],
        '/admin/settings/delete-image' => [HotelSettingsController::class, 'deleteImage'],
        '/admin/users/update' => [AdminController::class, 'updateUser'],
        '/admin/refund/process' => [RefundController::class, 'process'],
        '/admin/customers/update' => [AdminController::class, 'updateCustomer'],
        '/admin/customers/reset-password' => [AdminController::class, 'resetCustomerPassword'],

        // M-Pesa routes
        '/mpesa/pay' => [MpesaController::class, 'initiatePayment'],
        '/mpesa/callback' => [MpesaController::class, 'callback'],

        // Receipt routes
        '/receipt/generate' => [ReceiptController::class, 'generate'],
        '/receipt/download' => [ReceiptController::class, 'download'],

        // Review routes
        '/reviews/submit' => function () {
            requireAuth();
            verify_csrf();
            $pdo = db();
            $userId = currentUser()['id'];
            $bookingId = (int)(input('booking_id', '0') ?? '0');
            $rating = (int)(input('rating', '0') ?? '0');
            $comment = input('comment', '');
            
            if ($rating < 1 || $rating > 5) {
                redirect('/reviews?error=1');
                return;
            }
            
            // Check if user has confirmed bookings (can review)
            $hasConfirmedBooking = $pdo->prepare("SELECT 1 FROM bookings WHERE user_id = ? AND status = 'confirmed' LIMIT 1");
            $hasConfirmedBooking->execute([$userId]);
            if (!$hasConfirmedBooking->fetch()) {
                redirect('/reviews?error=2');
                return;
            }
            
            $stmt = $pdo->prepare('INSERT INTO reviews (user_id, booking_id, rating, comment, created_at) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$userId, $bookingId ?: null, $rating, $comment, now()]);
            
            redirect('/reviews?success=1');
        },
    ],
];

// --- Dispatch Function ---
function dispatch(array $routes, string $method, string $uri): void {
    $handlers = $routes[$method] ?? [];

    // First try exact match
    if (isset($handlers[$uri])) {
        $handler = $handlers[$uri];

        if (is_array($handler)) {
            [$class, $action] = $handler;
            $instance = new $class();
            $instance->$action();
            return;
        }

        if (is_callable($handler)) {
            $handler();
            return;
        }
    }

    // Then try pattern matching for dynamic routes
    foreach ($handlers as $pattern => $handler) {
        if (strpos($pattern, '{') !== false) {
            // Convert pattern to regex
            $regex = preg_replace('/\{[^}]+\}/', '([^/]+)', $pattern);
            $regex = '#^' . $regex . '$#';
            
            if (preg_match($regex, $uri, $matches)) {
                // Store URL parameters in $_GET for the controller to access
                $paramNames = [];
                preg_match_all('/\{([^}]+)\}/', $pattern, $paramNames);
                
                for ($i = 1; $i < count($matches); $i++) {
                    if (isset($paramNames[1][$i-1])) {
                        $_GET[$paramNames[1][$i-1]] = $matches[$i];
                    }
                }

                if (is_array($handler)) {
                    [$class, $action] = $handler;
                    $instance = new $class();
                    $instance->$action();
                    return;
                }

                if (is_callable($handler)) {
                    $handler();
                    return;
                }
            }
        }
    }

    http_response_code(404);
    render('errors/404', [], 'Page Not Found'); // better than plain text
}

// --- Run Dispatcher ---
dispatch($routes, $method, $uri);
