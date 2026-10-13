<?php

include('../config.php');

if(isset($_GET['prod_id'])){
  $product_id = $_GET['prod_id'];
  $stmt = $conn->prepare("SELECT * FROM products WHERE prod_id=?");
  $stmt->bind_param('i', $product_id);
  $stmt->execute();
  $products = $stmt->get_result();

} else if(isset($_POST['update_product'])){
  $product_id = $_POST['prod_id'];
  $name = $_POST['prod_name'];
  $desc = $_POST['prod_desc'];
  $price = $_POST['prod_price'];
  $category = $_POST['prod_category'];

  // Handle image upload
  if(isset($_FILES['prod_image']) && $_FILES['prod_image']['error'] == 0){
    $image = $_FILES['prod_image']['name'];
    $target_dir = "../assets/images/product/";
    $target_file = $target_dir . basename($image);

    // Move the uploaded file to the target directory
    if(move_uploaded_file($_FILES['prod_image']['tmp_name'], $target_file)){
      // Image upload successful
    } else {
      // Handle error if image upload fails
      header('Location: products.php?error=Image upload failed');
      exit();
    }
  } else {
    // If no new image is uploaded, use the existing image
    $stmt = $conn->prepare("SELECT prod_image FROM products WHERE prod_id=?");
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $image = $product['prod_image'];
  }

  $stmt = $conn->prepare("UPDATE products SET prod_name=?, prod_desc=?, prod_price=?, prod_category=?, prod_image=? WHERE prod_id=?");
  $stmt->bind_param('sssssi', $name, $desc, $price, $category, $image, $product_id);

  if($stmt->execute()){
    header('Location: products.php?edit_success_message=Product successfully updated');
  } else {
    header('Location: products.php?error=Update failed');
  }

  exit();
  
} else {
  header('Location: products.php');
  exit();
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
                        <form id="edit-form" action="edit-product.php" method="post" enctype="multipart/form-data" class="tm-edit-product-form">
                            <p style="color: red;"><?php if(isset($_GET['error'])){echo $_GET['error'];}?></p>
                            
                            <?php foreach($products as $product){ ?>
                            <input name="prod_id" type="hidden" value="<?php echo $product['prod_id']?>" id="prod_id" >
                            <div class="form-group mb-3">
                                <label for="name">Product Name</label>
                                <input type="text" id="prod_name" name="prod_name" class="form-control validate" required value="<?php echo $product['prod_name']?>"/>
                            </div>
                            <div class="form-group mb-3">
                                <label for="desc">Description</label>
                                <input id="desc" name="prod_desc" class="form-control validate" rows="3" required value="<?php echo $product['prod_desc']?>"></input>
                            </div>
                            <div class="row">
                                <div class="form-group mb-3 col-xs-12 col-sm-6">
                                    <label for="price">Price</label>
                                    <input id="price" name="prod_price" type="text" class="form-control validate" required value="<?php echo $product['prod_price']?>"/>
                                </div>
                                <div class="form-group mb-3 col-sm-6">
                                    <label for="category">Category</label>
                                    <select class="custom-select tm-select-accounts" id="category" name="prod_category" required>
                                        <option disabled>Select category</option>
                                        <option value="Bouquet" <?php if($product['prod_category'] === "Bouquet") echo "selected"; ?>>Bouquet</option>
                                        <option value="Fruit Basket" <?php if($product['prod_category'] === "Fruit Basket") echo "selected"; ?>>Fruit</option>
                                        <option value="Balloons" <?php if($product['prod_category'] === "Balloons") echo "selected"; ?>>Balloons</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 mx-auto mb-4">
                            <div class="tm-product-img-edit mx-auto">
                                <img src="../assets/images/product/<?php echo $product['prod_image']?>" alt="Product image" class="img-fluid d-block mx-auto" name="prod_image">
                                <i class="fas fa-cloud-upload-alt tm-upload-icon" onclick="document.getElementById('fileInput').click();"></i>
                            </div>
                            <div class="custom-file mt-3 mb-3">
                                <input id="fileInput" name="prod_image" type="file" style="display:none;" />
                                <input type="button" class="btn btn-primary btn-block mx-auto" value="CHANGE IMAGE NOW" onclick="document.getElementById('fileInput').click();"/>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block text-uppercase" name="update_product">Update Now</button>
                            <!-- Cancel Update button -->
                            <button type="button" class="btn btn-primary btn-block text-uppercase" id="cancel-update">Cancel Update</button>
                        </div>
                    </form>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // Add event listener to the Cancel Update button
    document.getElementById('cancel-update').addEventListener('click', function() {
        // Redirect user to the desired page
        window.location.href = 'products.php';
    });
</script>

    <footer class="tm-footer row tm-mt-small">
      <div class="col-12 font-weight-light">
        <p class="text-center text-black mb-0 px-4 small">
          Copyright &copy; <b>2024</b> Team ASEA. All rights reserved
        </p>
      </div>
    </footer>
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="jquery-ui-datepicker/jquery-ui.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>