<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Accounts;
use App\Transactions;
use App\Validator;

class AccountsController extends Controller
{
    /*
    | Create account
    |--------------------------------------------------------------
    | Cria uma conta para o usuário
    |--------------------------------------------------------------
    | ['users_id' => integer ], ['name' => string], ['balance' => float]
    |
     */
    public function create_account(Request $request, $users_id)
    {
        // Nome da conta
        $name_account   =   $request['name'];
        // Balanço da conta
        $balance        =   $request['balance'];

        // Validando os campos
        if( !$name_account || is_null($balance) || !$users_id)
        {
            return response()->json(['message' => 'Fill in all the fields'],406);
        }

        // Criando nova conta
        $account        =   DB::table('accounts')->insert([
            'name'              =>          $name_account,
            'users_id'          =>          $users_id,
            'balance'           =>          $balance,
            'opening_balance'   =>          $balance,
            'created_at'        =>          date("Y-m-d"),
            'updated_at'        =>          date("Y-m-d")
        ]);

        // Verificando o status da query e informando ao usuário
        if($account)
        {
            return response()->json(['message' => 'succeeds'],200);
        }else
        {
            return response()->json(['message' => 'error'],400);
        }
    }

    /*
    | Chage account name
    |-----------------------------------------------------------------------
    | Muda o nome da conta do usuário
    |-----------------------------------------------------------------------
    | ['users_id' => integer ], ['name' => string], ['account_id' => integer]
    |
     */
    public function change_account_name(Request $request, $users_id, $account_id)
    {

        // Validando o campo
        if(!$request['name'] || !$account_id || !$users_id)
        {
            return response()->json(['message' => 'Fill in all the fields'],406);
        }

        // Nome da conta
        $account_name = $request['name'];

        // Verificando se a conta é do usuário
        $account = Accounts::find($account_id);

        if($account->users_id != $users_id)
        {
            return response()->json(['message' => 'You can only change the name of your accounts.'],401);
        }else
        {
            $new_account_name = DB::table('accounts')->where('id',$account_id)->update(['name' => $account_name]);
        }

        // Verificando o status da query e informando ao usuário
        if($new_account_name)
        {
            return response()->json(['message' => 'Account name changed'],200);
        }else
        {
            return response()->json(['message' => 'error'],400);
        }
    }


    /*
    | Change the amount of the balance
    |-----------------------------------------------------------------------
    | Muda o valor do saldo da conta
    |-----------------------------------------------------------------------
    | ['users_id' => integer ], ['balance' => float], ['account_id' => integer]
    |
     */

    public function change_the_amount_of_the_account_balance(Request $request, $user_id, $account_id)
    {
        // Validando os campos
        if(!$request['balance'] || !$user_id || !$account_id)
        {
            return response()->json(['message' => 'Fill in all the fields'],406);
        }
        // Verificando se a conta é do usuário
        $account = Accounts::find($account_id);

        if($account->users_id != $user_id)
        {
            return response()->json(['message' => 'You can only change your account balance.'],401);
        }

        // Saldo antes de ser atualizado
        $old_balance_account = $account->balance;

        // Saldo posterior a mudança
        $post_change_balance = $request['balance'];

        // Diferença entre os saldos
        $difference = $post_change_balance - $old_balance_account;

        // Classificando a transanção entre 'recipe' ou 'expense'
        if($difference > 0)
        {
            $type = 'recipe';
        }else
        {
            $type = 'expense';
        }

        // Criando transação para correção do saldo
        $transaction = DB::table('transactions')->insert([
            'day'               =>          date('d'),
            'month'             =>          date('m'),
            'year'              =>          date('Y'),
            'value'             =>          $difference,
            'description'       =>          'CORREÇÃO DE SALDO REALIZADA NA DATA: '. date('d'). '/'. date('m').'/'.date('Y').' NO VALOR DE R$'.$difference.'.',
            'users_id'          =>          $user_id,
            'account_id'        =>          $account_id,
            'categories_id'     =>          1,
            'is_paid'           =>          1,
            'type'              =>          $type,
            'is_recurrent'      =>          0,
            'is_visible'        =>          0,
            'created_at'        =>          date("Y-m-d"),
            'updated_at'        =>          date("Y-m-d")
        ]);

        $balance    =   $this->update_account_balance($account_id, $user_id);

        // Verificando o status da query e informando ao usuário
        if($transaction && $balance)
        {
            return response()->json(['message' => 'Successfully adjusted balance'],200);
        }else
        {
            return response()->json(['message' => 'error'],400);
        }

    }

    /*
    | Update balance
    |-----------------------------------------------------------------------
    | Atualiza o saldo da conta especificada do usuário
    |-----------------------------------------------------------------------
    | ['account_id' => integer ], ['user_id' => integer]
    |
     */
    public static function update_account_balance($account_id, $user_id)
    {
        // Validando os campos
        if(!$account_id || !$user_id)
        {
            return response()->json(['message' => 'Fill in all the fields'],400);
        }

        // Selecionando todas as receitas na conta especificada pelo usuário
        $recipes = Transactions::where('users_id',$user_id)->where('account_id', $account_id)->where('type','recipe')->where('is_paid', 1)->get();
        //dd($recipes);

        // Contabilizando as receitas da conta
        $total_revenue = 0;
        foreach ($recipes as $r)
        {
            $total_revenue  += $r->value;
        }
        //dd($total_revenue);

        // Selecionando todas as despesas na conta especificada pelo usuário
        $expenses = Transactions::where('users_id',$user_id)->where('account_id', $account_id)->where('type','expense')->where('is_paid', 1)->get();

        //dd($expenses);
        // Contabilizando as despesas da conta
        $total_expenses = 0;
        foreach ($expenses as $e)
        {
            $total_expenses  +=$e->value;
        }

        //dd($total_expenses);
        // Capturando saldo anterior a atualização
        $account = Accounts::find($account_id);
        //dd($account);
        $current_balance = $account->opening_balance;
        //dd($current_balance);
        // Calculando o saldo final
        $balance = ($total_revenue - abs($total_expenses)) + $current_balance;

        //dd($balance);

        // Atualizando o saldo
        $balance = Accounts::where('users_id', $user_id)->where('id',$account_id)->update(['balance' => $balance]);

        // Verificando o status da query e informando ao usuário
        if($balance)
        {
            return true;
        }else
        {
            return false;
        }


    }


    /*
    | Delete account
    |-----------------------------------------------------------------------
    | Deleta uma conta especificada do usuário
    |-----------------------------------------------------------------------
    | ['account_id' => integer ], ['user_id' => integer]
    |
     */

    public function delete_account($user_id, $account_id)
    {
        // Validando os campos
        if(!$account_id || !$user_id)
        {
            return response()->json(['message' => 'Fill in all the fields'],400);
        }
        // Verificando a existêcnia dos id's
        $account = Accounts::find($account_id);
        //dd($account_id);
        $user    = User::find($user_id);
        if(!$account || !$user)
        {
                return response()->json(['message' => 'User or account does not exist']);
        }
        // Verificando se a conta é do usuário
        if($account->users_id != $user_id)
        {
            return response()->json(['message' => 'You can only delete your account.'],401);
        }
        // Deletando a conta
        $delete = Accounts::where('id', $account_id)->where('users_id', $user_id)->delete();
        // Verificando o status da query e informando ao usuário
        if($delete)
        {
            return response()->json(['message' => 'Successfully deleted.'],200);
        }else
        {
            return response()->json(['message' => 'error'],400);
        }
    }
    /*
   | Check balance
   |-----------------------------------------------------------------------
   | Consulta o saldo da conta
   |-----------------------------------------------------------------------
   | ['account_id' => integer ], ['user_id' => integer]
   |
    */
    public function check_balance(Request $request, $users_id)
    {
        // Validando os campos
        $data = array($request['account_id'], $users_id);
        $validator = Validator::check_empty_fields($data);
        if($validator['status'])
        {
            return $validator['response'];
        }
        // Verificando a existência dos dados informados
        $account        =       Accounts::find($request['account_id']);
        $user           =       User::find($users_id);
        if(!$user || !$account)
        {
            return response()->json(['message' => 'User or account does not exist']);
        }
        // Verificando se a conta é do usuário
        $validator = Validator::checks_whether_access_is_allowed($account,$users_id);
        if($validator['status'])
        {
           return $validator['response'];
        }
        // Consultado o saldo
        $balance = $account->balance;
        // Verificando o status da query e informando ao usuário
        if($balance)
        {
            return response()->json(['name' => $account->name,'saldo' => $balance]);
        }else
        {
            return response()->json(['message' => 'error']);
        }

    }
    /*
    | Accounts list
    |-----------------------------------------------------------------------
    | Lista todas as contas do usuario
    |-----------------------------------------------------------------------
    |  ['users_id' => integer]
    |
   */
    public function accounts_list($users_id)
    {
        // Validando os campos
        $data       =   array($users_id);
        $validator  =   Validator::check_empty_fields($data);
        if($validator['status'])
        {
            return $validator['response'];
        }
        // Verificando a existência dos dados informados
        $user = User::find($users_id);
        if(!$user)
        {
            return response()->json(['message' => 'User not found']);
        }
        // Selecionando todas as contas do usuário informado
        $accounts = Accounts::where('users_id', $users_id)->get();
        // Verificando o status da query e informando ao usuário
        if($accounts)
        {
            return response()->json(['accounts' => $accounts]);
        }else
        {
            return response()->json(['message' => 'error']);
        }
    }





}
