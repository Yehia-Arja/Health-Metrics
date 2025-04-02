<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dashboard extends Model{
    protected $fillable = [
        'user_id',
        'date',
        'steps',
        'active_minutes',
        'distance',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
