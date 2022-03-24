<?php

require_once "loader.php";

// Initialize the session
session_start();
 
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

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST['newQuant'])){
        $newQuant = $_POST['newQuant'];
        $pid = $_POST['productID'];
        $_SESSION['basket']->changeQuantity($pid, $newQuant);
    } else{
        if(isset($_POST['productID'])){
            $pid = $_POST['productID'];
            $_SESSION['basket']->removeItem($pid);
        }
    }
}


?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Basket</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
        echo '<a href="index.php" id = "return">
            <button class="returnButton">Return</button> 
            </a>';
    ?>
    <h1 class="my-5">Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>.
        <?php 
            if(isset($_SESSION['basket'])){
                if (!empty($_SESSION['basket']->getContent())){
                    echo 'Your current basket:</h1>';
                    // Include config file
                    require_once "config.php";
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
                        foreach ($_SESSION['basket']->getContent() as $data) {
                            echo "<tr>";
                                echo '<td>' . $data->getName(). '</td>';
                                echo '<td>' . $data->getQuantity() . '</td>';
                                echo '<td>';
                                    echo '£' . number_format((float)$data->getPrice(), 2, '.', '');
                                echo '</td>';
                                echo '<td>';
                                    echo '<form action="' . $_SERVER['PHP_SELF'] . '"name="basketChange" method="post">
                                        <input type=number min=1 name=newQuant style="display: inline;"class=form-control required>
                                        <input type=hidden name=productID value=' . $data->getID() . ' >                                
                                        <button type=submit name=changeQuantity>Change quantity</button>
                                        </form>' . '<form action="' . $_SERVER['PHP_SELF'] . '"name="basketDelete" method="post">
                                        <input type=hidden name=productID value=' . $data->getID() . ' >
                                        <button type=submit name=basketDelSubmit>Delete item</button>
                                        </form>';
                                echo '</td>';
                                echo '<td>';
                                    echo '£' . number_format((float)$data->getValue(), 2, '.', '');
                                echo '</td>';
                            echo "</tr>";
                        }
                        echo "</tbody>";
                    echo "</table>";
                    echo '<h2>Basket total value: <br> £' . number_format((float)$_SESSION['basket']->getValue(), 2, '.', '') . '</h2>';
                    echo '<a href="checkout.php" id = "checkout">
                        <button class="checkoutButton">Place order</button> 
                        </a><br>';
                    echo 'Place order button will place an order on current address.';
                }
                else{
                    echo '<h1>Your basket is currently empty.</h1>';
                }
            }
            else{
                echo '<h1>Your basket is currently empty.</h1>';
            }
        ?>

    

</body>
</html>