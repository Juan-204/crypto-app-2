<?php

namespace App\Http\Controllers;

use App\Models\Cryptocurrency;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function store(Request $request)
    {
        // Validar la solicitud
        $request->validate([
            'name' => 'required|string', // Nombre de la criptomoneda
            'symbol' => 'required|string|', // Símbolo de la criptomoneda
            'user_id' => 'required'
        ]);

        $userId = $request->user_id;

        // Buscar o crear la criptomoneda en la base de datos
        $cryptocurrency = Cryptocurrency::firstOrCreate(
            ['symbol' => $request->symbol], // Buscar por símbolo
            ['name' => $request->name] // Si no existe, crear con este nombre
        );

        // Verificar si la criptomoneda ya está en favoritos del usuario
        $existingFavorite = Favorite::where('user_id', $userId)
            ->where('cryptocurrency_id', $cryptocurrency->id)
            ->first();

        if ($existingFavorite) {
            return response()->json([
                'message' => 'La criptomoneda ya está en tus favoritos.',
            ], 409); // Código 409: Conflicto
        }

        // Crear el favorito
        $favorite = Favorite::create([
            'user_id' => $userId,
            'cryptocurrency_id' => $cryptocurrency->id,
        ]);

        return response()->json([
            'message' => 'Criptomoneda agregada a favoritos correctamente.',
            'favorite' => $favorite,
        ], 201);
    }

    public function index(Request $request)
    {
        $userId = $request->query('user_id'); // Obtener de query param o de Auth::id()

        if (!$userId) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $favorites = Favorite::where('user_id', $userId)
        ->with(['cryptocurrency' => function ($query) {
            $query->with('latestData'); // Importante incluir esto
        }])
        ->get();

        return response()->json($favorites);
    }

}
