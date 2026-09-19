<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Blog</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
  <div class="wrap">
    @auth
    <header class="app-header">
      <p class="status">Congrats you are logged in.</p>
      <div class="actions">
        <span class="user">{{ auth()->user()->name }}</span>
        <form action="/logout" method="POST">
          @csrf
          <button type="submit" class="ghost">Log out</button>
        </form>
      </div>
    </header>
    @endauth

    @if ($errors->any())
      <div class="alert">
        @foreach ($errors->all() as $error)
          <p>{{ $error }}</p>
        @endforeach
      </div>
    @endif

    @auth
    <section class="card">
      <h2>Create a New Post</h2>
      <form class="form" action="/create-post" method="POST">
        @csrf
        <label>
          Title
          <input type="text" name="title" placeholder="post title">
        </label>
        <label>
          Body
          <textarea name="body" placeholder="body content..."></textarea>
        </label>
        <div class="actions">
          <button type="submit">Save Post</button>
        </div>
      </form>
    </section>

    <section class="card">
      <h2>All Posts</h2>
      @forelse($posts as $post)
      <article class="post">
        <h3>{{ $post['title'] }} <span class="meta">by {{ $post->user->name }}</span></h3>
        <p class="post-body">{{ $post['body'] }}</p>
        <div class="actions">
          <a class="btn ghost" href="/edit-post/{{ $post->id }}">Edit</a>
          <form action="/delete-post/{{ $post->id }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="danger">Delete</button>
          </form>
        </div>
      </article>
      @empty
      <p class="empty">No posts yet.</p>
      @endforelse
    </section>

    @else
    <div class="grid-2">
      <section class="card">
        <h2>Register</h2>
        <form class="form" action="/register" method="POST">
          @csrf
          <label>
            Name
            <input name="name" type="text" placeholder="name" value="{{ old('name') }}">
          </label>
          <label>
            Email
            <input name="email" type="text" placeholder="email" value="{{ old('email') }}">
          </label>
          <label>
            Password
            <input name="password" type="password" placeholder="password">
          </label>
          <div class="actions">
            <button type="submit">Register</button>
          </div>
        </form>
      </section>
      <section class="card">
        <h2>Login</h2>
        <form class="form" action="/login" method="POST">
          @csrf
          <label>
            Name
            <input name="loginname" type="text" placeholder="name" value="{{ old('loginname') }}">
          </label>
          <label>
            Password
            <input name="loginpassword" type="password" placeholder="password">
          </label>
          <div class="actions">
            <button type="submit">Log in</button>
          </div>
        </form>
      </section>
    </div>
    @endauth
  </div>
</body>
</html>
