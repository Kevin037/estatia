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

    // Master Data - Lands
    Route::get('/lands/export', [\App\Http\Controllers\LandController::class, 'export'])->name('lands.export');
    Route::resource('lands', \App\Http\Controllers\LandController::class);

    // Master Data - Sales
    Route::get('/sales/export', [\App\Http\Controllers\SaleController::class, 'export'])->name('sales.export');
    Route::resource('sales', \App\Http\Controllers\SaleController::class);

    // Master Data - Milestones
    Route::get('/milestones/export', [\App\Http\Controllers\MilestoneController::class, 'export'])->name('milestones.export');
    Route::resource('milestones', \App\Http\Controllers\MilestoneController::class);

    // Transaction - Formulas
    Route::get('/formulas/export', [\App\Http\Controllers\FormulaController::class, 'export'])->name('formulas.export');
    Route::resource('formulas', \App\Http\Controllers\FormulaController::class);

    // Transaction - Products
    Route::get('/products/export', [\App\Http\Controllers\ProductController::class, 'export'])->name('products.export');
    Route::resource('products', \App\Http\Controllers\ProductController::class);

    // Transaction - Contractors
    Route::get('/contractors/export', [\App\Http\Controllers\ContractorController::class, 'export'])->name('contractors.export');
    Route::resource('contractors', \App\Http\Controllers\ContractorController::class);

    // Transaction - Types
    Route::get('/types/export', [\App\Http\Controllers\TypeController::class, 'export'])->name('types.export');
    Route::resource('types', \App\Http\Controllers\TypeController::class);
});

require __DIR__.'/auth.php';
