<?php
session_start();
include('config.php');

$error_message = '';
$success_message = '';
$pickup_delivery = '';

if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION["id"];

// Fetch customer details
$stmt = $conn->prepare("SELECT * FROM `customers` WHERE cust_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

// Count the number of rows in the cart/order table for the current cust_id
$stmt = $conn->prepare("SELECT COUNT(*) AS row_count FROM `cart/order` WHERE cust_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$cart_row_count = 0;
if ($cart_row = $result->fetch_assoc()) {
    $cart_row_count = $cart_row['row_count'];
}
$stmt->close();

// Check if the checkout form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'], $_POST['phone'], $_POST['full_address'], $_POST['payment_method'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $full_address = $conn->real_escape_string($_POST['full_address']);
    $payment_method = $_POST['payment_method'];

    // Insert billing details into the delivery table to get the delivery_id
    $stmt = $conn->prepare("INSERT INTO delivery (cust_id, cust_name, cust_phone, cust_address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $id, $name, $phone, $full_address);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Get the last inserted delivery_id
        $delivery_id = $stmt->insert_id;

        // Update the cart/order table with the payment method and delivery_id
        $stmt = $conn->prepare("UPDATE `cart/order` SET payment_method = ?, delivery_id = ? WHERE cust_id = ? AND prod_status = 'preconfirm'");
        $stmt->bind_param("sii", $payment_method, $delivery_id, $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            if ($payment_method == 'Ewallet') {
                $stmt = $conn->prepare("UPDATE `cart/order` SET prod_status = 'Paid' WHERE cust_id = ? AND payment_method = 'Ewallet' AND prod_status = 'preconfirm'");
            } elseif ($payment_method == 'Cash') {
                $stmt = $conn->prepare("UPDATE `cart/order` SET prod_status = 'Unpaid' WHERE cust_id = ? AND payment_method = 'Cash' AND prod_status = 'preconfirm'");
            }
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
            $success_message = "Your order was placed successfully.";
        } else {
            $error_message = "Error updating cart: " . $stmt->error;
        }
    } else {
        $error_message = "Error inserting billing details: " . $stmt->error;
    }
} else {
    $error_message = "Form data is missing.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon/gift.png">
    <title>The Gift Fairy</title>
    <link rel="stylesheet" href="assets/css/vendor/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/vendor/font.awesome.min.css">
    <link rel="stylesheet" href="assets/css/vendor/linearicons.min.css">
    <link rel="stylesheet" href="assets/css/plugins/swiper-bundle.min.css">
    <link rel="stylesheet" href="assets/css/plugins/animate.min.css">
    <link rel="stylesheet" href="assets/css/plugins/jquery-ui.min.css">
    <link rel="stylesheet" href="assets/css/plugins/nice-select.min.css">
    <link rel="stylesheet" href="assets/css/plugins/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: Helvetica, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .container h1 {
            color: #FFB6C1;
            margin-bottom: 20px;
        }
        .order-details p {
            margin: 10px 0;
        }
        .home-button {
            background-color: #FFB6C1;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .home-button:hover {
            background-color: #000000;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Order Successful!</h1>
        <?php if ($success_message): ?>
            <p><?php echo $success_message; ?></p>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <br>
        <a href="order.php" class="home-button">View Order</a>
        <a href="home.php" class="home-button">Return to Home</a>
    </div>
</body>
</html>