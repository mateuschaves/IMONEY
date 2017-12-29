<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            $table->float('value');
            $table->text('description');

            $table->integer('users_id')->unsigned();
            $table->foreign('users_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->integer('categories_id')->unsigned();
            $table->foreign('categories_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade');

            $table->integer('destination_account_id')->unsigned();
            $table->foreign('destination_account_id')
                  ->references('id')
                  ->on('accounts')
                  ->onDelete('cascade');

            $table->integer('sender_account_id')->unsigned();
            $table->foreign('sender_account_id')
                ->references('id')
                ->on('accounts')
                ->onDelete('cascade');

            $table->integer('is_recurrent');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfers');
    }
}
