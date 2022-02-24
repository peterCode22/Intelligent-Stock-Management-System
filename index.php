<?php
// Initialize the session
session_start();
 
// Include config file
require_once "config.php";

if(isset($_SESSION['orderPlaced'])){
    $message = "Order placed successfully!";
    echo "<script type='text/javascript'>alert('$message');</script>";
    unset($_SESSION['orderPlaced']);
}

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $searched = $_POST['productID'];
    if (!isset($_SESSION['basket'])){
        $_SESSION['basket'] = array();
        $_SESSION['basketValue'] = 0;
    }
    $key = array_search($searched, array_column($_SESSION['basket'], 'ProdID'));
    if ($key === false){
        $newBasketEntry = array(
            'ProdID'=>$_POST['productID'],
            'ProdName'=>$_POST['productName'],
            'Quantity'=>$_POST['quant'],
            'Price'=>$_POST['productPrice'],
            'Value'=>$_POST['productPrice'] * $_POST['quant']);
        $_SESSION['basket'][] = $newBasketEntry;
        $_SESSION['basketValue'] += $newBasketEntry['Value'];
    } else{
        $_SESSION['basket'][$key]['Quantity'] += $_POST['quant'];
        $_SESSION['basket'][$key]['Value'] = $_SESSION['basket'][$key]['Quantity'] * $_SESSION['basket'][$key]['Price'];
        $_SESSION['basketValue'] += $newBasketEntry['Value'];
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
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
        table tr td:last-child{
            width: 120px;
        }
		input[type='number']{
			width: 70px;
		} 
		a[id='login'] {
			font-size: 30px;
			position:absolute;
			top:0;
			right:0;
			margin-right: 20px;
			margin-top: 10px;
		}
		
		a[id='logout'] {
			font-size: 30px;
			position:absolute;
			top:0;
			right:0;
			margin-right: 20px;
			margin-top: 10px;
		}
		
		a[id='basket'] {
			font-size: 30px;
			position:absolute;
			top:0;
			left:0;
			margin-left: 20px;
			margin-top: 10px;
		}

        .loginButton {
            box-shadow:inset 0px 1px 0px 0px #54a3f7;
            background:linear-gradient(to bottom, #007dc1 5%, #0061a7 100%);
            background-color:#007dc1;
            border-radius:6px;
            border:1px solid #124d77;
            display:inline-block;
            cursor:pointer;
            color:#ffffff;
            font-family:Arial;
            font-size:15px;
            padding:6px 24px;
            text-decoration:none;
            text-shadow:0px 1px 0px #154682;
        }
        .loginButton:hover {
            background:linear-gradient(to bottom, #0061a7 5%, #007dc1 100%);
            background-color:#0061a7;
        }
        .loginButton:active {
            position:relative;
            top:1px;
        }

        .logoffButton {
            box-shadow:inset 0px 1px 0px 0px #f7c5c0;
            background:linear-gradient(to bottom, #fc8d83 5%, #e4685d 100%);
            background-color:#fc8d83;
            border-radius:6px;
            border:1px solid #d83526;
            display:inline-block;
            cursor:pointer;
            color:#ffffff;
            font-family:Arial;
            font-size:15px;
            font-weight:bold;
            padding:6px 24px;
            text-decoration:none;
            text-shadow:0px 1px 0px #b23e35;
        }
        .logoffButton:hover {
            background:linear-gradient(to bottom, #e4685d 5%, #fc8d83 100%);
            background-color:#e4685d;
        }
        .logoffButton:active {
            position:relative;
            top:1px;
        }

    </style>
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
        });
    </script>
</head>
<body>
	<?php
	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
		echo '<a href="login.php" id = "login">
            <button class="loginButton">Login</button> 
            </a>';
	}
	else{
		echo '<a href="logout.php" id = "logout">
            <button class="logoffButton">Log out</button> 
            </a>';
	}
	echo '<a href="basket.php" id = "basket">
            <button class="loginButton">Basket</button> 
            </a>';
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
                                    echo "<tr>";
                                        echo "<td>" . $row['ProdName'] . "</td>";
                                        echo "<td> £" . number_format((float)$row['RetailPrice'], 2, '.', '') . "</td>";                                        
										echo "<td>";
											echo "<form name = basketAdd method=post>
												<input type=number min=1 max=" . $tempQuant . " name=quant class=form-control required>
												<input type=hidden name=productID value=" . $row['ProdID'] . " >
                                                <input type=hidden name=productName value=" . $row['ProdName'] . " >
                                                <input type=hidden name=productPrice value=" . $row['RetailPrice'] . " >
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