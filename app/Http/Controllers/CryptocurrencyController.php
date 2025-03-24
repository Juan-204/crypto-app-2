<?php

namespace App\Http\Controllers;

use App\Models\Cryptocurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CryptocurrencyController extends Controller
{
    public function index()
    {
        $cryptocurrencies = Cryptocurrency::all();
        return response()->json($cryptocurrencies);
    }


    public function getCryptos()
    {
        // Revisar si la lista ya está en caché (durante 10 minutos)
        $cryptos = Cache::remember('cryptocurrencies', 600, function () {
            $response = Http::withHeaders([
                'X-CMC_PRO_API_KEY' => env('API_KEY_COINMAKERCAP'),
            ])->get('https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest');

            return $response->json();
        });

        return response()->json($cryptos['data'] ?? []);
    }

}
