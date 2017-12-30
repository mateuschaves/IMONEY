<?php

namespace App\Http\Controllers;

use App\Accounts;
use App\Categories;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers;

class TransactionsController extends Controller
{
    /*
   | Create transaction
   |-----------------------------------------------------------------------
   | Cria uma transação para o usuário
   |-----------------------------------------------------------------------
   | ['account_id' => integer ], ['user_id' => integer], ['date' => date{dd/mmm/yyyy}],
   | ['description' => text], ['categories_id' => integer], ['is_paid' => integer]
   | ['type' => text], ['is_fixed' => integer],['is_parceled' => integer], ['is_visible' => integer]
    */
    public function create_transaction(Request $request, $users_id)
    {
        // Validando os campos
        if( !$users_id || !$request['date'] || !$request['value'] ||  !$request['account_id'] || !$request['categories_id'] || is_null($request['is_paid']) || is_null($request['type']) || is_null($request['is_fixed'])  || is_null($request['is_visible']))
        {
                return response()->json(['message' => 'Fill in all the fields'],200);
        }
        // Verificando se os id's existem
        $user           =       User::find($users_id);
        $categories     =       Categories::find($request['categories_id']);
        $account        =       Accounts::find($request['account_id']);
        if(!$user || !$categories || !$account)
        {
            return response()->json(['message' => 'User, account, or category not found.']);
        }
        // Quebrando a  data
        $date_array = explode("/", $request['date']);
        //dd($date_array);
        // Verificando se a transação é parcelada
        if($request['is_parceled'] > 1 )
        {
            $mes = $date_array[1];
            $ano = $date_array[2];

            for ($i = 1; $i <= $request['is_parceled']; $i++)
            {
                if($mes == 13)
                {
                    $mes = 1;
                    $ano++;
                }
                $transaction = DB::table('transactions')->insert([
                    'day'               =>      $date_array[0],
                    'month'             =>      $mes,
                    'year'              =>      $ano,
                    'value'             =>      $request['value'],
                    'description'       =>      $request['description'].'('.$i.'/'.$request['is_parceled'].')',
                    'users_id'          =>      $users_id,
                    'account_id'        =>      $request['account_id'],
                    'categories_id'     =>      $request['categories_id'],
                    'is_paid'           =>      $request['is_paid'],
                    'type'              =>      $request['type'],
                    'is_fixed'          =>      $request['is_fixed'],
                    'is_parceled'       =>      $request['is_parceled'],
                    'is_visible'        =>      $request['is_visible']
                ]);

                $mes = $mes + 1;
            }
            // Atualizando o saldo da conta
            $update_balance  =   AccountsController::update_account_balance($request['account_id'], $users_id);
            // Verificando o status da query e informando ao usuário
            if($transaction && $update_balance)
            {
                return response()->json(['message' => 'Successfully created transactions'], 200);
            }else
            {
                return response()->json(['message' => 'error'], 400);
            }
        }else
        {
            // Criando a transação única
            $transaction = DB::table('transactions')->insert([
                'day'               =>      $date_array[0],
                'month'             =>      $date_array[1],
                'year'              =>      $date_array[2],
                'value'             =>      $request['value'],
                'description'       =>      $request['description'],
                'users_id'          =>      $users_id,
                'account_id'        =>      $request['account_id'],
                'categories_id'     =>      $request['categories_id'],
                'is_paid'           =>      $request['is_paid'],
                'type'              =>      $request['type'],
                'is_fixed'          =>      $request['is_fixed'],
                'is_parceled'       =>      $request['is_parceled'],
                'is_visible'        =>      $request['is_visible']
            ]);
            // Atualizando o saldo da conta
            $update_balance  =   AccountsController::update_account_balance($request['account_id'], $users_id);
            // Verificando o status da query e informando ao usuário
            if($transaction && $update_balance)
            {
                return response()->json(['message' => 'Transaction created successfully'], 200);
            }else
            {
                return response()->json(['message' => 'error'], 400);
            }
        }
    }

    /*
   | Update value transaction
   |-----------------------------------------------------------------------
   | Altera o valor de uma transação
   |-----------------------------------------------------------------------
   |
   |
   |
    */





}
