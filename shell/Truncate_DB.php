<?php

include 'DB_link.php';

$sql = "TRUNCATE products";
$res = mysqli_query( $link, $sql);

$sql = "TRUNCATE historical_products";
$res = mysqli_query( $link, $sql);

?>