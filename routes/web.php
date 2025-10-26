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

    // Transaction - Purchase Orders
    Route::get('/purchase-orders/export', [\App\Http\Controllers\PurchaseOrderController::class, 'export'])->name('purchase-orders.export');
    Route::get('/purchase-orders/materials-by-supplier', [\App\Http\Controllers\PurchaseOrderController::class, 'getMaterialsBySupplier'])->name('purchase-orders.materials-by-supplier');
    Route::resource('purchase-orders', \App\Http\Controllers\PurchaseOrderController::class);

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

    // Transaction - Projects
    Route::get('/projects/export', [\App\Http\Controllers\ProjectController::class, 'export'])->name('projects.export');
    Route::resource('projects', \App\Http\Controllers\ProjectController::class);

    // Transaction - Units (Read & Update only)
    Route::resource('units', \App\Http\Controllers\UnitController::class)->only(['index', 'show', 'edit', 'update']);

    // Transaction - Clusters (Read only)
    Route::resource('clusters', \App\Http\Controllers\ClusterController::class)->only(['index', 'show']);

    // Transaction - Orders (Full CRUD)
    Route::get('/orders/ajax/clusters', [\App\Http\Controllers\OrderController::class, 'getClusters'])->name('orders.clusters');
    Route::get('/orders/ajax/units', [\App\Http\Controllers\OrderController::class, 'getUnits'])->name('orders.units');
    Route::get('/orders/ajax/unit-details', [\App\Http\Controllers\OrderController::class, 'getUnitDetails'])->name('orders.unit-details');
    Route::resource('orders', \App\Http\Controllers\OrderController::class);

    // Transaction - Invoices (Full CRUD)
    Route::get('/invoices/ajax/order-details', [\App\Http\Controllers\InvoiceController::class, 'getOrderDetails'])->name('invoices.order-details');
    Route::get('/invoices/{invoice}/pdf', [\App\Http\Controllers\InvoiceController::class, 'exportPdf'])->name('invoices.pdf');
    Route::resource('invoices', \App\Http\Controllers\InvoiceController::class);

    // Transaction - Payments (Full CRUD)
    Route::get('/payments/ajax/invoice-details', [\App\Http\Controllers\PaymentController::class, 'getInvoiceDetails'])->name('payments.invoice-details');
    Route::get('/payments/{payment}/pdf', [\App\Http\Controllers\PaymentController::class, 'exportPdf'])->name('payments.pdf');
    Route::resource('payments', \App\Http\Controllers\PaymentController::class);

    // Transaction - Tickets (Full CRUD)
    Route::patch('/tickets/{ticket}/status', [\App\Http\Controllers\TicketController::class, 'updateStatus'])->name('tickets.update-status');
    Route::resource('tickets', \App\Http\Controllers\TicketController::class);

    // Transaction - Feedbacks (Full CRUD)
    Route::resource('feedbacks', \App\Http\Controllers\FeedbackController::class);

    // Accounting - Journal Entries
    Route::get('/journal-entries', [\App\Http\Controllers\JournalEntryController::class, 'index'])->name('journal-entries.index');

    // Accounting - Chart of Account
    Route::get('/chart-of-accounts', [\App\Http\Controllers\ChartOfAccountController::class, 'index'])->name('chart-of-accounts.index');

    // Accounting - Trial Balance (Neraca Saldo)
    Route::get('/trial-balance', [\App\Http\Controllers\TrialBalanceController::class, 'index'])->name('trial-balance.index');

    // Accounting - General Ledger (Buku Besar)
    Route::get('/general-ledger', [\App\Http\Controllers\GeneralLedgerController::class, 'index'])->name('general-ledger.index');
    Route::get('/general-ledger/{account}', [\App\Http\Controllers\GeneralLedgerController::class, 'show'])->name('general-ledger.show');

    // Reports - Profit & Loss
    Route::get('/reports/profit-loss', [\App\Http\Controllers\ReportController::class, 'profitLoss'])->name('reports.profit_loss');
    
    // Reports - Balance Sheet
    Route::get('/reports/balance-sheet', [\App\Http\Controllers\ReportController::class, 'balanceSheet'])->name('reports.balance_sheet');
    
    // Reports - Monthly Growth API
    Route::get('/reports/monthly-growth', [\App\Http\Controllers\ReportController::class, 'monthlyGrowth'])->name('reports.monthly_growth');
});

require __DIR__.'/auth.php';
