<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->string('platform', 50)->nullable();
            $table->string('order_code', 100)->primary();
            $table->string('reference_no', 100)->nullable();
            $table->string('seller_id', 100)->nullable();
            $table->string('sm_code', 100)->nullable();
            $table->string('add_time', 100)->nullable();
            $table->string('order_paydate', 100)->nullable();
            $table->string('order_status', 50);
            $table->string('warehouse_id', 100)->nullable();
            $table->string('process_time', 100)->nullable();
            $table->string('pack_time', 100)->nullable();
            $table->string('ship_time', 100)->nullable()->index('ship_time');
            $table->string('cutoff_time', 100)->nullable();
            $table->string('process_user_id', 50)->nullable();
            $table->string('packager_id', 50)->nullable();
            $table->string('pack_user_id', 50)->nullable();
            $table->string('ship_user_id', 50)->nullable();
            $table->string('import_user_id', 50)->nullable();
            $table->string('import_time', 100)->nullable();
            $table->string('dismountable_time', 100)->nullable();
            $table->string('service_number_convert', 100)->nullable();
            $table->string('tracking_number', 100)->nullable();
            $table->decimal('so_weight', 10, 3)->nullable();
            $table->string('platform_user_name')->nullable();
            $table->string('platform_ref_no')->nullable();
            $table->string('buyer_id', 100)->nullable();
            $table->string('buyer_name')->nullable();
            $table->string('warehouse_name')->nullable();
            $table->string('warehouse_code', 50)->nullable();
            $table->timestamp('created_at')->nullable()->index('created_at');
            $table->string('order_type', 50)->nullable();
            $table->string('package_type', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
