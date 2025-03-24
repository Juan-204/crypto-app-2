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
            'symbol' => 'required|string|unique:cryptocurrencies,symbol', // Símbolo de la criptomoneda
        ]);

        // Buscar o crear la criptomoneda en la base de datos
        $cryptocurrency = Cryptocurrency::firstOrCreate(
            ['symbol' => $request->symbol], // Buscar por símbolo
            ['name' => $request->name] // Si no existe, crear con este nombre
        );

        // Verificar si la criptomoneda ya está en favoritos del usuario
        $existingFavorite = Favorite::where('user_id', Auth::id())
            ->where('cryptocurrency_id', $cryptocurrency->id)
            ->first();

        if ($existingFavorite) {
            return response()->json([
                'message' => 'La criptomoneda ya está en tus favoritos.',
            ], 409); // Código 409: Conflicto
        }

        // Crear el favorito
        $favorite = Favorite::create([
            'user_id' => Auth::id(),
            'cryptocurrency_id' => $cryptocurrency->id,
        ]);

        return response()->json([
            'message' => 'Criptomoneda agregada a favoritos correctamente.',
            'favorite' => $favorite,
        ], 201);
    }

    public function index()
    {
        // Obtener los favoritos del usuario con la información de la criptomoneda
        $favorites = Favorite::where('user_id', Auth::id())
            ->with('cryptocurrency')
            ->get();

        return response()->json($favorites);
    }
}
