<div class="stats">
  <a href="{{ route('users.followings', $user->id) }}">
    <strong id="following" class="stat">
      {{ count($user->followings) }}
    </strong>
    关注
  </a>
  <a href="{{ route('users.followers', $user->id) }}">
    <strong id="followers" class="stat">
      {{ count($user->followers) }}
    </strong>
    粉丝
  </a>
  <a href="{{ route('users.show', $user->id) }}">
    <strong id="statuses" class="stat">
    <!--用count方法消耗数据库资源不是最佳实践，最好方法是在数据库添加一个模型计数器字段，在模型创建、删除时更新此字段-->
      {{ $user->statuses()->count() }}
    </strong>
    微博
  </a>
</div>