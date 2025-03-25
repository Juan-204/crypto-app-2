<?php

namespace App\Http\Controllers;

use App\Models\Cryptocurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CryptocurrencyController extends Controller
{


    public function getCryptos()
    {
        // Revisar si la lista ya está en caché (durante 10 minutos)
        $cryptos = Cache::remember('cryptocurrencies', 600, function () {
            $response = Http::withHeaders([
                'X-CMC_PRO_API_KEY' => env('API_KEY_COINMAKERCAP'),
            ])->get('https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest');

            // Devolver solo los campos necesarios
            return collect($response->json()['data'] ?? [])->map(function ($crypto) {
                return [
                    'id'     => $crypto['id'],
                    'name'   => $crypto['name'],
                    'symbol' => $crypto['symbol'],
                ];
            })->toArray();
        });

        return response()->json($cryptos);
    }

}
