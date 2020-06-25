<?php
//DB接続
include 'DB_link.php';
//エラーファンクションの読み込み
require_once('../shell/log.php');
//時刻を日本時間に設定
date_default_timezone_set('Asia/Tokyo');
$date = date('Y-m-d H:i:s');


// ・通信が発生する部分（今回でいえば、楽天のデータを取ってくる所） 
// ・DB周りの部分（今回だとselect、insert、update部分）


// <!-- 楽天に取りに行く -->
// <!-- こけた時のためにどこまで読み込んだか把握して、どこから読むか制御 -->

for($count = 1; $count<=$page; $count++){
    $rakuten_relust = getRakutenResult($keyword, $max_price, $min_price, $count); // キーワード、最大価格、最低価格、を指定
    foreach ($rakuten_relust as $item) :
        // <!-- 返ってきた値をインサート -->
        $name = $item['name'];
        $price = $item['price'];
        $code = $item['code'];
    
        // <!-- 既に入っているかチェック -->
        $check_result = mysqli_query($link,"SELECT 1 FROM products where code = '$code'");
        //DBエラー処理
        $errNO = mysqli_errno($link);
        $errMSG = mysqli_error($link);
        if($errMSG !== ''){
            error($errNO.":".$errMSG."重複確認時のエラー");
        }
        $rows = mysqli_num_rows($check_result);
    
        // <!-- 無かったら新規、既にあったら日付と値段を更新 -->
        if($rows == 0){
            $insert_sql = "INSERT INTO products (code,date,name,price,proportion) 
            VALUES ('$code','$date', '$name', '$price',0)";
            $res = mysqli_query( $link, $insert_sql);
            //DBエラー処理
            $errNO = mysqli_errno($link);
            $errMSG = mysqli_error($link);
            if($errMSG !== ''){
                error($errNO.":".$errMSG."インサート時のエラー");
            }
        }else{
            // 履歴テーブルに引っ越し（後で履歴表示するかもしれないからとりあえず全部インサート）
            $mig_sql = "INSERT INTO historical_products(code,date,name,price)
            SELECT products.code,products.date,products.name,products.price from products
            WHERE products.code = '$code'";
            $res = mysqli_query( $link, $mig_sql);
            //DBエラー処理
            $errNO = mysqli_errno($link);
            $errMSG = mysqli_error($link);
            if($errMSG !== ''){
                error($errNO.":".$errMSG."履歴テーブルへインサート時のエラー");
            }
            // 日付と値段と前回比を更新
            $update_sql = "UPDATE products SET date='$date', price='$price' WHERE code='$code'";
            $pro_sql = "UPDATE products,historical_products
            SET products.proportion = ((products.price / historical_products.price)-1)
            WHERE historical_products.code = products.code";

            $res = mysqli_query( $link, $update_sql);
            $res = mysqli_query( $link, $pro_sql);
            //DBエラー処理
            $errNO = mysqli_errno($link);
            $errMSG = mysqli_error($link);
            if($errMSG !== ''){
                error($errNO.":".$errMSG."Update時のエラー");
            }
        }
    endforeach;
}

function getRakutenResult($keyword, $max_price, $min_price, $page) {
    // try{

        // ベースとなるリクエストURL
        $baseurl = 'https://app.rakuten.co.jp/services/api/IchibaItem/Search/20140222';
        // リクエストのパラメータ作成
        $params = array();
        $params['applicationId'] = '1081445992483123286'; // アプリID
        $params['keyword'] = urlencode_rfc3986($keyword); // 任意のキーワード。※文字コードは UTF-8
        $params['sort'] = urlencode_rfc3986('+updateTimestamp'); // ソートの方法。※文字コードは UTF-8
        $params['maxPrice'] = $max_price; // 最大価格
        $params['minPrice'] = $min_price; // 最低価格
        $params['page'] = $page; // ページ番号
        
        $canonical_string='';
        
        foreach($params as $k => $v) {
            $canonical_string .= '&' . $k . '=' . $v;
        }
        // 先頭の'&'を除去
        $canonical_string = substr($canonical_string, 1);
        
        // リクエストURL を作成
        $url = $baseurl . '?' . $canonical_string;
        
        // XMLをオブジェクトに代入
        $rakuten_json=json_decode(@file_get_contents($url, true));
        
        $items = array();
        foreach($rakuten_json->Items as $item) {
            $items[] = array(
                            'name' => (string)$item->Item->itemName,
                            'url' => (string)$item->Item->itemUrl,
                            'img' => isset($item->Item->mediumImageUrls[0]->imageUrl) ? (string)$item->Item->mediumImageUrls[0]->imageUrl : '',
                            'price' => (int)$item->Item->itemPrice,
                            'shop' => (string)$item->Item->shopName,
                            'code' => (string)$item->Item->itemCode,
                            );
        }
        return $items;
    // }catch (Throwable $t){
    //     // Executed only in PHP 7, will not match in PHP 5
    //     error("楽天へのデータ取得時に問題が発生");
    //     echo "aaa";
    // }
    // catch (Exception $e){
    //    // Executed only in PHP 5, will not be reached in PHP 7
    //    error("楽天へのデータ取得時に問題が発生");
    //    echo "aaa";
    // }
}

// RFC3986 形式で URL エンコードする関数

function urlencode_rfc3986($str) {
    return str_replace('%7E', '~', rawurlencode($str));
}
?>