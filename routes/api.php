<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PesananController;
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

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    // user
    Route::get('/logout', [UserController::class, 'logout']);
    // menu
    Route::get('/category', [MenuController::class, 'category']);
    Route::get('/menu', [MenuController::class, 'menu']);
    Route::get('/menu/{id_category}', [MenuController::class, 'menuByCategory']);
    // pesanan
    Route::get('/pesanan/detail', [PesananController::class, 'index']);
    Route::post('/pesanan/{id}', [PesananController::class, 'pesanan']);
    Route::get('/hapus/pesanan/{id}', [PesananController::class, 'hapusPesanan']);
    Route::get('/buat/pesanan', [PesananController::class, 'buatPesanan']);
});
