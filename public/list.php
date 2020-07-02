<?php
  // MySQLへ接続する
  include '../shell/DB_link.php';

  //csvファイル操作のため
  include '../shell/output_csv.php';
  require_once('../shell/log.php');
  date_default_timezone_set('Asia/Tokyo');
  $date = date('Y-m-d H:i:s');

  // クエリを送信する
  $sql = "SELECT id,name,price,proportion from products WHERE name LIKE '%".$_POST['name']."%' order by id";

  $res = mysqli_query($link, $sql);


  //結果セットの行数を取得する
  $rows = mysqli_num_rows($res);

  //表示するデータを作成
  if($rows){
    while($row = mysqli_fetch_assoc($res)) {
      $tempHtml .= "<tr>";
      $tempHtml .= "<td>".$row["id"]."</td><td>".$row["name"]."</td>";
      $price = number_format($row["price"]);//桁区切り表示
      $tempHtml .= "<td>".$price."円</td><td>".$row["proportion"]."</td>";
      $tempHtml .= "<td><a href=\"details.php?id=".$row['id']."\" target=\"_self\">詳細</a></td>";
      $tempHtml .= "</tr>\r\n";
    }
    // $msg = $rows."件のデータがあります。";
  }
  //結果保持用メモリを開放する

  mysqli_free_result($res);

  //CSV入出力ボタンが押された場合
  if(isset($_POST['output_csv'])) {
    $res = mysqli_query($link, $sql);
    $filepath = '../shell/csv/list.csv';
    // output_csv(['id','name','price','proportion'],$filepath);
    while($row = mysqli_fetch_assoc($res)){
        // $row = mb_convert_encoding($row,"SJIS-win","UTF-8");
        output_csv($row,$filepath);
    }
    mysqli_free_result($res);
    // HTTPヘッダを設定
    // header('Content-Type: text/html;charset=SJIS-win');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=list.csv'); 
    header('Content-Type: charset=SJIS-win');
    header('Content-Length: '.filesize($filepath));
    // ファイル出力
    readfile($filepath);
    //ファイル削除
    unlink($filepath);
  }

  // File Upload機能
  if (is_uploaded_file($_FILES["upfile"]["tmp_name"])) {
    if (move_uploaded_file($_FILES["upfile"]["tmp_name"],"../shell/csv/input.csv")) {
      $upload ="../shell/csv/input.csv";
      chmod($upload,0777);
      if ( $upload ) {
          $fp = fopen($upload,"r");
          while( ! feof($fp) ) {
              $csv = fgets($fp);
              $csv = trim($csv,'"');
              $csv = mb_convert_encoding($csv,"UTF-8", "SJIS-win");
              $csv = str_replace('"','', $csv);
              $csv_array = explode(",",$csv);
          
              // //最初の行だけ無視したいが、、
              $id = $csv_array[0];
              $name= $csv_array[1];
              $price= $csv_array[2];
              $proprtion= $csv_array[3];

              $sql = "INSERT INTO products(id,code,date,name,price,proportion)
                            VALUES('$id','$id','$date','$name','$price','$proprtion')";

              $res = mysqli_query($link, $sql);

              //DBエラー処理
              $errNO = mysqli_errno($link);
              $errMSG = mysqli_error($link);
              if($errMSG !== '' && $errMSG !== $errMSG_NEW){
                  error($errNO.":".$errMSG."-->インサート時のエラー");
                  $errNO_NEW = mysqli_errno($link);
                  $errMSG_NEW = mysqli_error($link);
              }               
          }
          echo "ファイルをアップロードしました。";
          
          if (file_exists($upload)) {
              unlink("$upload");
          }
      }
    } else {
        echo"ファイルをアップロードできません。";
    }
  } else {
    echo"ファイルが選択されていません。";
  }


  
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="SJIS-win">
    <title>データ一覧</title>
  </head>
  <body>
  <h1>一覧</h1>

  <form action="list.php" method="post">
    <input type="submit" name="output_csv" value="csv出力" />
    <!-- <input type="submit" name="input_csv" value="csv入力" /> -->
  </form><br>
  <form action="list.php" enctype="multipart/form-data" method="post">
  <input type="submit" value="csv入力" /> <input name="upfile" type="file" />
  </form>
  <br>
  <form action="list.php" method="post">
  商品名検索:<input type="text" name="name" value="<?php echo $_POST['name']?>"> <input type="submit" value='検索'>
  </form>
    <table border =1>
      <tr bgcolor="##ccffcc"><br>
      <td width = "50">id</td><td width = "200" >NAME</td><td width='100'>price</td>
      <td width='100'>前日比</td><td width='50'></td>
      <?= $tempHtml ?></tr>
    </table>
  </body>
</html>


<!-- １．CSV出力ボタンを付けて、出力
２．CSV取り込み
→一応機能的には形になったが、課題が２点
　Ⅰ　エンコード（出力したファイルを入力すると文字化けする）
　Ⅱ　一行目（カラム名を飛ばす処理）
　　→暫定的に「一行目を削除してからインポート」「そもそもカラムを出力しない」での対応は可能 

３．検索機能
（名前、値段、前日比～％以下、以上）
→名前のみ検索機能追加（あとはSQLを調整するだけ）

４．一覧ページをキャッシュで出力する
→クエリキャッシュ？それとも拡張モジュールのAPC？

５．ログインページ（会員はあらかじめ作っておいても良い）
６．パスワードは平文では入れない　→　ハッシュ化する
７．ページネーション
８．カート機能
９．データ更新の際に、非同期処理（メッセージ出力） -->