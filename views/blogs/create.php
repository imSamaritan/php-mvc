<form method="POST" action="/blogs/create">
  <div class="field">
    <label for="author">Author</label>
    <input type="text" id="author" name="author"/>
    <?php if (isset($error["author"])): ?>
      <small><?php echo $error["author"] ?></small>
    <?php endif ?>
  </div>
  <div class="field">
    <label for="title">Title</label>
    <input type="text" id="title" name="title"/>
    <?php if (isset($error["title"])): ?>
      <small><?php echo $error["title"] ?></small>
    <?php endif ?>
  </div>
  <div class="field">
    <label for="post">Post</label>
    <textarea type="text" id="post" name="body"></textarea>
    <?php if (isset($error["body"])): ?>
      <small><?php echo $error["body"] ?></small>
    <?php endif ?>
  </div>  
  <div class="field">
    <button type="submit" name="submit">Post</button>
  </div>
</form>