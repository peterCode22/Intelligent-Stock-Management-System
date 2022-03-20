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

//Check if there is enough products to be sold
$quantSQL = "SELECT ProdID, Quantity FROM customer_product_view WHERE ProdID = ?";
foreach ($_SESSION['basket'] as $item){
    if ($stmt = $mysqli->prepare($quantSQL)){
        $stmt->bind_param("i", $item['ProdID']);
        if ($stmt->execute()){
            $result = $stmt->get_result();
            $currProd = $result->fetch_array(MYSQLI_ASSOC);
            if ($item['Quantity'] > $currProd['Quantity']){ //if there is not enough product in stock
                //throw error
                header("location: index.php");
                exit();
            }
        }
    }
    
        

}

//Record a customer order
$orderTime = date('Y-m-d H:i:s'); 
$sql = "INSERT INTO customer_orders (orderDate, customerID) VALUES (?,?)";
if($stmt = $mysqli->prepare($sql)){
    // Bind variables to the prepared statement as parameters
    $stmt->bind_param("si", $orderTime, $_SESSION['id']);
    
    // Attempt to execute the prepared statement
    if (!$stmt->execute()){
        //throw error
    }
    $orderID = $stmt->insert_id;
    foreach ($_SESSION['basket'] as $data) {
        $interSql = "INSERT INTO order_products VALUES (?,?,?)";
        $stmt = $mysqli->prepare($interSql);
        $stmt->bind_param("iii", $orderID, $data['ProdID'], $data['Quantity']);
        if (!$stmt->execute()){ //unsuccessful insertion into order_products
            //throw error
        }
    }
}

$sqlBatchUpd = "UPDATE batches SET Quantity=? WHERE BatchID=?";
$sqlBatchDel = "DELETE FROM batches WHERE BatchID = ?";
$sqlGetBatches = "SELECT BatchID, ProdID, Quantity FROM batches WHERE ProdID = ?";

foreach ($_SESSION['basket'] as $item){
    if ($stmt = $mysqli->prepare($sqlGetBatches)){
        $stmt->bind_param("i", $item['ProdID']);
        if($stmt->execute()){
            $result = $stmt->get_result();
            $quantToSell = $item['Quantity'];
            while ($quantToSell > 0 && $currRow = $result->fetch_array()){
                if ($currRow['Quantity'] > $quantToSell){
                    $newQuant = $currRow['Quantity'] - $quantToSell;
                    if ($stmt = $mysqli->prepare($sqlBatchUpd)){
                        $stmt->bind_param("ii", $newQuant, $currRow['BatchID']);
                        if ($stmt->execute()){
                            $quantToSell = 0;
                        }
                    }
                }
                else{
                    if ($stmt = $mysqli->prepare($sqlBatchDel)){
                        $stmt->bind_param("i", $currRow['BatchID']);
                        if($stmt->execute()){
                            $quantToSell -= $currRow['Quantity'];
                        }
                    }
                }
            } 
        }
    }

}

//record sales
$today = date('Y-m-d');
$sqlSales = "CALL record_sale(?, ?, ?)";
foreach ($_SESSION['basket'] as $item){
    if ($stmt = $mysqli->prepare($sqlSales)){
        $stmt->bind_param("isi", $item['ProdID'], $today, $item['Quantity']);
        if($stmt->execute()){
            //Sale recorded
            
        }
        else{
            //error
        }
    }
    else{
        //error
    }
}

echo 'passed';


$_SESSION['basket'] = array();
$_SESSION['basketValue'] = 0;
$_SESSION['orderPlaced'] = True;
header("location: index.php");
exit();

?>

