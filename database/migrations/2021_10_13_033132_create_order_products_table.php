<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_code', 100)->nullable()->index('order_code');
            $table->string('sku', 100)->nullable()->index('sku');
            $table->decimal('weight', 10, 3)->nullable();
            $table->string('supplier_type', 100)->nullable();
            $table->string('supplier', 100)->nullable()->index('supplier');
            $table->char('currency_code', 3)->nullable();
            $table->unsignedDecimal('sales_amount', 10, 4)->nullable()->default('0.0000');
            $table->unsignedDecimal('paypal_fee', 10, 4)->nullable()->default('0.0000');
            $table->unsignedDecimal('transaction_fee', 10, 4)->nullable()->default('0.0000');
            $table->unsignedDecimal('fba_fee', 10, 4)->nullable()->default('0.0000');
            $table->unsignedDecimal('first_mile_shipping_fee', 10, 4)->nullable()->default('0.0000');
            $table->unsignedDecimal('first_mile_tariff', 10, 4)->nullable()->default('0.0000');
            $table->unsignedDecimal('last_mile_shipping_fee', 10, 4)->nullable()->default('0.0000');
            $table->decimal('other_fee', 10, 4)->nullable()->default('0.0000');
            $table->unsignedDecimal('purchase_shipping_fee', 10, 4)->nullable()->default('0.0000');
            $table->unsignedDecimal('product_cost', 10, 4)->nullable()->default('0.0000');
            $table->unsignedDecimal('marketplace_tax', 10, 4)->nullable()->default('0.0000');
            $table->unsignedDecimal('cost_of_point', 10, 4)->nullable()->default('0.0000');
            $table->unsignedDecimal('exclusives_referral_fee', 10, 4)->nullable()->default('0.0000');
            $table->unsignedDecimal('gross_profit', 10, 4)->nullable()->default('0.0000');
            $table->unsignedDecimal('other_transaction', 10, 4)->nullable()->default('0.0000');
            $table->timestamps();
            $table->unsignedInteger('created_by')->nullable()->default(0);
            $table->unsignedInteger('updated_by')->nullable()->default(0);
            $table->tinyInteger('active');
            $table->decimal('promotion_discount_rate', 5, 2)->nullable();
            $table->decimal('promotion_amount', 7, 2)->nullable();
            $table->decimal('sku_commission_rate', 5, 2)->nullable();
            $table->decimal('sku_commission_amount', 10, 6)->nullable();
            $table->timestamp('sku_commission_computed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_products');
    }
}
