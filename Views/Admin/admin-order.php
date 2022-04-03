<?php

require_once "../../Config/loader.php";
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../../Actions/login.php");
    exit;
}

// Check if the user is a customer, else redirect to admin website
if($_SESSION["acc_type"] !== 'admin'){
	header("location: ../../index.php");
    exit;
}

// Processing basket form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST['newQuant'])){
        $newQuant = $_POST['newQuant'];
        $pid = $_POST['productID'];
        $_SESSION['adminBasket']->changeQuantity($pid, $newQuant);
    } else{
        if(isset($_POST['productID'])){
            $pid = $_POST['productID'];
            $_SESSION['adminBasket']->removeItem($pid);
        }
    }
}

?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order summary</title>
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
    <?php
            if(isset($_SESSION['adminBasket'])){
                if (!empty($_SESSION['adminBasket']->getContent())){
                    echo '<h1>Your current order:</h1>';
                    // Include config file
                    require_once "../../Config/config.php";
                    echo '<table class="table table-bordered table-striped">';
                        echo "<thead>";
                            echo "<tr>";
                                echo "<th>Name</th>";
                                echo "<th>Quantity</th>";
                                echo "<th>Price</th>";
                                echo "<th>Change/Remove</th>";
                                echo "<th>Value</th>";
                            echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                        foreach ($_SESSION['adminBasket']->getContent() as $item) {
                            echo "<tr>";
                                echo '<td>' . $item->getName(). '</td>';
                                echo '<td>' . $item->getQuantity() . '</td>';
                                echo '<td>';
                                    echo '£' . number_format((float)$item->getPrice(), 2, '.', '');
                                echo '</td>';
                                echo '<td>';
                                    echo '<form action="' . $_SERVER['PHP_SELF'] . '"name="orderChange" method="post">
                                        <input type=number min=1 name=newQuant style="display: inline;"class=form-control required>
                                        <input type=hidden name=productID value=' . $item->getID() . ' >                                
                                        <button type=submit name=changeQuantity>Change quantity</button>
                                        </form>';
                                    echo '<form action="' . $_SERVER['PHP_SELF'] . '"name="orderDelete" method="post">
                                        <input type=hidden name=productID value=' . $item->getID() . ' >
                                        <button type=submit name=orderDelSubmit>Delete item</button>
                                        </form>';
                                echo '</td>';
                                echo '<td>';
                                    echo '£' . number_format((float)$item->getValue(), 2, '.', '');
                                echo '</td>';
                            echo "</tr>";
                        }
                        echo "</tbody>";
                    echo "</table>";
                    echo '<h2>Order total value: <br> £' . number_format((float)$_SESSION['adminBasket']->getValue(), 2, '.', '') . '</h2>';
                    echo '<a href="../../Actions/Admin/place-order.php" id = "placeOrder" class="placeButton">Place order</a><br><br>';
                }
                else{
                    echo '<h1>There are currently no items on your order.</h1>';
                }
            }
            else{
                echo '<h1>There are currently no items on your order.</h1>';
            }
            echo '<a href="stock-order.php" id = "return" class="returnButton">Return</a>';
        ?>

    

</body>
</html>