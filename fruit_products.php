<?php

include('config.php');

$stmt = $conn->prepare("SELECT * FROM products WHERE prod_category='Fruit'");

$stmt->execute();

$fruit_products = $stmt->get_result();

?>