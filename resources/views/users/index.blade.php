@extends('layouts.default')
@section('title', '所有用户')

@section('content')
<div class="col-md-offset-2 col-md-8">
  <h1>所有用户</h1>
  <ul class="users">
    @foreach ($users as $user)
      @include('users._user')
    @endforeach
  </ul>
  <!--渲染分页，注意两侧的新语法，作用是生成的html不会被转义-->
  {!! $users->render() !!}
</div>  
@stop