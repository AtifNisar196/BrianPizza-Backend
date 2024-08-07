<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\ImageUploadController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\AddonTypeController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\VariationTypeController;
use App\Http\Controllers\Dashboard\AuthController;
use App\Http\Controllers\Dashboard\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::controller(ApiController::class)->group(function () {
    Route::get('/addons', 'getAddons');
    Route::get('/categories', 'getCategories');
    Route::get('/variations', 'getVariations');
    Route::get('/category-products', 'getCategoryProducts');
    Route::get('/products', 'getProducts');
    Route::get('/product-detail/{productId}', 'getProductDetail');
    Route::get('/featured-products', 'getFeaturedProducts');
});

Route::group(['prefix' => 'cart', 'controller' => CartController::class], function () {
    Route::get('/get', 'getCart');
    Route::post('/add', 'addToCart');
    Route::post('/remove', 'removeCart');
    Route::post('/clear', 'clearCart');
    Route::post('/quantity-update', 'quantityUpdateCart');
});

Route::group(['prefix' => 'order', 'controller' => OrderController::class], function () {
    Route::post('/save', 'save');
    Route::post('/check-availability', 'checkAvailability');
});

// Admin Dashboard APIs
Route::group(['prefix' => 'auth', 'controller' => AuthController::class], function () {
    Route::post('/login', 'login')->name('login');
    Route::post('/logout', 'logout')->name('logout')->middleware('auth:api');
});

Route::post('/upload-image', [ImageUploadController::class, 'save']);

Route::group(['prefix' => 'admin', 'middleware' => 'auth:api'], function () {

    Route::group(['prefix' => 'users', 'controller' => UserController::class], function () {
        Route::get('/get', 'getUsers');
        Route::post('/add', 'addUser');
        Route::post('/update/{userId}', 'updateUser');
        Route::post('/status-update', 'statusUpdateUser');
    });

    Route::group(['prefix' => 'categories', 'controller' => CategoryController::class], function () {
        Route::get('/get', 'getCategories');
        Route::post('/add', 'addCategory');
        Route::post('/update/{categoryId}', 'updateCategory');
        Route::post('/status-update', 'statusUpdateCategory');
    });

    Route::group(['prefix' => 'addon-types', 'controller' => AddonTypeController::class], function () {
        Route::get('/get', 'getAddonTypes');
        Route::post('/add', 'addAddonType');
        Route::post('/update/{addonTypeId}', 'updateAddonType');
        Route::post('/status-update', 'statusUpdateAddonType');
    });

    Route::group(['prefix' => 'variation-types', 'controller' => VariationTypeController::class], function () {
        Route::get('/get', 'getVariationTypes');
        Route::post('/add', 'addVariationType');
        Route::post('/update/{variationTypeId}', 'updateVariationType');
        Route::post('/status-update', 'statusUpdateVariationType');
    });

    Route::group(['prefix' => 'profile', 'controller' => ProfileController::class], function () {
        Route::post('/update', 'updateProfile');
        Route::post('/update-password', 'updatePassword');
    });
});
