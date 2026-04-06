<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentSettingsController;
use App\Http\Controllers\ProfileController;
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

// Subscription webhook (no auth, no CSRF)
Route::post('/webhook/stripe', [SubscriptionController::class, 'webhook'])->name('subscription.webhook');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('appointments', AppointmentController::class)->except(['show']);
    Route::resource('staff', StaffController::class)->except(['show']);
    Route::resource('services', ServiceController::class)->except(['show']);
    Route::resource('customers', CustomerController::class)->except(['show']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Payment Settings
    Route::get('/settings/payments', [PaymentSettingsController::class, 'index'])->name('settings.payments');
    Route::post('/settings/payments/stripe', [PaymentSettingsController::class, 'updateStripe'])->name('settings.payments.stripe');
    Route::post('/settings/payments/paypal', [PaymentSettingsController::class, 'updatePaypal'])->name('settings.payments.paypal');
    Route::post('/settings/payments/methods', [PaymentSettingsController::class, 'updatePaymentMethods'])->name('settings.payments.methods');

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
