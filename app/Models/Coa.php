<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coa extends Model
{
    protected $fillable = [
        'name',
        'code',
        'coa_category_id',
    ];
}
