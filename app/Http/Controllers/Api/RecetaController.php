<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;

class RecetaController extends Controller
{
    /**
     * Buscar recetas por término de búsqueda
     */
    public function buscar(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2|max:100',
            'number' => 'nullable|integer|min:1|max:20'
        ]);

        try {
            $apiKey = config('services.spoonacular.key');
            
            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'API key no configurada'
                ], 500);
            }

            // Generar clave de cache única basada en los parámetros
            $cacheKey = 'recetas_buscar_' . md5(
                $validated['query'] . '_' . ($validated['number'] ?? 8)
            );
            $cacheTTL = config('services.spoonacular.cache.search');

            // Usar cache para evitar llamadas repetidas a la API
            $data = Cache::remember($cacheKey, $cacheTTL, function () use ($validated, $apiKey) {
                $response = Http::timeout(config('services.spoonacular.timeout'))
                    ->get(config('services.spoonacular.base_url') . '/recipes/complexSearch', [
                        'query' => $validated['query'],
                        'number' => $validated['number'] ?? 8,
                        'language' => 'es',
                        'cuisine' => 'mexican,spanish,latin american',
                        'apiKey' => $apiKey,
                    ]);

                if ($response->failed()) {
                    throw new \Exception('API request failed');
                }

                return $response->json();
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'cached' => Cache::has($cacheKey)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud'
            ], 500);
        }
    }

    /**
     * Obtener detalle de una receta
     */
    public function detalle(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'id' => 'nullable|integer'
        ]);

        if (!is_numeric($id)) {
            return response()->json([
                'success' => false,
                'message' => 'ID de receta inválido'
            ], 400);
        }

        try {
            $apiKey = config('services.spoonacular.key');
            
            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'API key no configurada'
                ], 500);
            }

            // Cache de 24 horas para detalles (no cambian frecuentemente)
            $cacheKey = 'receta_detalle_' . $id;
            $cacheTTL = config('services.spoonacular.cache.detail');

            $data = Cache::remember($cacheKey, $cacheTTL, function () use ($id, $apiKey) {
                $response = Http::timeout(config('services.spoonacular.timeout'))
                    ->get(config('services.spoonacular.base_url') . "/recipes/{$id}/information", [
                        'apiKey' => $apiKey,
                    ]);

                if ($response->failed()) {
                    throw new \Exception('Recipe not found');
                }

                return $response->json();
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'cached' => Cache::has($cacheKey)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Receta no encontrada'
            ], 404);
        }
    }

    /**
     * Obtener recetas aleatorias
     */
    public function aleatorias(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'number' => 'nullable|integer|min:1|max:10'
        ]);

        try {
            $apiKey = config('services.spoonacular.key');
            
            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'API key no configurada'
                ], 500);
            }

            // Cache más corto (30 min) para mantener "frescura" de aleatorias
            $cacheKey = 'recetas_aleatorias_' . ($validated['number'] ?? 6);
            $cacheTTL = config('services.spoonacular.cache.random');

            $data = Cache::remember($cacheKey, $cacheTTL, function () use ($validated, $apiKey) {
                $response = Http::timeout(config('services.spoonacular.timeout'))
                    ->get(config('services.spoonacular.base_url') . '/recipes/random', [
                        'number' => $validated['number'] ?? 6,
                        'tags' => 'mexican,spanish,latin american',
                        'apiKey' => $apiKey,
                    ]);

                if ($response->failed()) {
                    throw new \Exception('API request failed');
                }

                return $response->json();
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'cached' => Cache::has($cacheKey)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud'
            ], 500);
        }
    }

    /**
     * Obtener recetas por categoría
     */
    public function categoria(Request $request, string $tipo): JsonResponse
    {
        $tiposPermitidos = ['breakfast', 'lunch', 'dinner', 'snack', 'dessert'];
        
        if (!in_array($tipo, $tiposPermitidos)) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de comida inválido',
                'tipos_permitidos' => $tiposPermitidos
            ], 400);
        }

        $validated = $request->validate([
            'number' => 'nullable|integer|min:1|max:20'
        ]);

        try {
            $apiKey = config('services.spoonacular.key');
            
            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'API key no configurada'
                ], 500);
            }

            // Cache de 1 hora para categorías
            $cacheKey = 'recetas_categoria_' . $tipo . '_' . ($validated['number'] ?? 8);
            $cacheTTL = config('services.spoonacular.cache.category');

            $data = Cache::remember($cacheKey, $cacheTTL, function () use ($tipo, $validated, $apiKey) {
                $response = Http::timeout(config('services.spoonacular.timeout'))
                    ->get(config('services.spoonacular.base_url') . '/recipes/complexSearch', [
                        'type' => $tipo,
                        'number' => $validated['number'] ?? 8,
                        'apiKey' => $apiKey,
                    ]);

                if ($response->failed()) {
                    throw new \Exception('API request failed');
                }

                return $response->json();
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'cached' => Cache::has($cacheKey)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud'
            ], 500);
        }
    }
}
