<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : redirect('/login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Master Data - Users
    Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
    Route::resource('users', UserController::class);

    // Master Data - Customers
    \App\Http\Controllers\CustomerController::class;
    Route::get('/customers/export', [\App\Http\Controllers\CustomerController::class, 'export'])->name('customers.export');
    Route::resource('customers', \App\Http\Controllers\CustomerController::class);

    // Master Data - Materials
    Route::get('/materials/export', [\App\Http\Controllers\MaterialController::class, 'export'])->name('materials.export');
    Route::resource('materials', \App\Http\Controllers\MaterialController::class);

    // Master Data - Suppliers
    Route::get('/suppliers/export', [\App\Http\Controllers\SupplierController::class, 'export'])->name('suppliers.export');
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);
});

require __DIR__.'/auth.php';
