<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
    <div class="max-w-6xl mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-5xl font-bold text-gray-900 mb-4">
                Welcome to <?php echo htmlspecialchars(CONFIG()['app']['name']); ?>
            </h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Experience luxury hospitality with seamless room booking, delicious meals, and exceptional service
            </p>
        </div>

        <!-- Customer Section -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user text-white text-3xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Guest Portal</h2>
                <p class="text-gray-600">Book rooms, order meals, and enjoy our services</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-2xl mx-auto">
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-plus text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">New Guest?</h3>
                    <p class="text-gray-600 mb-4">Create an account to start booking and ordering</p>
                    <a href="/register" 
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-300 shadow-lg hover:shadow-xl">
                        <i class="fas fa-user-plus mr-2"></i>
                        Create Account
                    </a>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-sign-in-alt text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Returning Guest?</h3>
                    <p class="text-gray-600 mb-4">Sign in to access your bookings and orders</p>
                    <a href="/login" 
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Sign In
                    </a>
                </div>
            </div>
        </div>

        <!-- Staff Section -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-gradient-to-r from-orange-500 to-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-white text-3xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Staff Portal</h2>
                <p class="text-gray-600">Access your department dashboard</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-6 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors">
                    <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-concierge-bell text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Reception</h3>
                    <p class="text-gray-600 mb-4">Manage bookings and guest services</p>
                    <a href="/reception" 
                       class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                        <i class="fas fa-arrow-right mr-2"></i>
                        Access Dashboard
                    </a>
                </div>
                
                <div class="text-center p-6 bg-green-50 rounded-xl hover:bg-green-100 transition-colors">
                    <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-utensils text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Kitchen</h3>
                    <p class="text-gray-600 mb-4">Manage food orders and menu</p>
                    <a href="/kitchen" 
                       class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                        <i class="fas fa-arrow-right mr-2"></i>
                        Access Dashboard
                    </a>
                </div>
                
                <div class="text-center p-6 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors">
                    <div class="w-16 h-16 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chart-line text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Admin</h3>
                    <p class="text-gray-600 mb-4">System management and analytics</p>
                    <a href="/admin" 
                       class="inline-flex items-center px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors">
                        <i class="fas fa-arrow-right mr-2"></i>
                        Access Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Features Preview -->
        <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center p-6 bg-white rounded-xl shadow-lg">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bed text-blue-600 text-xl"></i>
                </div>
                <h4 class="font-semibold text-gray-900 mb-2">Room Booking</h4>
                <p class="text-sm text-gray-600">Easy online room reservation</p>
            </div>
            
            <div class="text-center p-6 bg-white rounded-xl shadow-lg">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-utensils text-green-600 text-xl"></i>
                </div>
                <h4 class="font-semibold text-gray-900 mb-2">Food Ordering</h4>
                <p class="text-sm text-gray-600">Order meals to your room</p>
            </div>
            
            <div class="text-center p-6 bg-white rounded-xl shadow-lg">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-comments text-purple-600 text-xl"></i>
                </div>
                <h4 class="font-semibold text-gray-900 mb-2">Live Chat</h4>
                <p class="text-sm text-gray-600">24/7 customer support</p>
            </div>
        </div>
    </div>
</div>
