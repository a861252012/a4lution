<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('client_code', 50)->default('')->comment('客戶代碼');
            $table->string('transaction_type', 50)->comment('收款=deposit、退款=refund');
            $table->char('billing_month', 6)->default('')->comment('月費年月YYYYMM(關聯report_date)');
            $table->string('amount_type', 50)->default('')->comment('monthly_fee');
            $table->string('amount_description', 50)->default('');
            $table->date('deposit_date')->comment('收款日期(實際付款日期)');
            $table->char('currency', 6)->default('')->comment('付款幣別');
            $table->decimal('amount', 10, 2)->comment('付款金額');
            $table->string('remarks', 100)->default(null)
                ->comment('備註說明(費用金額(參照supplier master file - contract info))');
            $table->tinyInteger('active')->comment('資料狀態(1:有效,0:刪除)');
            $table->tinyInteger('is_dirty')->comment('是否已結算 (0:是/1:否)');
            $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate()->comment('新增時間');
            $table->integer('created_by')->comment('新增用戶(users.id)');
            $table->timestamp('updated_at')->default('0000-00-00 00:00:00')->comment('修改時間');
            $table->integer('updated_by')->comment('修改用戶(users.id)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statements');
    }
}
