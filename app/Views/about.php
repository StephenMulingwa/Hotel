<?php
$title = 'About Us - ' . ($settings['hotel_name'] ?? 'Hotel');
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
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .feature-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .amenity-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="flex items-center justify-between p-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                    <i class="fas fa-hotel"></i>
                </div>
                <div>
                    <h1 class="font-semibold text-gray-900"><?= htmlspecialchars($settings['hotel_name'] ?? 'Hotel') ?></h1>
                    <p class="text-sm text-gray-500">About Our Hotel</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <a href="/" class="p-2 text-gray-600 hover:text-gray-800 rounded-full hover:bg-gray-100">
                    <i class="fas fa-home"></i>
                </a>
                <a href="/login" class="p-2 text-gray-600 hover:text-gray-800 rounded-full hover:bg-gray-100">
                    <i class="fas fa-sign-in-alt"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <div class="hero-section text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    Welcome to <?= htmlspecialchars($settings['hotel_name'] ?? 'Our Hotel') ?>
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-blue-100">
                    Your Home Away From Home
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                    <a href="/register" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                        <i class="fas fa-user-plus mr-2"></i>Book Now
                    </a>
                    <a href="/reviews" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
                        <i class="fas fa-star mr-2"></i>Read Reviews
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Hotel Information -->
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">About Our Hotel</h2>
                <div class="w-24 h-1 bg-blue-500 mx-auto"></div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Our Story</h3>
                    <div class="prose prose-lg text-gray-600">
                        <?= nl2br(htmlspecialchars($settings['hotel_info'] ?? 'Welcome to our beautiful hotel with excellent service and amenities. We provide comfortable rooms, delicious food, and outstanding hospitality.')) ?>
                    </div>
                    
                    <div class="mt-8 grid grid-cols-2 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600 mb-2"><?= $settings['total_rooms'] ?? 40 ?></div>
                            <div class="text-sm text-gray-600">Rooms Available</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600 mb-2">24/7</div>
                            <div class="text-sm text-gray-600">Customer Support</div>
                        </div>
                    </div>
                </div>
                
                <div class="relative">
                    <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800" 
                         alt="Hotel Interior" 
                         class="rounded-lg shadow-xl">
                    <div class="absolute -bottom-6 -right-6 bg-white p-6 rounded-lg shadow-lg">
                        <div class="flex items-center space-x-2">
                            <div class="flex items-center space-x-1">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star text-yellow-400"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">5.0 Rating</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Why Choose Us?</h2>
                <p class="text-lg text-gray-600">Experience the best in hospitality and comfort</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="feature-card bg-white rounded-lg p-6 shadow-sm">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-wifi text-blue-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Free WiFi</h3>
                    <p class="text-gray-600">High-speed internet access throughout the hotel</p>
                </div>
                
                <div class="feature-card bg-white rounded-lg p-6 shadow-sm">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-utensils text-green-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Room Service</h3>
                    <p class="text-gray-600">Delicious meals delivered to your room 24/7</p>
                </div>
                
                <div class="feature-card bg-white rounded-lg p-6 shadow-sm">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-concierge-bell text-purple-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">24/7 Support</h3>
                    <p class="text-gray-600">Round-the-clock assistance for all your needs</p>
                </div>
                
                <div class="feature-card bg-white rounded-lg p-6 shadow-sm">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-shield-alt text-orange-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Secure & Safe</h3>
                    <p class="text-gray-600">Your safety and security are our top priority</p>
                </div>
                
                <div class="feature-card bg-white rounded-lg p-6 shadow-sm">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-credit-card text-red-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Easy Payments</h3>
                    <p class="text-gray-600">Secure payment processing with M-Pesa integration</p>
                </div>
                
                <div class="feature-card bg-white rounded-lg p-6 shadow-sm">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-comments text-yellow-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Live Chat</h3>
                    <p class="text-gray-600">Instant communication with our staff</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Amenities Section -->
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Hotel Amenities</h2>
                <p class="text-lg text-gray-600">Everything you need for a comfortable stay</p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-bed text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Comfortable Rooms</h3>
                    <p class="text-sm text-gray-600">Spacious and well-appointed accommodations</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-car text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Parking</h3>
                    <p class="text-sm text-gray-600">Free parking for all guests</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-swimming-pool text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Swimming Pool</h3>
                    <p class="text-sm text-gray-600">Relax and unwind in our pool</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-dumbbell text-orange-600 text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Fitness Center</h3>
                    <p class="text-sm text-gray-600">Stay fit during your stay</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Section -->
    <div class="py-16 bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">Get In Touch</h2>
                <p class="text-lg text-gray-300">We're here to help with any questions or special requests</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-phone text-white"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Call Us</h3>
                    <p class="text-gray-300">+254 700 000 000</p>
                </div>
                
                <div class="text-center">
                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-envelope text-white"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Email Us</h3>
                    <p class="text-gray-300">info@<?= strtolower(str_replace(' ', '', $settings['hotel_name'] ?? 'hotel')) ?>.com</p>
                </div>
                
                <div class="text-center">
                    <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-map-marker-alt text-white"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Visit Us</h3>
                    <p class="text-gray-300">Nairobi, Kenya</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="bg-gray-800 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-gray-400">&copy; 2024 <?= htmlspecialchars($settings['hotel_name'] ?? 'Hotel') ?>. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>