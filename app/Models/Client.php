<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'client';
    protected $fillable = [
        'ip4',
        'key_private',
        'key_public',
        'is_granted',
        'handshake_at',
        'active_at',
    ];

    protected $hidden = [
        'password',
    ];
}
