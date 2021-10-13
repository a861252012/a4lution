<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRmaRefundListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rma_refund_list', function (Blueprint $table) {
            $table->timestamp('create_date')->useCurrent()->useCurrentOnUpdate()->index('create_date');
            $table->string('ref_no')->nullable();
            $table->string('warehouse_ref_no')->nullable();
            $table->string('reason')->nullable();
            $table->string('trans_id')->nullable();
            $table->string('create_user')->nullable();
            $table->string('note')->nullable();
            $table->string('financial_note')->nullable();
            $table->string('verify_date')->nullable();
            $table->string('verify_user')->nullable();
            $table->string('user_account')->nullable();
            $table->string('user_account_name')->nullable();
            $table->string('refrence_no_platform')->nullable();
            $table->string('rma_refrence_no_platform')->nullable();
            $table->string('paid_date')->nullable();
            $table->string('warehouse_ship_date')->nullable();
            $table->string('country')->nullable();
            $table->string('site')->nullable();
            $table->string('warehous_id')->nullable();
            $table->string('shipping_method')->nullable();
            $table->string('operator_note')->nullable();
            $table->string('customer_service_note')->nullable();
            $table->string('product_sku')->nullable();
            $table->string('sale_user')->nullable();
            $table->string('product_title', 500)->nullable();
            $table->string('qty')->nullable();
            $table->string('pc_like', 50)->index('pc_like');
            $table->string('pc_name', 50)->index('pc_name');
            $table->string('pay_ref_id')->nullable();
            $table->string('refund_type')->nullable();
            $table->string('amount_refund')->nullable();
            $table->string('amount_paid')->nullable();
            $table->string('amount_order')->nullable();
            $table->string('currency')->nullable();
            $table->string('buyer_id')->nullable();
            $table->string('refund_date')->nullable();
            $table->string('status')->nullable();
            $table->string('refund_step')->nullable();
            $table->string('refund_data_source')->nullable();
            $table->string('sync_message', 1000)->nullable();
            $table->string('rmap_ship_fee')->nullable();
            $table->timestamp('created_at')->default('0000-00-00 00:00:00 COMMENT '系統資料-資料同步寫入時間'');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rma_refund_list');
    }
}
