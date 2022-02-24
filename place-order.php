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
if($_SESSION["acc_type"] !== 'admin'){
	header("location: index.php");
    exit;
}

if(!isset($_SESSION['adminOrder'])){
    header("location: stock-order.php");
    exit;
}

if($_SESSION['orderValue'] == 0){
    header("location: stock-order.php");
    exit;
}

$orderTime = date('Y-m-d H:i:s'); 
$sql = "INSERT INTO supplier_orders (DTime, managerID) VALUES (?,?)";
if($stmt = $mysqli->prepare($sql)){
    // Bind variables to the prepared statement as parameters
    $stmt->bind_param("si", $orderTime, $_SESSION['id']);
    
    // Attempt to execute the prepared statement
    $stmt->execute();
    $orderID = $stmt->insert_id;
    foreach ($_SESSION['adminOrder'] as $data) {
        $interSql = "INSERT INTO batches (ProdID, SuppOrdID, Quantity) VALUES (?,?,?)";
        $stmt = $mysqli->prepare($interSql);
        $stmt->bind_param("iii", $data['ProdID'], $orderID, $data['Quantity']);
        $stmt->execute();
    }
    $_SESSION['adminOrder'] = array();
    $_SESSION['orderValue'] = 0;
    $_SESSION['adminOrderPlaced'] = True;
    header("location: stock-order.php");
    exit();
   
}

?>

