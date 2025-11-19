<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SimitRegistroController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [SimitRegistroController::class, 'index'])->name('dashboard');
    
    // Rutas para clientes
    Route::get('/clientes/create', [ClienteController::class, 'create'])->name('clientes.create');
    Route::get('/clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
    Route::post('/clientes', [SimitRegistroController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');
    
    // Rutas para multas
    Route::put('/simit-registros/{id}', [SimitRegistroController::class, 'update'])->name('simit-registros.update');
    Route::get('/simit-registros/{id}/edit-data', [SimitRegistroController::class, 'getEditData'])->name('simit-registros.edit-data');
    Route::delete('/simit-registros/{id}', [SimitRegistroController::class, 'destroy'])->name('simit-registros.destroy');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
