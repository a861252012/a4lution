<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExchangeRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->date('quoted_date')->nullable();
            $table->char('base_currency', 3)->index('base_currency');
            $table->char('quote_currency', 3);
            $table->decimal('exchange_rate', 10, 6);
            $table->tinyInteger('active')->nullable();
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            
            $table->unique(['quoted_date', 'base_currency'], 'unique_quoted_date_base_currency');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exchange_rates');
    }
}
