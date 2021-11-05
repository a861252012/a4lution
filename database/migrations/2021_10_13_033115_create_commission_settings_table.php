<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommissionSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commission_settings', function (Blueprint $table) {
            $table->string('client_code', 50)->unique('client_code');
            $table->char('is_sku_level_commission', 1)->nullable();
            $table->char('tier', 1)->nullable();
            $table->decimal('basic_rate', 5, 2)->nullable();
            $table->string('currency', 3)->nullable();
            $table->bigInteger('tier_1_threshold')->nullable();
            $table->bigInteger('tier_2_threshold')->nullable();
            $table->bigInteger('tier_3_threshold')->nullable();
            $table->bigInteger('tier_4_threshold')->nullable();
            $table->decimal('tier_1_rate', 5, 2)->nullable();
            $table->decimal('tier_1_amount', 5, 2)->nullable();
            $table->decimal('tier_2_rate', 5, 2)->nullable();
            $table->decimal('tier_2_amount', 5, 2)->nullable();
            $table->decimal('tier_3_rate', 5, 2)->nullable();
            $table->decimal('tier_3_amount', 5, 2)->nullable();
            $table->decimal('tier_4_rate', 5, 2)->nullable();
            $table->decimal('tier_4_amount', 5, 2)->nullable();
            $table->decimal('tier_top_rate', 5, 2)->nullable();
            $table->decimal('tier_top_amount', 5, 2)->nullable();
            $table->string('promotion_threshold', 50)->nullable();
            $table->string('tier_promotion', 50)->nullable();
            $table->char('invoice', 1)->nullable();
            $table->boolean('active')->default(0);
            $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
            $table->integer('created_by');
            $table->timestamp('updated_at')->default('0000-00-00 00:00:00');
            $table->integer('updated_by');
            
            $table->unique(['client_code', 'tier'], 'client_code_tier_promotion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commission_settings');
    }
}
