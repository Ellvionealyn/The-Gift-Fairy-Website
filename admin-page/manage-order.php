<?php
include('../config.php');

// Fetch unique order statuses from the cart/order table
$status_query = "SELECT DISTINCT prod_status FROM `cart/order`";
$status_result = mysqli_query($conn, $status_query);

// Query to select orders with prod_status = 'confirm_paid' or 'confirm_unpaid', joined with customers table
$sql = "
    SELECT 
        o.cust_id,
        GROUP_CONCAT(DISTINCT o.prod_status ORDER BY o.prod_status SEPARATOR ', ') AS prod_status,
        c.cust_name,
        GROUP_CONCAT(DISTINCT o.`pickup/delivery` ORDER BY o.`pickup/delivery` SEPARATOR ', ') AS `pickup_delivery`,
        GROUP_CONCAT(DISTINCT o.payment_method ORDER BY o.payment_method SEPARATOR ', ') AS payment_method,
        SUM(o.prod_price) AS total_price
    FROM `cart/order` o
    JOIN `customers` c ON o.cust_id = c.cust_id
    WHERE o.prod_status IN ('Paid', 'Unpaid')
    GROUP BY o.cust_id, c.cust_name
";

// Execute the query
$result = mysqli_query($conn, $sql);

// Check for query errors
if (!$result) {
    die('Query Error: ' . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Orders - Admin Page</title>
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
                    <h2 class="tm-block-title">Orders List</h2>

                    <label for="statusFilter">Filter by status:</label>
                    <select class="tm-product-table-container" id="statusFilter">
                        <option value="all">All Status</option>
                        <?php while ($status_row = mysqli_fetch_assoc($status_result)) { 
                            if ($status_row['prod_status'] == 'Paid' || $status_row['prod_status'] == 'Unpaid') { ?>
                                <option value="<?php echo $status_row['prod_status']; ?>">
                                    <?php echo $status_row['prod_status']; ?>
                                </option>
                        <?php } 
                        } ?>
                    </select>

                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">INDEX</th>
                                <th scope="col">STATUS</th>
                                <th scope="col">RECIPIENT NAME</th>
                                <th scope="col">PICKUP/DELIVERY</th>
                                <th scope="col">TOTAL PRICE</th>
                                <th scope="col">PAYMENT METHOD</th>
                                <th scope="col">ACTION</th>
                            </tr>
                        </thead>
                        <tbody id="orderTableBody">
                            <?php
                            // Check if there are any results
                            if (mysqli_num_rows($result) > 0) {
                                // Output data of each row
                                while ($row = mysqli_fetch_assoc($result)) {
                            ?>      
                                    <tr class="order-row" data-status="<?php echo $row['prod_status']; ?>">
                                        <th scope="row"><b>#<?php echo $row["cust_id"]; ?></b></th>
                                        <td>
                                            <div class="status-container">
                                                <div class="tm-status-circle <?php echo $row['prod_status']; ?>"></div>
                                                <b><?php echo $row["prod_status"]; ?></b>
                                            </div>
                                        </td>
                                        <td><b><?php echo $row["cust_name"]; ?></b></td>
                                        <td><b><?php echo $row["pickup_delivery"]; ?></b></td>
                                        <td><b><?php echo 'RM' . $row["total_price"]; ?></b></td>
                                        <td><?php echo $row["payment_method"]; ?></td>
                                        <td><a href="view-order.php?cust_id=<?php echo $row['cust_id']; ?>" class="btn btn-primary">View Order</a></td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo "<tr><td colspan='7'>No orders found.</td></tr>";
                            }
                            ?>
                        </tbody>
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
<script>
    // JavaScript to handle status filter
    document.getElementById('statusFilter').addEventListener('change', function() {
        var selectedStatus = this.value;
        var orderRows = document.querySelectorAll('.order-row');
        
        orderRows.forEach(function(row) {
            if (selectedStatus === 'all' || row.getAttribute('data-status') === selectedStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
</body>
</html>