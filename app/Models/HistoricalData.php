<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricalData extends Model
{
    use HasFactory;

    protected $fillable = [
        'cryptocurrency_id',
        'price',
        'market_cap',
        'volume',
        'percent_change_24h',
        'timestamp'
    ];

    public function cryptocurrency()
    {
        return $this->belongsTo(Cryptocurrency::class);
    }

}
