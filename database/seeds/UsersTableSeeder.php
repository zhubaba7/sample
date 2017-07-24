<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //创建用户模型后，用工厂提供的API，times生成50个模型，make为所有模型
        //创建一个集合
        $users = factory(User::class)->times(50)->make();
        User::insert($users->toArray());

        //修改第一个数据，方便登陆测试，没什么特别意义
        $user = User::find(1);
        $user->name = 'Aufree';
        $user->email = 'Aufree@126.com';
        $user->password = bcrypt('password');
        $user->is_admin = true;
        $user->activated = true;
        $user->save();
    }
}
