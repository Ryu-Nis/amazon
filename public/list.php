<?php
  // MySQLへ接続する
  include '../shell/DB_link.php';
  // クエリを送信する
  $sql = "SELECT id,name,price,proportion from products order by id";
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
    // $msg = $rows."件のデータがあります。";
  }

  //結果保持用メモリを開放する
  mysqli_free_result($res);
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>データ操作</title>
  </head>
  <body>
  <h1>一覧</h1>
    <table border =1>
      <tr bgcolor="##ccffcc"><td width = "50">id</td><td width = "200" >NAME</td>
      <td width='100'>price</td><td width='100'>前日比</td><td width='50'></td>
      <?= $tempHtml ?></tr>
    </table>
  </body>
</html>