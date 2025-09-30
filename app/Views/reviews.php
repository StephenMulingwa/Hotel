<style>
        .review-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .review-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .star-rating {
            color: #fbbf24;
        }
        .star-rating .empty {
            color: #d1d5db;
        }
        .rating-input {
            display: none;
        }
        .rating-label {
            cursor: pointer;
            font-size: 24px;
            color: #d1d5db;
            transition: color 0.2s ease-in-out;
        }
        .rating-input:checked ~ .rating-label,
        .rating-label:hover,
        .rating-label:hover ~ .rating-label {
            color: #fbbf24;
        }
    </style>

    <div class="p-6">
        <!-- Review Statistics -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-4xl font-bold text-gray-900 mb-2">
                        <?= count($reviews) > 0 ? number_format(array_sum(array_column($reviews, 'rating')) / count($reviews), 1) : '0.0' ?>
                    </div>
                    <div class="flex items-center justify-center space-x-1 mb-2">
                        <?php 
                        $avgRating = count($reviews) > 0 ? array_sum(array_column($reviews, 'rating')) / count($reviews) : 0;
                        for ($i = 1; $i <= 5; $i++): 
                        ?>
                            <i class="fas fa-star text-lg <?= $i <= $avgRating ? 'text-yellow-400' : 'text-gray-300' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="text-sm text-gray-600">Average Rating</p>
                </div>
                
                <div class="text-center">
                    <div class="text-4xl font-bold text-gray-900 mb-2"><?= count($reviews) ?></div>
                    <p class="text-sm text-gray-600">Total Reviews</p>
                </div>
                
                <div class="text-center">
                    <div class="text-4xl font-bold text-gray-900 mb-2">
                        <?= count(array_filter($reviews, fn($r) => $r['rating'] >= 4)) ?>
                    </div>
                    <p class="text-sm text-gray-600">Positive Reviews</p>
                </div>
            </div>
        </div>

        <!-- Add Review Form (for authenticated users) -->
        <?php if (isAuthenticated()): ?>
            <?php if ($canReview): ?>
                <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Share Your Experience</h3>
            <?php else: ?>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mr-3"></i>
                        <div>
                            <h3 class="text-lg font-semibold text-yellow-800 mb-2">Review Not Available</h3>
                            <p class="text-yellow-700 mb-4">You need to have a confirmed booking to leave a review. Please book a room first to share your experience.</p>
                            <a href="/booking/new" class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                                <i class="fas fa-calendar-plus mr-2"></i>Book a Room
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6 mb-8 hidden">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Share Your Experience</h3>
            <?php endif; ?>
                
                <form id="reviewForm" method="POST" action="/reviews/submit" class="space-y-4">
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Your Rating</label>
                        <div class="flex items-center space-x-1">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" id="rating_<?= $i ?>" name="rating" value="<?= $i ?>" class="rating-input" required>
                                <label for="rating_<?= $i ?>" class="rating-label">
                                    <i class="fas fa-star"></i>
                                </label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                    <div>
                        <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Your Review</label>
                        <textarea id="comment" name="comment" rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                  placeholder="Tell us about your stay..."></textarea>
                    </div>
                    
                    <div>
                        <label for="booking_id" class="block text-sm font-medium text-gray-700 mb-2">Booking (Optional)</label>
                        <select id="booking_id" name="booking_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                            <option value="">Select a booking (optional)</option>
                            <?php if (isset($userBookings)): ?>
                                <?php foreach ($userBookings as $booking): ?>
                                    <option value="<?= $booking['id'] ?>">
                                        Room <?= $booking['room_number'] ?> - <?= date('M j, Y', strtotime($booking['start_date'])) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="flex items-center justify-end">
                        <button type="submit" 
                                class="px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                            <i class="fas fa-star mr-2"></i>Submit Review
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Reviews List -->
        <div class="space-y-6">
            <?php if (empty($reviews)): ?>
                <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                    <i class="fas fa-star text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Reviews Yet</h3>
                    <p class="text-gray-600">Be the first to share your experience!</p>
                </div>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="review-card bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-blue-600"></i>
                            </div>
                            
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">
                                            <?= htmlspecialchars($review['customer_name']) ?>
                                        </h4>
                                        <?php if ($review['room_number']): ?>
                                            <p class="text-sm text-gray-600">Room <?= $review['room_number'] ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-right">
                                        <div class="flex items-center space-x-1 mb-1">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star text-sm <?= $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <p class="text-sm text-gray-500">
                                            <?= date('M j, Y', strtotime($review['created_at'])) ?>
                                        </p>
                                    </div>
                                </div>
                                
                                <?php if ($review['comment']): ?>
                                    <p class="text-gray-700 leading-relaxed"><?= htmlspecialchars($review['comment']) ?></p>
                                <?php endif; ?>
                                
                                <div class="mt-4 flex items-center space-x-4">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-thumbs-up text-green-500"></i>
                                        <span class="text-sm text-gray-600">Helpful</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-reply text-blue-500"></i>
                                        <span class="text-sm text-gray-600">Reply</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Load More Button -->
        <?php if (count($reviews) > 10): ?>
            <div class="text-center mt-8">
                <button class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    Load More Reviews
                </button>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Rating input interaction
        document.querySelectorAll('.rating-input').forEach(input => {
            input.addEventListener('change', function() {
                const rating = this.value;
                const labels = document.querySelectorAll('.rating-label');
                
                labels.forEach((label, index) => {
                    if (index < rating) {
                        label.style.color = '#fbbf24';
                    } else {
                        label.style.color = '#d1d5db';
                    }
                });
            });
        });

        // Form submission
        document.getElementById('reviewForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('/reviews/submit', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    alert('Review submitted successfully!');
                    location.reload();
                } else {
                    alert('Error submitting review');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error submitting review');
            });
        });
    </script>