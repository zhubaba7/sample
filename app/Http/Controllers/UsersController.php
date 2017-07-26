<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\User;
use Auth;
use Mail;

class UsersController extends Controller
{
    //调用中间件进行过滤，第一个参数是中间件名称，第二个是过滤动作
    //only方法用来指定动作。验证不通过，重定向到auth/login，例子
    //登陆页是/login，所以修改了中间件Authenticate.php的handle()
    //方法
    public function __construct()
    {
        $this->middleware('auth', [
            'only' => ['edit', 'update', 'destroy', 'followings', 'followers']
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

    //用户显示页
    public function show($id)
    {
        $user = User::findOrFail($id);
        $statuses = $user->statuses()
                            ->orderBy('created_at', 'desc')
                            ->paginate(30);
        return view('users.show', compact('user', 'statuses'));
    }

    //用户注册
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

        $this->sendActivationEmailTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');

        //Auth::login($user);
        //session()->flash('success', '欢迎，您将在这里开启一段新的里程~');
        //return redirect()->route('users.show', [$user]);
    }

    //用户邮箱确认
    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜您，激活成功！');
        return redirect()->route('users.show', [$user]);
    }

    //发送邮箱确认邮件
    public function sendActivationEmailTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'aufree@126.com';
        $name = 'Aufree';
        $to = $user->email;
        $subject = '感谢注册 Sample 应用！请确认你的邮箱。';

        Mail::send($view, $data, function($message) use ($from, $name, $to, $subject) {
            $message->from($from, $name)->to($to)->subject($subject);
        });
    }

    //编辑用户
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    //更新用户
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

    public function followings($id)
    {
        $user = User::findOrFail($id);
        $users = $user->followings()->paginate(30);
        $title = '关注的人';
        return view('users.show_follow', compact('users', 'title'));
    }

    public function followers($id)
    {
        $user = User::findOrFail($id);
        $users = $user->followers()->paginate(20);
        $title = '粉丝';
        return view('users.show_follow', compact('users', 'title'));
    }
}
