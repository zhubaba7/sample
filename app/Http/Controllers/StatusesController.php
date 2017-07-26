<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Status;
use Auth;

class StatusesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', [
            'only' => ['store', 'destroy']
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'content' => 'required|max:140'
        ]);

        //Auth::user()方法获取当前用户，当前用户的statuses()方法返回多条动态，
        //这个不确定，或许是返回用户动态的模型。这个create方法像是否从Models基类
        //继承来的方法，不确定是否用于创建数据集
        Auth::user()->statuses()->create([
            'content' => $request->content
        ]);

        return redirect()->back();
    }

    public function destroy($id)
    {
        $status = Status::findOrFail($id);
        //授权检查，不通过抛出403
        $this->authorize('destroy', $status);
        $status->delete();
        session()->flash('success', '动态已被成功删除！');
        return redirect()->back();
    }
}
