<?php

require_once "../../Config/loader.php";

// Initialize the session
session_start();

// Include config file
require_once "../../Config/config.php";
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}

// Check if the user is a customer, else redirect to admin website
if($_SESSION["acc_type"] !== 'admin'){
	header("location: ../../index.php");
    exit;
}

if(!isset($_SESSION['adminBasket'])){
    header("location: ../../Views/Admin/stock-order.php");
    exit;
}

$orderTime = date('Y-m-d H:i:s'); 
$sql = "INSERT INTO supplier_orders (DTime, managerID) VALUES (?,?)";
if($stmt = $mysqli->prepare($sql)){
    $stmt->bind_param("si", $orderTime, $_SESSION['id']);
    $stmt->execute();
    $orderID = $stmt->insert_id;
    foreach ($_SESSION['adminBasket']->getContent() as $item) {
        $interSql = "INSERT INTO batches (ProdID, SuppOrdID, Quantity) VALUES (?,?,?)";
        $stmt = $mysqli->prepare($interSql);
        $stmt->bind_param("iii", $item->getID(), $orderID, $item->getQuantity());
        $stmt->execute();
    }
    unset($_SESSION['adminBasket']);
    $_SESSION['adminOrderPlaced'] = True;
    header("location: ../../Views/Admin/stock-order.php");
    exit();
   
}

?>

