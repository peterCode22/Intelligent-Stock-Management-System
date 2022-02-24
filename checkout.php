<?php
// Initialize the session
session_start();

// Include config file
require_once "config.php";
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Check if the user is a customer, else redirect to admin website
if($_SESSION["acc_type"] !== 'customer'){
	header("location: admin-page.php");
    exit;
}

if(!isset($_SESSION['basket'])){
    header("location: index.php");
    exit;
}

if($_SESSION['basketValue'] == 0){
    header("location: index.php");
    exit;
}

$orderTime = date('Y-m-d H:i:s'); 
$sql = "INSERT INTO customer_orders (orderDate, customerID) VALUES (?,?)";
if($stmt = $mysqli->prepare($sql)){
    // Bind variables to the prepared statement as parameters
    $stmt->bind_param("si", $orderTime, $_SESSION['id']);
    
    // Attempt to execute the prepared statement
    $stmt->execute();
    $orderID = $stmt->insert_id;
    foreach ($_SESSION['basket'] as $data) {
        $interSql = "INSERT INTO order_products VALUES (?,?,?)";
        $stmt = $mysqli->prepare($interSql);
        $stmt->bind_param("iii", $orderID, $data['ProdID'], $data['Quantity']);
        $stmt->execute();
    }
    $_SESSION['basket'] = array();
    $_SESSION['basketValue'] = 0;
    $_SESSION['orderPlaced'] = True;
    header("location: index.php");
    exit();
   
}

?>

