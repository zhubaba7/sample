<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Status;

class StatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_ids = ['1', '2', '3'];
        //用app方法获取一个Faker容器实例，实例的randomElement方法取指定数组的任意一个值
        $faker = app(Faker\Generator::class);
        //make是调用100次工厂方法后组成结果集合，然后单独加工每个数据
        //$status代表单个，命名无所谓，取其他名也可，就像foreach那样
        $statuses = factory(Status::class)->times(100)->make()->each(function($status) use ($faker, $user_ids) {
            $status->user_id = $faker->randomElement($user_ids);
        });

        //模型通用静态方法插入
        Status::insert($statuses->toArray());
    }
}
