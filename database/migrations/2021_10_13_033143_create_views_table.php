<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('views', function (Blueprint $table) {
            $table->increments('id');
            $table->string('system_type', 20)->index('system_type');
            $table->string('module', 3);
            $table->string('menu_slug', 10)->unique('menu_slug');
            $table->string('menu_title', 50);
            $table->string('path');
            $table->unsignedInteger('level');
            $table->unsignedInteger('order');
            $table->bit('active', 1);
            $table->timestamps();
            $table->integer('created_by');
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
        Schema::dropIfExists('views');
    }
}
