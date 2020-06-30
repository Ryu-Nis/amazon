<?php
function output_csv($ary,$filepath){
    // ファイルを書き込み用に開きます。
    $f = fopen($filepath,"a");
    // 正常にファイルを開くことができていれば、書き込みます。
    if ( $f ) {
        // fputcsv関数でファイルに書き込みます。
        fputcsv($f, $ary);
    }
    // ファイルを閉じます。
    // fclose($f);
}
?>