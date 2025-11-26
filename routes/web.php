<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SimitRegistroController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

/*
|--------------------------------------------------------------------------
| Fallback para archivos del storage
|--------------------------------------------------------------------------
*/
Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path("app/public/{$path}");

    if (file_exists($filePath) && is_file($filePath)) {
        return response()->file($filePath);
    }

    abort(404);
})
->where('path', '.*')
->name('storage.serve');

/*
|--------------------------------------------------------------------------
| Rutas públicas de consulta
|--------------------------------------------------------------------------
*/
Route::post('/consulta', [ClienteController::class, 'consulta'])->name('consulta.publica');
Route::get('/consulta/resultados', [ClienteController::class, 'resultados'])->name('consulta.resultados');
Route::match(['get', 'post'], '/consulta/confirmar-pago', [ClienteController::class, 'confirmarPago'])->name('pago.confirmar');
Route::post('/pago/procesar', [ClienteController::class, 'procesarPago'])->name('pago.procesar');

/*
|--------------------------------------------------------------------------
| Rutas protegidas (auth + verified)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [SimitRegistroController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Clientes
    |--------------------------------------------------------------------------
    */
    Route::prefix('clientes')->group(function () {
        Route::get('/create', [ClienteController::class, 'create'])->name('clientes.create');
        Route::post('/', [SimitRegistroController::class, 'store'])->name('clientes.store');
        Route::get('/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');
        Route::get('/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
    });

    /*
    |--------------------------------------------------------------------------
    | Simit Registros
    |--------------------------------------------------------------------------
    */
    Route::prefix('simit-registros')->group(function () {
        Route::put('/{id}', [SimitRegistroController::class, 'update'])->name('simit-registros.update');
        Route::get('/{id}/edit-data', [SimitRegistroController::class, 'getEditData'])->name('simit-registros.edit-data');
        Route::delete('/{id}', [SimitRegistroController::class, 'destroy'])->name('simit-registros.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Perfil
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Configuración
    |--------------------------------------------------------------------------
    */
    Route::prefix('configuracion')->group(function () {
        Route::get('/qr', [ConfiguracionController::class, 'qr'])->name('configuracion.qr');
        Route::post('/qr/upload', [ConfiguracionController::class, 'uploadQR'])->name('configuracion.qr.upload');
    });
});
