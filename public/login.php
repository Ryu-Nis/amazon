<?php

session_start();
$error_message = "";


if($_SESSION['id']){
  printf($_SESSION['id']."さん、こんにちは<br>");
}else{
  // ログインボタンが押された場合
  if (isset($_POST['login'])) {
    // ユーザIDとパスワードが入力されていたら認証する
    if (!empty($_POST['id']) && !empty($_POST['password'])) {
      // mysqlへの接続
      include '../shell/DB_link.php';

      // 入力値のサニタイズ
      $username = mysqli_real_escape_string($link,$_POST['id']);

      // クエリの実行
      $sql = "SELECT * FROM users WHERE name = '" . $username . "'";
      
      $res = mysqli_query($link, $sql);
      // $errorMSG= mysqli_error($link);    
      if (!$res) {
        print('クエリーが失敗しました。' . $errorMSG);
        exit();
      }

      while ($row = mysqli_fetch_array($res)) {
        // パスワード(暗号化済み）の取り出し
        $db_hashed_pwd = $row[2];
      }
      // データベースの切断
      mysqli_close($link);

      // ３．画面から入力されたパスワードとデータベースから取得したパスワードのハッシュを比較します。
      //if ($_POST['password'] == $pw) {
      if (password_verify($_POST['password'], $db_hashed_pwd)) {
        // ４．認証成功なら、セッションIDを新規に発行する
        session_regenerate_id(true);
        $_SESSION['id'] = $_POST['id'];
        // echo "OK";
        header('Location: loginok.php');
        exit;
      } 
      else {
        // 認証失敗
        $errorMessage = "ユーザIDあるいはパスワードに誤りがあります。";
        echo $errorMessage;
      } 
    } else {
      // 未入力なら何もしない
      echo "ID,パスワードを入力してください";
      
      // printf("
      // <script src='//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js'></script>
      // <script>
      // $(function(){
      //   $('#resultLoadtest').load('temp.php #idColorGreen');
      // });
      // </script>");
    } 
  }
}

if(isset($_POST['clear'])){
// セッション変数のクリア
$_SESSION = array();
// セッションクリア
@session_destroy();
header('Location: login.php');
}
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="SJIS-win">
    <title>ログイン</title>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

  </head>
  <body>  
    <h1>ログインページ</h1>
    <form action="" method="POST">
      <p>ログインID：<input type="text" name="id" id="inputID"></p>
      <p>パスワード：<input type="password" name="password" id="inputPW"></p>
      <input type="submit" name="login" value="ログイン" id="sub" >
    </form>

    <form action="login.php" method="POST">
      <p><input type="submit" name="clear" value="ログアウト"></p>
    </form>    
    <p><a href="adduser.php">ユーザー登録</p>

<!-- 非同期処理
IDとPWに一文字以上入力しないとログインボタンが非活性のまま -->
<script>
$(function(){
  if (($("#inputID").val().length == 0) && ($("#inputPW").val().length == 0)) {
    $("#sub").prop("disabled", true);
  } 
  $("#inputID").on("keydown keyup keypress change", function() {
    if ($("#inputID").val().length < 1 || $("#inputPW").val().length < 1) {
      $("#sub").prop("disabled", true);
    } else {
       $("#sub").prop("disabled", false);
    }
  });
  $("#inputPW").on("keydown keyup keypress change", function() {
    if ($("#inputID").val().length < 1 || $("#inputPW").val().length < 1) {
      $("#sub").prop("disabled", true);
    } else {
       $("#sub").prop("disabled", false);
    }
  });
});
</script>

</body>
</html>