<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function sectors()
    {
        return $this->hasMany(Sector::class);
    }

    public function artifacts()
    {
        return $this->hasMany(ArtifactAANR::class);
    }

    public function commodities() {
        return $this->hasMany(Commodity::class);
    }
}
