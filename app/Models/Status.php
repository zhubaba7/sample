<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = ['content'];

    //每条动态都属于一个用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
