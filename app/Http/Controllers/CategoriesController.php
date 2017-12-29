<?php

namespace App\Http\Controllers;

use App\Categories;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriesController extends Controller
{
    /*
    | Create categories
    |--------------------------------------------------------------
    | Cria categorias para o usuário classificar as transações
    |--------------------------------------------------------------
    | ['flag' => text ], ['user_id' => integer]
    |
     */
    public function create_categories(Request $request, $users_id)
    {
        // Validando os campos
        if(!$request['flag'] || is_null($users_id))
        {
            return response()->json(['message' => 'Fill in all the fields'],406);
        }
        // Verificando se o usuário existe
        $user = User::find($users_id);
        if(!$user)
        {
            return response()->json(['message' => 'User not found'],404);
        }
        // Criando a categoria
        $categorie = DB::table('categories')->insert([
            'flag'          =>      $request['flag'],
            'users_id'      =>      $users_id,
            'created_at'    =>      date("Y-m-d"),
            'updated_at'    =>      date("Y-m-d")
        ]);
        // Verificando o status da query e informando ao usuário
        if($categorie)
        {
            return response()->json(['message' => 'Category created successfully'],200);
        }else
        {
            return response()->json(['message' => 'error'],400);
        }
    }

    /*
    | Update categories
    |--------------------------------------------------------------
    | Edita categorias criada pelo usuário
    |--------------------------------------------------------------
    | ['flag' => text ], ['user_id' => integer], ['categorie_id' => integer]
    |
     */
    public function update_categories(Request $request, $users_id, $categorie_id)
    {
        // Validando os campos
        if(!$request['flag'] || is_null($users_id) || is_null($categorie_id))
        {
            return response()->json(['message' => 'Fill in all the fields'],406);
        }
        // Verificando se a categoria e o usuário informado existem
        $categorie  =   Categories::find($categorie_id);
        $user       =   User::find($users_id);
        if(!$user || !$categorie)
        {
            return response()->json(['message' => 'User or category not found'],404);
        }
        // Verificando se a categoria é do usuário
        if($categorie->users_id != $users_id)
        {
            return response()->json(['message' => 'You can only edit your categories.']);
        }
        // Editando categoria
        $categorie =  DB::table('categories')->where('id',$categorie_id)->where('users_id', $users_id)->update(['flag' => $request['flag']]);
        // Verificando o status da query e informando ao usuário
        if($categorie)
        {
            return response()->json(['message' => 'Category edited successfully'],200);
        }else
        {
            return response()->json(['message' => 'error'],400);
        }
    }

    /*
    | Delete categories
    |--------------------------------------------------------------
    | Deleta categorias criada pelo usuário
    |--------------------------------------------------------------
    |  ['user_id' => integer], ['categorie_id' => integer]
    |
     */
    public function delete_categories($users_id, $categorie_id)
    {
        // Validando as variáveis
        if(is_null($users_id) || is_null($categorie_id))
        {
            return response()->json(['message' => 'Fill in all the fields'],406);
        }
        // Verificando se a categoria informada e o usuário existem
        $user           =       User::find($users_id);
        $categorie      =       Categories::find($categorie_id);
        if(!$user || !$categorie)
        {
            return response()->json(['message' => 'User or category not found'],404);
        }
        // Verificando se a categoria é do usuário
        if($categorie->users_id != $users_id)
        {
            return response()->json(['message' => 'You can only edit your categories.']);
        }
        // Deletando a categoria
        $categorie = DB::table('categories')->where('id', $categorie_id)->where('users_id', $users_id)->delete();
        // Verificando o status da query e informando ao usuário
        if($categorie)
        {
            return response()->json(['message' => 'Category deleted successfully'], 200);
        }else
        {
            return response()->json(['message' => 'error'],400);
        }

    }
}
