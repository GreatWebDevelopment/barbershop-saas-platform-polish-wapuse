<?php

use App\Http\Controllers\Api\V1\LocationController;
use App\Http\Controllers\Api\V1\QueueController;
use App\Http\Controllers\Api\V1\StaffQueueController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public endpoints
    Route::get('/locations/nearby', [LocationController::class, 'nearby']);
    Route::get('/locations/{shop}', [LocationController::class, 'show']);

    // Booking / Availability
    Route::get('/availability/{shop}', [\App\Http\Controllers\BookingController::class, 'availability']);
    Route::post('/book/{shop}', [\App\Http\Controllers\BookingController::class, 'store']);

    Route::post('/queue/check-in', [QueueController::class, 'checkIn']);
    Route::get('/queue/{queueNumber}', [QueueController::class, 'status']);
    Route::delete('/queue/{queueNumber}', [QueueController::class, 'cancel']);
    Route::get('/queue/shop/{shop}', [QueueController::class, 'shopStatus']);

    // Staff endpoints (auth required)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/staff/queue', [StaffQueueController::class, 'queue']);
        Route::post('/staff/queue/{entry}/call', [StaffQueueController::class, 'callNext']);
        Route::post('/staff/queue/{entry}/start', [StaffQueueController::class, 'startService']);
        Route::post('/staff/queue/{entry}/complete', [StaffQueueController::class, 'completeService']);
        Route::post('/staff/status', [StaffQueueController::class, 'updateStatus']);
    });
});
