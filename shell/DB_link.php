<?php
$link = mysqli_connect('mysql', 'root', 'root', 'amazon');
$link->set_charset('utf8');

// ↓もしDBを切り替えるときがあったら、これを使う
// $link->select_db("amazon");
// /* 現在のデフォルトデータベース名を返します */
// if ($result = $link->query("SELECT DATABASE()")) {
//     $row = $result->fetch_row();
//     printf("Default database is %s.\n", $row[0]);
//     $result->close();
// }

?>