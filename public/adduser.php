<html>
  <meta charset="UTF-8">
  <head>
    <title>ログインユーザ追加用の入力画面</title>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
  </head>
  <body>
  <h1>ログインユーザ追加用の入力画面</h1>
    <form action="adduser.php" method="post">
      <table>
        <tr>
          <td>NAME</td>
          <td><input type="text" name="name" id="name"></td>
        </tr>
        <tr>
          <td>PASSWORD</td>
          <td><input type="password" name="password" id="pass"></td>
        </tr>
      </table>
      <p><div id="attention"></div></p>
      <p><div id="attention1"></div></p>
      <p><div id="attention2"></div></p>
      <p><input type="submit" value="追加"></p>
    </form>

<!-- 非同期処理：パスワードの長さチェック-->
<script>
$(function(){
  if ($("#pass").val().length == 0) {
    var MSG = document.getElementById("attention");
    MSG.innerHTML = "IDとパスワードを入力してください";
  } 

  $("#name").on("keydown keyup keypress change", function() { 
    if ($("#name").val().length < 1 || $("#pass").val().length < 1) {
       MSG.innerHTML = "IDとパスワードを入力してください";
    } else {
      if($("#pass").val().length < 8){
        MSG.innerHTML = "8文字以上のパスワードを推奨します";
      }else{
        MSG.innerHTML = "OK";
      }
    }
  });

  $("#pass").on("keydown keyup keypress change", function() { 
    if ($("#name").val().length < 1 || $("#pass").val().length < 1) {
      MSG.innerHTML = "IDとパスワードを入力してください";
    } else {
      if($("#pass").val().length < 8){
        MSG.innerHTML = "8文字以上のパスワードを推奨します";
      }else{
        MSG.innerHTML = "OK";
      }
    }
  });
});

</script>


<?php
include '../shell/DB_link.php';
mysqli_set_charset('utf8');

$name = filter_input(INPUT_POST, 'name');
//入力文字をエスケープしてエスケープされればエラーにする
$name_esc = addslashes($name);
if(empty($name_esc)){
    print('新しく登録する名前を入力してください<br></a>');
    $mysqli->close();
    exit();
}
if($name != $name_esc){
    print('使用できない文字（\',\\,NULL,"）が含まれています。<br>');
    mysqli_close($link);
    exit();
}
$password = filter_input(INPUT_POST, 'password');
if(empty($password)){
    print('PASSWORDが入力されていません。<br>');
    mysqli_close($link);
    exit();
}
$hashpass = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (name, password) VALUES ('$name_esc','$hashpass')";
$result_flag = mysqli_query($link, $sql);

if (!$result_flag) {
    print('クエリーが失敗しました。' . $mysqli->error.'<br>');
    mysqli_close($link);
    exit();
}

print('<p>ユーザー' . $name_esc . 'を登録しました。</p>');
$close_flag = mysqli_close($link);

printf("<br><a href='login.php'>ログイン画面へ</a>")
?>

</body>
</html>