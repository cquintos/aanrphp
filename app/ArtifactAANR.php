<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class ArtifactAANR extends Model
{
    use SearchableTrait;
    use HasFactory;
    protected $table = 'artifactaanr';
    protected $fillable = ['title'];

    protected $searchable = [
        'columns' => [
            'artifactaanr.title' => 10,
            'artifactaanr.keywords' => 5,
            'artifactaanr.description' => 3,
            'artifactaanr.author_institution' => 2,
            'artifactaanr.author' => 2,
            'artifactaanr.date_published' => 2,
        ]
    ];

    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }

    public function consortia()
    {
        return $this->belongsTo(Consortia::class);
    }

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function content_subtype()
    {
        return $this->belongsTo(ContentSubtype::class, 'contentsubtype_id');
    }

    public function isp()
    {
        return $this->belongsToMany(ISP::class, 'artifactaanr_isp', 'artifactaanr_id', 'isp_id')->withPivot('industry_id');
    }

    public function commodities()
    {
        return $this->belongsToMany(Commodity::class, 'artifactaanr_commodity', 'artifactaanr_id', 'commodity_id')->withPivot('industry_id');
    }

    public function commodity_subtypes()
    {
        return $this->belongsToMany(CommoditySubtype::class, 'artifactaanr_commodity_subtype', 'artifactaanr_id', 'commodity_subtype_id');
    }
}
