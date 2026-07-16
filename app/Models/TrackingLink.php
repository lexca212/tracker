<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'name',
    ];

    public function updates()
    {
        return $this->hasMany(LocationUpdate::class);
    }

    public function latestUpdate()
    {
        return $this->hasOne(LocationUpdate::class)->latestOfMany();
    }
}
