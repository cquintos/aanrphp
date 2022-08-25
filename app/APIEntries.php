<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class APIEntries extends Model
{
    use HasFactory;
    protected $table = 'api_entries';
    protected $dates = ['time'];
}
