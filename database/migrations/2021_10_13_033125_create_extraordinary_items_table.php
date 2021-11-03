<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExtraordinaryItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extraordinary_items', function (Blueprint $table) {
            $table->increments('id');
            $table->date('report_date');
            $table->string('client_code', 50);
            $table->string('item_name', 100)->nullable();
            $table->string('description');
            $table->decimal('item_amount', 10, 2)->default(0.00);
            $table->decimal('receivable_amount', 10, 2)->default(0.00);
            $table->decimal('payable_amount', 10, 2)->default(0.00);
            $table->char('currency_code', 3);
            $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedInteger('created_by');
            $table->timestamp('updated_at')->default('0000-00-00 00:00:00');
            $table->integer('updated_by')->default(0);
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
        Schema::dropIfExists('extraordinary_items');
    }
}
