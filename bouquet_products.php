<?php

include('config.php');

$stmt = $conn->prepare("SELECT * FROM products WHERE prod_category='Bouquet'");

$stmt->execute();

$bouquet_products = $stmt->get_result();

?>