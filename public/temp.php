<?php
//データベースへ接続
//   $link = include '../shell/DB_link.php';
  include '../shell/DB_link.php';
  

 $sql = "SELECT id,name,price,proportion from products WHERE name LIKE '%".$_POST['name']."%' order by id";

  $res = mysqli_query($link, $sql);

      //結果セットの行数を取得する
  $rows = mysqli_num_rows($res);

  //表示するデータを作成
  if($rows){
    while($row = mysqli_fetch_assoc($res)) {
      $tempHtml .= "<tr>";
      $tempHtml .= "<td>".$row["id"]."</td><td>".$row["name"]."</td>";
      $tempHtml .= "<td>".$row["price"]."</td><td>".$row["proportion"]."</td>";
      $tempHtml .= "<td><a href=\"details.php?id=".$row['id']."\" target=\"_self\">詳細</a></td>";
      $tempHtml .= "</tr>\r\n";
    }
}
?>

<html>
    <head>
        <title>Temp Page</title>
    </head>
    <body>
        <form action="temp.php" method="post"><br>
            <?php
                if(preg_match("/[^0-9]/", $_POST['id'])){
                   echo " IDは数字で入力してください！";
                }
            ?>
            <br>
            商品名検索:<input type="text" name="name" value="<?php echo $_POST['name']?>"><br>
            <input type="submit">
            
            
            <table border =1>
            <tr bgcolor="##ccffcc">
            <td width = "50">id</td><td width = "200" >NAME</td><td width='100'>price</td>
            <td width='100'>前日比</td><td width='50'></td>
            <?= $tempHtml ?></tr>
            </table>
            
        </form>
    </body>
</html>