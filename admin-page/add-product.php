<?php
include('../config.php');

if(isset($_POST['add_product'])){
  $prod_name = mysqli_real_escape_string($conn, $_POST['name']);
  $prod_category = mysqli_real_escape_string($conn, $_POST['category']);
  $prod_desc = mysqli_real_escape_string($conn, $_POST['desc']);
  $prod_price = mysqli_real_escape_string($conn, $_POST['price']);
  $prod_image = $_FILES['image']['name'];
  $prod_image_temp_name = $_FILES['image']['tmp_name'];
  $prod_image_folder = '../assets/images/product/' . $prod_image;

  move_uploaded_file($prod_image_temp_name, $prod_image_folder);

  $insert_query = mysqli_query($conn, "INSERT INTO `products` (prod_name, prod_category, prod_desc, prod_image, prod_price)
  VALUES('$prod_name', '$prod_category', '$prod_desc', '$prod_image', '$prod_price')");

  if($insert_query){
    echo '<script>alert("Product inserted successfully")</script>';
  } else{
    echo '<script>alert("There is some error inserting product")</script>';
  }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Add Product - Admin Page</title>
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css?family=Roboto:400,700"
    />
    <!-- https://fonts.google.com/specimen/Roboto -->
    <link rel="stylesheet" href="css/fontawesome.min.css" />
    <!-- https://fontawesome.com/ -->
    <link rel="stylesheet" href="jquery-ui-datepicker/jquery-ui.min.css" type="text/css" />
    <!-- http://api.jqueryui.com/datepicker/ -->
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <!-- https://getbootstrap.com/ -->
    <link rel="stylesheet" href="css/templatemo-style.css">
    <!--
	Product Admin CSS Template
	https://templatemo.com/tm-524-product-admin
	-->
	<!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="img/gift.png">
  </head>

  <body>
    <nav class="navbar navbar-expand-xl">
      <div class="container h-100">
        <a class="navbar-brand" href="admin.php">
          <h1 class="tm-site-title mb-0">Admin Page</h1>
        </a>
        <button
          class="navbar-toggler ml-auto mr-0"
          type="button"
          data-toggle="collapse"
          data-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
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
              <a class="nav-link active" href="products.php">
               <b>Manage Products</b>
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="manage-order.php">
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
    <div class="container tm-mt-big tm-mb-big">
        <div class="row">
            <div class="col-xl-9 col-lg-10 col-md-12 col-sm-12 mx-auto">
                <div class="tm-bg-primary-dark tm-block tm-block-h-auto">
                    <div class="row">
                        <div class="col-12">
                            <h2 class="tm-block-title d-inline-block">Add Product</h2>
                        </div>
                    </div>
                    <div class="row tm-edit-product-row">
                        <div class="col-xl-6 col-lg-6 col-md-12">
                            <form action="" method="post" enctype="multipart/form-data" class="tm-edit-product-form">
                                <div class="form-group mb-3">
                                    <label for="name">Product Name</label>
                                    <input id="name" name="name" type="text" class="form-control validate" required/>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="desc">Description</label>
                                    <textarea id="desc" name="desc" class="form-control validate" rows="3" required></textarea>
                                </div>
                                <div class="row">
                                    <div class="form-group mb-3 col-xs-12 col-sm-6">
                                        <label for="price">Price</label>
                                        <input id="price" name="price" type="text" class="form-control validate" required />
                                    </div>
                                    <div class="form-group mb-3 col-sm-6">
                                        <label for="category">Category</label>
                                        <select class="custom-select tm-select-accounts" id="category" name="category" required>
                                            <option selected disabled>Select category</option>
                                            <option value="Bouquet">Bouquet</option>
                                            <option value="Fruit">Fruit</option>
                                            <option value="Balloons">Balloons</option>
                                        </select>
                                    </div>
                                </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 mx-auto mb-4">
                            <div class="tm-product-img-dummy mx-auto">
                                <i class="fas fa-cloud-upload-alt tm-upload-icon" onclick="document.getElementById('fileInput').click();"></i>
                            </div>
                            <div class="custom-file mt-3 mb-3">
                                <input id="fileInput" name="image" type="file" style="display:none;" required />
                                <input type="button" class="btn btn-primary btn-block mx-auto" value="UPLOAD PRODUCT IMAGE" onclick="document.getElementById('fileInput').click();" />
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="add_product" class="btn btn-primary btn-block text-uppercase">Add Product Now</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="tm-footer row tm-mt-small">
        <!-- Footer Content -->
    </footer>

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="jquery-ui-datepicker/jquery-ui.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
