<?php

use App\Http\Controllers\SalesController;
use Illuminate\Support\Facades\Route;

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
    return redirect()->route('login');
});

Route::redirect('/dashboard', '/sales');

Route::middleware('auth')->group(function () {
    Route::get('/sales', [SalesController::class, 'index'])
    ->middleware(['auth'])->name('coffee.sales');

    Route::post('record-sale', [SalesController::class, 'recordSale'])
        ->name('record.sale');

    Route::post('get-sales', [SalesController::class, 'getSales'])
        ->name('get.sales');

    Route::post('selling-price', [SalesController::class, 'sellingPrice'])
        ->name('selling.price');

    Route::post('remove-record', [SalesController::class, 'removeRecord'])
        ->name('remove.record');
});

Route::get('/shipping-partners', function () {
    return view('shipping_partners');
})->middleware(['auth'])->name('shipping.partners');

require __DIR__.'/auth.php';
