<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'actor_id',
        'type',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user(): BelongsTo   // người được xem thông báo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function actor(): BelongsTo  // người hành động
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
