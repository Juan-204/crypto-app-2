<?php

namespace App\Http\Controllers;

use App\Models\Cryptocurrency;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class FavoriteController extends Controller
{
    public function store(Request $request)
    {
        // Validar la solicitud
        $request->validate([
            'name' => 'required|string', // Nombre de la criptomoneda
            'symbol' => 'required|string|', // Símbolo de la criptomoneda
            'user_id' => 'required' //id del usuario autenticado
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

            //si ya esta devuelve un mesaje de error
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

        //llamamos a el comando creado para obtener datos de esta crypto de manera automatica
        Artisan::call('crypto:fetch', ['--crypto_id' => $cryptocurrency->id]);

        return response()->json([
            'message' => 'Criptomoneda agregada a favoritos correctamente.',
            'favorite' => $favorite,
        ], 201);
    }

    public function index(Request $request)
    {
        $userId = $request->query('user_id'); // Obtener de query param o de Auth::id()

        //verificamos si el usuario esta autenticado
        if (!$userId) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        //devolvemos toda la informacion de las cryptos en formato json
        $favorites = Favorite::where('user_id', $userId)
        ->with(['cryptocurrency' => function ($query) {
            $query->with('latestData');
        }])
        ->get();

        return response()->json($favorites);
    }



}
