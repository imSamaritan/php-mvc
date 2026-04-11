<div class="card">
  <div class="card-title">
    <h4><?php echo $blog->title ?></h4>
  </div>
  <div class="card-body">
    <div class="text">
      <p><?php echo $blog->body ?></p>
    </div>
  </div>
  <div class="card-footer">
    <?php $path = rtrim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), "/"); ?>
    <?php if ($path === "/blogs" || $path === "/blogs/index"): ?>
      <a href="/blogs/show/<?php echo $blog->id ?>">Read more...</a>
    <?php else: ?>
      <a href="/blogs">Go back</a>
      <a href="">Edit</a>
      <a href="">Delete</a>
    <?php endif ?>
  </div>
</div>