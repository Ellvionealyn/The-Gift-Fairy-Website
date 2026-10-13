<?php
// Include the configuration file to establish database connection
include('../config.php');

// Get order_id from query parameter
$order_id = isset($_GET['cust_id']) ? $_GET['cust_id'] : 0;

// Fetch order details
$order_query = "
    SELECT 
        o.cust_id,
        p.prod_name,
        p.prod_image,
        p.prod_price,
        o.prod_quantity
    FROM `cart/order` o
    JOIN `products` p ON o.prod_id = p.prod_id
    WHERE o.cust_id = ?
";

// Prepare statement
$stmt = $conn->prepare($order_query);

// Check if statement preparation was successful
if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}

$stmt->bind_param("i", $order_id);

// Execute statement
if (!$stmt->execute()) {
    die('Execute failed: ' . $stmt->error);
}

$orders_result = $stmt->get_result();

// Check if order exists
if ($orders_result->num_rows == 0) {
    echo "Order not found";
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>View Order - Admin Page</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700" />
    <link rel="stylesheet" href="css/fontawesome.min.css" />
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/templatemo-style.css" />
    <link rel="shortcut icon" type="image/x-icon" href="img/gift.png">
</head>
<body id="reportsPage">
<div class="" id="home">
    <nav class="navbar navbar-expand-xl">
        <div class="container h-100">
            <a class="navbar-brand" href="admin.php">
                <h1 class="tm-site-title mb-0">Admin Page</h1>
            </a>
            <button class="navbar-toggler ml-auto mr-0" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars tm-nav-icon"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mx-auto h-100">
                    <li class="nav-item">
                        <a class="nav-link" href="admin.php">
                            <b>Dashboard</b>
                            <span class="sr-only">(current)</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">
                            <b>Manage Products</b>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="manage-order.php">
                            <b>Manage Orders</b>
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link d-block" href="../login.php" name="logout" onclick="return confirm('Are you sure you want to log out?');">
                            <strong><span class="menu-text">Logout</span></strong>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
    <div class="row tm-content-row">
        <div class="col-12 tm-block-col">
            <div class="tm-bg-primary-dark tm-block tm-block-taller tm-block-scroll">
                <h2 class="tm-block-title">Order Details</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Index</th>
                            <th scope="col">Image</th>
                            <th scope="col">Product Name</th>
                            <th scope="col">Price</th>
                            <th scope="col">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_order_amount = 0; // Initialize total order amount
                        $index = 1;
                        while ($order = mysqli_fetch_assoc($orders_result)) {
                            echo "<tr>";
                            echo "<td>" . $index . "</td>";
                            echo '<td class="pro-thumbnail"><img class="img-fluid" style="width: 100px; height: auto;" src="../assets/images/product/' . $order['prod_image'] . '" alt="Product"></td>';
                            echo "<td>" . $order['prod_name'] . "</td>";
                            echo "<td>RM" . $order['prod_price'] . "</td>";
                            echo "<td>" . $order['prod_quantity'] . "</td>";
                            echo "</tr>";
                            // Add prod_price to total order amount
                            $total_order_amount += $order['prod_price'] * $order['prod_quantity'];
                            $index++;
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" style="text-align: right;"><strong>Total Order Amount:</strong></td>
                            <td><strong>RM <?php echo $total_order_amount; ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
// Close the connection
mysqli_close($conn);
?>

    <footer class="tm-footer row tm-mt-small">
        <div class="col-12 font-weight-light">
            <p class="text-center text-black mb-0 px-4 small">
                Copyright &copy; <b>2024</b> Team ASEA. All rights reserved
            </p>
        </div>
    </footer>
</div>

<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
