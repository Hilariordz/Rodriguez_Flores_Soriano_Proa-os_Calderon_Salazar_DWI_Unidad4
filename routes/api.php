<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/favoritas', function () {
    return response()->json([
        ['id' => 1, 'nombre' => 'Pasta Alfredo', 'tiempo' => '25 min'],
        ['id' => 2, 'nombre' => 'Tacos de Pollo', 'tiempo' => '20 min'],
        ['id' => 3, 'nombre' => 'Ensalada César', 'tiempo' => '15 min'],
    ]);
});
