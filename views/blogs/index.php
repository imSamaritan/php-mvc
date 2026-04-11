<article>
  <header>
    <h1>Blogs</h1>
    <br/>
    <a href="/blogs/create">Create post</a>
  </header>

  <div class="container">
    <?php foreach ($blogs as $blog): ?>
      <?php require dirname(__DIR__) . "/components/blog.php"; ?>
    <?php endforeach; ?>
  </div>
</article>
