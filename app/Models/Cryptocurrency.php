<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Cryptocurrency extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'symbol'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }
    public function historicalData()
    {
        return $this->hasMany(HistoricalData::class);
    }
    public function latestData()
    {
        return $this->hasOne(HistoricalData::class, 'cryptocurrency_id')->latestOfMany();
    }
}
