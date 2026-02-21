<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TabbyController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\AboutAcademyController;
use App\Http\Controllers\Api\LiveClassController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/tabby/webhook', [TabbyController::class, 'webhook'])->name('api.tabby.webhook');

// Auth API Routes
Route::post('/auth/register', [AuthController::class, 'register'])->name('api.auth.register');
Route::post('/auth/login', [AuthController::class, 'login'])->name('api.auth.login');

// Protected Auth Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
    Route::get('/auth/user', [AuthController::class, 'user'])->name('api.auth.user');
    Route::put('/users/profile', [AuthController::class, 'updateProfile'])->name('api.users.profile.update');
    Route::post('/auth/verify-email', [AuthController::class, 'verifyEmail'])->name('api.auth.verify');
    Route::post('/auth/resend-verification', [AuthController::class, 'resendVerification'])->name('api.auth.resend');
});

// Course API Routes
Route::get('/courses', [CourseController::class, 'index'])->name('api.courses.index');
Route::get('/courses/featured', [CourseController::class, 'featured'])->name('api.courses.featured');
Route::get('/courses/search', [CourseController::class, 'search'])->name('api.courses.search');
Route::get('/courses/{id}', [CourseController::class, 'show'])->name('api.courses.show');
Route::get('/courses/{id}/reviews', [CourseController::class, 'getReviews'])->name('api.courses.reviews.index');
Route::get('/courses/{id}/questions', [App\Http\Controllers\Api\CourseQuestionController::class, 'index'])->name('api.courses.questions.index');

// Course review & Q&A (protected: auth required)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/courses/{id}/review', [CourseController::class, 'storeReview'])->name('api.courses.review.store');
    Route::post('/courses/{id}/questions', [App\Http\Controllers\Api\CourseQuestionController::class, 'store'])->name('api.courses.questions.store');
});

// Category API Routes
Route::get('/categories', [CategoryController::class, 'index'])->name('api.categories.index');
Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('api.categories.show');

// Instructor API Routes
Route::get('/instructors', [App\Http\Controllers\Api\InstructorController::class, 'index'])->name('api.instructors.index');
Route::get('/instructors/top', [App\Http\Controllers\Api\InstructorController::class, 'top'])->name('api.instructors.top');
Route::get('/instructors/{id}', [App\Http\Controllers\Api\InstructorController::class, 'show'])->name('api.instructors.show');

// Enrollment API Routes (Protected)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/enrollments', [EnrollmentController::class, 'index'])->name('api.enrollments.index');
    Route::post('/enrollments', [EnrollmentController::class, 'store'])->name('api.enrollments.store');
    Route::get('/enrollments/{id}', [EnrollmentController::class, 'show'])->name('api.enrollments.show');
    Route::get('/enrollments/check/{courseId}', [EnrollmentController::class, 'check'])->name('api.enrollments.check');
});

// Order API Routes (Protected)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/orders', [OrderController::class, 'store'])->name('api.orders.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('api.orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('api.orders.show');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('api.orders.cancel');
});

// Blog API Routes
Route::get('/blogs', [BlogController::class, 'index'])->name('api.blogs.index');
Route::get('/blogs/{id}', [BlogController::class, 'show'])->name('api.blogs.show');
Route::get('/blog-categories', [BlogController::class, 'categories'])->name('api.blog-categories.index');

// Language API Routes
Route::get('/languages', [LanguageController::class, 'index'])->name('api.languages.index');

// About Academy API Routes
Route::get('/about-academy', [AboutAcademyController::class, 'index'])->name('api.about-academy.index');

// Live Classes API Routes
Route::get('/live-classes', [LiveClassController::class, 'index'])->name('api.live-classes.index');
Route::get('/live-classes/{id}', [LiveClassController::class, 'show'])->name('api.live-classes.show');
Route::post('/live-classes/{id}/register', [LiveClassController::class, 'register'])
    ->middleware('auth:sanctum')
    ->name('api.live-classes.register');

// Sliders API (home page banner - same as website)
Route::get('/sliders', [App\Http\Controllers\Api\SliderController::class, 'index'])->name('api.sliders.index');

// Cart API Routes (Protected)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cart', [App\Http\Controllers\Api\CartController::class, 'index'])->name('api.cart.index');
    Route::post('/cart/items', [App\Http\Controllers\Api\CartController::class, 'store'])->name('api.cart.store');
    Route::delete('/cart/items/{courseId}', [App\Http\Controllers\Api\CartController::class, 'destroy'])->name('api.cart.destroy');
    Route::get('/cart/total', [App\Http\Controllers\Api\CartController::class, 'total'])->name('api.cart.total');
    Route::get('/cart/count', [App\Http\Controllers\Api\CartController::class, 'count'])->name('api.cart.count');
    Route::delete('/cart', [App\Http\Controllers\Api\CartController::class, 'clear'])->name('api.cart.clear');
});

// Wishlist API Routes (Protected)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/wishlist', [App\Http\Controllers\Api\WishlistController::class, 'index'])->name('api.wishlist.index');
    Route::post('/wishlist/{courseId}', [App\Http\Controllers\Api\WishlistController::class, 'store'])->name('api.wishlist.store');
    Route::delete('/wishlist/{courseId}', [App\Http\Controllers\Api\WishlistController::class, 'destroy'])->name('api.wishlist.destroy');
    Route::post('/wishlist/{courseId}/toggle', [App\Http\Controllers\Api\WishlistController::class, 'toggle'])->name('api.wishlist.toggle');
    Route::get('/wishlist/{courseId}/check', [App\Http\Controllers\Api\WishlistController::class, 'check'])->name('api.wishlist.check');
});
