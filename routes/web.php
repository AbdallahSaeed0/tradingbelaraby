<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\QuestionsAnswersController;
use App\Http\Controllers\LiveClassController;
use App\Http\Controllers\HomeworkController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseRatingController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ComingSoonController;

// Coming Soon routes
Route::get('/coming-soon', [ComingSoonController::class, 'index'])->name('coming-soon');
Route::post('/coming-soon/subscribe', [ComingSoonController::class, 'subscribe'])->name('coming-soon.subscribe');

// Public routes test
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.attempt');
});

// Email Verification Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');
    
    Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('home')->with('success', 'Your email has been verified successfully!');
    })->middleware(['signed'])->name('verification.verify');
    
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Verification link sent! Please check your email.');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Public pages
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [App\Http\Controllers\ContactController::class, 'store'])->name('contact.store');
Route::get('/page/{slug}', [PageController::class, 'termsConditions'])->name('terms-conditions');

// Newsletter routes
Route::post('/newsletter/subscribe', [App\Http\Controllers\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::post('/newsletter/unsubscribe', [App\Http\Controllers\NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

// Instructor routes
Route::get('/instructors', [App\Http\Controllers\InstructorController::class, 'index'])->name('instructor.index');
Route::get('/instructors/{id}', [App\Http\Controllers\InstructorController::class, 'show'])->name('instructor.show');

// Language switching routes
Route::get('/language/{code}', function($code, \Illuminate\Http\Request $request) {
    // Frontend language switching
    $success = \App\Helpers\TranslationHelper::setFrontendLanguage($code);
    if ($success) {
        \App\Helpers\TranslationHelper::clearCache();
    }

    // Get the intended URL from query parameter or referrer, fallback to home
    $intendedUrl = $request->query('redirect');

    // If no redirect parameter, try to use referrer
    if (!$intendedUrl) {
        $referer = $request->header('referer');
        if ($referer) {
            // Extract path from full URL if it's a full URL
            $parsedUrl = parse_url($referer);
            $intendedUrl = $parsedUrl['path'] ?? $referer;
            if (isset($parsedUrl['query'])) {
                $intendedUrl .= '?' . $parsedUrl['query'];
            }
        }
    }

    // Validate that the URL is from the same domain to prevent open redirects
    if ($intendedUrl) {
        $appUrl = config('app.url');
        // Check if it's a relative path or from the same domain
        if (strpos($intendedUrl, '/') === 0 || strpos($intendedUrl, $appUrl) === 0) {
            return redirect($intendedUrl);
        }
    }

    // Fallback to home if no valid redirect URL
    return redirect()->route('home');
})->name('language.switch');

// Admin language switching route
Route::get('/admin/language/{code}', function($code) {
    // Admin language switching
    $success = \App\Helpers\TranslationHelper::setAdminLanguage($code);
    if ($success) {
        \App\Helpers\TranslationHelper::clearCache();
    }
    return redirect()->back();
})->name('admin.language.switch')->middleware('auth');

// Debug route to test language switching
Route::get('/debug-language', function() {
    $frontendLanguage = \App\Helpers\TranslationHelper::getFrontendLanguage();
    $adminLanguage = \App\Helpers\TranslationHelper::getAdminLanguage();
    $frontendLocale = session('frontend_locale');
    $adminLocale = session('admin_locale');
    $appLocale = app()->getLocale();

    return response()->json([
        'frontend_language' => $frontendLanguage ? $frontendLanguage->toArray() : null,
        'admin_language' => $adminLanguage ? $adminLanguage->toArray() : null,
        'frontend_locale' => $frontendLocale,
        'admin_locale' => $adminLocale,
        'app_locale' => $appLocale,
        'is_admin_area' => request()->is('admin*'),
    ]);
})->name('debug.language');

// Test language page
Route::get('/test-language', function() {
    return view('test-language');
})->name('test.language');

// Course-related routes
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/search', [CourseController::class, 'search'])->name('courses.search');
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
Route::post('/courses/{course}/review', [CourseRatingController::class, 'store'])
    ->middleware('auth')
    ->name('courses.review.store');

// Blog routes
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{blog:slug}', [BlogController::class, 'show'])->name('blog.show');

// Trader registration routes
Route::post('/traders', [App\Http\Controllers\TraderController::class, 'store'])->name('traders.store');

// Admin trader management routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('traders/export', [App\Http\Controllers\Admin\TraderController::class, 'export'])->name('traders.export');
    Route::delete('traders/bulk-delete', [App\Http\Controllers\Admin\TraderController::class, 'bulkDelete'])->name('traders.bulk-delete');
    Route::resource('traders', App\Http\Controllers\Admin\TraderController::class)->only(['index', 'show', 'destroy']);
});
Route::post('/courses/{course}/enroll', [EnrollmentController::class, 'enroll'])->name('courses.enroll');
Route::get('/category/{category:slug}', [CourseController::class, 'category'])->name('category.show');
Route::get('/categories/{category:slug}', [CourseController::class, 'category'])->name('categories.show'); // Alias for layout compatibility

// Quiz routes
Route::get('/quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');
Route::get('/quizzes/{quiz}/take/{attempt}', [QuizController::class, 'take'])->name('quizzes.take');
Route::get('/quizzes/{quiz}/attempts', [QuizController::class, 'attempts'])->name('quizzes.attempts');
Route::get('/quizzes/{quiz}/results/{attempt}', [QuizController::class, 'results'])->name('quizzes.results');
Route::post('/quizzes/{quiz}/start', [QuizController::class, 'start'])->name('quizzes.start');
Route::post('/quizzes/{quiz}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');
Route::post('/quizzes/{quiz}/save-answer', [QuizController::class, 'saveAnswer'])->name('quizzes.save-answer');

// Course content routes
Route::get('/course/{id}/content', [PageController::class, 'courseContent'])->name('course.content');
Route::get('/course/{id}/quiz', [PageController::class, 'quiz'])->name('course.quiz');

// Protected routes (require authentication)
Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Student routes
    Route::get('/student/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
    Route::get('/student/my-courses', [StudentController::class, 'myCourses'])->name('student.my-courses');
    Route::get('/courses/{course}/learn', [StudentController::class, 'learnCourse'])->name('courses.learn');

    // Enrollment routes
    Route::get('/enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
    Route::get('/enrollments/export', [EnrollmentController::class, 'export'])->name('enrollments.export');

    // Progress routes
    Route::get('/progress/overview', [ProgressController::class, 'overview'])->name('progress.overview');
    Route::get('/progress/course/{course}', [ProgressController::class, 'courseProgress'])->name('progress.course');
    Route::post('/progress/lecture/{lecture}/complete', [ProgressController::class, 'completeLecture'])->name('progress.complete-lecture');
    Route::post('/courses/{course}/complete-lectures', [ProgressController::class, 'completeLectures'])->name('courses.complete-lectures');
    Route::post('/courses/{course}/incomplete-lectures', [ProgressController::class, 'incompleteLectures'])->name('courses.incomplete-lectures');

    // Bundle routes
    Route::get('/bundles', [App\Http\Controllers\BundlesController::class, 'index'])->name('bundles.index');
    Route::get('/bundles/{bundle:slug}', [App\Http\Controllers\BundlesController::class, 'show'])->name('bundles.show');

    // Cart routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{course}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/add-bundle/{bundle}', [CartController::class, 'addBundle'])->name('cart.add-bundle');
    Route::delete('/cart/remove/{course}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart/remove-bundle/{bundle}', [CartController::class, 'removeBundle'])->name('cart.remove-bundle');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // Wishlist routes
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add/{course}', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::delete('/wishlist/remove/{course}', [WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::post('/wishlist/toggle/{course}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Checkout routes
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.apply-coupon');
    Route::post('/checkout/remove-coupon', [CheckoutController::class, 'removeCoupon'])->name('checkout.remove-coupon');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/payment', [CheckoutController::class, 'payment'])->name('checkout.payment');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');

    // Tabby Payment Routes
    Route::get('/tabby/success', [App\Http\Controllers\TabbyController::class, 'success'])->name('tabby.success');
    Route::get('/tabby/cancel', [App\Http\Controllers\TabbyController::class, 'cancel'])->name('tabby.cancel');
    Route::get('/tabby/failure', [App\Http\Controllers\TabbyController::class, 'failure'])->name('tabby.failure');

    // Purchase routes
    Route::get('/purchases', [PurchaseController::class, 'history'])->name('purchases.history');
    Route::get('/purchases', [PurchaseController::class, 'history'])->name('purchase.history'); // Alias for layout compatibility
    Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');

    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Questions & Answers routes
    Route::get('/qa', [QuestionsAnswersController::class, 'index'])->name('qa.index');
    Route::get('/qa/create', [QuestionsAnswersController::class, 'create'])->name('qa.create');
    Route::post('/qa', [QuestionsAnswersController::class, 'store'])->name('qa.store');
    Route::get('/qa/{question}', [QuestionsAnswersController::class, 'show'])->name('qa.show');
    Route::post('/qa/{question}/answer', [QuestionsAnswersController::class, 'answer'])->name('qa.answer');
    Route::post('/qa/{question}/vote', [QuestionsAnswersController::class, 'vote'])->name('qa.vote');
    Route::get('/qa/course/{course}/questions', [QuestionsAnswersController::class, 'courseQuestions'])->name('qa.course-questions');

    // Live Class routes
    Route::get('/live-classes', [LiveClassController::class, 'index'])->name('live-classes.index');
    Route::get('/live-classes/{liveClass}', [LiveClassController::class, 'show'])->name('live-classes.show');
    Route::post('/live-classes/{liveClass}/register', [LiveClassController::class, 'register'])->name('live-classes.register');
    Route::delete('/live-classes/{liveClass}/unregister', [LiveClassController::class, 'unregister'])->name('live-classes.unregister');
    Route::get('/live-classes/{liveClass}/join', [LiveClassController::class, 'join'])->name('live-classes.join');

    // Course-specific Live Class routes
Route::post('/courses/{course}/schedule-live-class', [LiveClassController::class, 'schedule'])->name('courses.schedule-live-class');
Route::post('/courses/{course}/join-live-class', [LiveClassController::class, 'joinCourseLiveClass'])->name('courses.join-live-class');
Route::post('/courses/{course}/live-classes/{liveClass}/download-materials', [LiveClassController::class, 'downloadMaterials'])->name('courses.live-classes.download-materials');

    // Homework routes
    Route::get('/homework', [HomeworkController::class, 'index'])->name('homework.index');
    Route::get('/homework/{homework}', [HomeworkController::class, 'show'])->name('homework.show');
    Route::get('/homework/{homework}/create', [HomeworkController::class, 'create'])->name('homework.create');
    Route::post('/homework/{homework}/submit', [HomeworkController::class, 'store'])->name('homework.submit');
    Route::get('/homework/submission/{submission}', [HomeworkController::class, 'viewSubmission'])->name('homework.submission');
    Route::get('/homework/submission/{submission}/download/{filename}', [HomeworkController::class, 'downloadAttachment'])->name('homework.download-attachment');
    Route::get('/homework/upcoming', [HomeworkController::class, 'upcoming'])->name('homework.upcoming');
    Route::post('/homework/{homework}/view', [HomeworkController::class, 'markAsViewed'])->name('homework.mark-viewed');

    // Course-specific Homework routes
    Route::post('/courses/{course}/submit-homework', [HomeworkController::class, 'submitCourseHomework'])->name('courses.submit-homework');
    Route::get('/courses/{course}/homework/{assignment_id}', [HomeworkController::class, 'getCourseHomework'])->name('courses.homework.details');
    Route::post('/courses/{course}/submit-homework-assignment', [HomeworkController::class, 'submitAssignment'])->name('courses.submit-homework-assignment');
});

// Admin authentication routes
Route::middleware('guest:admin')->group(function () {
    Route::get('/admin/login', [App\Http\Controllers\Admin\AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [App\Http\Controllers\Admin\AdminAuthController::class, 'login'])->name('admin.login.attempt');
});

Route::post('/admin/logout', [App\Http\Controllers\Admin\AdminAuthController::class, 'logout'])->name('admin.logout')->middleware('auth:admin');

// Admin routes
Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard')->middleware('admin.permission:view_analytics,view_own_analytics');

    // Admin profile
    Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');

    // Admin management routes
    Route::resource('admins', App\Http\Controllers\Admin\AdminsController::class)->middleware('admin.permission:manage_admins');
    Route::resource('admin-types', App\Http\Controllers\Admin\AdminTypeController::class)->middleware('admin.permission:manage_admins');
    Route::post('/admin-types/{adminType}/toggle-status', [App\Http\Controllers\Admin\AdminTypeController::class, 'toggleStatus'])->name('admin-types.toggle_status')->middleware('admin.permission:manage_admins');
    Route::delete('/users/bulk-delete', [App\Http\Controllers\Admin\UsersController::class, 'bulkDelete'])->name('users.bulk-delete');
    Route::resource('users', App\Http\Controllers\Admin\UsersController::class)->middleware('admin.permission:manage_users');
    Route::resource('categories', App\Http\Controllers\Admin\CategoriesController::class)->middleware('admin.permission:manage_categories');
    Route::resource('courses', App\Http\Controllers\Admin\CoursesController::class)->middleware('admin.permission:manage_courses,manage_own_courses');
    Route::get('/courses/{course}/duplicate', [App\Http\Controllers\Admin\CoursesController::class, 'duplicate'])
        ->name('courses.duplicate')
        ->middleware('admin.permission:manage_courses,manage_own_courses');
    Route::resource('enrollments', App\Http\Controllers\Admin\EnrollmentsController::class)->middleware('admin.permission:manage_enrollments');
    Route::resource('homework', App\Http\Controllers\Admin\HomeworkManagementController::class)->middleware('admin.permission:manage_homework,manage_own_homework');
    Route::resource('quizzes', App\Http\Controllers\Admin\QuizManagementController::class)->middleware('admin.permission:manage_quizzes,manage_own_quizzes');
    Route::get('/quizzes/get-sections/{course}', [App\Http\Controllers\Admin\QuizManagementController::class, 'getSections'])->name('quizzes.get-sections');
    Route::get('/quizzes/get-lectures/{course}', [App\Http\Controllers\Admin\QuizManagementController::class, 'getLectures'])->name('quizzes.get-lectures');
Route::resource('quizzes.questions', App\Http\Controllers\Admin\QuizQuestionManagementController::class)->middleware('admin.permission:manage_quizzes,manage_own_quizzes');
    Route::resource('live-classes', App\Http\Controllers\Admin\LiveClassManagementController::class)->middleware('admin.permission:manage_live_classes,manage_own_live_classes');
    Route::get('/live-classes/{liveClass}/registrations', [App\Http\Controllers\Admin\LiveClassManagementController::class, 'registrations'])->name('live-classes.registrations')->middleware('admin.permission:manage_live_classes,manage_own_live_classes');
    Route::get('/live-classes/{liveClass}/analytics', [App\Http\Controllers\Admin\LiveClassManagementController::class, 'analytics'])->name('live-classes.analytics')->middleware('admin.permission:manage_live_classes,manage_own_live_classes');
    Route::post('/live-classes/{liveClass}/toggle-status', [App\Http\Controllers\Admin\LiveClassManagementController::class, 'toggleStatus'])->name('live-classes.toggle_status')->middleware('admin.permission:manage_live_classes,manage_own_live_classes');
    Route::get('/live-classes/{liveClass}/duplicate', [App\Http\Controllers\Admin\LiveClassManagementController::class, 'duplicate'])->name('live-classes.duplicate')->middleware('admin.permission:manage_live_classes,manage_own_live_classes');
    Route::post('/live-classes/bulk-delete', [App\Http\Controllers\Admin\LiveClassManagementController::class, 'bulkDelete'])->name('live-classes.bulk_delete')->middleware('admin.permission:manage_live_classes,manage_own_live_classes');
    Route::post('/live-classes/bulk-update-status', [App\Http\Controllers\Admin\LiveClassManagementController::class, 'bulkUpdateStatus'])->name('live-classes.bulk_update_status')->middleware('admin.permission:manage_live_classes,manage_own_live_classes');
    Route::delete('/questions-answers/bulk-delete', [App\Http\Controllers\Admin\QuestionsAnswersManagementController::class, 'bulkDelete'])->name('questions-answers.bulk-delete');
    Route::resource('questions-answers', App\Http\Controllers\Admin\QuestionsAnswersManagementController::class)->middleware('admin.permission:manage_questions_answers,manage_own_questions_answers');
    Route::resource('languages', App\Http\Controllers\Admin\LanguageController::class)->middleware('admin.permission:manage_languages');
    Route::resource('translations', App\Http\Controllers\Admin\TranslationController::class)->middleware('admin.permission:manage_translations');
    Route::resource('blogs', App\Http\Controllers\Admin\BlogsController::class)->middleware('admin.permission:manage_blogs');
    Route::resource('blog-categories', App\Http\Controllers\Admin\BlogCategoryController::class)->parameters(['blog-categories' => 'category'])->middleware('admin.permission:manage_blogs');
    Route::get('/subscribers/export', [App\Http\Controllers\Admin\SubscriberController::class, 'export'])->name('subscribers.export')->middleware('admin.permission:manage_users');
    Route::post('/subscribers/bulk-delete', [App\Http\Controllers\Admin\SubscriberController::class, 'bulkDelete'])->name('subscribers.bulk-delete')->middleware('admin.permission:manage_users');
    Route::resource('subscribers', App\Http\Controllers\Admin\SubscriberController::class)->only(['index', 'show', 'destroy'])->middleware('admin.permission:manage_users');
    
    // Bundle management routes
    Route::resource('bundles', App\Http\Controllers\Admin\BundlesController::class)->middleware('admin.permission:manage_courses');
    
    // Coupon management routes
    Route::resource('coupons', App\Http\Controllers\Admin\CouponsController::class)->middleware('admin.permission:manage_courses');

    // Settings routes
    Route::put('/settings/coming-soon', [App\Http\Controllers\Admin\SettingsController::class, 'updateComingSoon'])->name('settings.coming-soon.update');
    Route::resource('partner-logos', App\Http\Controllers\Admin\PartnerLogoController::class);

    // Admin analytics routes
    Route::get('/courses/{course}/analytics', [App\Http\Controllers\Admin\CoursesController::class, 'analytics'])->name('courses.analytics')->middleware('admin.permission:view_analytics,view_own_analytics');
    Route::get('/courses/{course}/enrollments', [App\Http\Controllers\Admin\CoursesController::class, 'enrollments'])->name('courses.enrollments');
    Route::get('/courses/{course}/enrollments/export', [App\Http\Controllers\Admin\CoursesController::class, 'exportEnrollments'])->name('courses.enrollments.export');

    // Course Sections Routes
    Route::post('/courses/{course}/sections', [App\Http\Controllers\Admin\CourseSectionController::class, 'store'])->name('courses.sections.store');
    Route::put('/courses/{course}/sections/{section}', [App\Http\Controllers\Admin\CourseSectionController::class, 'update'])->name('courses.sections.update');
    Route::delete('/courses/{course}/sections/{section}', [App\Http\Controllers\Admin\CourseSectionController::class, 'destroy'])->name('courses.sections.destroy');

    // Course Lectures Routes
    Route::post('/courses/sections/{section}/lectures', [App\Http\Controllers\Admin\CourseLectureController::class, 'store'])->name('courses.sections.lectures.store');
    Route::put('/courses/sections/{section}/lectures/{lecture}', [App\Http\Controllers\Admin\CourseLectureController::class, 'update'])->name('courses.sections.lectures.update');
    Route::delete('/courses/sections/{section}/lectures/{lecture}', [App\Http\Controllers\Admin\CourseLectureController::class, 'destroy'])->name('courses.sections.lectures.destroy');
    Route::get('/courses/export', [App\Http\Controllers\Admin\CoursesController::class, 'export'])->name('courses.export');
    Route::post('/courses/import', [App\Http\Controllers\Admin\CoursesController::class, 'import'])->name('courses.import');
    Route::get('/courses/template', [App\Http\Controllers\Admin\CoursesController::class, 'downloadTemplate'])->name('courses.template');
    Route::post('/courses/bulk-delete', [App\Http\Controllers\Admin\CoursesController::class, 'bulkDelete'])->name('courses.bulk_delete');
    Route::post('/courses/bulk-update-status', [App\Http\Controllers\Admin\CoursesController::class, 'bulkUpdateStatus'])->name('courses.bulk_update_status');
    Route::get('/homework/{homework}/analytics', [App\Http\Controllers\Admin\HomeworkManagementController::class, 'analytics'])->name('homework.analytics')->middleware('admin.permission:view_analytics,view_own_analytics');
    Route::get('/homework/{homework}/submissions', [App\Http\Controllers\Admin\HomeworkManagementController::class, 'submissions'])->name('homework.submissions');
    Route::post('/homework/submissions/{submission}/grade', [App\Http\Controllers\Admin\HomeworkManagementController::class, 'gradeSubmission'])->name('homework.submissions.grade');
    Route::get('/quizzes/{quiz}/analytics', [App\Http\Controllers\Admin\QuizManagementController::class, 'analytics'])->name('quizzes.analytics')->middleware('admin.permission:view_analytics,view_own_analytics');
    Route::get('/quizzes/{quiz}/attempts', [App\Http\Controllers\Admin\QuizManagementController::class, 'attempts'])->name('quizzes.attempts');
    Route::get('/quizzes/export-list/{format?}', [App\Http\Controllers\Admin\QuizManagementController::class, 'exportList'])->name('quizzes.export_list');
    Route::get('/quizzes/{quiz}/export', [App\Http\Controllers\Admin\QuizManagementController::class, 'export'])->name('quizzes.export');
    Route::post('/quizzes/import', [App\Http\Controllers\Admin\QuizManagementController::class, 'import'])->name('quizzes.import');
    Route::get('/quizzes/template', [App\Http\Controllers\Admin\QuizManagementController::class, 'downloadTemplate'])->name('quizzes.template');
    Route::post('/quizzes/bulk-delete', [App\Http\Controllers\Admin\QuizManagementController::class, 'bulkDelete'])->name('quizzes.bulk_delete');
    Route::post('/quizzes/bulk-update-status', [App\Http\Controllers\Admin\QuizManagementController::class, 'bulkUpdateStatus'])->name('quizzes.bulk_update_status');
    Route::post('/quizzes/{quiz}/toggle-status', [App\Http\Controllers\Admin\QuizManagementController::class, 'toggleStatus'])->name('quizzes.toggle_status');
    Route::get('/quizzes/{quiz}/duplicate', [App\Http\Controllers\Admin\QuizManagementController::class, 'duplicate'])->name('quizzes.duplicate');
    Route::get('/questions-answers/{question}/analytics', [App\Http\Controllers\Admin\QuestionsAnswersManagementController::class, 'analytics'])->name('questions-answers.analytics')->middleware('admin.permission:view_analytics,view_own_analytics');


    // Admin action routes
    Route::post('/questions-answers/{questions_answer}/approve', [App\Http\Controllers\Admin\QuestionsAnswersManagementController::class, 'approve'])->name('questions-answers.approve');
    Route::post('/questions-answers/{questions_answer}/reject', [App\Http\Controllers\Admin\QuestionsAnswersManagementController::class, 'reject'])->name('questions-answers.reject');
    Route::post('/questions-answers/{questions_answer}/close', [App\Http\Controllers\Admin\QuestionsAnswersManagementController::class, 'close'])->name('questions-answers.close');
    Route::post('/questions-answers/{questions_answer}/reopen', [App\Http\Controllers\Admin\QuestionsAnswersManagementController::class, 'reopen'])->name('questions-answers.reopen');
    Route::put('/questions-answers/{questions_answer}/priority', [App\Http\Controllers\Admin\QuestionsAnswersManagementController::class, 'updatePriority'])->name('questions-answers.priority');
    Route::post('/questions-answers/bulk-update', [App\Http\Controllers\Admin\QuestionsAnswersManagementController::class, 'bulkUpdateStatus'])->name('questions-answers.bulk-update');
    Route::get('/questions-answers/{questions_answer}/reply', [App\Http\Controllers\Admin\QuestionsAnswersManagementController::class, 'reply'])->name('questions-answers.reply');

    // Additional admin routes
    Route::get('/categories/data', [App\Http\Controllers\Admin\CategoriesController::class, 'data'])->name('categories.data');
    Route::post('/users/{user}/toggle-active', [App\Http\Controllers\Admin\UsersController::class, 'toggleActive'])->name('users.toggle-active');
    Route::post('/users/{user}/active', [App\Http\Controllers\Admin\UsersController::class, 'active'])->name('users.active');
    Route::get('/blog-categories/analytics', [App\Http\Controllers\Admin\BlogCategoryController::class, 'analytics'])->name('blog-categories.analytics')->middleware('admin.permission:view_analytics');
    Route::post('/blog-categories/bulk-delete', [App\Http\Controllers\Admin\BlogCategoryController::class, 'bulkDelete'])->name('blog-categories.bulk_delete');
    Route::get('/blogs/analytics', [App\Http\Controllers\Admin\BlogsController::class, 'analytics'])->name('blogs.analytics')->middleware('admin.permission:view_analytics');
    Route::post('/blogs/{blog}/toggle-status', [App\Http\Controllers\Admin\BlogsController::class, 'toggleStatus'])->name('blogs.toggle_status');
    Route::post('/blogs/{blog}/toggle-featured', [App\Http\Controllers\Admin\BlogsController::class, 'toggleFeatured'])->name('blogs.toggle_featured');
    Route::post('/blogs/bulk-delete', [App\Http\Controllers\Admin\BlogsController::class, 'bulkDelete'])->name('blogs.bulk_delete');
    Route::post('/translations/clear-cache', [App\Http\Controllers\Admin\TranslationController::class, 'clearCache'])->name('translations.clear_cache');
    Route::post('/translations/bulk-import', [App\Http\Controllers\Admin\TranslationController::class, 'bulkImport'])->name('translations.bulk_import');
    Route::get('/translations/export', [App\Http\Controllers\Admin\TranslationController::class, 'export'])->name('translations.export');
    Route::post('/languages/{language}/default', [App\Http\Controllers\Admin\LanguageController::class, 'setDefault'])->name('languages.default');
    Route::post('/languages/{language}/toggle-status', [App\Http\Controllers\Admin\LanguageController::class, 'toggleActive'])->name('languages.toggle-status');

    // Features routes
    Route::get('/settings/features', [App\Http\Controllers\Admin\FeaturesController::class, 'index'])->name('settings.features.index');
    Route::post('/settings/features/bulk-action', [App\Http\Controllers\Admin\FeaturesController::class, 'bulkAction'])->name('settings.features.bulk-action');
    Route::post('/settings/feature', [App\Http\Controllers\Admin\FeaturesController::class, 'store'])->name('settings.feature.store');
    Route::post('/settings/feature/{feature}', [App\Http\Controllers\Admin\FeaturesController::class, 'update'])->name('settings.feature.update');
    Route::delete('/settings/feature/{feature}', [App\Http\Controllers\Admin\FeaturesController::class, 'destroy'])->name('settings.feature.destroy');
    Route::post('/settings/feature/{feature}/toggle-status', [App\Http\Controllers\Admin\FeaturesController::class, 'toggleStatus'])->name('settings.feature.toggle-status');
    Route::post('/settings/feature/order', [App\Http\Controllers\Admin\FeaturesController::class, 'updateOrder'])->name('settings.feature.order');

    // Hero Features Management
    Route::get('/settings/hero-features', [App\Http\Controllers\Admin\HeroFeaturesController::class, 'index'])->name('settings.hero-features.index');
    Route::post('/settings/hero-features/bulk-action', [App\Http\Controllers\Admin\HeroFeaturesController::class, 'bulkAction'])->name('settings.hero-features.bulk-action');
    Route::post('/settings/hero-feature', [App\Http\Controllers\Admin\HeroFeaturesController::class, 'store'])->name('settings.hero-feature.store');
    Route::post('/settings/hero-feature/{heroFeature}', [App\Http\Controllers\Admin\HeroFeaturesController::class, 'update'])->name('settings.hero-feature.update');
    Route::delete('/settings/hero-feature/{heroFeature}', [App\Http\Controllers\Admin\HeroFeaturesController::class, 'destroy'])->name('settings.hero-feature.destroy');
    Route::post('/settings/hero-feature/{heroFeature}/toggle-status', [App\Http\Controllers\Admin\HeroFeaturesController::class, 'toggleStatus'])->name('settings.hero-feature.toggle-status');
    Route::post('/settings/hero-feature/order', [App\Http\Controllers\Admin\HeroFeaturesController::class, 'updateOrder'])->name('settings.hero-feature.order');

    // About University Management
    Route::get('/settings/about-university', [App\Http\Controllers\Admin\AboutUniversityController::class, 'index'])->name('settings.about-university.index');
    Route::post('/settings/about-university', [App\Http\Controllers\Admin\AboutUniversityController::class, 'store'])->name('settings.about-university.store');
    Route::post('/settings/about-university/toggle-status', [App\Http\Controllers\Admin\AboutUniversityController::class, 'toggleStatus'])->name('settings.about-university.toggle-status');
    Route::post('/settings/about-university/feature', [App\Http\Controllers\Admin\AboutUniversityController::class, 'storeFeature'])->name('settings.about-university.feature.store');
    Route::post('/settings/about-university/feature/{feature}', [App\Http\Controllers\Admin\AboutUniversityController::class, 'updateFeature'])->name('settings.about-university.feature.update');
    Route::delete('/settings/about-university/feature/{feature}', [App\Http\Controllers\Admin\AboutUniversityController::class, 'destroyFeature'])->name('settings.about-university.feature.destroy');
    Route::post('/settings/about-university/feature/{feature}/toggle-status', [App\Http\Controllers\Admin\AboutUniversityController::class, 'toggleFeatureStatus'])->name('settings.about-university.feature.toggle-status');
    Route::post('/settings/about-university/features/order', [App\Http\Controllers\Admin\AboutUniversityController::class, 'updateFeaturesOrder'])->name('settings.about-university.features.order');

    // FAQ Management
    Route::get('/settings/faqs', [App\Http\Controllers\Admin\FAQController::class, 'index'])->name('settings.faqs.index');
    Route::post('/settings/faq', [App\Http\Controllers\Admin\FAQController::class, 'store'])->name('settings.faq.store');
    Route::post('/settings/faq/{faq}', [App\Http\Controllers\Admin\FAQController::class, 'update'])->name('settings.faq.update');
    Route::delete('/settings/faq/{faq}', [App\Http\Controllers\Admin\FAQController::class, 'destroy'])->name('settings.faq.destroy');
    Route::post('/settings/faq/{faq}/toggle-status', [App\Http\Controllers\Admin\FAQController::class, 'toggleStatus'])->name('settings.faq.toggle-status');
    Route::post('/settings/faq/{faq}/toggle-expanded', [App\Http\Controllers\Admin\FAQController::class, 'toggleExpanded'])->name('settings.faq.toggle-expanded');
    Route::post('/settings/faqs/order', [App\Http\Controllers\Admin\FAQController::class, 'updateOrder'])->name('settings.faqs.order');

    // Content Management (Scholarship Banner & CTA Video)
    Route::get('/settings/content-management', [App\Http\Controllers\Admin\ContentManagementController::class, 'index'])->name('settings.content-management.index');
    Route::post('/settings/scholarship-banner', [App\Http\Controllers\Admin\ContentManagementController::class, 'storeScholarshipBanner'])->name('settings.scholarship-banner.store');
    Route::post('/settings/cta-video', [App\Http\Controllers\Admin\ContentManagementController::class, 'storeCTAVideo'])->name('settings.cta-video.store');
    Route::post('/settings/scholarship-banner/toggle-status', [App\Http\Controllers\Admin\ContentManagementController::class, 'toggleScholarshipBannerStatus'])->name('settings.scholarship-banner.toggle-status');
    Route::post('/settings/cta-video/toggle-status', [App\Http\Controllers\Admin\ContentManagementController::class, 'toggleCTAVideoStatus'])->name('settings.cta-video.toggle-status');

    // Contact Forms Management
    Route::get('/settings/contact-forms', [App\Http\Controllers\Admin\ContentManagementController::class, 'contactForms'])->name('settings.contact-forms.index');
    Route::get('/settings/contact-forms/{contactForm}', [App\Http\Controllers\Admin\ContentManagementController::class, 'showContactForm'])->name('settings.contact-forms.show');
    Route::post('/settings/contact-forms/{contactForm}/status', [App\Http\Controllers\Admin\ContentManagementController::class, 'updateContactFormStatus'])->name('settings.contact-forms.update-status');
    Route::delete('/settings/contact-forms/{contactForm}', [App\Http\Controllers\Admin\ContentManagementController::class, 'deleteContactForm'])->name('settings.contact-forms.destroy');
    Route::get('/settings/contact-forms/export', [App\Http\Controllers\Admin\ContentManagementController::class, 'exportContactForms'])->name('settings.contact-forms.export');

    // Testimonials Management
    Route::get('/settings/testimonials', [App\Http\Controllers\Admin\TestimonialsController::class, 'index'])->name('settings.testimonials.index');
    Route::post('/settings/testimonials', [App\Http\Controllers\Admin\TestimonialsController::class, 'store'])->name('settings.testimonials.store');
    Route::post('/settings/testimonials/{testimonial}', [App\Http\Controllers\Admin\TestimonialsController::class, 'update'])->name('settings.testimonials.update');
    Route::delete('/settings/testimonials/{testimonial}', [App\Http\Controllers\Admin\TestimonialsController::class, 'destroy'])->name('settings.testimonials.destroy');
    Route::post('/settings/testimonials/{testimonial}/toggle-status', [App\Http\Controllers\Admin\TestimonialsController::class, 'toggleStatus'])->name('settings.testimonials.toggle-status');
    Route::post('/settings/testimonials/update-order', [App\Http\Controllers\Admin\TestimonialsController::class, 'updateOrder'])->name('settings.testimonials.update-order');
    Route::post('/settings/testimonials/bulk-action', [App\Http\Controllers\Admin\TestimonialsController::class, 'bulkAction'])->name('settings.testimonials.bulk-action');

    // Features Split Section Management
    Route::get('/settings/features-split', [App\Http\Controllers\Admin\FeaturesSplitController::class, 'index'])->name('settings.features-split.index');
    Route::post('/settings/features-split', [App\Http\Controllers\Admin\FeaturesSplitController::class, 'store'])->name('settings.features-split.store');
    Route::post('/settings/features-split/toggle-status', [App\Http\Controllers\Admin\FeaturesSplitController::class, 'toggleStatus'])->name('settings.features-split.toggle-status');
    Route::post('/settings/features-split/feature', [App\Http\Controllers\Admin\FeaturesSplitController::class, 'storeFeature'])->name('settings.features-split.feature.store');
    Route::post('/settings/features-split/feature/{feature}', [App\Http\Controllers\Admin\FeaturesSplitController::class, 'updateFeature'])->name('settings.features-split.feature.update');
    Route::delete('/settings/features-split/feature/{feature}', [App\Http\Controllers\Admin\FeaturesSplitController::class, 'destroyFeature'])->name('settings.features-split.feature.destroy');
    Route::post('/settings/features-split/feature/{feature}/toggle-status', [App\Http\Controllers\Admin\FeaturesSplitController::class, 'toggleFeatureStatus'])->name('settings.features-split.feature.toggle-status');
    Route::post('/settings/features-split/features/order', [App\Http\Controllers\Admin\FeaturesSplitController::class, 'updateFeaturesOrder'])->name('settings.features-split.features.order');
    Route::post('/settings/features-split/features/bulk-action', [App\Http\Controllers\Admin\FeaturesSplitController::class, 'bulkAction'])->name('settings.features-split.features.bulk-action');

    // Info Split Section Management
    Route::get('/settings/info-split', [App\Http\Controllers\Admin\InfoSplitController::class, 'index'])->name('settings.info-split.index');
    Route::post('/settings/info-split', [App\Http\Controllers\Admin\InfoSplitController::class, 'store'])->name('settings.info-split.store');
    Route::post('/settings/info-split/toggle-status', [App\Http\Controllers\Admin\InfoSplitController::class, 'toggleStatus'])->name('settings.info-split.toggle-status');

    // Newsletter Management
    Route::get('/settings/newsletters', [App\Http\Controllers\Admin\NewsletterController::class, 'index'])->name('settings.newsletters.index');
    Route::get('/settings/newsletters/{newsletter}', [App\Http\Controllers\Admin\NewsletterController::class, 'show'])->name('settings.newsletters.show');
    Route::post('/settings/newsletters/{newsletter}/status', [App\Http\Controllers\Admin\NewsletterController::class, 'updateStatus'])->name('settings.newsletters.update-status');
    Route::delete('/settings/newsletters/{newsletter}', [App\Http\Controllers\Admin\NewsletterController::class, 'destroy'])->name('settings.newsletters.destroy');
    Route::post('/settings/newsletters/bulk-action', [App\Http\Controllers\Admin\NewsletterController::class, 'bulkAction'])->name('settings.newsletters.bulk-action');
    Route::get('/settings/newsletters/export', [App\Http\Controllers\Admin\NewsletterController::class, 'export'])->name('settings.newsletters.export');

    // Contact Management
    Route::get('/settings/contact-management', [App\Http\Controllers\Admin\ContactManagementController::class, 'index'])->name('settings.contact-management.index');
    Route::post('/settings/contact-management/update-settings', [App\Http\Controllers\Admin\ContactManagementController::class, 'updateSettings'])->name('settings.contact-management.update-settings');
    Route::get('/settings/contact-management/contact-forms', [App\Http\Controllers\Admin\ContactManagementController::class, 'contactForms'])->name('settings.contact-management.contact-forms');
    Route::get('/settings/contact-management/contact-forms/{contactForm}', [App\Http\Controllers\Admin\ContactManagementController::class, 'showContactForm'])->name('settings.contact-management.show');
    Route::post('/settings/contact-management/contact-forms/{contactForm}/status', [App\Http\Controllers\Admin\ContactManagementController::class, 'updateContactFormStatus'])->name('settings.contact-management.update-status');
    Route::delete('/settings/contact-management/contact-forms/{contactForm}', [App\Http\Controllers\Admin\ContactManagementController::class, 'deleteContactForm'])->name('settings.contact-management.destroy');
    Route::post('/settings/contact-management/contact-forms/bulk-action', [App\Http\Controllers\Admin\ContactManagementController::class, 'bulkAction'])->name('settings.contact-management.bulk-action');
    Route::get('/settings/contact-management/contact-forms/export', [App\Http\Controllers\Admin\ContactManagementController::class, 'exportContactForms'])->name('settings.contact-management.export');

    // Main Content Settings Management
    Route::get('/settings/main-content', [App\Http\Controllers\Admin\MainContentSettingsController::class, 'index'])->name('settings.main-content.index');
    Route::put('/settings/main-content', [App\Http\Controllers\Admin\MainContentSettingsController::class, 'update'])->name('settings.main-content.update');
    Route::post('/settings/main-content/remove-logo', [App\Http\Controllers\Admin\MainContentSettingsController::class, 'removeLogo'])->name('settings.main-content.remove-logo');
    Route::post('/settings/main-content/remove-favicon', [App\Http\Controllers\Admin\MainContentSettingsController::class, 'removeFavicon'])->name('settings.main-content.remove-favicon');

    // Terms and Conditions Management
    Route::get('/settings/terms-conditions', [App\Http\Controllers\Admin\TermsConditionsController::class, 'index'])->name('settings.terms-conditions.index');
    Route::put('/settings/terms-conditions', [App\Http\Controllers\Admin\TermsConditionsController::class, 'update'])->name('settings.terms-conditions.update');
    Route::post('/settings/terms-conditions/generate-slug', [App\Http\Controllers\Admin\TermsConditionsController::class, 'generateSlug'])->name('settings.terms-conditions.generate-slug');

    Route::get('/enrollments/export', [App\Http\Controllers\Admin\EnrollmentsController::class, 'export'])->name('enrollments.export');
    Route::get('/homework/export/{format?}', [App\Http\Controllers\Admin\HomeworkManagementController::class, 'export'])->name('homework.export');
    Route::post('/homework/import', [App\Http\Controllers\Admin\HomeworkManagementController::class, 'import'])->name('homework.import');
    Route::get('/questions-answers/analytics', [App\Http\Controllers\Admin\QuestionsAnswersManagementController::class, 'analytics'])->name('questions-answers.analytics')->middleware('admin.permission:view_analytics,view_own_analytics');
    Route::get('/questions-answers/export', [App\Http\Controllers\Admin\QuestionsAnswersManagementController::class, 'export'])->name('questions-answers.export');
    Route::post('/questions-answers/{questions_answer}/store-reply', [App\Http\Controllers\Admin\QuestionsAnswersManagementController::class, 'storeReply'])->name('questions-answers.store_reply');
    Route::post('/questions-answers/{questions_answer}/update-reply', [App\Http\Controllers\Admin\QuestionsAnswersManagementController::class, 'updateReply'])->name('questions-answers.update_reply');
    Route::delete('/questions-answers/{questions_answer}/delete-reply', [App\Http\Controllers\Admin\QuestionsAnswersManagementController::class, 'deleteReply'])->name('questions-answers.delete_reply');
    Route::put('/questions-answers/{questions_answer}/update-priority', [App\Http\Controllers\Admin\QuestionsAnswersManagementController::class, 'updatePriority'])->name('questions-answers.update_priority');

    // Settings routes
    Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::get('/settings/sliders', [App\Http\Controllers\Admin\SettingsController::class, 'slidersIndex'])->name('settings.sliders.index');
    Route::post('/settings/sliders/bulk-action', [App\Http\Controllers\Admin\SettingsController::class, 'slidersBulkAction'])->name('settings.sliders.bulk-action');
    Route::post('/settings/slider', [App\Http\Controllers\Admin\SettingsController::class, 'storeSlider'])->name('settings.slider.store');
    Route::post('/settings/slider/{slider}', [App\Http\Controllers\Admin\SettingsController::class, 'updateSlider'])->name('settings.slider.update');
    Route::delete('/settings/slider/{slider}', [App\Http\Controllers\Admin\SettingsController::class, 'destroySlider'])->name('settings.slider.destroy');
    Route::post('/settings/slider/{slider}/toggle-status', [App\Http\Controllers\Admin\SettingsController::class, 'toggleSliderStatus'])->name('settings.slider.toggle-status');
    Route::post('/settings/slider/order', [App\Http\Controllers\Admin\SettingsController::class, 'updateSliderOrder'])->name('settings.slider.order');

    // Debug route for translations (remove in production)
    Route::get('/debug-translations', function () {
        return view('admin.debug-translations');
    })->name('debug-translations');
});
