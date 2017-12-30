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
                    'status'    => true,
                ];
                return $data;
            }
        }
    }

    /*
       |  Checks whether access is allowed
       |--------------------------------------------------------------
       | Verifica se é permitido o acesso a determinado dado
       |--------------------------------------------------------------
       | ['object' => object], ['users_id' => integer']
    */
    public static function checks_whether_access_is_allowed($object, $users_id)
    {
        if($object->users_id != $users_id)
        {
            $data = [
                'response'      =>      response()->json(['message' => 'You are not allowed to do this.'],400),
                'status'        =>      true,
            ];

            return $data;
        }
    }





}
