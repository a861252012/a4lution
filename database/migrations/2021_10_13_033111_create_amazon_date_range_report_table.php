<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmazonDateRangeReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amazon_date_range_report', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->string('account', 50)->nullable()->index('account');
            $table->string('country', 50)->nullable();
            $table->string('paid_date', 50)->nullable();
            $table->string('shipped_date', 50)->nullable();
            $table->string('settlement_id', 50)->nullable();
            $table->string('type', 50)->nullable();
            $table->string('description')->nullable();
            $table->string('order_id', 100)->nullable()->index('order_id');
            $table->string('order_type', 50)->nullable();
            $table->string('msku', 50)->nullable();
            $table->string('asin', 50)->nullable();
            $table->string('product_name')->nullable();
            $table->string('sku', 50)->nullable()->index('sku');
            $table->string('supplier_type', 50)->nullable();
            $table->string('supplier', 50)->nullable()->index('supplier');
            $table->string('marketplace', 50)->nullable();
            $table->string('fulfillment', 50)->nullable();
            $table->integer('quantity')->nullable();
            $table->string('currency', 3)->nullable();
            $table->decimal('product_sales', 10, 2)->nullable();
            $table->decimal('shipping_credits', 10, 2)->nullable();
            $table->decimal('gift_wrap_credits', 10, 2)->nullable();
            $table->decimal('promotional_rebates', 10, 2)->nullable();
            $table->decimal('cost_of_point', 10, 2)->nullable();
            $table->decimal('tax', 10, 2)->nullable();
            $table->decimal('marketplace_withheld_tax', 10, 2)->nullable();
            $table->decimal('selling_fees', 10, 2)->nullable();
            $table->decimal('fba_fees', 10, 2)->nullable();
            $table->decimal('other_transaction_fees', 10, 2)->nullable();
            $table->decimal('other', 10, 2)->nullable();
            $table->decimal('amazon_total', 10, 2)->nullable();
            $table->decimal('hkd_rate', 10, 2)->nullable();
            $table->decimal('amazon_total_hkd', 10, 2)->nullable();
            $table->string('upload_id', 50)->nullable()->index('upload_id');
            $table->date('report_date')->nullable()->index('report_date');
            $table->tinyInteger('active')->nullable()->index('active');
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
        Schema::dropIfExists('amazon_date_range_report');
    }
}
