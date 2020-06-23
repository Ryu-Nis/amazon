<?php
include 'DB_link.php';

//時刻を日本時間に設定
date_default_timezone_set('Asia/Tokyo');
$date = date('Y-m-d H:i:s');

// <!-- 楽天に取りに行く -->
// <!-- こけた時のためにどこまで読み込んだか把握して、どこから読むか制御 -->

for($count = 1; $count<10; $count++){

    $rakuten_relust = getRakutenResult('Bianchi', 20000, $count); // キーワードと最低価格を指定
    foreach ($rakuten_relust as $item) :
        // <!-- 返ってきた値をインサート -->
        $name = $item['name'];
        $price = $item['price'];
    
        // <!-- 既に入っているかチェック -->
        $check_result = mysqli_query($link,"SELECT 1 FROM products where name = '$name'");
        $rows = mysqli_num_rows($check_result);
    
        // <!-- 無かったら新規、既にあったら日付と値段を更新 -->
        if($rows == 0){
            $insert_sql = "INSERT INTO products (date,name,price,proportion) 
            VALUES ('$date', '$name', '$price',300)";
            $res = mysqli_query( $link, $insert_sql);
        }else{
            $update_sql = "UPDATE products SET date='$date', price='$price' WHERE name='$name'";
            $res = mysqli_query( $link, $update_sql);
        }
    endforeach;
}

function getRakutenResult($keyword, $min_price, $page) {

    // ベースとなるリクエストURL
    $baseurl = 'https://app.rakuten.co.jp/services/api/IchibaItem/Search/20140222';
    // リクエストのパラメータ作成
    $params = array();
    $params['applicationId'] = '1081445992483123286'; // アプリID
    $params['keyword'] = urlencode_rfc3986($keyword); // 任意のキーワード。※文字コードは UTF-8
    $params['sort'] = urlencode_rfc3986('+updateTimestamp'); // ソートの方法。※文字コードは UTF-8
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
                        );
    }
    return $items;
}

// RFC3986 形式で URL エンコードする関数

function urlencode_rfc3986($str) {
    return str_replace('%7E', '~', rawurlencode($str));
}
?>