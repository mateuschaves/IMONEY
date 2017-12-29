<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    protected $fillable = [

        'date','value','description','users_id','account_id','categories_id','is_paid','type','is_recurrent','created_at','updated_at',

    ];
}
