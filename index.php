<?php

// Include database config file
require_once "config.php";

require_once "loader.php";

// Initialize the session
session_start();


if(isset($_SESSION['orderPlaced'])){
    $message = "Order placed successfully!";
    echo "<script type='text/javascript'>alert('$message');</script>";
    unset($_SESSION['orderPlaced']);
}

if(isset($_SESSION['acc_type'])){
    if($_SESSION["acc_type"] !== 'customer'){
        header("location: admin-page.php");
        exit;
    }
}

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $pid = intval($_POST['productID']);
    $name = $_POST['productName'];
    $quantity = intval($_POST['quant']);
    $price = floatval($_POST['productPrice']);

    if (!isset($_SESSION['basket'])){
        $_SESSION['basket'] = new Basket();
    }

    if ($_SESSION['basket']->itemExists($pid)){
        $_SESSION['basket']->addQuantity($pid, $quantity);
    } else{
        $_SESSION['basket']->addItem($pid, $name, $price, $quantity);
    }
	//Check if the user is already logged in, if no then redirect to login page
	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
		header("location: login.php");
		exit;
	}
	else{
		header("location: basket.php");
		exit;
	}
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
	<?php
	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
		echo '<a href="login.php" id = "login" class="loginButton">Login</a>';
	}
	else{
		echo '<a href="logout.php" id = "logout" class="logoutButton">Log out</a>';
	}
	echo '<a href="basket.php" id = "basket" class="loginButton">Basket</a>';
    
    ?>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="mt-5 mb-3 clearfix">
                        <h2 class="pull-left">Products </h2>
                    </div>
					
                    <?php
                    // Include config file
                    require_once "config.php";
                    
                    // Attempt select query execution
                    $sql = "SELECT * FROM customer_product_view WHERE Quantity > 0";
                    if($result = $mysqli->query($sql)){
                        if($result->num_rows > 0){
                            echo '<table class="table table-bordered table-striped">';
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>Name</th>";
                                        echo "<th>Price</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = $result->fetch_array()){
									$tempQuant = $row['Quantity'];
                                    $tempName = $row['ProdName'];
                                    $tempPrice = $row['RetailPrice'];
                                    $tempID = $row['ProdID'];
                                    echo "<tr>";
                                        echo "<td>" . $row['ProdName'] . "</td>";
                                        echo "<td> £" . number_format((float)$row['RetailPrice'], 2, '.', '') . "</td>";                                        
										echo "<td>";
											echo "<form name = basketAdd method=post>
												<input type=number min=1 max='$tempQuant' name=quant class=form-control required>
												<input type=hidden name=productID value='$tempID' >
                                                <input type=hidden name=productName value='$tempName'>
                                                <input type=hidden name=productPrice value='$tempPrice'>
												<button type=submit name=addToBasket>Add to basket</button>
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