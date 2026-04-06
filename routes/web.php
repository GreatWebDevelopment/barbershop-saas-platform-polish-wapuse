<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentSettingsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('appointments', AppointmentController::class)->except(['show']);
    Route::resource('staff', StaffController::class)->except(['show']);
    Route::resource('services', ServiceController::class)->except(['show']);
    Route::resource('customers', CustomerController::class)->except(['show']);

    // Payment settings
    Route::get('/settings/payments', [PaymentSettingsController::class, 'index'])->name('settings.payments');
    Route::post('/settings/payments/stripe', [PaymentSettingsController::class, 'updateStripe'])->name('settings.payments.stripe');
    Route::post('/settings/payments/paypal', [PaymentSettingsController::class, 'updatePaypal'])->name('settings.payments.paypal');
    Route::post('/settings/payments/methods', [PaymentSettingsController::class, 'updatePaymentMethods'])->name('settings.payments.methods');

    // Payments
    Route::post('/payments/{appointment}/checkout', [PaymentController::class, 'checkout'])->name('payments.checkout');
    Route::post('/payments/{appointment}/paypal-capture', [PaymentController::class, 'paypalCapture'])->name('payments.paypal.capture');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/webhooks/stripe', [PaymentController::class, 'stripeWebhook'])->name('webhooks.stripe');

require __DIR__.'/auth.php';
