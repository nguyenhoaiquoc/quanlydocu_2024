<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'gender',
        'image',
        'bio',
        'is_honored',
        'is_trusted'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Super-Admin')) {
            return true;
        }

        return null; // see the note above in Gate::before about why null must be returned here.
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')
            ->withPivot('created_at', 'is_read');
    }

    public function followings()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')
            ->withPivot('created_at', 'is_read');
    }



    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function commentsReceived()
    {
        return $this->hasManyThrough(
            Comment::class,
            Product::class,
            'user_id',
            'product_id',
            'id',
            'id'
        );
    }

    public function receivedComments()
    {
        return $this->hasMany(Comment::class, 'target_user_id');
    }

    public function allReceivedComments()
    {
        return $this->hasMany(Comment::class, 'target_user_id') // trực tiếp cho user
            ->orWhereHas('product', function ($q) {
                $q->where('user_id', $this->id);
            });
    }

    public function productNotifications()
    {
        return $this->hasMany(ProductNotification::class, 'user_id');
    }
}
