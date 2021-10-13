<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderSkuCostDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_sku_cost_details', function (Blueprint $table) {
            $table->string('platform', 100)->nullable();
            $table->string('order_platform_type', 50)->nullable();
            $table->string('order_sale_type', 10)->nullable();
            $table->string('site_id', 100)->nullable();
            $table->string('seller_id', 100)->nullable();
            $table->string('warehouse_id', 100)->nullable();
            $table->string('sm_code', 100)->nullable();
            $table->string('aci_id', 100)->nullable();
            $table->string('product_barcode', 100)->nullable()->index('product_barcode');
            $table->string('op_platform_sales_sku', 100)->nullable();
            $table->string('op_platform_sales_sku_quantity', 100)->nullable();
            $table->string('quantity', 100)->nullable();
            $table->string('give_up', 10)->nullable();
            $table->string('reference_no', 100)->nullable()->index('reference_no');
            $table->string('currency_code', 100)->nullable();
            $table->string('order_total_amount', 100)->nullable();
            $table->string('product_amount', 100)->nullable();
            $table->string('buyer_pay_shipping', 100)->nullable();
            $table->string('product_id', 100)->nullable();
            $table->string('ebay_seller_rebate', 100)->nullable();
            $table->string('shipping_fee', 100)->nullable();
            $table->string('payment_platform_fee', 100)->nullable();
            $table->string('platform_cost', 100)->nullable();
            $table->string('fba_fee', 100)->nullable();
            $table->string('package_fee', 100)->nullable();
            $table->string('warehouse_storage_charges', 100)->nullable();
            $table->string('processing_fee', 100)->nullable();
            $table->string('other_fee', 100)->nullable();
            $table->string('purchase_shipping_fee', 100)->nullable();
            $table->string('purchase_taxation_fee', 100)->nullable();
            $table->string('purchase_cost', 100)->nullable();
            $table->string('service_transport_fee', 100)->nullable();
            $table->string('currency_rate', 100)->nullable();
            $table->string('pay_time', 100)->nullable();
            $table->string('update_time', 100)->nullable();
            $table->string('avg_unit_price', 100)->nullable();
            $table->string('avg_purchase_price', 100)->nullable();
            $table->string('first_carrier_freight', 100)->nullable();
            $table->string('tariff_fee', 100)->nullable();
            $table->string('reference_price', 100)->nullable();
            $table->string('platform_reference_no', 100)->nullable();
            $table->string('order_total_amount_org', 100)->nullable();
            $table->string('product_amount_org', 100)->nullable();
            $table->string('buyer_pay_shipping_org', 100)->nullable();
            $table->string('ebay_seller_rebate_org', 100)->nullable();
            $table->string('shipping_fee_org', 100)->nullable();
            $table->string('payment_platform_fee_org', 100)->nullable();
            $table->string('platform_cost_org', 100)->nullable();
            $table->string('fba_fee_org', 100)->nullable();
            $table->string('other_fee_org', 100)->nullable();
            $table->string('package_fee_org', 100)->nullable();
            $table->string('warehouse_storage_charges_org', 100)->nullable();
            $table->string('processing_fee_org', 100)->nullable();
            $table->string('currency_code_org', 100)->nullable();
            $table->string('avg_unit_price_org', 100)->nullable();
            $table->string('develop_responsible_id', 100)->nullable();
            $table->string('buyer_id', 100)->nullable();
            $table->string('seller_responsible_id', 100)->nullable();
            $table->string('product_title')->nullable();
            $table->string('develop_responsible_name', 100)->nullable();
            $table->string('buyer_name', 100)->nullable();
            $table->string('seller_responsible_name', 100)->nullable();
            $table->string('destination_country', 100)->nullable();
            $table->string('receiving_code', 100)->nullable();
            $table->string('asin_or_item', 100)->nullable();
            $table->string('date_release', 100)->nullable();
            $table->string('so_ship_time', 100)->nullable();
            $table->string('ship_time', 100)->nullable();
            $table->string('total_cost', 100)->nullable();
            $table->string('gross_profit', 100)->nullable();
            $table->string('gross_profit_rate', 100)->nullable();
            $table->string('factory_gross_profit', 100)->nullable();
            $table->string('factory_gross_margin', 100)->nullable();
            $table->timestamp('created_at')->nullable();
            
            $table->unique(['reference_no', 'product_barcode'], 'order_sku_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_sku_cost_details');
    }
}
