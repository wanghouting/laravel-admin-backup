<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLtbackupSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        //设置分类表
        Schema::create('ltbackup_setting_types', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedSmallInteger('sort')->default(50)->comment('排序，从小到大');
            $table->string('name',120)->default('');
            $table->string('module','50')->comment('模块名称')->default('');
            $table->timestamps();
            $table->unique('name', 'ltbackup_setting_types_name_unique');
            $table->index('name','ltbackup_setting_types_name_index');
        });
        //设置表
        Schema::create('ltbackup_settings', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedSmallInteger('type')->comment('配置类型')->default(0);
            $table->string('form','50')->comment('表单类型')->default('');
            $table->string('name',120)->default('');
            $table->string('cname',120)->default('');
            $table->string('plainValue',255)->default('');
            $table->json('extra');
            $table->timestamps();
            $table->unique('name', 'ltbackup_settings_name_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::dropIfExists('ltbackup_settings');
        Schema::dropIfExists('ltbackup_setting_types');
    }
}
