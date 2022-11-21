<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    use HasFactory;
    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }

    public function isps()
    {
        return $this->hasMany(ISP::class);
    }

    public function commodities()
    {
        return $this->hasMany(Commodity::class);
    }
}
