<?php
// Initialize the session
session_start();
 
require_once "config.php";

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

$_SESSION['adminOrder'] = array();
$_SESSION['orderValue'] = 0;


// Attempt select query execution
$sql = "SELECT ProdID, ProdName, SupplierPrice FROM products";

$output = shell_exec("python python/order.py");
$prediction = json_decode($output, TRUE);

if($result = $mysqli->query($sql)){
    if($result->num_rows > 0){
        while($row = $result->fetch_array()){
            $tempID = $row['ProdID'];
            $tempPred = $prediction[$tempID];

            if ($tempPred > 0) {
                $newOrderEntry = array(
                    'ProdID'=>intval($tempID),
                    'ProdName'=>$row['ProdName'],
                    'Quantity'=>intval($tempPred),
                    'Price'=>floatval($row['SupplierPrice']),
                    'Value'=>floatval($row['SupplierPrice']) * $tempPred);
                $_SESSION['adminOrder'][] = $newOrderEntry;
                $_SESSION['orderValue'] += $newOrderEntry['Value'];
            }
        }   
        $result->free();
    } else{
        echo "Oops! Something went wrong. Please try again later.";
    }
} else{
    echo "Oops! Something went wrong. Please try again later.";
}

if (empty($_SESSION['adminOrder'])){
    $_SESSION['noSugg'] = True;
    header("location: stock-order.php");
    exit;
}

// Close connection
$mysqli->close();

header("location: admin-order.php");
exit;

?>