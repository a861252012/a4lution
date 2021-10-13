<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmazonReportListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amazon_report_list', function (Blueprint $table) {
            $table->string('ras_id')->nullable();
            $table->string('order_id')->nullable()->index('order_id');
            $table->string('user_account')->nullable()->index('user_account');
            $table->string('site_id')->nullable()->index('site_id');
            $table->string('currency')->nullable();
            $table->string('transaction_type')->nullable();
            $table->string('amount_type')->nullable();
            $table->string('amount_description')->nullable()->index('amount_description');
            $table->string('amount')->nullable();
            $table->string('fulfillment_id')->nullable();
            $table->string('posted_date')->nullable();
            $table->string('posted_date_time')->nullable();
            $table->string('sku')->nullable()->index('sku');
            $table->string('quantity_purchased')->nullable();
            $table->string('promotion_id')->nullable();
            $table->string('warehouse_sku_info', 3000)->nullable();
            $table->string('order_status')->nullable();
            $table->string('settlement_start_date')->nullable();
            $table->string('settlement_end_date')->nullable();
            $table->string('deposit_date')->nullable();
            $table->string('marketplace_name')->nullable();
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
        Schema::dropIfExists('amazon_report_list');
    }
}
