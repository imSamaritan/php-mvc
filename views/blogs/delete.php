<h1>Delete blog #<?php echo $id ?></h1>
<br/><br/>
<div>
  <p>Are you sure?</p>
  <form method="POST" action="/blogs/delete/<?php echo $id ?>">
    <button type="submit" name="delete">Yes</button> | <a href="/blogs/show/<?php echo $id ?>">No</a>
  </form>
</div>