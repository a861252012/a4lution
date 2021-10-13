<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSystemChangelogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_changelogs', function (Blueprint $table) {
            $table->increments('log_id');
            $table->string('menu_path', 100);
            $table->char('event_type', 1);
            $table->string('table_name', 100);
            $table->string('reference_id', 100);
            $table->string('field_name', 100);
            $table->string('original_value')->nullable();
            $table->string('new_value')->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_changelogs');
    }
}
