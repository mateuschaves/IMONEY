<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {
    $api->group(['prefix' => 'auth'], function(Router $api) {
        $api->post('signup', 'App\\Api\\V1\\Controllers\\SignUpController@signUp');
        $api->post('login', 'App\\Api\\V1\\Controllers\\LoginController@login');

        $api->post('recovery', 'App\\Api\\V1\\Controllers\\ForgotPasswordController@sendResetEmail');
        $api->post('reset', 'App\\Api\\V1\\Controllers\\ResetPasswordController@resetPassword');

        $api->post('logout', 'App\\Api\\V1\\Controllers\\LogoutController@logout');
        $api->post('refresh', 'App\\Api\\V1\\Controllers\\RefreshController@refresh');
        $api->get('me', 'App\\Api\\V1\\Controllers\\UserController@me');
    });

    $api->group(['middleware' => 'jwt.auth'], function(Router $api) {
        $api->get('protected', function() {
            return response()->json([
                'message' => 'Access to protected resources granted! You are seeing this text as you provided the token correctly.'
            ]);
        });

        $api->get('refresh', [
            'middleware' => 'jwt.refresh',
            function() {
                return response()->json([
                    'message' => 'By accessing this endpoint, you can refresh your access token at each request. Check out this response headers!'
                ]);
            }
        ]);


    });

    $api->group(['middleware' => 'jwt.auth', 'prefix' => 'user'], function(Router $api){
        $api->post('create/account/{users_id}','App\\Http\\Controllers\\AccountsController@create_account')->name('create.account');
        $api->post('change-name/account/{users_id}/{account_id}', 'App\\Http\\Controllers\\AccountsController@change_account_name')->name('change.account.name');
        $api->post('change-balance/account/{users_id}/{account_id}', 'App\\Http\\Controllers\\AccountsController@change_the_amount_of_the_account_balance')->name('change.account.balance');
        $api->post('delete/account/{users_id}/{account_id}', 'App\\Http\\Controllers\\AccountsController@delete_account')->name('delete.account');
        $api->post('create/categorie/{users_id}/', 'App\\Http\\Controllers\\CategoriesController@create_categories')->name('create.categories');
        $api->post('update/categorie/{users_id}/{categorie_id}', 'App\\Http\\Controllers\\CategoriesController@update_categories')->name('update.categories');
        $api->post('delete/categorie/{users_id}/{categorie_id}', 'App\\Http\\Controllers\\CategoriesController@delete_categories')->name('delete.categories');
        $api->post('create/transactions/{users_id}/', 'App\\Http\\Controllers\\TransactionsController@create_transaction')->name('create.transaction');
        $api->post('consult/balance/{users_id}/', 'App\\Http\\Controllers\\AccountsController@check_balance')->name('consult.balance');
        $api->get('accounts/{users_id}/', 'App\\Http\\Controllers\\AccountsController@accounts_list')->name('accounts.list');
    });

    $api->get('hello', function() {
        return response()->json([
            'message' => 'This is a simple example of item returned by your APIs. Everyone can see it.'
        ]);
    });
});
