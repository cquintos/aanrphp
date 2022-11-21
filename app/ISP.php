<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ISP extends Model
{
    use HasFactory;
    protected $table = 'isp';
    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }
    
    public function artifacts()
    {
        return $this->belongsToMany(ArtifactAANR::class, 'artifactaanr_isp', 'artifactaanr_id', 'isp_id')->withPivot('industry_id');
    }
}
