<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController; // Importe o controller

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('dashboard'); // Redireciona a raiz para o dashboard
});

// Rotas para o Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/parking-history-pdf', [DashboardController::class, 'generateParkingHistoryPdf'])->name('dashboard.pdf');