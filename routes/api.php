<?php

use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::prefix('/user')->controller(UserController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
})->name('user');

Route::middleware('auth:api')->group(function () {
    Route::get('/user/logout', [UserController::class, 'logout']);
    Route::get('/user/reservations/{userId}', [ReservationController::class, 'getUserReservations']);

    Route::prefix('/room')->group(function () {
        Route::get('/all', [RoomController::class, 'index'])->name('room.all');

        Route::prefix('/reservation')->controller(ReservationController::class)->group(function () {
            Route::get('/{roomId}', 'getRoomReservations')->name('room.reservations');
            Route::post('/create', 'createReservation')->name('room.create');
            Route::post('/edit', 'updateReservation')->name('room.update');
            Route::post('/delete', 'deleteReservation')->name('room.delete');
        });
    });
});