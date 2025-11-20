<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/recetas', function () {
    return Inertia::render('Recetas');
})->middleware(['auth', 'verified'])->name('recetas');

Route::get('/receta/{id}', function ($id) {
    return Inertia::render('RecetaDetalle', ['recetaId' => $id]);
})->middleware(['auth', 'verified'])->name('receta.detalle');

Route::get('/mis-favoritas', function () {
    return Inertia::render('MisFavoritas');
})->middleware(['auth', 'verified'])->name('favoritas');

Route::get('/mis-reservaciones', function () {
    return Inertia::render('MisReservaciones');
})->middleware(['auth', 'verified'])->name('reservaciones');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Ruta de búsqueda para el Dashboard (usa la misma lógica de cache que la API)
Route::get('/buscar', function (Illuminate\Http\Request $request) {
    if (!$request->has('query')) {
        return Inertia::render('Dashboard');
    }

    $query = $request->query('query');
    $apiKey = config('services.spoonacular.key');
    
    // Usar la misma clave de cache que el endpoint de API
    $cacheKey = 'recetas_buscar_' . md5($query . '_8');
    $cacheTTL = config('services.spoonacular.cache.search');
    
    $recetas = Cache::remember($cacheKey, $cacheTTL, function () use ($query, $apiKey) {
        $response = Http::timeout(config('services.spoonacular.timeout'))
            ->get(config('services.spoonacular.base_url') . '/recipes/complexSearch', [
                'query' => $query,
                'number' => 8,
                'language' => 'es',
                'cuisine' => 'mexican,spanish,latin american',
                'apiKey' => $apiKey,
            ]);
        
        return $response->json();
    });

    return Inertia::render('Dashboard', ['recetas' => $recetas]);
})->middleware(['auth', 'verified']);

// Rutas de Administrador
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('admin.dashboard');
    
    Route::get('/reservaciones', function () {
        return Inertia::render('Admin/Reservaciones');
    })->name('admin.reservaciones');
    
    Route::get('/menu', function () {
        return Inertia::render('Admin/Menu');
    })->name('admin.menu');
});

require __DIR__.'/auth.php';
