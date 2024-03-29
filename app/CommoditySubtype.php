<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommoditySubtype extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'commodity_id'];

    public function commodity()
    {
        return $this->belongsTo(Commodity::class);
    }

    public function artifacts()
    {
        return $this->belongsToMany(ArtifactAANR::class, 'artifactaanr_commodity_subtype', 'commodity_subtype_id', 'artifactaanr_id');
    }
}
