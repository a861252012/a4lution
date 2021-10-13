<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->string('client_code', 100)->primary();
            $table->string('supplier_code', 100)->nullable();
            $table->string('company_name', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('contact_person', 100)->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('address1', 100)->nullable();
            $table->string('address2', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('zip', 50)->nullable();
            $table->char('sales_region', 2)->nullable();
            $table->date('contract_date')->nullable();
            $table->boolean('active')->nullable()->default(0);
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
        Schema::dropIfExists('customers');
    }
}
