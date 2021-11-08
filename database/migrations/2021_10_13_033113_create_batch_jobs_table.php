<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index('batch_jobs_user_name_index');
            $table->string('fee_type', 100)->index('batch_jobs_type_index');
            $table->string('file_name');
            $table->date('report_date');
            $table->unsignedInteger('total_count')->nullable();
            $table->string('status', 50)->default('Processing')->index('batch_jobs_status_index');
            $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate()->index('created_at');
            $table->timestamp('finished_at')->nullable();
            $table->longText('exit_message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('batch_jobs');
    }
}
