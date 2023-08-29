<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemGraphController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::name('item-graph.')
    ->prefix('item-graph')
    ->group(function () {
        Route::get('/items/search')
            ->uses([ItemGraphController::class, 'searchItems'])
            ->name('search-items');
        Route::get('/{item}/data')
            ->uses([ItemGraphController::class, 'getData'])
            ->name('data');
    });

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('items', \App\Http\Controllers\ItemsController::class)
        ->middleware('can:create-items')
        ->only(['index', 'create', 'store']);

    Route::resource('inventory', \App\Http\Controllers\InventoryController::class)
        ->middleware('can:search-inventory')
        ->only(['index', 'store']);
});
