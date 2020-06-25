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

//開始ページをファイルから読み込み(int型へ変換）
$start_page = file_get_contents('../shell/logs/currentpage.txt');
$start_page = intval($start_page);

for($count = $start_page; $count<=2; $count++){
    $rakuten_relust = getRakutenResult("Bianchi",100000,100, $count); // キーワード、最大価格、最低価格、を指定
    //HTTPレスポンスコードでエラー処理
    $res_code = http_response_code();
    switch($res_code){
        case 200:
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
                if($errMSG !== '' && $errMSG !== $errMSG_NEW){
                    error($errNO.":".$errMSG."-->重複チェック時のエラー");
                    $errNO_NEW = mysqli_errno($link);
                    $errMSG_NEW = mysqli_error($link);
                }
                $rows = mysqli_num_rows($check_result);
            
                // <!-- 無かったら新規、既にあったら日付と値段を更新 -->
                if($rows == 0){
                    $insert_sql = "INSERT INTO products(code,date,name,price,proportion) 
                    VALUES ('$code','$date', '$name', '$price',0)";
                    $res = mysqli_query( $link, $insert_sql);
                    //DBエラー処理
                    $errNO = mysqli_errno($link);
                    $errMSG = mysqli_error($link);
                    if($errMSG !== '' && $errMSG !== $errMSG_NEW){
                        error($errNO.":".$errMSG."-->インサート時のエラー");
                        $errNO_NEW = mysqli_errno($link);
                        $errMSG_NEW = mysqli_error($link);
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
                    if($errMSG !== '' && $errMSG !== $errMSG_NEW){
                        error($errNO.":".$errMSG."-->履歴テーブルインサートのエラー");
                        $errNO_NEW = mysqli_errno($link);
                        $errMSG_NEW = mysqli_error($link);
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
                    if($errMSG !== '' && $errMSG !== $errMSG_NEW){
                        error($errNO.":".$errMSG."-->更新時のエラー");
                        $errNO_NEW = mysqli_errno($link);
                        $errMSG_NEW = mysqli_error($link);
                    }
                }
            endforeach;
        break;

        case 400:
            error("データ取得時のエラー：パラメーターエラー (必須パラメータ不足)");
        break;

        case 404:
            error("データ取得時のエラー：対象のデータが存在しなかった");
        break;

        case 429:
            error("データ取得時のエラー：リクエスト過多 (各ユーザ制限値超過)");
        break;

        case 500:
            error("データ取得時のエラー：楽天ウェブサービス内のエラー");
        break;

        case 503:
            error("データ取得時のエラー：メンテナンス・リクエスト過多 (全ユーザ制限値超過)");
        break;
    }
    currentpage($count);
}
currentpage(1);


function getRakutenResult($keyword, $max_price, $min_price, $page) {

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
}

// RFC3986 形式で URL エンコードする関数

function urlencode_rfc3986($str) {
    return str_replace('%7E', '~', rawurlencode($str));
}
?>