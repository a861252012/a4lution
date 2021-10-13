<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFirstMileShipmentFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('first_mile_shipment_fees', function (Blueprint $table) {
            $table->increments('id');
            $table->date('report_date')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->integer('created_by')->nullable();
            $table->tinyInteger('active')->nullable();
            $table->string('fulfillment_center', 50)->nullable();
            $table->string('client_code', 50)->nullable();
            $table->string('ids_sku')->nullable();
            $table->string('title')->nullable();
            $table->string('asin')->nullable();
            $table->string('fnsku')->nullable();
            $table->string('external_id')->nullable();
            $table->string('condition')->nullable();
            $table->string('who_will_prep')->nullable();
            $table->string('prep_type')->nullable();
            $table->string('who_will_label')->nullable();
            $table->string('shipped')->nullable();
            $table->string('fba_shipment')->nullable();
            $table->string('shipment_type')->nullable();
            $table->string('date')->nullable();
            $table->string('account')->nullable();
            $table->string('ship_from')->nullable();
            $table->string('first_mile')->nullable();
            $table->string('last_mile_est_orig')->nullable();
            $table->string('last_mile_act_orig')->nullable();
            $table->string('shipment_remark')->nullable();
            $table->string('currency_last_mile')->nullable();
            $table->string('exchange_rate')->nullable();
            $table->string('total')->nullable();
            $table->string('is_payment_settled')->nullable();
            $table->string('remark')->nullable();
            $table->unsignedInteger('upload_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('first_mile_shipment_fees');
    }
}
