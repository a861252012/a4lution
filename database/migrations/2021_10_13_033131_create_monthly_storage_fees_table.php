<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonthlyStorageFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monthly_storage_fees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('account', 50)->nullable()->index('account');
            $table->string('asin', 50)->nullable();
            $table->string('fnsku', 50)->nullable();
            $table->string('product_name')->nullable();
            $table->string('fulfilment_center', 50)->nullable();
            $table->string('country_code', 50)->nullable();
            $table->string('supplier_type', 50)->nullable();
            $table->string('supplier', 50)->nullable()->index('supplier');
            $table->string('longest_side', 50)->nullable();
            $table->string('median_side', 50)->nullable();
            $table->string('shortest_side', 50)->nullable();
            $table->string('measurement_units', 50)->nullable();
            $table->string('weight', 50)->nullable();
            $table->string('weight_units', 50)->nullable();
            $table->string('item_volume', 50)->nullable();
            $table->string('volume_units', 50)->nullable();
            $table->string('product_size_tier', 50)->nullable();
            $table->string('average_quantity_on_hand', 50)->nullable();
            $table->string('average_quantity_pending_removal', 50)->nullable();
            $table->string('total_item_volume_est', 50)->nullable();
            $table->string('month_of_charge', 50)->nullable();
            $table->string('storage_rate', 50)->nullable();
            $table->char('currency', 3)->nullable();
            $table->string('hkd', 50)->nullable();
            $table->string('monthly_storage_fee_est', 50)->nullable();
            $table->string('hkd_rate', 50)->nullable();
            $table->string('dangerous_goods_storage_type', 50)->nullable();
            $table->string('category', 50)->nullable();
            $table->string('eligible_for_discount', 50)->nullable();
            $table->string('qualified_for_discount', 50)->nullable();
            $table->string('total_incentive_fee_amount', 50)->nullable();
            $table->string('breakdown_incentive_fee_amount', 50)->nullable();
            $table->string('average_quantity_customer_orders', 50)->nullable();
            $table->unsignedInteger('upload_id')->nullable()->index('upload_id');
            $table->date('report_date')->nullable()->index('data_period');
            $table->unsignedTinyInteger('active')->nullable()->index('active');
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            
            $table->index(['created_at'], 'created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monthly_storage_fees');
    }
}
