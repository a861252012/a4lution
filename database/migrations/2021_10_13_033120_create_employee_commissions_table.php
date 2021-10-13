<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_commissions', function (Blueprint $table) {
            $table->increments('id');
            $table->date('report_date')->index('report_date');
            $table->integer('employee_user_id')->nullable()->index('employee_user_id');
            $table->integer('role_id')->nullable();
            $table->string('role_name', 50)->nullable();
            $table->char('currency', 3)->nullable();
            $table->char('region', 2)->nullable();
            $table->string('company_type', 10)->nullable();
            $table->integer('customer_qty')->nullable();
            $table->decimal('extra_monthly_fee_amount', 10, 2)->nullable();
            $table->decimal('extra_monthly_fee_rate', 10, 2)->nullable();
            $table->decimal('extra_ops_commission_amount', 10, 2)->nullable();
            $table->decimal('extra_ops_commission_rate', 10, 2)->nullable();
            $table->decimal('total_billed_commissions_amount', 10, 2)->nullable();
            $table->char('total_billed_commission_currency', 3)->nullable();
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
        Schema::dropIfExists('employee_commissions');
    }
}
