<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class FollowersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        $user = $users->first();
        $user_id = $user->id;

        //获取除第一个以外的其他用户
        $followers = $users->slice(1);
        //获取这些用户的id，数组形式
        $follower_ids = $followers->pluck('id')->toArray();
        //一号用户，关注其他用户
        $user->follow($follower_ids);
        //其他用户，关注一号用户
        foreach ($followers as $follower) {
            $follower->follow($user_id);
        }
    }
}
