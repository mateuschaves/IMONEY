<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Accounts extends Model
{
    protected $fillable = [
        'name', 'balance', 'users_id',
    ];
}
