<form method="POST" action="/blogs/create">
  <div class="field">
    <label for="author">Author</label>
    <input type="text" id="author" name="author"/>
  </div>
  <div class="field">
    <label for="title">Title</label>
    <input type="text" id="title" name="title"/>
  </div>
  <div class="field">
    <label for="post">Post</label>
    <textarea type="text" id="post" name="body"></textarea>
  </div>  
  <div class="field">
    <button type="submit" name="submit">Post</button>
  </div>
</form>