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
    protected $signature = 'crypto:fetch {--crypto_id=}'; // Parámetro opcional

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
        $apiKey = env('API_KEY_COINMAKERCAP');
        $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest';

        // **Verificamos si se proporcionó una crypto específica**
        $cryptoId = $this->option('crypto_id');

        $parameters = [
            'start' => '1',
            'limit' => '100',
            'convert' => 'USD',
        ];

        $response = Http::withHeaders([
            'X-CMC_PRO_API_KEY' => $apiKey,
        ])->get($url, $parameters);

        if ($response->successful()) {
            $data = $response->json();

            if ($cryptoId) {
                // **Solo actualizar la criptomoneda específica**
                $crypto = Cryptocurrency::find($cryptoId);
                if ($crypto) {
                    $apiCrypto = collect($data['data'])->firstWhere('symbol', $crypto->symbol);
                    if ($apiCrypto) {
                        HistoricalData::create([
                            'cryptocurrency_id' => $crypto->id,
                            'price' => $apiCrypto['quote']['USD']['price'],
                            'market_cap' => $apiCrypto['quote']['USD']['market_cap'],
                            'volume' => $apiCrypto['quote']['USD']['volume_24h'],
                            'percent_change_24h' => $apiCrypto['quote']['USD']['percent_change_24h'],
                            'timestamp' => Carbon::now(),
                        ]);
                        $this->info("Datos históricos de {$crypto->name} actualizados.");
                    }
                }
            } else {
                // **Actualizar todas las criptomonedas existentes**
                $existingCrypto = Cryptocurrency::pluck('symbol')->toArray();

                foreach ($data['data'] as $crypto) {
                    if (in_array($crypto['symbol'], $existingCrypto)) {
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
                $this->info('Datos históricos de criptomonedas actualizados correctamente.');
            }
        } else {
            $this->error('Error al obtener datos de la API de CoinMarketCap');
        }
    }
}
