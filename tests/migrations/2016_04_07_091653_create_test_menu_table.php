<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestMenuTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pid')->default(0)->comment('父级');
            $table->string('name')->nullable()->comment('菜单名称');
            $table->string('icon')->nullable()->comment('图标');
            $table->string('permission_id')->nullable()->comment('菜单对应的权限ID');
            $table->string('url')->nullable()->comment('菜单链接地址');
            $table->string('active')->nullable()->comment('菜单高亮地址');
            $table->tinyInteger('sort')->default(0)->comment('排序');
            $table->string('description')->nullable()->comment('描述');
            $table->tinyInteger('status')->default(1)->comment('是否显示：0不显示；1显示');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('menus');
    }
}
