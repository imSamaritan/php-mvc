<h2>Listing post #<?php echo $blog->id; ?></h2>

<div class="container">
  <div class="card">
    <header class="card-header">
      <h6><?php echo $blog->title; ?>
        <p>By: <small><?php echo $blog->author; ?></small>
    </header>
    <div class="card-body">
      <p><?php echo $blog->body; ?></p>
    </div>
  </div>
</div>

<style>
body {
  background-color: #333;
  color: ghostwhite;
  padding: 0;
  margin: 0;
}

.container {
  width: 95%;
  margin: 0 auto;
  margin-top: 100px !important;
}

h2 {
  position: fixed;
  top: 0;
  display: block;
  width: 100%;
  padding: 15px 0;
  margin: 0;
  text-align: center;
  background-color: #111;
}

.card {
  width: fit-content;
  padding: 5px;
  border-radius: 15px;
  background-color: #222;
  margin: 15px 0;
  box-shadow: 1px 1px 3px rgba(0,0,0,0.8);
}
</style>
