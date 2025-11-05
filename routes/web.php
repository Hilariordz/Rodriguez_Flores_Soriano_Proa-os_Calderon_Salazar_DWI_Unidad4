<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// API de Spoonacular
Route::get('/api/recetas/{busqueda}', function ($busqueda) {
    $apiKey = env('SPOONACULAR_KEY');
    $response = Http::get("https://api.spoonacular.com/recipes/complexSearch", [
        'query' => $busqueda,
        'number' => 8,
        'language' => 'es', // Español
        'cuisine' => 'mexican,spanish,latin american', // Cocinas que usan español
        'apiKey' => $apiKey,
    ]);
    return $response->json();
});

// API de favoritas
Route::get('/api/favoritas', function () {
    return response()->json([
        [
            'id' => 1, 
            'nombre' => 'Risotto de Trufa Negra', 
            'tiempo' => '35 min',
            'descripcion' => 'Cremoso risotto con trufa negra y parmesano',
            'precio' => '$450'
        ],
        [
            'id' => 2, 
            'nombre' => 'Salmón Glaseado con Miso', 
            'tiempo' => '25 min',
            'descripcion' => 'Salmón fresco con glaseado de miso y vegetales asiáticos',
            'precio' => '$380'
        ],
        [
            'id' => 3, 
            'nombre' => 'Cordero Confitado', 
            'tiempo' => '45 min',
            'descripcion' => 'Pierna de cordero confitada con hierbas provenzales',
            'precio' => '$520'
        ],
        [
            'id' => 4, 
            'nombre' => 'Tarta de Chocolate Valrhona', 
            'tiempo' => '20 min',
            'descripcion' => 'Exquisita tarta con chocolate belga y frutos rojos',
            'precio' => '$180'
        ]
    ]);
});

// API para obtener detalles de una receta específica
Route::get('/api/recetas/detalle/{id}', function ($id) {
    $apiKey = env('SPOONACULAR_KEY');
    $response = Http::get("https://api.spoonacular.com/recipes/{$id}/information", [
        'apiKey' => $apiKey,
    ]);
    return $response->json();
});

// API para obtener recetas aleatorias
Route::get('/api/recetas/aleatorias', function () {
    $apiKey = env('SPOONACULAR_KEY');
    $response = Http::get("https://api.spoonacular.com/recipes/random", [
        'number' => 6,
        'tags' => 'mexican,spanish,latin american',
        'apiKey' => $apiKey,
    ]);
    return $response->json();
});

// API para obtener recetas por categoría/tipo de comida
Route::get('/api/recetas/categoria/{tipo}', function ($tipo) {
    $apiKey = env('SPOONACULAR_KEY');
    $response = Http::get("https://api.spoonacular.com/recipes/complexSearch", [
        'type' => $tipo, // breakfast, lunch, dinner, snack, dessert
        'number' => 8,
        'apiKey' => $apiKey,
    ]);
    return $response->json();
});

// Vista que busca y muestra recetas
Route::get('/buscar', function (Illuminate\Http\Request $request) {
    if (!$request->has('query')) {
        return Inertia::render('Dashboard');
    }

    $apiKey = env('SPOONACULAR_KEY');
    $response = Http::get("https://api.spoonacular.com/recipes/complexSearch", [
        'query' => $request->query('query'),
        'number' => 8,
        'language' => 'es',
        'cuisine' => 'mexican,spanish,latin american',
        'apiKey' => $apiKey,
    ]);
    $recetas = $response->json();

    return Inertia::render('Dashboard', ['recetas' => $recetas]);
});

require __DIR__.'/auth.php';
