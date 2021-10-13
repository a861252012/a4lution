<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeOpsCommissionRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_ops_commission_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('currency', 3);
            $table->boolean('month_threshold_1')->default(0);
            $table->boolean('month_threshold_2')->default(0);
            $table->decimal('tier_1', 10, 2)->nullable();
            $table->decimal('tier_2', 10, 2)->nullable();
            $table->unsignedDecimal('tier_3', 10, 2)->nullable();
            $table->unsignedInteger('total_commission_threshold_1')->nullable()->default(0);
            $table->unsignedInteger('total_commission_threshold_2')->nullable()->default(0);
            $table->unsignedInteger('total_commission_threshold_3')->nullable()->default(0);
            $table->decimal('total_tier_1', 10, 2)->nullable();
            $table->decimal('total_tier_2', 10, 2)->nullable();
            $table->decimal('total_tier_3', 10, 2)->nullable();
            $table->tinyInteger('active');
            $table->timestamp('created_at')->nullable();
            $table->unsignedInteger('created_by')->nullable()->default(0);
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedInteger('deleted_by')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_ops_commission_rules');
    }
}
