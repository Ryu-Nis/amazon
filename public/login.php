<?php
session_start();

include '../shell/DB_link.php';

$id = $_POST['id'];
$pw = $_POST['pass'];



$sql = "SELECT id,password,name FROM users WHERE id=".$id;
$res = mysqli_query($link, $sql);


// $_SESSION['id']=1;
// $_SESSION['pass']=0;
// $_SESSION['name']=0;

// include '../shell/output_csv.php';
// require_once('../shell/log.php');

// $errMSG = mysqli_error($link);
// error($errMSG);
  
foreach($res as $row){
    if($id==$row[0] && $pw==$row[1]){
    
    $_SESSION['id']=$row[0];
    $_SESSION['pass']=$row[1];
    $_SESSION['name']=$row[2];

    $_SESSION['OK']="OK";
    // header("Location:loginok.php");
    echo "OK";
    exit();
    
    }else{
        echo "NO";
        var_dump($row[0]);
    }
}
?>



<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>Login</title>
  </head>
  <body>
  <h1>Login Page</h1>
  <form action="login.php" method="post">
  ID: <input type="text" name="id" value="<?php echo $_POST['id']?>">
  <!-- </form> -->
  <br><br>
  <!-- <form action="login.php" method="post"> -->
  PASS: <input type="password" name="pass" value="<?php echo $_POST['pass']?>"> <input type="submit" value='ENTER'>
  </form>
  </body>
</html>