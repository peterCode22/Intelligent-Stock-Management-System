<?php

// Initialize the session
session_start();

// Include config file
require_once "config.php";
 
// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Check if the user is an admin, else redirect to user website
if (!isset($_SESSION["acc_type"]) || $_SESSION["acc_type"] !== 'admin') {
    header("location: index.php");
    exit;
}

// Process delete operation after confirmation
if(isset($_POST["pid"]) && !empty($_POST["pid"])){
    // Include config file
    require_once "config.php";
    
    // Prepare a delete statement
    $sql = "DELETE FROM products WHERE ProdID = ?";
    
    if($stmt = $mysqli->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("i", $paramPID);
        
        // Set parameters
        $paramPID = trim($_POST["pid"]);
        
        // Attempt to execute the prepared statement
        if($stmt->execute()){
            // Records deleted successfully. Redirect to landing page
            header("location: product-management.php");
            exit();
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
     
    // Close statement
    $stmt->close();
    
    // Close connection
    $mysqli->close();
} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        // Get URL parameter
        $pid =  trim($_GET["id"]);
      
        // Prepare a select statement
        $sql = "SELECT * FROM products WHERE ProdID = ?";
        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("i", $paramPID);
          
            // Set parameters
            $paramPID = $pid;
          
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                $result = $stmt->get_result();
              
                if($result->num_rows == 1){
                    /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                    $row = $result->fetch_array(MYSQLI_ASSOC);

                    $productID = $row['ProdID'];
                    $productName = $row['ProdName'];
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
      
      // Close statement
      $stmt->close();
      
      // Close connection
      $mysqli->close();
  }  else{
      // URL doesn't contain id parameter. Redirect to error page
      header("location: error.php");
      exit();
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        .topnav {
            overflow: hidden;
            background-color: #333;
        }

        .topnav a {
            float: left;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
            font-size: 17px;
        }

        .topnav a:hover {
            background-color: #ddd;
            color: black;
        }

        .topnav a.active {
            background-color: #04AA6D;
            color: white;
        }
    </style>
</head>
<body>
    <div class="topnav">
        <a href="admin-page.php">Home</a>
        <a href="stock-count.php">Stock count</a>
        <a href="sales.php">Sales summary</a>
        <a class="active" href="product-management.php">Product management</a>
        <a href="stock-order.php">Order stock</a>
        <a href="pred-config.php">Prediction settings</a>
    </div>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5 mb-3">Delete a product</h2>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="alert alert-danger">
                            <input type="hidden" name="pid" value="<?php echo trim($_GET["id"]); ?>"/>
                            <p>Are you sure you want to delete this product <?php echo $productName; ?> ( <?php echo $productID; ?> )  ?</p>
                            <p>
                                <input type="submit" value="Yes" class="btn btn-danger">
                                <a href="product-management.php" class="btn btn-secondary ml-2">No</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>