<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->text('content');
            $table->integer('user_id')->index();
            $table->index(['created_at']);
            //timestamps方法会自动生成created_at和updated_at字段，所以
            //上方只加了索引，无需重复创建。应用中，创建时间会用于倒序排列，
            //用户id字段用于查询，都加上索引提高效率
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
        Schema::drop('statuses');
    }
}
