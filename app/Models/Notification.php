<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id','actor_id','category','type','related_id','related_type','data','is_read'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];

    public function actor()   { return $this->belongsTo(User::class, 'actor_id'); }
    public function user()    { return $this->belongsTo(User::class, 'user_id'); }
}
