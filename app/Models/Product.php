<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'deal_type',
        'description',
        'image',
        'user_id',
        'payment_method',
        'location_primary',
        'location_secondary',
        'condition',
        'material',
        'size',
        'brand',
        'used_duration',
        'reason',
        'new_category',
        'is_approved',
        'views',
        'deleted_by',
        'expires_at',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getImageArrayAttribute()
    {
        return json_decode($this->image, true) ?: [];
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function favoritedBy()
    {
        return $this->hasMany(Favorite::class);
    }
    public function favorites()
    {
        return $this->hasMany(\App\Models\Favorite::class);
    }
}
