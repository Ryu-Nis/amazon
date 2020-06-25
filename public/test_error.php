<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>TEST Page</title>
  </head>
  <body>
    <h1>ERROR TEST</h1>

    <?php
        require_once('../shell/log.php');
        //URLを指定する
        $url = 'https://www.sejuku.net/blog/blog/';
        
        try{
            file_get_contents($url);
        }catch (Throwable $t){
            // Executed only in PHP 7, will not match in PHP 5
            error("楽天へのデータ取得時に問題が発生");
            echo "aaa";
        }
        catch (Exception $e){
           // Executed only in PHP 5, will not be reached in PHP 7
           error("楽天へのデータ取得時に問題が発生");
           echo "aaa";
        }

    
        // //URLの存在有無の確認
        // if (file_get_contents($url) != FALSE){
        
        //     //URLを表示
        //     echo file_get_contents($url);
        //     echo "DONE";
        
        // }else{
        //     //エラーログを出力
        //     // error_log($url.'の読み込みに失敗しました。', 3, "../shell/logs/error.log");
        //     error("テスト");
        //     currentpage("2回目");
        //     echo "Error";
        // }
    ?>

    <form action="test.php" method="post">
    <input type="submit" name="update" value="データ更新" />
    <input type="submit" name="truncate" value="データ削除" />        
    </form>

  </body>
</html>