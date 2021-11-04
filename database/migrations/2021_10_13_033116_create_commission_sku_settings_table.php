<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommissionSkuSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commission_sku_settings', function (Blueprint $table) {
            $table->string('client_code', 50);
            $table->string('site', 50);
            $table->string('currency', 50);
            $table->string('sku', 50);
            $table->decimal('threshold', 10, 2);
            $table->decimal('basic_rate', 10, 2);
            $table->decimal('upper_bound_rate', 10, 2);
            $table->boolean('active')->default(1);
            $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
            $table->integer('created_by');
            $table->timestamp('updated_at')->default('0000-00-00 00:00:00');
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
        Schema::dropIfExists('commission_sku_settings');
    }
}
