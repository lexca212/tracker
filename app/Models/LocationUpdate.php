<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_link_id',
        'latitude',
        'longitude',
        'device',
        'user_agent',
        'ip_address',
        'operator',
    ];

    public function trackingLink()
    {
        return $this->belongsTo(TrackingLink::class);
    }
}
