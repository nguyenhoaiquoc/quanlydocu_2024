<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

   protected $fillable = [
    'chat_id', 'sender_id', 'message', 'file_path', 'file_type', 'is_revoked', 'revoked_at'
];


    protected $casts = [
        'is_revoked' => 'boolean',
        'revoked_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class);
    }
    
}
