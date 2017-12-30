<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Validator extends Model
{
    /*
    |  Check empty fields
    |--------------------------------------------------------------
    | Verifica se todos os campos foram preenchidos
    |--------------------------------------------------------------
    | ['data' => array ]
    |
     */
    public static function check_empty_fields($data)
    {
        foreach ($data as $d)
        {
            if(is_null($d))
            {
                $data  = [
                    'response'  => response()->json(['message' => 'Fill in all the fields'],400),
                    'status'    => false,
                ];
                return $data;
            }
        }
    }



}
