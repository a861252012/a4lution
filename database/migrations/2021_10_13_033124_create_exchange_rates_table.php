<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExchangeRatesTable extends Migration
{
    private $tableName = 'exchange_rates';

    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->date('quoted_date')->nullable()->comment('匯率適用月份');
            $table->char('base_currency', 3)->index('base_currency')->comment('貨幣代碼(原本貨幣)');
            $table->char('quote_currency', 3)->comment('貨幣代碼(兌換成目的貨幣)');
            $table->decimal('exchange_rate', 10, 6);
            $table->tinyInteger('active')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            
            $table->unique(['quoted_date', 'base_currency'], 'unique_quoted_date_base_currency');

            DB::statement("ALTER TABLE `{$this->tableName}` comment '系統資料-(每月)匯率\r\n(說明-資料內容)做2021年8月份的报表，使用21年9月的“上月Avg. rate”\r\n(說明-程式比對)做2021年8月份的报表，使用quoted_date=8月份'");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }
}
