<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductHistory extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'action',
        'old_data',
        'reason',
    ];

    protected $table = 'product_history';
}
