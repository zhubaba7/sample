<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\User;
use Auth;

class UsersController extends Controller
{
    //调用中间件进行过滤，第一个参数是中间件名称，第二个是过滤动作
    //only方法用来指定动作。验证不通过，重定向到auth/login，例子
    //登陆页是/login，所以修改了中间件Authenticate.php的handle()
    //方法
    public function __construct()
    {
        $this->middleware('auth', [
            'only' => ['edit', 'update', 'destroy']
        ]);

        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    //用户列表页面
    public function index()
    {
        $users = User::paginate(30);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('users.show', compact('user'));
    }

    public function store(Request $request){
        $this->validate($request, [
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:8'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        Auth::login($user);
        session()->flash('success', '欢迎，您将在这里开启一段新的里程~');

        return redirect()->route('users.show', [$user]);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'confirmed|min:8'
        ]);

        $user = User::findOrFail($id);
        $this->authorize('update', $user);

        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);

        session()->flash('success', '个人资料更新成功！');

        return redirect()->route('users.show', $id);
    }

    //删除用户
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        //绑定授权，根据授权策略，只有管理员且不是删除自己时才能操作
        $this->authorize('destroy', $user);

        $user->delete();
        session()->flash('success', '成功删除用户！');

        //有个疑问，下面的back是否等同于redirect()->back()？
        return back();
    }
}
