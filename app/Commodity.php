<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commodity extends Model
{
    use HasFactory;
    protected $fillable = ['industry_id'];

    public function artifacts()
    {
        return $this->belongsToMany(ArtifactAANR::class, 'artifactaanr_commodity', 'commodity_id', 'artifactaanr_id')->withPivot('industry_id');
    }

    public function subtypes()
    {
        return $this->hasMany(CommoditySubtype::class);
    }

    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }
}
