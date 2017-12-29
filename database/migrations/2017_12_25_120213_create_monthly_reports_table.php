<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonthlyReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monthly_reports', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('users_id')->unsigned();
            $table->foreign('users_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->integer('account_id')->unsigned();
            $table->foreign('account_id')
                  ->references('id')
                  ->on('accounts')
                  ->onDelete('cascade');

            $table->float('balance')->nullable();
            $table->float('higher_expenditure')->nullable();
            $table->float('higher_revenue')->nullable();
            $table->integer('month');
            $table->integer('year');
            $table->float('average_expenditure')->nullable();
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
        Schema::dropIfExists('monthly_reports');
    }
}
