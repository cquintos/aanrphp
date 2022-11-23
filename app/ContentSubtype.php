<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentSubtype extends Model
{
    use HasFactory;
    
    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function artifacts()
    {
        return $this->hasMany(ArtifactAANR::class);
    }
}
