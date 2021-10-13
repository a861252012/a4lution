<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_name', 20)->unique('account');
            $table->string('password');
            $table->unsignedTinyInteger('actor_type');
            $table->string('full_name');
            $table->char('currency', 3)->nullable();
            $table->string('company_type', 10)->nullable();
            $table->string('region', 10)->nullable();
            $table->string('company_name', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone_number', 100)->nullable();
            $table->string('address', 100)->nullable();
            $table->bit('active', 1)->nullable();
            $table->boolean('lang_type')->default(1);
            $table->unsignedInteger('token_validity_period')->default(60);
            $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
            $table->integer('created_by');
            $table->timestamp('updated_at')->default('0000-00-00 00:00:00 COMMENT '更新時間'');
            $table->integer('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
