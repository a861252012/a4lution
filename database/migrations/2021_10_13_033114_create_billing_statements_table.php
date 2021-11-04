<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingStatementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_statements', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->date('report_date')->index('report_date');
            $table->string('client_code', 50)->index('client_code');
            $table->integer('total_sales_orders')->nullable();
            $table->decimal('total_sales_amount', 10, 2)->nullable();
            $table->decimal('total_expenses', 10, 2)->nullable();
            $table->decimal('sales_gp', 10, 2)->nullable();
            $table->decimal('a4_account_logistics_fee', 10, 2)->nullable();
            $table->decimal('a4_account_fba_fee', 10, 2)->nullable();
            $table->decimal('a4_account_fba_storage_fee', 10, 2)->nullable();
            $table->decimal('a4_account_platform_fee', 10, 2)->nullable();
            $table->decimal('a4_account_refund_and_resend', 10, 2)->nullable();
            $table->decimal('a4_account_miscellaneous', 10, 2)->nullable();
            $table->decimal('a4_account_advertisement', 10, 2)->nullable();
            $table->decimal('a4_account_marketing_and_promotion', 10, 2)->nullable();
            $table->decimal('client_account_logistics_fee', 10, 2)->nullable();
            $table->decimal('client_account_fba_fee', 10, 2)->nullable();
            $table->decimal('client_account_fba_storage_fee', 10, 2)->nullable();
            $table->decimal('client_account_platform_fee', 10, 2)->nullable();
            $table->decimal('client_account_refund_and_resend', 10, 2)->nullable();
            $table->decimal('client_account_miscellaneous', 10, 2)->nullable();
            $table->decimal('client_account_advertisement', 10, 2)->nullable();
            $table->decimal('client_account_marketing_and_promotion', 10, 2)->nullable();
            $table->decimal('avolution_commission', 10, 2)->nullable();
            $table->decimal('sales_tax_handling', 10, 2)->nullable();
            $table->decimal('extraordinary_item', 10, 2)->nullable();
            $table->decimal('sales_credit', 10, 2)->nullable();
            $table->decimal('opex_invoice', 10, 2)->nullable();
            $table->decimal('fba_storage_fee_invoice', 10, 2)->nullable();
            $table->decimal('final_credit', 10, 2)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->unsignedInteger('created_by')->nullable()->default(0);
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedInteger('deleted_by')->nullable()->default(0);
            $table->timestamp('cutoff_time')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->integer('closed_by')->nullable();
            $table->tinyInteger('active');
            $table->string('commission_type')->default('');
            
            $table->primary(['id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('billing_statements');
    }
}
