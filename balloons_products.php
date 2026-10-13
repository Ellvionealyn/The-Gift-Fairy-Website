<?php

include('config.php');

$stmt = $conn->prepare("SELECT * FROM products WHERE prod_category='Balloons'");

$stmt->execute();

$balloons_products = $stmt->get_result();

?>