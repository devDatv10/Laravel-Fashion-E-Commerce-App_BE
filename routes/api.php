<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AdminController,
    CartController,
    CartDetailController,
    ProductController,
    CategoryController,
    CustomerController,
    EventController,
    NotificationController,
    OrderController,
    OrderDetailController,
    ProductFavoriteController,
    ReportReviewController,
    ReviewController,
    StaffController,
    StoreController,
    StoreSettingController,
    WidgetNoReviewSettingController
};

Route::prefix('')->group(function () {
    // Store Routes Store and Store Settings
    Route::apiResource('stores', StoreController::class);
    Route::apiResource('store_settings', StoreSettingController::class);

    // Event Routes
    Route::prefix('events')->group(function () {
        Route::get('/is-active/active', [EventController::class, 'getActiveEvents']);
        Route::get('/is-active/inactive', [EventController::class, 'getInactiveEvents']);
        Route::put('/{event_id}/set-active', [EventController::class, 'activateEvent']);
        Route::put('/{event_id}/set-inactive', [EventController::class, 'deactivateEvent']);
    });
    Route::apiResource('events', EventController::class);


    // Category and Product Routes
    Route::prefix('categories')->group(function () {
        Route::get('/{limit}/limit', [CategoryController::class, 'getLimitedCategories']);
    });
    Route::apiResource('categories', CategoryController::class);


    Route::prefix('products')->group(function () {
        Route::get('/search', [ProductController::class, 'searchProducts']);
        Route::get('filter-by-stars', [ProductController::class, 'filterProductsByStars']);
        Route::get('filter-by-sizes', [ProductController::class, 'filterProductsBySizes']);
        Route::get('filter-by-total-reviews', [ProductController::class, 'filterProductsByTotalReviews']);
        Route::get('filter-by-average-reviews', [ProductController::class, 'filterProductsByAverageReviews']);
        Route::get('category/{category_id}', [ProductController::class, 'getProductsByCategoryId']);
        Route::get('category/{category_id}/{limit}/limit', [ProductController::class, 'getLimitedProductsByCategoryId']);
        Route::get('{limit}/limit', [ProductController::class, 'getLimitedProducts']);
    });
    Route::apiResource('products', ProductController::class);

    // User Management Routes (Admin, Staff, Customer)
    Route::apiResource('admins', AdminController::class);
    Route::apiResource('staffs', StaffController::class);
    Route::apiResource('customers', CustomerController::class);
    Route::middleware('api')->post('customers/auth/login', [CustomerController::class, 'authenticateLoginCustomer']);
    Route::middleware('auth:sanctum')->post('customers/auth/logout', [CustomerController::class, 'logout']);
    Route::post('customers/auth/refresh-token', [CustomerController::class, 'refreshAccessToken']);
    // Route::middleware('auth:sanctum')->post('customers/auth/refresh-token', [CustomerController::class, 'refreshAccessToken']);
    Route::middleware('auth:sanctum')->get('/profile', function (Request $request) {
        return response()->json($request->user());
    });

    // Product favorite Routes
    Route::apiResource('product_favorites', ProductFavoriteController::class);
    Route::get('product_favorites/customer/{customer_id}', [ProductFavoriteController::class, 'getFavoriteProductByCustomerId']);

    // Cart and CartDetail Routes
    Route::prefix('carts')->group(function () {
        Route::get('/{cart_id}', [CartController::class, 'getCartByCartId']);
        Route::get('/all-carts/customer/{customer_id}', [CartController::class, 'getAllCartByCustomerId']);
        Route::get('/not-ordered-carts/customer/{customer_id}', [CartController::class, 'getNotOrderedCartByCustomerId']);
    });
    Route::apiResource('carts', CartController::class);

    Route::prefix('cart_details')->group(function () {
        Route::get('/cart/{cart_id}', [CartDetailController::class, 'getCartDetailByCartId']);
        Route::get('/customer/{customer_id}/all-cart-details', [CartDetailController::class, 'getAllCartDetailByCustomerId']);
        Route::get('/customer/{customer_id}/not-ordered-cart_details', [CartDetailController::class, 'getNotOrderedCartDetailByCustomerId']);
        Route::delete('/{cart_detail_id}/delete-item-in-cart', [CartDetailController::class, 'deleteItemInCart']);
    });
    Route::resource('cart_details', CartDetailController::class)->except(['destroy']);


    // Order and Order Detail Routes
    Route::apiResource('orders', OrderController::class);

    Route::apiResource('order_details', OrderDetailController::class);

    // Notification Routes
    Route::prefix('notifications')->group(function () {
        Route::post('/hide/{notification_id}', [NotificationController::class, 'hideNotification']);
    });
    Route::apiResource('notifications', NotificationController::class);

    // Review Routes
    Route::prefix('reviews')->group(function () {
        Route::post('/delete-review', [ReviewController::class, 'destroyMany']);
        Route::get('/product/{product_id}', [ReviewController::class, 'getReviewsByProductId']);
        Route::get('/product/{product_id}/limit', [ReviewController::class, 'getReviewsByProductIdLimit']);
        Route::get('/status/pending', [ReviewController::class, 'getPendingReviews']);
        Route::get('/status/approved', [ReviewController::class, 'getApprovedReviews']);
        Route::get('/status/unpublished', [ReviewController::class, 'getUnpublishedReviews']);
        Route::put('/{review_id}/publish', [ReviewController::class, 'publishReview']);
        Route::put('/{review_id}/unpublish', [ReviewController::class, 'unpublishReview']);
        Route::put('/{review_id}/reply', [ReviewController::class, 'replyReview']);
        Route::put('/{review_id}/feature', [ReviewController::class, 'featureReview']);
        Route::put('/{review_id}/unfeature', [ReviewController::class, 'unFeatureReview']);
        Route::get('/filter-by-most-helpful', [ReviewController::class, 'filterReviewByMostHelpful']);
        Route::get('/filter-by-star', [ReviewController::class, 'filterReviewsByStar']);
        Route::get('/filter-by-highest', [ReviewController::class, 'filterReviewByHighest']);
        Route::get('/filter-by-lowest', [ReviewController::class, 'filterReviewByLowest']);
        Route::get('/filter-by-newest', [ReviewController::class, 'filterReviewByNewest']);
        Route::get('/filter-by-oldest', [ReviewController::class, 'filterReviewByOldest']);
        Route::get('/filter-by-media', [ReviewController::class, 'filterReviewByMedia']);
        Route::post('/post-helpful-count', [ReviewController::class, 'postHelpfulCount']);
    });
    Route::apiResource('reviews', ReviewController::class);

    // Report Review Routes
    Route::prefix('report_reviews')->group(function () {
        Route::put('/{report_id}/reply', [ReportReviewController::class, 'replyReport']);
    });
    Route::apiResource('report_reviews', ReportReviewController::class);

    // Settings Widget No Reviews Routes
    Route::prefix('widget_no_reviews_settings')->group(function () {
        Route::post('/{no_review_setting_id}/reset', [WidgetNoReviewSettingController::class, 'resetDefaultSettings']);
    });
    Route::apiResource('widget_no_reviews_settings', WidgetNoReviewSettingController::class);

    // Authenticated User Route
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');
});
