<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlatformAdFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('platform_ad_fees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('client_code', 50)->nullable()->index('client_code');
            $table->string('client_type', 50)->nullable();
            $table->string('platform', 100)->nullable()->index('platform');
            $table->string('account', 100)->nullable();
            $table->string('campagin_type', 100)->nullable();
            $table->string('campagin')->nullable();
            $table->string('currency', 50)->nullable();
            $table->integer('Impressions')->nullable();
            $table->integer('clicks')->nullable();
            $table->decimal('ctr', 10, 4)->nullable();
            $table->decimal('spendings', 10, 4)->nullable();
            $table->decimal('spendings_hkd', 10, 4)->nullable();
            $table->decimal('cpc', 10, 4)->nullable();
            $table->decimal('sales_qty', 10, 4)->nullable();
            $table->decimal('sales_amount', 10, 4)->nullable();
            $table->decimal('sales_amount_hkd', 10, 4)->nullable();
            $table->decimal('acos', 10, 4)->nullable();
            $table->decimal('exchange_rate', 10, 4)->nullable();
            $table->unsignedInteger('upload_id')->nullable()->index('upload_id');
            $table->date('report_date')->nullable()->index('data_ym');
            $table->unsignedTinyInteger('active')->nullable()->default(0)->index('active');
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
        Schema::dropIfExists('platform_ad_fees');
    }
}
