<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeMonthlyFeeRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_monthly_fee_rules', function (Blueprint $table) {
            $table->string('client_code', 50)->unique('client_code');
            $table->tinyInteger('role_id')->nullable();
            $table->char('is_tiered_rate', 1)->nullable();
            $table->decimal('rate_base', 5, 2)->nullable();
            $table->decimal('rate', 5, 2)->nullable();
            $table->string('currency', 3)->nullable();
            $table->bigInteger('threshold')->nullable();
            $table->decimal('tier_1_first_year', 5, 2)->nullable();
            $table->decimal('tier_2_first_year', 5, 2)->nullable();
            $table->decimal('tier_1_over_a_year', 5, 2)->nullable();
            $table->decimal('tier_2_over_a_year', 5, 2)->nullable();
            $table->boolean('active')->default(0);
            $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
            $table->integer('created_by');
            $table->timestamp('updated_at')->default('0000-00-00 00:00:00 COMMENT '修改時間'');
            $table->integer('updated_by');
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_monthly_fee_rules');
    }
}
