<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViewPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('view_permission', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('role_id')->index('role_id');
            $table->unsignedInteger('view_id')->index('view_id');
            $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('updated_at')->default('0000-00-00 00:00:00');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('view_permission');
    }
}
