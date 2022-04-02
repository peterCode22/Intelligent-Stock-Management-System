<?php
// Initialize the session
session_start();

// Include config file
require_once "../../Config/config.php";
 
// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

// Check if the user is an admin, else redirect to user website
if (!isset($_SESSION["acc_type"]) || $_SESSION["acc_type"] !== 'admin') {
    header("location: ../../index.php");
    exit;
}

// Define variables and initialize with empty values
$quantity = 0;
$quantityErr = "";
 
// Processing form data when form is submitted
if(isset($_POST["bid"]) && !empty($_POST["bid"])){
    // Get hidden input value
    $bid = $_POST["bid"];
    // Validate quantity
    $inputQuantity = trim($_POST["quantForm"]);
    if(empty($inputQuantity)){
        $quantityErr = "Please enter the desired quantity.";     
    } elseif(!ctype_digit($inputQuantity)){
        $quantityErr = "Please enter a positive integer value.";
    } else{
        $quantity = $inputQuantity;
    }
        
    // Check input errors before inserting in database
    if(empty($quantityErr)){
        // Prepare an update statement
        $sql = "UPDATE batches SET Quantity=? WHERE BatchID=?";
    
        if($stmt = $mysqli->prepare($sql)){
             // Bind variables to the prepared statement as parameters
             $stmt->bind_param("ii", $paramQuantity, $paramBID);
                
            // Set parameters
            $paramQuantity = $quantity;
            $paramBID = $bid;
                
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Records updated successfully. Redirect to landing page
                header("location: ../../Views/Admin/stock-count.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
            
            // Close statement
        $stmt->close();
    }
    // Close connection
    $mysqli->close();

} else{
      // Check existence of id parameter before processing further
      if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
          // Get URL parameter
          $bid =  trim($_GET["id"]);
          
          // Prepare a select statement
          $sql = "SELECT * FROM batches WHERE BatchID = ?";
          if($stmt = $mysqli->prepare($sql)){
              // Bind variables to the prepared statement as parameters
              $stmt->bind_param("i", $paramBID);
              
              // Set parameters
              $paramBID = $bid;
              
              // Attempt to execute the prepared statement
              if($stmt->execute()){
                  $result = $stmt->get_result();
                  
                  if($result->num_rows == 1){
                      /* Fetch result row as an associative array. Since the result set
                      contains only one row, we don't need to use while loop */
                      $row = $result->fetch_array(MYSQLI_ASSOC);

                      $productID = $row['ProdID'];
                  } else{
                      // URL doesn't contain valid id. Redirect to error page
                      header("location: ../../error.php");
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
          header("location: ../../error.php");
          exit();
      }
  }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Batch</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../style.css">
</head>
<body>
    <div class="topnav">
        <a href="../../Views/Admin/admin-page.php">Home</a>
        <a class="active" href="../../Views/Admin/stock-count.php">Stock count</a>
        <a href="../../Views/Admin/reports.php">Reports</a>
        <a href="../../Views/Admin/product-management.php">Product management</a>
        <a href="../../Views/Admin/stock-order.php">Order stock</a>
        <a href="../../Views/Admin/pred-config.php">Prediction settings</a>
    </div>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Update Batch</h2>
                    <p>Update the batch quantity of product ID ( <?php echo $productID; ?> ).</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group">
                            <label>Quantity</label>
                            <input type="number" name="quantForm" class="form-control <?php echo (!empty($quantityErr)) ? 'is-invalid' : ''; ?>" value="<?php echo $quantity; ?>">
                            <span class="invalid-feedback"><?php echo $quantityErr;?></span>
                        </div>
                        <input type="hidden" name="bid" value="<?php echo $bid; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="../../Views/Admin/stock-count.php" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
