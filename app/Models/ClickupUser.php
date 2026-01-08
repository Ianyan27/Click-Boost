<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClickupUser extends Model
{
    protected $table = 'clickup_user';

    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];
}
