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
$prodName = "";
$retailPrice = $supplierPrice = 0;
$prodNameErr = $retailPriceErr = $supplierPriceErr = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate name
    $inputName = trim($_POST["prName"]);
    if(empty($inputName)){
        $name_err = "Please enter a name.";
    } else{
        $prodName = $inputName;
    }

    // Validate retail price
    $inputRPrice = trim($_POST["rPrice"]);
    if (empty($inputRPrice)){
        $retailPriceErr = "Please enter a product's retail price.";
    } else if(!ctype_digit($inputRPrice) && $inputRPrice == 0){
        $retailPriceErr = "Please enter a positive integer value.";
    } else{
        $retailPrice = $inputRPrice;
    }

    // Validate supplier price
    $inputSPrice = trim($_POST["supPrice"]);
    if (empty($inputSPrice)){
        $supplierPriceErr = "Please enter a product's supplier price.";
    } else if(!ctype_digit($inputSPrice) && $inputSPrice == 0){
        $supplierPriceErr = "Please enter a positive integer value.";
    } else{
        $supplierPrice = $inputSPrice;
    }
    
    // Check input errors before inserting in database
    if(empty($prodNameErr) && empty($retailPriceErr) && empty($supplierPriceErr)){
        // Prepare an insert statement
        $sql = "INSERT INTO products (ProdName, RetailPrice, SupplierPrice) VALUES (?, ?, ?)";
 
        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("sdd", $prodName, $retailPrice, $supplierPrice);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Records created successfully. Redirect to landing page
                header("location: ../../Views/Admin/product-management.php");
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
}
?>
 
 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Product</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../style.css">
</head>
<body>
    <div class="topnav">
        <a href="../../Views/Admin/admin-page.php">Home</a>
        <a href="../../Views/Admin/stock-count.php">Stock count</a>
        <a href="../../Views/Admin/reports.php">Reports</a>
        <a class="active" href="../../Views/Admin/product-management.php">Product management</a>
        <a href="../../Views/Admin/stock-order.php">Order stock</a>
        <a href="../../Views/Admin/pred-config.php">Prediction settings</a>
    </div>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Create a product.</h2>
                    <p>Please fill this form and submit to add a new product.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                            <label>Product Name</label>
                            <input type="text" name="prName" class="form-control <?php echo (!empty($prodNameErr)) ? 'is-invalid' : ''; ?>" value="<?php echo $prodName; ?>">
                            <span class="invalid-feedback"><?php echo $prodNameErr;?></span>
                        </div>
                        <div class="form-group">
                            <label>Retail Price</label>
                            <input type="text" name="rPrice" class="form-control <?php echo (!empty($retailPriceErr)) ? 'is-invalid' : ''; ?>" value="<?php echo $retailPrice; ?>">
                            <span class="invalid-feedback"><?php echo $retailPriceErr;?></span>
                        </div>
                        <div class="form-group">
                            <label>Supplier Price</label>
                            <input type="text" name="supPrice" class="form-control <?php echo (!empty($supplierPriceErr)) ? 'is-invalid' : ''; ?>" value="<?php echo $supplierPrice; ?>">
                            <span class="invalid-feedback"><?php echo $supplierPriceErr;?></span>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="../../Views/Admin/product-management.php" class="btn btn-secondary ml-2">Return</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
