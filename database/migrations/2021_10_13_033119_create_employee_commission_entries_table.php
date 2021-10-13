<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeCommissionEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_commission_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_commissions_id')->nullable()->index('employee_commissions_id');
            $table->integer('billing_statement_id')->nullable()->index('billing_statement_id');
            $table->date('report_date')->nullable()->index('report_date');
            $table->string('client_code', 50)->nullable()->index('client_code');
            $table->integer('contract_length')->nullable();
            $table->decimal('monthly_fee', 10, 2)->nullable();
            $table->decimal('cross_sales', 10, 2)->nullable();
            $table->decimal('ops_commission', 10, 2)->nullable();
            $table->string('calculation_expression')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->tinyInteger('active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_commission_entries');
    }
}
