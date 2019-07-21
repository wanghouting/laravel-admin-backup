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
            $table->tinyInteger('status')->default(0)->comment('执行状态：0:运行中，1:成功，2失败,3停止');
            $table->string('file')->default('')->comment('备份文件');
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
