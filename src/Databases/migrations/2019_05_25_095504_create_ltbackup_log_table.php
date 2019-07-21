<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLtbackupLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ltbackup_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('rule_id')->default(0)->comment('规则id');
            $table->tinyInteger('status')->default(0)->comment('执行状态：0:等待中，1:运行中，2成功,3失败,4停止');
            $table->string('file')->default('')->comment('备份文件');
            $table->dateTime('running_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ltbackup_log');
    }
}
