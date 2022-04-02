<?php

require_once "../../Config/loader.php";
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../../Actions/login.php");
    exit;
}

// Check if the user is an admin, else redirect to user website
if(!isset($_SESSION["acc_type"]) || $_SESSION["acc_type"] !== 'admin'){
	header("location: ../../index.php");
    exit;
}

if(isset($_SESSION['adminOrderPlaced'])){
    $message = "Order placed successfully!";
    echo "<script type='text/javascript'>alert('$message');</script>";
    unset($_SESSION['adminOrderPlaced']);
}

if(isset($_SESSION['noSugg'])){
    $message = "Current stock levels match the predictions.";
    echo "<script type='text/javascript'>alert('$message');</script>";
    unset($_SESSION['noSugg']);
}

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $pid = intval($_POST['productID']);
    $name = $_POST['productName'];
    $quantity = intval($_POST['quant']);
    $price = floatval($_POST['productPrice']);

    if (!isset($_SESSION['adminBasket'])){
        $_SESSION['adminBasket'] = new Basket();
    }

    if ($_SESSION['adminBasket']->itemExists($pid)){
        $_SESSION['adminBasket']->addQuantity($pid, $quantity);
    } else{
        $_SESSION['adminBasket']->addItem($pid, $name, $price, $quantity);
    }
	//Check if the user is already logged in, if no then redirect to login page
	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
		header("location: ../../Actions/login.php");
		exit;
	}
	else{
		header("location: admin-order.php");
		exit;
	}
}

?>
 
<!DOCTYPE html>
<html>
<head>
<title>Order stock</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="../../style.css">
</head>
<body>

<div class="topnav">
    <a href="admin-page.php">Home</a>
    <a href="stock-count.php">Stock count</a>
    <a href="reports.php">Reports</a>
    <a href="product-management.php">Product management</a>
    <a class="active" href="stock-order.php">Order stock</a>
    <a href="pred-config.php">Prediction settings</a>
</div>

<a href= "admin-order.php" id = "adminOrder" class= "loginButton" >Order summary</a>
<a href= "../../Actions/Admin/auto-order.php" id = "autoOrder" class= "loginButton" >Suggested Order</a>
<br>
<h2 class="mt-5">Place an order.</h2>
<p>Please fill this form and submit to order stock.</p>
<div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    
                    <?php
                    // Include config file
                    require_once "../../Config/config.php";

                    // Attempt select query execution
                    $sql = "SELECT * FROM products";
                    if($result = $mysqli->query($sql)){
                        if($result->num_rows > 0){
                            echo '<table class="table table-bordered table-striped">';
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>Product ID</th>";
                                        echo "<th>Product Name</th>";
                                        echo "<th>Supplier Price</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = $result->fetch_array()){
                                    echo "<tr>";
                                        echo "<td>" . $row['ProdID'] . "</td>";
                                        echo "<td>" . $row['ProdName'] . "</td>";
                                        echo "<td> £" . number_format((float)$row['SupplierPrice'], 2, '.', '') . "</td>";                                        
										echo "<td>";
											echo "<form name = orderAdd method=post>
												<input type=number min=1 name=quant class=form-control required>
												<input type=hidden name=productID value=" . $row['ProdID'] . " >
                                                <input type=hidden name=productName value=" . $row['ProdName'] . " >
                                                <input type=hidden name=productPrice value=" . $row['SupplierPrice'] . " >
												<button type=submit name=addToOrder>Add to order</button>
												</form>";
                                        echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            // Free result set
                            $result->free();
                        } else{
                            echo '<div class="alert alert-danger"><em>No records were found.</em></div>';
                        }
                    } else{
                        echo "Oops! Something went wrong. Please try again later.";
                    }
                    
                    // Close connection
                    $mysqli->close();
                    ?>
                </div>
            </div>        
        </div>
    </div>

</body>
</html>
