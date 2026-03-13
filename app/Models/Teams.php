<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teams extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'age_category',
        'season',
        'description',
    ];
}
