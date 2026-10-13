<?php
session_start();
include('config.php');

// Get the current session cust_ID
if(!empty($_SESSION["id"])){
    $id = $_SESSION["id"];
    $result = mysqli_query($conn, "SELECT * FROM `customers` WHERE cust_id = $id");
    $row = mysqli_fetch_assoc($result);

    // Count the number of rows in the cart/order table for the current cust_id
    $cart_row_count = 0;
    $cart_result = mysqli_query($conn, "SELECT COUNT(*) AS row_count FROM `cart/order` WHERE cust_id = $id");
    if ($cart_result) {
        $cart_row = mysqli_fetch_assoc($cart_result);
        $cart_row_count = $cart_row['row_count'];
    }
}

if(isset($_POST["add_to_cart"])) {
    $product_id = $_POST["prod_id"];
    $product_name = $_POST["prod_name"];
    $product_cate = $_POST["prod_category"];
    $product_price = $_POST["prod_price"];
    $product_image = $_POST["prod_image"];
    $product_qty = $_POST["prod_quantity"];
    
    // Check if the product is already in the cart for this customer
    $result = $conn->query("SELECT * FROM `cart/order` WHERE cust_id='$id' AND prod_id='$product_id'");
    if ($result->num_rows > 0) {
        // Update the product quantity if it already exists
        $conn->query("UPDATE `cart/order` SET prod_quantity=prod_quantity+$product_qty WHERE cust_id='$id' AND prod_id='$product_id'");
    } else {
        // Add the new product to the cart for this customer
        $stmt = $conn->prepare("INSERT INTO `cart/order` (cust_id, prod_id, prod_name, prod_category, prod_price, prod_image, prod_quantity, prod_status) VALUES (?, ?, ?, ?, ?, ?, ?, 'preconfirm')");
        $stmt->bind_param('iissssi', $id, $product_id, $product_name, $product_cate, $product_price, $product_image, $product_qty);
        $stmt->execute();
        $stmt->close();
    }
}
?>

<!doctype html>
<html class="no-js" lang="en">


<!-- Mirrored from htmldemo.net/flosun/flosun/cart.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 04 Dec 2022 05:03:26 GMT -->
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>The Gift Fairy</title>
    <meta name="robots" content="noindex, follow" />
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon/gift.png">

    <!-- CSS
	============================================ -->
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/vendor/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="assets/css/vendor/font.awesome.min.css">
    <!-- Linear Icons CSS -->
    <link rel="stylesheet" href="assets/css/vendor/linearicons.min.css">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="assets/css/plugins/swiper-bundle.min.css">
    <!-- Animation CSS -->
    <link rel="stylesheet" href="assets/css/plugins/animate.min.css">
    <!-- Jquery ui CSS -->
    <link rel="stylesheet" href="assets/css/plugins/jquery-ui.min.css">
    <!-- Nice Select CSS -->
    <link rel="stylesheet" href="assets/css/plugins/nice-select.min.css">
    <!-- Magnific Popup -->
    <link rel="stylesheet" href="assets/css/plugins/magnific-popup.css">

    <!-- Main Style CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        header {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <!-- Header Area Start Here -->
    <header class="main-header-area">
        <!-- Main Header Area Start -->
        <div class="main-header header-sticky">
            <div class="container custom-area">
                <div class="row align-items-center">
                    <div class="col-lg-2 col-xl-2 col-md-6 col-6 col-custom">
                        <div class="header-logo d-flex align-items-center">
                            <a href="home.php">
                                <img class="img-full" src="assets/images/logo/logo.png" alt="Header Logo">
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-8 d-none d-lg-flex justify-content-center col-custom">
                        <nav class="main-nav d-none d-lg-flex">
                            <ul class="nav">
                                <li>
                                    <a href="home.php">
                                        <span class="menu-text"> Home</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="active" href="shop-menu.php">
                                        <span class="menu-text">Shop</span>
										<i class="fa fa-angle-down"></i>
                                    </a>
									 <ul class="dropdown-submenu dropdown-hover">
										<li><a class="mega-menu-text"><strong>Categories</strong></a></li>
                                        <li><a href="shop-menu.php">Flower Bouquets</a></li>
                                        <li><a href="shop-menu.php-">Hampers</a></li>
                                        <li><a href="shop-menu.php">Balloons</a></li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="about-us.php">
                                        <span class="menu-text"> About Us</span>
                                    </a>
                                </li>
                                <li> 
                                    <a href="order.php">
                                        <span class="menu-text">My Order</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="login.php" name="logout" onclick="return confirm('Are you sure you want to log out?');">
                                        <span class="menu-text">Logout</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <div class="col-lg-2 col-md-6 col-6 col-custom">
                        <div class="header-right-area main-nav">
                            <ul class="nav">
                                <li class="minicart-wrap">
                                    <a href="cart.php" class="minicart-btn toolbar-btn">
                                        <i class="fa fa-shopping-cart"></i>
                                        <span class="cart-item_count"><?php echo $cart_row_count; ?></span>
                                    </a>
                                </li>
                                <li class="sidemenu-wrap">
                                    <a href="#"><i class="fa fa-search"></i> </a>
                                    <ul class="dropdown-sidemenu dropdown-hover-2 dropdown-search">
                                        <li>
                                            <form method="GET" action="product-search.php">
                                                <input name="searchTerm" id="search" placeholder="Search" type="text">
                                                <button type="submit" value="Search"><i class="fa fa-search"></i></button>
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                                <li class="account-menu-wrap d-none d-lg-flex">
                                    <a href="#" class="off-canvas-menu-btn">
                                        <i class="fa fa-bars"></i>
                                    </a>
                                </li>
                                <li class="mobile-menu-btn d-lg-none">
                                    <a class="off-canvas-btn" href="#">
                                        <i class="fa fa-bars"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Main Header Area End -->
<!-- off-canvas menu start -->
<aside class="off-canvas-wrapper" id="mobileMenu">
            <div class="off-canvas-overlay"></div>
            <div class="off-canvas-inner-content">
                <div class="btn-close-off-canvas">
                    <i class="fa fa-times"></i>
                </div>
                <div class="off-canvas-inner">
                    <!-- mobile menu start -->
                    <div class="mobile-navigation">
                        <!-- mobile menu navigation start -->
                        <nav>
                            <ul class="mobile-menu">
                                <li><a><strong>MENU</strong></a></li>
                                <li><a href="home.php">Home</a></li>
                                <li><a href="shop-menu.php">Shop</a></li>
                                <li><a href="about-us.php">About Us</a></li>
                                <li><a href="order.php">My Order</a></li>
                                <li>
                                    <a href="login.php" name="logout" onclick="return confirm('Are you sure you want to log out?');">
                                        <span>Logout</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                        <!-- mobile menu navigation end -->
                    </div>
                    <!-- mobile menu end -->
                    <div class="offcanvas-widget-area">
                        <div class="border"></div>
                        <div class="top-info-wrap text-left text-black">
                            <ul class="address-info">
                                <li>
                                    <i class="fa fa-phone"></i>
                                    <a href="#">(60+)19 277-0582</a>
                                </li>
                                <li>
                                    <i class="fa fa-envelope"></i>
                                    <a href="#">thegiftfairy@gmail.com</a>
                                </li>
                            </ul>
                            <div class="widget-social">
                                <a class="facebook-color-bg" title="Facebook-f" href="https://web.facebook.com/TheGiftFairybyLuckySky/?_rdc=1&_r"><i class="fa fa-facebook-f"></i></a>
                                <a class="instagram-color-bg" title="Instagram" href="https://www.instagram.com/thegiftfairypapar/"><i class="fa fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
        <!-- off-canvas menu end -->
        <!-- off-canvas menu start -->
        <aside class="off-canvas-menu-wrapper" id="sideMenu">
            <div class="off-canvas-overlay"></div>
            <div class="off-canvas-inner-content">
                <div class="off-canvas-inner">
                    <div class="btn-close-off-canvas">
                        <i class="fa fa-times"></i>
                    </div>
                    <!-- offcanvas widget area start -->
                    <div class="offcanvas-widget-area">
                        <ul class="menu-top-menu">
                             <li><a href="about-us.php">About Us</a></li>
                        </ul>
                        <p class="desc-content">We are passionate about delivering the best gifts to our customers. Our expert florists create stunning bouquets for any occasion, from weddings to birthdays to everyday moments. We also provide hampers and balloons to surprise your loves one. We pride ourselves on our attention to detail and personalized service, ensuring that every customer is satisfied with their purchase. Located in Papar, Sabah shop with us today and experience the Gift Fairy difference!</p>
							<p>
                            <ul class="address-info">
                                <li>
                                    <i class="fa fa-phone"></i>
                                    <a href="#">(60+)19 277-0582</a>
                                </li>
							</p>
							<p>
                                <li>
                                    <i class="fa fa-envelope"></i>
                                    <a href="#">thegiftfairy@gmail.com</a>
                                </li>
                            </ul>
							</p>
                            <div class="widget-social">
                                <a class="facebook-color-bg" title="Facebook-f" href="https://web.facebook.com/TheGiftFairybyLuckySky/?_rdc=1&_r"><i class="fa fa-facebook-f"></i></a>
                                <a class="instagram-color-bg" title="Instagram" href="https://www.instagram.com/thegiftfairypapar/"><i class="fa fa-instagram"></i></a>
                            </div>
                    </div>
                    <!-- offcanvas widget area end -->
                </div>
            </div>
        </aside>
        <!-- off-canvas menu end -->
    </header>
    <!-- Header Area End Here -->
    
    <!-- Cart Area Starts Here -->
    <?php
    include 'config.php';

    // Check if the user is logged in
    if (!empty($_SESSION["id"])) {
        $cust_id = $_SESSION["id"];

        // Handle quantity update
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_cart"])) {
            foreach ($_POST["quantities"] as $prod_id => $quantity) {
                $quantity = (int)$quantity;
                if ($quantity > 0) {
                    $sql = "UPDATE `cart/order` SET prod_quantity = ? WHERE cust_id = ? AND prod_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iii", $quantity, $cust_id, $prod_id);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }

        // Handle item removal
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["remove_item"])) {
            $prod_id = $_POST["remove_item"];
            $sql = "DELETE FROM `cart/order` WHERE cust_id = ? AND prod_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $cust_id, $prod_id);
            $stmt->execute();
            $stmt->close();
        }

        // Handle pickup/delivery selection
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["shipping_option"])) {
            $shipping_option = $_POST["shipping_option"];
            $sql = "UPDATE `cart/order` SET `pickup/delivery` = ? WHERE cust_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $shipping_option, $cust_id);
            $stmt->execute();
            $stmt->close();
        }

        // Fetch data from the cart or order table for the logged-in customer
        $sql = "SELECT prod_id, prod_name, prod_category, prod_price, prod_image, prod_quantity FROM `cart/order` WHERE cust_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $cust_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $cart_items = [];
        if ($result->num_rows > 0) {
            // Fetch all rows
            while ($row = $result->fetch_assoc()) {
                $cart_items[] = $row;
            }
        }

        $stmt->close();
    } else {
        echo "User not logged in.";
    }

    $conn->close();
    ?>

    <!-- Cart main wrapper start -->
    <div class="cart-main-wrapper mt-no-text">
        <div class="container custom-area">
            <div class="row">
                <div class="col-lg-12 col-custom">
                    <!--Cart Table Area-->
                    <div class="cart-table table-responsive">
                        <form method="POST" action="cart.php">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="pro-thumbnail">Image</th>
                                        <th class="pro-title">Product</th>
                                        <th class="pro-price">Price</th>
                                        <th class="pro-quantity">Quantity</th>
                                        <th class="pro-subtotal">Total</th>
                                        <th class="pro-remove">Remove</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($cart_items)): ?>
                                        <?php foreach ($cart_items as $item): ?>
                                            <tr>
                                                <td class="pro-thumbnail"><img class="img-fluid" src="assets/images/product/<?php echo $item['prod_image']; ?>" alt="Product" /></td>
                                                <td class="pro-title"><?php echo $item['prod_name']; ?></td>
                                                <td class="pro-price"><span>RM <?php echo number_format($item['prod_price'], 2); ?></span></td>
                                                <td class="pro-quantity">
                                                    <div class="quantity">
                                                        <div class="cart-plus-minus">
                                                            <input class="cart-plus-minus-box" name="quantities[<?php echo $item['prod_id']; ?>]" value="<?php echo $item['prod_quantity']; ?>" type="text">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="pro-subtotal"><span>RM <?php echo number_format($item['prod_price'] * $item['prod_quantity'], 2); ?></span></td>
                                                <td class="pro-remove">
                                                    <button type="submit" name="remove_item" value="<?php echo $item['prod_id']; ?>" class="btn-remove"><i class="lnr lnr-trash"></i></button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6">No items in the cart.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            <div class="cart-update-option d-block d-md-flex justify-content-between">
                                <div class="cart-update mt-sm-16">
                                    <button type="submit" name="update_cart" class="btn flosun-button primary-btn rounded-0 black-btn">Update Cart</button>
                                </div>
                            </div>
                            <div class="shipping-option mt-3">
                                <label for="shipping_option">Choose your option:</label>
                                <select name="shipping_option" id="shipping_option" class="form-control">
                                    <option value="Delivery">Delivery</option>
                                    <option value="Pickup">Pickup</option>
                                </select>
                                <button type="submit" name="update_shipping" class="btn flosun-button primary-btn rounded-0 black-btn mt-2">Update Shipping Option</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-5 ml-auto col-custom">
                    <form id="cart-form" method="POST" action="checkout.php">
                        <!-- Cart Calculation Area -->
                        <div class="cart-calculator-wrapper">
                            <div class="cart-calculate-items">
                                <h3>Cart Totals</h3>
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <td>Sub Total</td>
                                            <td>RM <?php echo number_format(array_sum(array_map(function($item) {
                                                return $item['prod_price'] * $item['prod_quantity'];
                                            }, $cart_items)), 2); ?>
                                                <input type="hidden" name="subtotal" value="<?php echo $subtotal; ?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Shipping</td>
                                            <?php 
                                            if (isset($_POST["shipping_option"]) && strtolower($_POST["shipping_option"]) === 'pickup'): ?>
                                                <td>RM 0.00</td>
                                                <input type="hidden" name="shipping_cost" value="0">
                                            <?php else: ?>
                                                <td>RM 30.00</td>
                                                <input type="hidden" name="shipping_cost" value="30">
                                            <?php endif; ?>
                                        </tr>
                                        <tr class="total">
                                            <td>Total</td>
                                            <?php
                                            $shipping_cost = isset($_POST["shipping_option"]) && strtolower($_POST["shipping_option"]) === 'pickup' ? 0 : 30;
                                            $total_amount = array_sum(array_map(function($item) {
                                                return $item['prod_price'] * $item['prod_quantity'];
                                            }, $cart_items)) + $shipping_cost;
                                            ?>
                                            <td class="total-amount">RM <?php echo number_format($total_amount, 2); ?>
                                                <input type="hidden" name="total_amount" value="<?php echo $total_amount; ?>">
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <button type="submit" class="btn flosun-button primary-btn rounded-0 black-btn w-100">Proceed To Checkout</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Cart main wrapper end -->
    <!-- Cart Area Ends Here -->

    <!--Footer Area Start-->
    <footer class="footer-area mt-no-text">
        <div class="footer-widget-area">
            <div class="container container-default custom-area">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-custom">
                        <div class="single-footer-widget m-0">
                            <div class="footer-logo">
                                <a href="home.php">
                                    <img src="assets/images/logo/logo-footer.png" alt="Logo Image" width="100%" height="80">
                                </a>
                            </div>
                            <p class="desc-content">We hope you had a pleasant<br>experience shopping with us!</p>
                            <div class="social-links">
                                <ul class="d-flex">
                                    <li>
                                        <a class="rounded-circle" href="https://web.facebook.com/TheGiftFairybyLuckySky/?_rdc=1&_r" title="Facebook">
                                            <i class="fa fa-facebook-f"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="rounded-circle" href="https://www.instagram.com/thegiftfairypapar/" title="Instagram">
                                            <i class="fa fa-instagram"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-6 col-lg-3 col-custom">
                        <div class="single-footer-widget">
                            <h2 class="widget-title">Operating Hours:</h2>
                            <ul class="widget-list">
                                <li><a>Monday - Sunday</a></li>
                                <li><a>09:00 am - 06:00 pm</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-6 col-lg-2 col-custom">
                        <div class="single-footer-widget">
                            <h2 class="widget-title">Quicklink</h2>
                            <ul class="widget-list">
                                <li><a href="about-us.php">About</a></li>
                                <li><a href="shop-menu.php">Shop</a></li>
                                <li><a href="home.php">Home</a></li>
                                <li><a href="order.php">My Order</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-6 col-lg-3 col-custom">
                        <div class="single-footer-widget">
                            <h2 class="widget-title">See Information</h2>
                            <div class="widget-body">
                                <address>Lot 26, Papar Commercial Centre, 89600, Papar, Sabah.</address>
                                <ul class="widget-list">
                                    <li><a>Phone: (60+)19 277-0582</a></li>
                                    <li><a>Email: thegiftfairy@gmail.com</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-copyright-area">
            <div class="container custom-area">
                <div class="row">
                    <div class="col-12 text-center col-custom">
                        <div class="copyright-content">
                            <p>Copyright © 2024 by Team ASEA</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </footer>
        <!--Footer Area End-->

    <!-- JS
============================================ -->


    <!-- jQuery JS -->
    <script src="assets/js/vendor/jquery-3.6.0.min.js"></script>
    <!-- jQuery Migrate JS -->
    <script src="assets/js/vendor/jquery-migrate-3.3.2.min.js"></script>
    <!-- Modernizer JS -->
    <script src="assets/js/vendor/modernizr-3.7.1.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="assets/js/vendor/bootstrap.bundle.min.js"></script>


    <!-- Swiper Slider JS -->
    <script src="assets/js/plugins/swiper-bundle.min.js"></script>
    <!-- nice select JS -->
    <script src="assets/js/plugins/nice-select.min.js"></script>
    <!-- Ajaxchimpt js -->
    <script src="assets/js/plugins/jquery.ajaxchimp.min.js"></script>
    <!-- Jquery Ui js -->
    <script src="assets/js/plugins/jquery-ui.min.js"></script>
    <!-- Jquery Countdown js -->
    <script src="assets/js/plugins/jquery.countdown.min.js"></script>
    <!-- jquery magnific popup js -->
    <script src="assets/js/plugins/jquery.magnific-popup.min.js"></script>

    <!-- Main JS -->
    <script src="assets/js/main.js"></script>


</body>


<!-- Mirrored from htmldemo.net/flosun/flosun/cart.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 04 Dec 2022 05:03:26 GMT -->
</html>