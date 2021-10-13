<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoleAssignmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_assignment', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index('user_id');
            $table->unsignedInteger('role_id')->index('role_id');
            $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('updated_at')->default('0000-00-00 00:00:00 COMMENT '更新時間'');
            $table->bit('active', 1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_assignment');
    }
}
