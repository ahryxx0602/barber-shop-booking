<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'image',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // branches ─────────── barbers (1-n) thợ cắt thuộc chi nhánh
    public function barbers()
    {
        return $this->hasMany(Barber::class);
    }
}
