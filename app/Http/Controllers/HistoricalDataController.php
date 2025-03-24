<?php

namespace App\Http\Controllers;

use App\Models\Cryptocurrency;
use Illuminate\Http\Request;

class HistoricalDataController extends Controller
{
    public function show(Cryptocurrency $cryptocurrency)
    {
        $historicalData = $cryptocurrency->historicalData()->orderBy('timestamp', 'asc')->get();

        return response()->json($historicalData);
    }
}
