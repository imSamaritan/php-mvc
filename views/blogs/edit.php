<a href="/blogs/show/<?php echo $id ?>">&lt;&lt;Go back</a>
<form method="POST" action="/blogs/edit/<?php echo $id ?>">
  <?php require dirname(__DIR__) . "/components/form.php"; ?>
</form>