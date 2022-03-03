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
$prodName = "";
$retailPrice = $supplierPrice = 0;
$prodNameErr = $retailPriceErr = $supplierPriceErr = "";
 
// Processing form data when form is submitted
if(isset($_POST["pid"]) && !empty($_POST["pid"])){
    // Get hidden input value
    $pid = $_POST["pid"];
    
    // Validate name
    if (!empty($_POST["prName"])){
        $prodName = trim($_POST["prName"]);
        $sql = "UPDATE products SET ProdName = ? WHERE ProdID = ?";
        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("si", $paramName, $paramPID);
            
            // Set parameters
            $paramName = $prodName;
            $paramPID = $pid;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Records updated successfully. 
            } else{
                header("location: error.php");
                exit();
            }
        }
        $stmt->close();
    }

    // Validate retail price
    if (!empty($_POST["rPrice"])){
        $inputRPrice = trim($_POST["rPrice"]);
        if(!ctype_digit($inputRPrice) && $inputRPrice == 0){
            $retailPriceErr = "Please enter a positive integer value.";
        } else{
            $retailPrice = $inputRPrice;
            $sql = "UPDATE products SET RetailPrice = ? WHERE ProdID = ?";
            if($stmt = $mysqli->prepare($sql)){
                // Bind variables to the prepared statement as parameters
                $stmt->bind_param("di", $paramRPrice, $paramPID);
                
                // Set parameters
                $paramRPrice = $retailPrice;
                $paramPID = $pid;
                
                // Attempt to execute the prepared statement
                if($stmt->execute()){
                    // Records updated successfully. 
                } else{
                    header("location: error.php");
                    exit();
                }
            }
            $stmt->close();
        }
    }

    // Validate supplier price
    if (!empty($_POST["supPrice"])){
        $inputSPrice = trim($_POST["supPrice"]);
        if(!ctype_digit($inputSPrice) && $inputSPrice == 0){
            $supplierPriceErr = "Please enter a positive integer value.";
        } else{
            $supplierPrice = $inputSPrice;
            $sql = "UPDATE products SET SupplierPrice = ? WHERE ProdID = ?";
            if($stmt = $mysqli->prepare($sql)){
                // Bind variables to the prepared statement as parameters
                $stmt->bind_param("di", $paramSPrice, $paramPID);
                
                // Set parameters
                $paramSPrice = $supplierPrice;
                $paramPID = $pid;
                
                // Attempt to execute the prepared statement
                if($stmt->execute()){
                    // Records updated successfully. 
                } else{
                    header("location: error.php");
                    exit();
                }
            }
            $stmt->close();
        }
    }



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
                
                if($result->num_rows == 0){
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
    <title>Update Product</title>
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
                    <h2 class="mt-5">Update Product Details</h2>
                    <h3>Current details:</h3>
                    <?php
                        $sql = "SELECT * FROM products WHERE ProdID = ?";
                        if($stmt = $mysqli->prepare($sql)){
                            // Bind variables to the prepared statement as parameters
                            $stmt->bind_param("i", $paramPID);
                            
                            // Set parameters
                            $pid =  trim($_GET["id"]);
                            $paramPID = $pid;
                            
                            // Attempt to execute the prepared statement
                            if($stmt->execute()){
                                $result = $stmt->get_result(); 
                                $row = $result->fetch_array(MYSQLI_ASSOC);
                            }
                        }
                        $stmt->close();
                        echo '<table class="table table-bordered table-striped">';
                        echo "<thead>";
                              echo '<tr style="background-color:#c9dfca">';
                                    echo "<th>Product ID</th>";
                                    echo "<th>Product Name</th>";
                                    echo "<th>Retail Price</th>";
                                    echo "<th>Supplier Price</th>";
                            echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                            echo '<tr>';
                                echo "<td>" . $row['ProdID'] . "</td>";
                                echo "<td>" . $row['ProdName'] . "</td>";
                                echo "<td>" . $row['RetailPrice'] . "</td>";
                                echo "<td>" . $row['SupplierPrice'] . "</td>";
                            echo "</tr>";
                        echo "</tbody>";
                    echo "</table>";
        
                    // Close connection
                    $mysqli->close();
                    ?>
                    <p>Please edit the input values and submit to update the product.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
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
                        <input type="hidden" name="pid" value="<?php echo $pid; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="product-management.php" class="btn btn-secondary ml-2">Return</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>