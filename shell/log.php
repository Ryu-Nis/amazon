<?php
    //時刻を日本時間に設定
    date_default_timezone_set('Asia/Tokyo');

    function error($text) {
        $date = date('Ymd', time());
        $time = date('Y-m-d H:i:s', time());
        error_log("$time {$text}\r\n", 3, "../shell/logs/error.log");
    }
    
    function currentpage($text){
        $file = '../shell/logs/currentpage.txt';
        file_put_contents($file, $text);
    }
?>