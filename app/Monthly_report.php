<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Monthly_report extends Model
{
    protected $fillable = [
        'users_id','account_id','balance','higer_expenditure','higher_revenue','month','year','average_expenditure','created_at','updated_at',
    ];
}
