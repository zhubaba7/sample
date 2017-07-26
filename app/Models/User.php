<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use Auth;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * boot方法在用户模型加载完成后执行，Eloquent提供的事件监听creating
     * 需要放在该方法中
     *
     * @var array
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->activation_token = str_random(30);
        });
    }

    /**
     * Avatar service
     *
     * @var $size int
     */
    public function gravatar($size = '100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    //和Status模型互动，一个用户可以拥有多条动态
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    //动态流
    public function feed()
    {
        //pluck把自己去关注的人的id分离出来
        $user_ids = Auth::user()->followings->pluck('id')->toArray();
        array_push($user_ids, Auth::user()->id);

        //Eloquent关联的预加载方法with，避免了N+1查找的问题
        return Status::whereIn('user_id', $user_ids)
                                ->with('user')
                                ->orderBy('created_at', 'desc');
        //return $this->statuses()->orderBy('created_at', 'desc');
    }

    //粉丝列表
    public function followers()
    {
        //通过$user->followers()获取粉丝列表
        return $this->belongsToMany(User::Class, 'followers', 'user_id', 'follower_id');
    }

    //关注列表
    public function followings()
    {
        //通过$user->followings()获取自己关注的用户列表
        return $this->belongsToMany(User::Class, 'followers', 'follower_id', 'user_id');
    }

    public function follow($user_ids)
    {
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->sync($user_ids, false);
    }

    public function unfollow($user_ids)
    {
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }

    public function isFollowing($user_id)
    {
        return $this->followings->contains($user_id);
    }
}
