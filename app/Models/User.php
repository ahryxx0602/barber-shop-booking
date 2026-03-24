<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
        'is_active',
    ];

    // users ──────────── barbers (1-1)
    public function barber()
    {
        return $this->hasOne(Barber::class);
    }

    // users ──────────── bookings (1-n)
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }

    // users ──────────── notifications (1-n)
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // users ──────────── loyalty_points (1-n) lịch sử điểm thưởng
    public function loyaltyPoints()
    {
        return $this->hasMany(LoyaltyPoint::class);
    }

    // users ──────────── favorite_barbers (n-n) barber yêu thích
    public function favoriteBarbers()
    {
        return $this->belongsToMany(Barber::class, 'favorite_barbers')->withTimestamps();
    }

    // users ──────────── orders (1-n) đơn hàng sản phẩm
    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    // users ──────────── shipping_addresses (1-n) địa chỉ giao hàng
    public function shippingAddresses()
    {
        return $this->hasMany(ShippingAddress::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }
}
