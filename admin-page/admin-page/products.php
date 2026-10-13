<?php
include('../config.php');

// Fetch unique categories from the products table
$category_query = "SELECT DISTINCT prod_category FROM products";
$category_result = mysqli_query($conn, $category_query);

// Fetch all products
$display_product_query = "SELECT * FROM products";
$display_product = mysqli_query($conn, $display_product_query);

if(isset($_GET['delete'])){
  $delete_id = $_GET['delete'];
  // Use prepared statement to prevent SQL injection
  $delete_statement = mysqli_prepare($conn, "DELETE FROM `products` WHERE prod_id = ?");
  mysqli_stmt_bind_param($delete_statement, "i", $delete_id);
  $delete_result = mysqli_stmt_execute($delete_statement);

  if($delete_result){
      echo '<script>alert("Product deleted");</script>';
      // Redirect after displaying alert
      echo '<script>window.location.href = "products.php";</script>';
      exit(); // Ensure script stops execution after redirect
  }else{
      echo '<script>alert("Product not deleted");</script>';
      // Redirect even if deletion fails
      echo '<script>window.location.href = "products.php";</script>';
      exit(); // Ensure script stops execution after redirect
  }
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Product - Admin Page</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700" />
    <link rel="stylesheet" href="css/fontawesome.min.css" />
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/templatemo-style.css">
    <link rel="shortcut icon" type="image/x-icon" href="img/gift.png">
  </head>

  <body id="reportsPage">
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
    
    <div class="container mt-5">
      <div class="row tm-content-row">
        <div class="col-sm-12">
          <div class="tm-bg-primary-dark tm-block tm-block-products">
            <div class="tm-product-table-container">
              <div class="form-group">
                <label for="categoryFilter">Filter by Category:</label>
                <select class="tm-product-table-container" id="categoryFilter">
                  <option value="all">All Categories</option>
                  <?php while ($category_row = mysqli_fetch_assoc($category_result)) { ?>
                    <option value="<?php echo $category_row['prod_category']; ?>">
                      <?php echo $category_row['prod_category']; ?>
                    </option>
                  <?php } ?>
                </select>
              </div>

              <?php if (isset($_GET['edit_success_message'])) { ?>
                <p class="text-center"><?php echo $_GET['edit_success_message']; ?></p>
              <?php } ?>
              <table class="table table-hover tm-table-small tm-product-table">
                <thead>
                  <tr>
                    <th scope="col">INDEX NUMBER</th>
                    <th scope="col">PRODUCT IMAGE</th>
                    <th scope="col">CATEGORY</th>
                    <th scope="col">PRODUCT NAME</th>
                    <th scope="col">PRODUCT PRICE</th>
                    <th scope="col">ACTION</th>
                  </tr>
                </thead>
                <tbody id="productTableBody">
                  <?php
                  $num = 1;
                  if (mysqli_num_rows($display_product) > 0) {
                    while ($row = mysqli_fetch_assoc($display_product)) {
                  ?>
                      <tr class="product-row" data-category="<?php echo $row['prod_category']; ?>">
                        <td class="tm-name"><?php echo $num; ?></td>
                        <td><img src="../assets/images/product/<?php echo $row['prod_image']; ?>" alt="<?php echo $row['prod_name']; ?>" style="max-width: 100px; max-height: 100px;"></td>
                        <td class="tm-name"><?php echo $row['prod_category']; ?></td>
                        <td class="tm-name"><?php echo $row['prod_name']; ?></td>
                        <td>RM <?php echo $row['prod_price']; ?></td>
                        <td>
                        <a href="products.php?delete=<?php echo $row['prod_id']; ?>" class="delete_product_btn" style="color: #E72463;" onclick="return confirm('Are you sure to delete this product?');"><i class="fas fa-trash"></i></a>
                          <a href="edit-product.php?prod_id=<?php echo $row['prod_id']; ?>" class="update_product_btn" style="color: #E72463;"><i class="fas fa-edit"></i></a>
                        </td>
                      </tr>
                  <?php
                      $num++;
                    }
                  } else {
                    echo "<div class='empty_text'>No products available</div>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
            
            <a href="add-product.php" class="btn btn-primary btn-block text-uppercase mb-3">Add new product</a>
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

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
      // JavaScript to handle category filter
      document.getElementById('categoryFilter').addEventListener('change', function() {
        var selectedCategory = this.value;
        var productRows = document.querySelectorAll('.product-row');
        
        productRows.forEach(function(row) {
          if (selectedCategory === 'all' || row.getAttribute('data-category') === selectedCategory) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });
      });
    </script>
  </body>
</html>
