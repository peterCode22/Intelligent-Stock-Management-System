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

// Define variables and initialize with empty values
$prodName = $prodType = $priceUnit = "";
$lifeTime = $retailPrice = $supplierPrice = 0;
$prodNameErr = $prodTypeErr = $priceUnitErr = $lifeTimeErr = $retailPriceErr = $supplierPriceErr = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate name
    $inputName = trim($_POST["prName"]);
    if(empty($inputName)){
        $name_err = "Please enter a name.";
    } else{
        $prodName = $inputName;
    }
    
    // Validate product type
    $inputProdType = trim($_POST["prType"]);
    if (empty($inputProdType)){
        $prodTypeErr = "Please enter a product type (Ambient, Chilled etc.)";
    }else if(!filter_var($inputProdType, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $prodTypeErr = "Please enter a valid product type.";
    } else{
        $prodType = $inputProdType;
    }

    // Validate lifetime
    $inputLTime = trim($_POST["lTime"]);
    if (empty($inputLTime)){
        $lifeTimeErr = "Please enter a product's life time.";
    } else if(!ctype_digit($inputLTime) && $inputLTime == 0){
        $lifeTimeErr = "Please enter a positive integer value.";
    } else{
        $lifeTime = $inputLTime;
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

    // Validate price unit
    $inputUnit = trim($_POST["prUnit"]);
    if (empty($inputUnit)){
        $priceUnitErr = "Please enter a price unit (per kilogram, per piece etc.)";
    }else if(!filter_var($inputUnit, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $priceUnitErr = "Please enter a valid product type.";
    } else{
        $priceUnit = $inputUnit;
    }
    
    // Check input errors before inserting in database
    if(empty($prodNameErr) && empty($prodTypeErr) && empty($priceUnitErr) && empty($lifeTimeErr)
     && empty($retailPriceErr) && empty($supplierPriceErr)){
        // Prepare an insert statement
        $sql = "INSERT INTO products (ProdName, ProdType, Lifetime, RetailPrice, SupplierPrice, PriceUnit) VALUES (?, ?, ?, ?, ?, ?)";
 
        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ssidds", $prodName, $prodType, $lifeTime, $retailPrice, $supplierPrice, $priceUnit);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Records created successfully. Redirect to landing page
                header("location: product-management.php");
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
                            <label>Product Type</label>
                            <textarea name="prType" class="form-control <?php echo (!empty($prodTypeErr)) ? 'is-invalid' : ''; ?>"><?php echo $prodType; ?></textarea>
                            <span class="invalid-feedback"><?php echo $prodTypeErr;?></span>
                        </div>
                        <div class="form-group">
                            <label>Life time (Days)</label>
                            <input type="text" name="lTime" class="form-control <?php echo (!empty($lifeTimeErr)) ? 'is-invalid' : ''; ?>" value="<?php echo $lifeTime; ?>">
                            <span class="invalid-feedback"><?php echo $lifeTimeErr;?></span>
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
                        <div class="form-group">
                            <label>Price Unit</label>
                            <textarea name="prUnit" class="form-control <?php echo (!empty($priceUnitErr)) ? 'is-invalid' : ''; ?>"><?php echo $priceUnit; ?></textarea>
                            <span class="invalid-feedback"><?php echo $priceUnitErr;?></span>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="product-management.php" class="btn btn-secondary ml-2">Return</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
