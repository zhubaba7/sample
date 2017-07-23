<li>
  <!--<img src="{{ $user->gravatar() }}" alt="{{ $user->name }}" class="gravatar"/>-->
  <a href="{{ route('users.show', $user->id )}}" class="username">{{ $user->name }}</a>
  <!--blade的can指令做授权判断-->
  @can('destroy', $user)
    <form action="{{ route('users.destroy', $user->id) }}" method="POST">
      {{ csrf_field() }}
      {{ method_field('DELETE') }}
      <button type="submit" class="btn btn-sm btn-danger delete-btn">删除</button>
    </form>
  @endcan
</li>