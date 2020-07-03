<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>Login OK</title>
  </head>
  <body>
  <h1>Login OK</h1>
  <form action="login.php" method="post">
  <input type="submit" value='BACK'>
  </form>
  </body>
</html>

<?php
session_start();
if (!isset($_SESSION['id'])) {
  header("Location: login.php");
  exit;
}
?>