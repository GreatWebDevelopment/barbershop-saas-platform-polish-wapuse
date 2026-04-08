<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\CompanyDashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentSettingsController;
use App\Http\Controllers\PayPalConnectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\ShopSettingsController;
use App\Http\Controllers\StripeConnectController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
});

Route::get('/features', function () {
    return Inertia::render('Features');
})->name('features');

Route::get('/pricing', function () {
    return Inertia::render('Pricing');
})->name('pricing');

Route::get('/about', function () {
    return Inertia::render('About');
})->name('about');

Route::get('/contact', function () {
    return Inertia::render('Contact');
})->name('contact');

Route::get('/privacy', function () {
    return Inertia::render('Privacy');
})->name('privacy');

Route::get('/terms', function () {
    return Inertia::render('Terms');
})->name('terms');

// Public: Locations & Queue
Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
Route::get('/locations/{shop}', [LocationController::class, 'show'])->name('locations.show');
Route::get('/queue/check-in/{shop}', [QueueController::class, 'checkIn'])->name('queue.checkin');
Route::get('/queue/status/{queueNumber}', [QueueController::class, 'status'])->name('queue.status');
Route::get('/display/{shop}', [QueueController::class, 'display'])->name('queue.display');

// Subscription webhook (no auth, no CSRF)
Route::post('/webhook/stripe', [SubscriptionController::class, 'webhook'])->name('subscription.webhook');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/company', [CompanyDashboardController::class, 'index'])->name('company.dashboard');

    Route::resource('appointments', AppointmentController::class)->except(['show']);
    Route::resource('customers', CustomerController::class)->except(['show']);

    Route::middleware('role:owner,manager')->group(function () {
        Route::resource('staff', StaffController::class);
        Route::resource('services', ServiceController::class)->except(['show']);
        Route::post('services/reorder', [ServiceController::class, 'reorder'])->name('services.reorder');
        Route::resource('service-categories', ServiceCategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    });

    // Staff schedule & commissions (owner/manager can view any; stylists can view their own)
    Route::middleware('role:owner,manager,stylist')->group(function () {
        Route::get('/staff/{staff}/schedule', [StaffController::class, 'schedule'])->name('staff.schedule');
        Route::patch('/staff/{staff}/schedule', [StaffController::class, 'updateSchedule'])->name('staff.schedule.update');
        Route::get('/staff/{staff}/commissions', [StaffController::class, 'commissions'])->name('staff.commissions');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Shop Settings (owner/manager only)
    Route::middleware('role:owner,manager')->group(function () {
        Route::get('/settings/shop', [ShopSettingsController::class, 'edit'])->name('settings.shop');
        Route::patch('/settings/shop', [ShopSettingsController::class, 'update'])->name('settings.shop.update');
    });

    // Payment Settings
    Route::get('/settings/payments', [PaymentSettingsController::class, 'index'])->name('settings.payments');
    Route::post('/settings/payments/methods', [PaymentSettingsController::class, 'updatePaymentMethods'])->name('settings.payments.methods');

    // Stripe Connect OAuth
    Route::get('/settings/payments/stripe/connect', [StripeConnectController::class, 'redirect'])->name('settings.payments.stripe.connect');
    Route::get('/settings/payments/stripe/callback', [StripeConnectController::class, 'callback'])->name('settings.payments.stripe.callback');
    Route::post('/settings/payments/stripe/disconnect', [StripeConnectController::class, 'disconnect'])->name('settings.payments.stripe.disconnect');

    // PayPal Connect OAuth
    Route::get('/settings/payments/paypal/connect', [PayPalConnectController::class, 'redirect'])->name('settings.payments.paypal.connect');
    Route::get('/settings/payments/paypal/callback', [PayPalConnectController::class, 'callback'])->name('settings.payments.paypal.callback');
    Route::post('/settings/payments/paypal/disconnect', [PayPalConnectController::class, 'disconnect'])->name('settings.payments.paypal.disconnect');

    // Subscription / Checkout
    Route::get('/checkout', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::post('/checkout/stripe', [SubscriptionController::class, 'createStripeSession'])->name('subscription.stripe');
    Route::post('/checkout/paypal/create', [SubscriptionController::class, 'createPaypalOrder'])->name('subscription.paypal.create');
    Route::post('/checkout/paypal/capture', [SubscriptionController::class, 'capturePaypalOrder'])->name('subscription.paypal.capture');
    Route::get('/subscription/success', [SubscriptionController::class, 'success'])->name('subscription.success');

    // Appointment payments
    Route::post('/appointments/{appointment}/pay', [PaymentController::class, 'checkout'])->name('appointments.pay');
    Route::post('/appointments/{appointment}/paypal-capture', [PaymentController::class, 'paypalCapture'])->name('appointments.paypal.capture');
});

// Appointment payment webhook (no auth, no CSRF)
Route::post('/webhook/stripe/appointments', [PaymentController::class, 'stripeWebhook'])->name('payments.stripe.webhook');

require __DIR__.'/auth.php';
