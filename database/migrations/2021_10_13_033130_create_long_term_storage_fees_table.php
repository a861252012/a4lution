<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLongTermStorageFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('long_term_storage_fees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('account', 50)->nullable()->index('account');
            $table->string('snapshot_date', 50)->nullable();
            $table->string('sku', 100)->nullable();
            $table->string('fnsku', 100)->nullable();
            $table->string('asin', 100)->nullable();
            $table->string('product_name')->nullable();
            $table->string('supplier_type', 50)->nullable();
            $table->string('supplier', 50)->nullable();
            $table->string('condition', 50)->nullable();
            $table->string('qty_charged_12_mo_long_term_storage_fee', 50)->nullable();
            $table->decimal('per_unit_volume', 10, 4)->nullable();
            $table->char('currency', 3)->nullable();
            $table->decimal('12_mo_long_terms_storage_fee', 5, 2)->nullable();
            $table->decimal('hkd', 10, 5)->nullable();
            $table->decimal('hkd_rate', 10, 5)->nullable();
            $table->decimal('qty_charged_6_mo_long_term_storage_fee', 10, 5)->nullable();
            $table->decimal('6_mo_long_terms_storage_fee', 10, 5)->nullable();
            $table->string('volume_unit', 50)->nullable();
            $table->char('country', 2)->nullable();
            $table->string('enrolled_in_small_and_light', 50)->nullable();
            $table->unsignedInteger('upload_id')->nullable()->index('upload_id');
            $table->date('report_date')->nullable()->index('data_period');
            $table->tinyInteger('active')->nullable()->index('avtive');
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('long_term_storage_fees');
    }
}
