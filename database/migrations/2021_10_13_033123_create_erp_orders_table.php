<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateErpOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('erp_orders', function (Blueprint $table) {
            $table->string('Platform')->nullable();
            $table->string('Acc_Nick_Name');
            $table->string('Acc_Name')->nullable();
            $table->string('Site')->nullable();
            $table->string('ERP_Order_ID')->nullable();
            $table->string('Package_Type')->nullable();
            $table->string('Order_Type')->nullable();
            $table->string('Paid_Date')->nullable();
            $table->string('Shipped_Date')->nullable();
            $table->string('Audit_Date')->nullable();
            $table->string('Platform_SKU')->nullable();
            $table->string('ITEM_ID_ASIN')->nullable();
            $table->string('Product_Name', 500)->nullable();
            $table->string('SKU', 500)->nullable();
            $table->string('Supplier_Type')->nullable();
            $table->string('Supplier')->nullable();
            $table->string('Warehouse')->nullable();
            $table->string('Site_Order_ID')->nullable();
            $table->string('Package_ID')->nullable();
            $table->string('QTY')->nullable();
            $table->string('Shipping_Method')->nullable();
            $table->string('Tracking')->nullable();
            $table->string('Product_Weight')->nullable();
            $table->string('Original_Currency')->nullable();
            $table->string('Order_Price_Original_Currency')->nullable();
            $table->string('PayPal_Fee_Original_Currency')->nullable();
            $table->string('Transaction_Fee_Original_Currency')->nullable();
            $table->string('FBA_Fee_Original_Currency')->nullable();
            $table->string('First_Mile_Shipping_Fee_Original_Currency')->nullable();
            $table->string('First_Mile_Tariff_Original_Currency')->nullable();
            $table->string('Last_Mile_Shipping_Fee_Original_Currency')->nullable();
            $table->string('Other_Fee_Original_Currency')->nullable();
            $table->string('Purchase_Shipping_Fee_Original_Currency')->nullable();
            $table->string('Product_Cost_Original_Currency')->nullable();
            $table->string('Marketplace_Tax_Original_Currency')->nullable();
            $table->string('Cost_of_Point_Original_Currency')->nullable();
            $table->string('Exclusives_Referral_Fee_Original_Currency')->nullable();
            $table->string('Country')->nullable();
            $table->string('Note')->nullable();
            $table->string('Gross_Profit_Original_Currency')->nullable();
            $table->string('Gross_Margin_Original_Currency')->nullable();
            $table->string('HKD')->nullable();
            $table->string('HKD_Rate')->nullable();
            $table->string('Order_Price_HKD')->nullable();
            $table->string('PayPal_Fee_HKD')->nullable();
            $table->string('Transaction_Fee_HKD')->nullable();
            $table->string('FBA_Fee_HKD')->nullable();
            $table->string('First_Mile_Shipping_Fee_HKD')->nullable();
            $table->string('First_Mile_Tariff_HKD')->nullable();
            $table->string('Last_Mile_Shipping_Fee_HKD')->nullable();
            $table->string('Other_Fee_HKD')->nullable();
            $table->string('Purchase_Shipping_Fee_HKD')->nullable();
            $table->string('Product_Cost_HKD')->nullable();
            $table->string('Marketplace_Tax_HKD')->nullable();
            $table->string('Cost_of_Point_HKD')->nullable();
            $table->string('Exclusives_Referral_Fee_HKD')->nullable();
            $table->string('Gross_Profit_HKD')->nullable();
            $table->string('Gross_Margin_HKD')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('erp_orders');
    }
}
