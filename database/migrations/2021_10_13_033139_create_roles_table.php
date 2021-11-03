<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('system_type', 20)->index('system_type');
            $table->string('role_name', 50)->index('role_name');
            $table->string('role_desc', 100);
            $table->bit('active', 1);
            $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedInteger('created_by');
            $table->timestamp('updated_at')->default('0000-00-00 00:00:00');
            $table->unsignedInteger('updated_by');
            
            $table->unique(['system_type', 'role_name'], 'unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
