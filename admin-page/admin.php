<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard - Admin Page</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700">
    <!-- https://fonts.google.com/specimen/Roboto -->
    <link rel="stylesheet" href="css/fontawesome.min.css">
    <!-- https://fontawesome.com/ -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- https://getbootstrap.com/ -->
    <link rel="stylesheet" href="css/templatemo-style.css">
    <!--
	Product Admin CSS Template
	https://templatemo.com/tm-524-product-admin
	-->
	<!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="img/gift.png">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body id="reportsPage">
    <div class="" id="home">
        <nav class="navbar navbar-expand-xl">
            <div class="container h-100">
                <a class="navbar-brand" href="admin.php">
					<img class="img-full" src="img/logo.png" alt="Header Logo">
                </a>
                <button class="navbar-toggler ml-auto mr-0" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fas fa-bars tm-nav-icon"></i>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mx-auto h-100">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">
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
                            <a class="nav-link" href="manage-order.php">
                            <b>Manage Orders</>
                            </a>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link d-block" href="../login.php" name="logout" onclick="return confirm('Are you sure you want to log out?');">
                                <span class="menu-text">Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container">
            <div class="row">
                <div class="col">
                    <h2 class="text-black mt-5 mb-5">Welcome to <b>Admin Page</b></h2>
                </div>
            </div>
			<!-- New row for the four boxes -->
        <div class="tm-content-row-four-boxes">

        <?php
            // Include the configuration file to establish database connection
            include('../config.php');

            // Fetch total revenue
            $totalRevenueQuery = "
                SELECT SUM(prod_price * prod_quantity) AS total_revenue
                FROM `cart/order`
                WHERE prod_status = 'Paid'
            ";

            $totalRevenueResult = mysqli_query($conn, $totalRevenueQuery);

            if (!$totalRevenueResult) {
                die('Query Error: ' . mysqli_error($conn));
            }

            $totalRevenueRow = mysqli_fetch_assoc($totalRevenueResult);
            $totalRevenue = $totalRevenueRow['total_revenue'];

            // Fetch total number of orders (both paid and unpaid)
            $totalOrdersQuery = "
                SELECT COUNT(*) AS total_orders
                FROM `cart/order`
                WHERE prod_status IN ('Paid', 'Unpaid')
            ";

            $totalOrdersResult = mysqli_query($conn, $totalOrdersQuery);

            if (!$totalOrdersResult) {
                die('Query Error: ' . mysqli_error($conn));
            }

            $totalOrdersRow = mysqli_fetch_assoc($totalOrdersResult);
            $totalOrders = $totalOrdersRow['total_orders'];

            // Fetch total customers registered
            $totalCustQuery = "
                SELECT COUNT(*) AS total_cust
                FROM `customers`";

            $totalCustResult = mysqli_query($conn, $totalCustQuery);

            if (!$totalCustResult) {
                die('Query Error: ' . mysqli_error($conn));
            }

            $totalCustRow = mysqli_fetch_assoc($totalCustResult);
            $totalCust = $totalCustRow['total_cust'];
        ?>

            <div class="tm-col-big" >
                <div class="tm-block tm-block-h-auto tm-block-avatar" style="width: 80%">
                    <h2 class="tm-block-title">Total Orders</h2>
                    <p class="tm-text-color-secondary"><?php echo $totalOrders; ?></p>
                </div>
            </div>
            <div class="tm-col-big">
                <div class="tm-block tm-block-h-auto tm-block-avatar" style="width: 80%">
                    <h2 class="tm-block-title">Total Revenue</h2>
                    <p class="tm-text-color-secondary">RM <?php echo number_format($totalRevenue, 2); ?></p>
                </div>
            </div>
            <div class="tm-col">
                <div class="tm-block tm-block-h-auto tm-block-avatar" style="width: 120%;">
                    <h2 class="tm-block-title">Total Customers Registered</h2>
                    <p class="tm-text-color-secondary"><?php echo $totalCust; ?></p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .container-chart {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .chart-container {
            width: 700px;
            margin-bottom: 20px;
        }
        .tm-footer {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>

   <!-- row -->
    <div class="row tm-content-row justify-content-center">
        <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6 tm-block-col">
            <div class="tm-bg-primary-dark tm-block">
                <h2 class="tm-block-title">Total Products Bought per Category</h2>
            
                <div class="container-chart">
                    <!-- statistical data -->
                    <div class="chart-container">
                        <canvas id="myChart"></canvas>
                    </div>

                    <?php 
                    include('../config.php');

                    // Query to fetch the total count of products bought for each category
                    $query = mysqli_query($conn, "
                        SELECT 
                        prod_category AS category,
                        COUNT(*) AS count
                        FROM `cart/order`
                        GROUP BY prod_category
                    ");

                    // Initialize arrays to store the categories and their respective counts
                    $categories = [];
                    $counts = [];

                    // Fetch data from the query result
                    while ($data = mysqli_fetch_assoc($query)) {
                        $categories[] = $data['category'];
                        $counts[] = $data['count'];
                    }
                    ?>

                    <script>
                    // === include 'setup' then 'config' above ===
                    const labels = <?php echo json_encode($categories); ?>;
                    const data = {
                        labels: labels,
                        datasets: [{
                        label: 'Total Products Bought per Category',
                        data: <?php echo json_encode($counts); ?>,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                            'rgba(255, 205, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(201, 203, 207, 0.2)'
                        ],
                        borderColor: [
                            'rgb(255, 99, 132)',
                            'rgb(255, 159, 64)',
                            'rgb(255, 205, 86)',
                            'rgb(75, 192, 192)',
                            'rgb(54, 162, 235)',
                            'rgb(153, 102, 255)',
                            'rgb(201, 203, 207)'
                        ],
                        borderWidth: 1
                        }]
                    };

                    const config = {
                        type: 'bar',
                        data: data,
                        options: {
                        scales: {
                            y: {
                            beginAtZero: true
                            }
                        }
                        },
                    };

                    var myChart = new Chart(
                        document.getElementById('myChart'),
                        config
                    );
                    </script>
                </div>
            </div>
        </div>
    </div>
    <footer class="tm-footer row tm-mt-small">
                        <div class="col-12 font-weight-light">
                            <p class="text-center text-black mb-0 px-4 small">
                                Copyright &copy; <b>2024</b> Team ASEA. All rights reserved
                            </p>
                        </div>
                    </footer>

</body>
</html>