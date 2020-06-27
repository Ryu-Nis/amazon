<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>データ操作</title>
  </head>
  <body>
    <h1>データ操作</h1>

    <?php
        $keyword = $_POST['keyword'];
        $max_price = $_POST['max'];
        $min_price = $_POST['min'];
        $page = $_POST['page']; 
          if(isset($_POST['update'])) {
            include '../shell/get.php';
          } else if(isset($_POST['truncate'])) {
              include '../shell/Truncate_DB.php';
          }
    ?>

    <form action="test.php" method="post">
    キーワード：<input type="text" name="keyword" value= 'Bianchi'/><br>
    最大値段　：<input type="text" name="max" value= 100000><br>
    最小値段　：<input type="text" name="min"value= 1000><br>
    ページ数　：<input type="text" name="page"value=2 ><br>
    <br>
    <input type="submit" name="update" value="データ更新" />
    <input type="submit" name="truncate" value="データ削除" />        
    </form>

  </body>
</html>