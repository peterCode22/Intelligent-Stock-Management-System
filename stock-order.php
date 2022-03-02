<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Check if the user is an admin, else redirect to user website
if(!isset($_SESSION["acc_type"]) || $_SESSION["acc_type"] !== 'admin'){
	header("location: index.php");
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
    $searched = $_POST['productID'];
    if (!isset($_SESSION['adminOrder'])){
        $_SESSION['adminOrder'] = array();
        $_SESSION['orderValue'] = 0;
    }
    $key = array_search($searched, array_column($_SESSION['adminOrder'], 'ProdID'));
    if ($key === false){
        $newOrderEntry = array(
            'ProdID'=>$_POST['productID'],
            'ProdName'=>$_POST['productName'],
            'Quantity'=>$_POST['quant'],
            'Price'=>$_POST['productPrice'],
            'Value'=>$_POST['productPrice'] * $_POST['quant']);
        $_SESSION['adminOrder'][] = $newOrderEntry;
        $_SESSION['orderValue'] += $newOrderEntry['Value'];
    } else{
        $_SESSION['adminOrder'][$key]['Quantity'] += $_POST['quant'];
        $_SESSION['adminOrder'][$key]['Value'] = $_SESSION['adminOrder'][$key]['Quantity'] * $_SESSION['adminOrder'][$key]['Price'];
        $_SESSION['orderValue'] += $newOrderEntry['Value'];
    }
	//Check if the user is already logged in, if no then redirect to login page
	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
		header("location: login.php");
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
<style>
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

a[id='adminOrder'] {
	font-size: 30px;
	position:absolute;
	top:0;
	right:0;
	margin-right: 20px;
	margin-top: 10px;
}

a[id='autoOrder'] {
	font-size: 30px;
	position:absolute;
	top:0;
	left:0;
	margin-left: 20px;
	margin-top: 10px;
}

</style>
</head>
<body>

<div class="topnav">
    <a href="admin-page.php">Home</a>
    <a href="stock-count.php">Stock count</a>
    <a href="sales.php">Sales summary</a>
    <a href="product-management.php">Product management</a>
    <a class="active" href="stock-order.php">Order stock</a>
</div>
<div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    
                    <?php
                    // Include config file
                    require_once "config.php";
                    echo '<a href="admin-order.php" id = "adminOrder">
                        <button class="loginButton">Order summary</button> 
                        </a>';
                    echo '<a href="auto-order.php" id = "autoOrder">
                        <button class="loginButton">Suggested order</button> 
                        </a>';
                    echo '<br>';
                    echo '<h2 class="mt-5">Place an order.</h2>
                    <p>Please fill this form and submit to order stock.</p>';

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
