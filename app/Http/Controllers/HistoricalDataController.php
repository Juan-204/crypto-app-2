<?php

namespace App\Http\Controllers;

use App\Models\HistoricalData;


class HistoricalDataController extends Controller
{
    public function show($cryptocurrencyId)
    {
        $historicalData = HistoricalData::where('cryptocurrency_id', $cryptocurrencyId)
            ->orderBy('timestamp', 'DESC')
            ->get();

        return response()->json($historicalData);
    }


}
