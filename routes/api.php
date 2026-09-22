<?php

use App\Http\Controllers\Api\V1\CatalogController;
use App\Http\Controllers\Api\V1\BranchController;
use App\Http\Controllers\Api\V1\CmsContentController;
use App\Http\Controllers\Api\V1\MobileAuthController;
use App\Http\Controllers\Api\V1\MobileOrderController;
use App\Http\Controllers\Api\V1\MobileOrderHistoryController;
use App\Http\Controllers\Api\V1\MobileOnboardingController;
use App\Http\Controllers\Api\V1\MobileProfileController;
use App\Http\Controllers\Api\V1\PromotionController;
use App\Http\Controllers\Api\V1\PublicSettingsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/v1/catalog', [CatalogController::class, 'index'])->name('api.v1.catalog.index');
Route::get('/v1/branches', [BranchController::class, 'index'])->name('api.v1.branches.index');
Route::get('/v1/content', [CmsContentController::class, 'index'])->name('api.v1.content.index');
Route::get('/v1/settings', [PublicSettingsController::class, 'index'])->name('api.v1.settings.index');
Route::get('/v1/promotions', [PromotionController::class, 'index'])->name('api.v1.promotions.index');
Route::post('/v1/auth/register', [MobileAuthController::class, 'register'])->name('api.v1.auth.register');
Route::post('/v1/onboarding/check-email', [MobileOnboardingController::class, 'checkEmail'])->middleware('throttle:10,1');
Route::post('/v1/onboarding/send-otp', [MobileOnboardingController::class, 'sendOtp'])->middleware('throttle:3,10');
Route::post('/v1/onboarding/verify-otp', [MobileOnboardingController::class, 'verifyOtp'])->middleware('throttle:10,10');
Route::post('/v1/onboarding/complete', [MobileOnboardingController::class, 'complete'])->middleware('throttle:5,10');
Route::post('/v1/auth/login', [MobileAuthController::class, 'login'])->name('api.v1.auth.login');
Route::post('/v1/auth/logout', [MobileAuthController::class, 'logout'])->middleware('auth:sanctum')->name('api.v1.auth.logout');
Route::get('/v1/me', [MobileProfileController::class, 'show'])->middleware('auth:sanctum')->name('api.v1.profile.show');
Route::put('/v1/me', [MobileProfileController::class, 'update'])->middleware('auth:sanctum')->name('api.v1.profile.update');
Route::post('/v1/orders', [MobileOrderController::class, 'store'])->middleware('auth:sanctum')->name('api.v1.orders.store');
Route::get('/v1/orders', [MobileOrderHistoryController::class, 'index'])->middleware('auth:sanctum')->name('api.v1.orders.index');
Route::get('/v1/orders/{order}', [MobileOrderHistoryController::class, 'show'])->middleware('auth:sanctum')->name('api.v1.orders.show');
