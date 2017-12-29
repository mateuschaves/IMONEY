<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transfers extends Model
{
    protected $fillable =[
      'date','value','description','users_id','categories_id','destination_account_id','sender_account_id','is_recurrent','created_at','updated_at',

    ];
}
