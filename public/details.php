<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>データ一覧</title>
  </head>
  <body>
    <h1>詳細</h1>

    <?php
    include '../shell/DB_link.php';
    $id = $_GET['id'];
    $sql = "SELECT * from products WHERE id=".$id;

    $res = mysqli_query($link, $sql);
    $rows = mysqli_num_rows($res);

    if($rows){  
        while($row = mysqli_fetch_array($res)) {
            // printf("<p>ID:".$row[0])."</p>" ;
            printf("<p>商品コード:  ".$row[1]."</p>") ;
            printf("<p>更新日:  ".$row[2]."</p>") ;
            printf("<p>名前:  ".$row[3]."</p>") ;
            printf("<p>値段:  ".$row[4]."円</p>") ;
            printf("<p>前回比:  ".$row[5]."％</p>") ;     
        }
    }
    ?>
    
    <h2>値段履歴</h2>
    
    <?php
    $sql = "SELECT date,price from historical_products WHERE code = (SELECT code from products WHERE id=".$id.")order by date desc";

    $res = mysqli_query($link, $sql);
    $rows = mysqli_num_rows($res);


    if($rows){  
        while($row = mysqli_fetch_array($res)) {
          // 　$tempHtml .= "";
            $tempHtml .= "<tr><td>".$row[0]."</td>";
            $tempHtml .= "<td>".$row[1]."円</td>";
            $tempHtml .= "</tr>\r\n";  
        }
    }

    ?>

    <table border =1>
      <tr bgcolor="##ccffcc"><td width = "200" >日時</td><td width='200'>値段</td>
      <?= $tempHtml ?></tr>
    </table>

  </body>
</html>
