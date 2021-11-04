<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_relations', function (Blueprint $table) {
            $table->string('client_code', 50);
            $table->integer('user_id');
            $table->tinyInteger('role_id');
            $table->tinyInteger('active');
            $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedInteger('created_by');
            $table->timestamp('updated_at')->default('0000-00-00 00:00:00 COMMENT '更新時間'');
            $table->unsignedInteger('updated_by');
            
            $table->unique(['client_code', 'user_id', 'role_id'], 'client_code_sales_user_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_relations');
    }
}
