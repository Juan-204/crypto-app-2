<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
