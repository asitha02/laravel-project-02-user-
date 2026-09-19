<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Edit Post</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
  <div class="wrap">
    <header class="app-header">
      <h1>Edit Post</h1>
      <a class="btn ghost" href="/">Back</a>
    </header>

    @if ($errors->any())
      <div class="alert">
        @foreach ($errors->all() as $error)
          <p>{{ $error }}</p>
        @endforeach
      </div>
    @endif

    <section class="card">
      <form class="form" action="/edit-post/{{ $post->id }}" method="POST">
        @csrf
        @method('PUT')
        <label>
          Title
          <input type="text" name="title" value="{{ $post->title }}">
        </label>
        <label>
          Body
          <textarea name="body">{{ $post->body }}</textarea>
        </label>
        <div class="actions">
          <button type="submit">Save Changes</button>
          <a class="btn ghost" href="/">Cancel</a>
        </div>
      </form>
    </section>
  </div>
</body>
</html>
