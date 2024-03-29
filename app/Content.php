<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;
    protected $table = 'content';

    public function content_subtypes()
    {
        return $this->hasMany(ContentSubtype::class);
    }
    
    public function artifacts()
    {
        return $this->hasMany(ArtifactAANR::class);
    }
}
