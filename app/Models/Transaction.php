<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $table = 'transactions';
    protected $fillable = [
        'coa_id',
        'type',
        'amount',
        'created_by',
        'description'
    ];
}
