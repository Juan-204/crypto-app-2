<?php

namespace App\Console\Commands;

use App\Models\Cryptocurrency;
use App\Models\HistoricalData;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchCryptoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crypto:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //clave de la api
        $apiKey = env('API_KEY_COINMAKERCAP');

        //url de el enpoint que vamos a usar
        $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest';
        //parametros de la solicitud
        $parameters = [
            'start' => '1',
            'limit' => '10',
            'convert' => 'USD',
        ];

        //realizamos la solicitud a la API
        $response = Http::withHeaders([
            'X-CMC_PRO_API_KEY' => $apiKey,
        ])->get($url, $parameters);

        //verificar si la solicitud es exitosa
        if($response->successful()){
            $data = $response->json();

            $existingCrypto = Cryptocurrency::pluck('symbol')->toArray();

            foreach ($data['data'] as $crypto){
                if(in_array($crypto['symbol'], $existingCrypto)) {
                    $cryptoCurrency = Cryptocurrency::where('symbol', $crypto['symbol'])->first();

                    HistoricalData::create([
                        'cryptocurrency_id' => $cryptoCurrency->id,
                        'price' => $crypto['quote']['USD']['price'],
                        'market_cap' => $crypto['quote']['USD']['market_cap'],
                        'volume' => $crypto['quote']['USD']['volume_24h'],
                        'percent_change_24h' => $crypto['quote']['USD']['percent_change_24h'],
                        'timestamp' => Carbon::now(),
                    ]);
                }
            }

            $this->info('Datos historicos de criptomonedas actualizados correctamente');
        } else {
            $this->error('Error al obtener datos de la API de CoinMarketCap');
        }

        //obtenemos las cryptos ya registradas en la base de datos

    }
}
