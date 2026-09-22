<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CmsContentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ModifierController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleManagementController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SettingController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', AdminDashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
Route::get('/ventas', [SalesController::class, 'index'])->middleware(['auth', 'verified'])->name('sales.index');
Route::get('/pedidos', [OrderController::class, 'index'])->middleware(['auth', 'verified'])->name('orders.index');
Route::put('/pedidos/{order}/estado', [OrderController::class, 'updateStatus'])->middleware(['auth', 'verified'])->name('orders.status.update');
Route::get('/cms', [CmsContentController::class, 'index'])->middleware(['auth', 'verified'])->name('cms.index');
Route::post('/cms', [CmsContentController::class, 'store'])->middleware(['auth', 'verified'])->name('cms.store');
Route::put('/cms/{cmsContent}', [CmsContentController::class, 'update'])->middleware(['auth', 'verified'])->name('cms.update');
Route::get('/clientes', [CustomerController::class, 'index'])->middleware(['auth', 'verified'])->name('customers.index');
Route::get('/almacen', [InventoryController::class, 'index'])->middleware(['auth', 'verified'])->name('inventory.index');
Route::post('/almacen/insumos', [InventoryController::class, 'storeItem'])->middleware(['auth', 'verified'])->name('inventory.items.store');
Route::post('/almacen/ajustes', [InventoryController::class, 'adjust'])->middleware(['auth', 'verified'])->name('inventory.adjust');
Route::post('/almacen/proveedores', [InventoryController::class, 'storeSupplier'])->middleware(['auth', 'verified'])->name('inventory.suppliers.store');
Route::post('/almacen/ordenes-compra', [InventoryController::class, 'storePurchaseOrder'])->middleware(['auth', 'verified'])->name('inventory.purchase-orders.store');
Route::put('/almacen/ordenes-compra/{purchaseOrder}/estado', [InventoryController::class, 'updatePurchaseOrderStatus'])->middleware(['auth', 'verified'])->name('inventory.purchase-orders.status');
Route::get('/recetas', [RecipeController::class, 'index'])->middleware(['auth', 'verified'])->name('recipes.index');
Route::post('/recetas', [RecipeController::class, 'store'])->middleware(['auth', 'verified'])->name('recipes.store');
Route::post('/recetas/{recipe}/ingredientes', [RecipeController::class, 'storeIngredient'])->middleware(['auth', 'verified'])->name('recipes.ingredients.store');
Route::get('/notificaciones', [NotificationController::class, 'index'])->middleware(['auth', 'verified'])->name('notifications.index');
Route::post('/notificaciones/{notification}/leida', [NotificationController::class, 'markRead'])->middleware(['auth', 'verified'])->name('notifications.read');
Route::put('/notificaciones/plantillas/{template}', [NotificationController::class, 'updateTemplate'])->middleware(['auth', 'verified'])->name('notifications.templates.update');
Route::get('/auditoria', [AuditLogController::class, 'index'])->middleware(['auth', 'verified'])->name('audit.index');
Route::get('/reportes', [ReportController::class, 'index'])->middleware(['auth', 'verified'])->name('reports.index');
Route::get('/configuracion', [SettingController::class, 'index'])->middleware(['auth', 'verified'])->name('settings.index');
Route::put('/configuracion', [SettingController::class, 'update'])->middleware(['auth', 'verified'])->name('settings.update');
Route::get('/roles', [RoleManagementController::class, 'index'])->middleware(['auth', 'verified'])->name('roles.index');
Route::put('/roles/usuarios/{user}', [RoleManagementController::class, 'updateUserRole'])->middleware(['auth', 'verified'])->name('roles.users.update');
Route::post('/roles/usuarios', [RoleManagementController::class, 'store'])->middleware(['auth', 'verified'])->name('roles.users.store');
Route::get('/promociones', [PromotionController::class, 'index'])->middleware(['auth', 'verified'])->name('promotions.index');
Route::post('/promociones', [PromotionController::class, 'store'])->middleware(['auth', 'verified'])->name('promotions.store');

Route::get('/catalogo', [CatalogController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('catalog.index');
Route::get('/sucursales', [BranchController::class, 'index'])->middleware(['auth', 'verified'])->name('branches.index');
Route::post('/sucursales', [BranchController::class, 'store'])->middleware(['auth', 'verified'])->name('branches.store');
Route::put('/sucursales/{branch}', [BranchController::class, 'update'])->middleware(['auth', 'verified'])->name('branches.update');
Route::delete('/sucursales/{branch}', [BranchController::class, 'deactivate'])->middleware(['auth', 'verified'])->name('branches.deactivate');
Route::post('/sucursales/{branch}/servicios', [BranchController::class, 'storeService'])->middleware(['auth', 'verified'])->name('branches.services.store');
Route::post('/catalogo/categorias', [CatalogController::class, 'storeCategory'])->middleware(['auth', 'verified'])->name('catalog.categories.store');
Route::post('/catalogo/productos', [CatalogController::class, 'storeProduct'])->middleware(['auth', 'verified'])->name('catalog.products.store');
Route::put('/catalogo/categorias/orden', [CatalogController::class, 'updateCategoryOrder'])->middleware(['auth', 'verified'])->name('catalog.categories.order');
Route::put('/catalogo/categorias/productos/orden', [CatalogController::class, 'updateProductOrder'])->middleware(['auth', 'verified'])->name('catalog.products.order');
Route::put('/catalogo/productos/{product}/categorias', [CatalogController::class, 'updateProductCategories'])->middleware(['auth', 'verified'])->name('catalog.products.categories.update');
Route::put('/catalogo/productos/{product}', [CatalogController::class, 'update'])->middleware(['auth', 'verified'])->name('catalog.products.update');
Route::post('/catalogo/productos/{product}/duplicar', [CatalogController::class, 'duplicate'])->middleware(['auth', 'verified'])->name('catalog.products.duplicate');
Route::delete('/catalogo/productos/{product}', [CatalogController::class, 'destroy'])->middleware(['auth', 'verified'])->name('catalog.products.destroy');
Route::get('/modificadores', [ModifierController::class, 'index'])->middleware(['auth', 'verified'])->name('modifiers.index');
Route::post('/modificadores', [ModifierController::class, 'storeGroup'])->middleware(['auth', 'verified'])->name('modifiers.store');
Route::post('/modificadores/{modifierGroup}/opciones', [ModifierController::class, 'storeOption'])->middleware(['auth', 'verified'])->name('modifiers.options.store');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
