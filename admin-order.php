<?php
// Initialize the session
session_start();
 
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

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST['productID']) && isset($_POST['newQuant'])){
        $_POST['productID'] = intval($_POST['productID']);
        $_POST['newQuant'] = intval($_POST['newQuant']);
        if (($key = array_search($_POST['productID'], array_column($_SESSION['adminOrder'], 'ProdID'))) !== false){
            $oldQuant = $_SESSION['adminOrder'][$key]['Quantity'];
            $_SESSION['adminOrder'][$key]['Quantity'] = $_POST['newQuant'];
            $_SESSION['adminOrder'][$key]['Value'] = $_SESSION['adminOrder'][$key]['Quantity'] * $_SESSION['adminOrder'][$key]['Price'];
            if ($_POST['newQuant'] >= $oldQuant){
                $_SESSION['orderValue'] += ($_POST['newQuant'] - $oldQuant) * $_SESSION['adminOrder'][$key]['Price'];
            } else{
                $_SESSION['orderValue'] -= ($oldQuant - $_POST['newQuant']) * $_SESSION['adminOrder'][$key]['Price'];
            }
        }
    }
    else if(isset($_POST['prodID'])){
        $_POST['prodID'] = intval($_POST['prodID']);
        $key = array_search($_POST['prodID'], array_column($_SESSION['adminOrder'], 'ProdID'));
        if ($key !== false){
            $oldQuant = $_SESSION['adminOrder'][$key]['Quantity'];
            $_SESSION['orderValue'] -= $oldQuant * $_SESSION['adminOrder'][$key]['Price'];
            unset($_SESSION['adminOrder'][$key]);
            $_SESSION['adminOrder'] = array_values($_SESSION['adminOrder']);
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
    <style>
        body{ font: 14px sans-serif; text-align: center; }
        input[type="number"] {
            width:100px;
        }
            
        .returnButton {
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
        .returnButton:hover {
            background:linear-gradient(to bottom, #0061a7 5%, #007dc1 100%);
            background-color:#0061a7;
        }
        .returnButton:active {
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

        .checkoutButton {
            box-shadow:inset 0px 1px 0px 0px #fce2c1;
            background:linear-gradient(to bottom, #ffc477 5%, #fb9e25 100%);
            background-color:#ffc477;
            border-radius:6px;
            border:1px solid #eeb44f;
            display:inline-block;
            cursor:pointer;
            color:#ffffff;
            font-family:Arial;
            font-size:15px;
            font-weight:bold;
            padding:6px 24px;
            text-decoration:none;
            text-shadow:0px 1px 0px #cc9f52;
        }
        .checkoutButton:hover {
            background:linear-gradient(to bottom, #fb9e25 5%, #ffc477 100%);
            background-color:#fb9e25;
        }
        .checkoutButton:active {
            position:relative;
            top:1px;
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

    </style>
</head>
<body>
<div class="topnav">
    <a href="admin-page.php">Home</a>
    <a href="stock-count.php">Stock count</a>
    <a href="sales.php">Sales summary</a>
    <a href="product-management.php">Product management</a>
    <a class="active" href="stock-order.php">Order stock</a>
    <a href="pred-config.php">Prediction settings</a>
</div>
    <?php
            if(isset($_SESSION['adminOrder'])){
                if (!empty($_SESSION['adminOrder'])){
                    echo '<h1>Your current order:</h1>';
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
                        foreach ($_SESSION['adminOrder'] as $data) {
                            if ($data['Quantity'] > 0){
                            echo "<tr>";
                                echo '<td>' . $data['ProdName'] . '</td>';
                                echo '<td>' . $data['Quantity'] . '</td>';
                                echo '<td>';
                                    echo '£' . number_format((float)$data['Price'], 2, '.', '');
                                echo '</td>';
                                echo '<td>';
                                    echo '<form action="' . $_SERVER['PHP_SELF'] . '"name="orderChange" method="post">
                                        <input type=number min=1 name=newQuant style="display: inline;"class=form-control required>
                                        <input type=hidden name=productID value=' . $data['ProdID'] . ' >                                
                                        <button type=submit name=changeQuantity>Change quantity</button>
                                        </form>';
                                    echo '<form action="' . $_SERVER['PHP_SELF'] . '"name="orderDelete" method="post">
                                        <input type=hidden name=prodID value=' . $data['ProdID'] . ' >
                                        <button type=submit name=orderDelSubmit>Delete item</button>
                                        </form>';
                                echo '</td>';
                                echo '<td>';
                                    echo '£' . number_format((float)$data['Value'], 2, '.', '');
                                echo '</td>';
                            echo "</tr>";
                            }
                        }
                        echo "</tbody>";
                    echo "</table>";
                    echo '<h2>Order total value: <br> £' . number_format((float)$_SESSION['orderValue'], 2, '.', '') . '</h2>';
                    echo '<a href="place-order.php" id = "placeOrder">
                        <button class="placeButton">Place order</button> 
                        </a><br>';
                }
                else{
                    echo '<h1>There are currently no items on your order.</h1>';
                }
            }
            else{
                echo '<h1>There are currently no items on your order.</h1>';
            }
            echo '<a href="stock-order.php" id = "return">
            <button class="returnButton">Return</button> 
            </a>';
        ?>

    

</body>
</html>