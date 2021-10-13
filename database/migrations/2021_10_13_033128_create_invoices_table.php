<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('billing_statement_id')->default(0);
            $table->date('report_date')->index('report_date');
            $table->string('client_code', 50)->index('client_code');
            $table->string('supplier_name', 100);
            $table->string('client_contact', 100)->nullable();
            $table->string('client_company', 100)->nullable();
            $table->string('client_address1', 100)->nullable();
            $table->string('client_address2', 100)->nullable();
            $table->string('client_city', 100)->nullable();
            $table->string('client_district', 100)->nullable();
            $table->string('client_zip', 100)->nullable();
            $table->string('client_country', 100)->nullable();
            $table->string('opex_invoice_no', 100);
            $table->string('fba_shipment_invoice_no', 100);
            $table->string('credit_note_no', 100)->nullable();
            $table->date('issue_date')->nullable();
            $table->date('due_date')->nullable();
            $table->boolean('payment_terms')->default(0);
            $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedInteger('created_by');
            $table->timestamp('updated_at')->default('0000-00-00 00:00:00 COMMENT '修改時間'');
            $table->integer('updated_by')->default(0);
            $table->string('doc_status', 50)->default('0 COMMENT 'processing,deleted,active'')->index('doc_status');
            $table->string('doc_storage_token', 50)->nullable();
            $table->string('doc_file_name', 100)->nullable();
            $table->tinyInteger('active');
            $table->timestamp('approved_at')->nullable();
            $table->integer('approved_by')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
