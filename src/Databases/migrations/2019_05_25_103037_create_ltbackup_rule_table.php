<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLtbackupRuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ltbackup_rule', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('name',120)->default('')->comment('规则名称');
            $table->tinyInteger('type')->default(0)->comment('0：全站备份,1:只备份源代码，2：只备份数据库，-99：指定备份目录');
            $table->unsignedTinyInteger('status')->default(0)->comment('启用状态');
            $table->time('time_at')->comment('备份时间');
            $table->tinyInteger('period')->default(-1)->comment('周期,-1：每天，0:1次，-99指定天数');
            $table->integer('period_days')->default(0)->comment('周期具体天数');
            $table->string('path')->default('')->comment('指定的备份目录');
            $table->dateTime('next_run')->nullable()->comment('下次执行时间');
            $table->unsignedInteger('run_times')->default(0)->comment('执行次数');
            $table->unsignedInteger('run_fail_times')->default(0)->comment('失败次数');
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
        Schema::dropIfExists('ltbackup_rule');
    }
}
