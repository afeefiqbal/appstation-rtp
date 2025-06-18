<?php
use App\Http\Controllers\AdSlotController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BidController;
use App\Http\Middleware\AdminOnly;
use Illuminate\Support\Facades\Route;





Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    // Public to authenticated users (regular + admin)
    Route::get('/ad-slots/{id}/bids', [AdSlotController::class, 'bids']);
    Route::get('/ad-slots/{id}/winner', [AdSlotController::class, 'winner']);
    Route::post('/ad-slots/{id}/bids', [BidController::class, 'store']);
    Route::get('/user/bids', [BidController::class, 'history']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin-only section
    Route::middleware([AdminOnly::class])->group(function () {
        Route::get('/ad-slots', [AdSlotController::class, 'index']);
        Route::post('/ad-slots', [AdSlotController::class, 'store']);
        Route::put('/ad-slots/{id}', [AdSlotController::class, 'update']);
        Route::patch('/ad-slots/{id}/status', [AdSlotController::class, 'updateStatus']);
    });
});
