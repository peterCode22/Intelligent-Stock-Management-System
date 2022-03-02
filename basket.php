<?php
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
    if(isset($_POST['productID'])){
        $_POST['productID'] = intval($_POST['productID']);
        if(isset($_POST['newQuant'])){
            $_POST['newQuant'] = intval($_POST['newQuant']);
            if (($key = array_search($_POST['productID'], array_column($_SESSION['basket'], 'ProdID'))) !== false){
                $oldQuant = $_SESSION['basket'][$key]['Quantity'];
                $_SESSION['basket'][$key]['Quantity'] = $_POST['newQuant'];
                $_SESSION['basket'][$key]['Value'] = $_SESSION['basket'][$key]['Quantity'] * $_SESSION['basket'][$key]['Price'];
                if ($_POST['newQuant'] >= $oldQuant){
                    $_SESSION['basketValue'] += ($_POST['newQuant'] - $oldQuant) * $_SESSION['basket'][$key]['Price'];
                } else{
                    $_SESSION['basketValue'] -= ($oldQuant - $_POST['newQuant']) * $_SESSION['basket'][$key]['Price'];
                }
            }
        }
        else{
            if (($key = array_search($_POST['productID'], array_column($_SESSION['basket'], 'ProdID'))) !== false){
                $oldQuant = $_SESSION['basket'][$key]['Quantity'];
                $_SESSION['basketValue'] -= $oldQuant * $_SESSION['basket'][$key]['Price'];
                unset($_SESSION['basket'][$key]);
                $_SESSION['basket'] = array_values($_SESSION['basket']);
            }
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
    <style>
        body{ font: 14px sans-serif; text-align: center; }
        input[type="number"] {
            width:100px;
        }
        a[id='logout'] {
			font-size: 30px;
			position:absolute;
			top:0;
			right:0;
			margin-right: 20px;
			margin-top: 10px;
		}
		
		a[id='return'] {
			font-size: 30px;
			position:absolute;
			top:0;
			left:0;
			margin-left: 20px;
			margin-top: 10px;
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

    </style>
</head>
<body>
    <?php
        echo '<a href="index.php" id = "return">
            <button class="returnButton">Return</button> 
            </a>';
		echo '<a href="logout.php" id = "logout">
            <button class="logoffButton">Log out</button> 
            </a>';
    ?>
    <h1 class="my-5">Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>.
        <?php 
            if(isset($_SESSION['basket'])){
                if (!empty($_SESSION['basket'])){
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
                        foreach ($_SESSION['basket'] as $data) {
                            echo "<tr>";
                                echo '<td>' . $data['ProdName'] . '</td>';
                                echo '<td>' . $data['Quantity'] . '</td>';
                                echo '<td>';
                                    echo '£' . number_format((float)$data['Price'], 2, '.', '');
                                echo '</td>';
                                echo '<td>';
                                    echo '<form action="' . $_SERVER['PHP_SELF'] . '"name="basketChange" method="post">
                                        <input type=number min=1 name=newQuant style="display: inline;"class=form-control required>
                                        <input type=hidden name=productID value=' . $data['ProdID'] . ' >                                
                                        <button type=submit name=changeQuantity>Change quantity</button>
                                        </form>' . '<form action="' . $_SERVER['PHP_SELF'] . '"name="basketDelete" method="post">
                                        <input type=hidden name=productID value=' . $data['ProdID'] . ' >
                                        <button type=submit name=basketDelSubmit>Delete item</button>
                                        </form>';
                                echo '</td>';
                                echo '<td>';
                                    echo '£' . number_format((float)$data['Value'], 2, '.', '');
                                echo '</td>';
                            echo "</tr>";
                        }
                        echo "</tbody>";
                    echo "</table>";
                    echo '<h2>Basket total value: <br> £' . number_format((float)$_SESSION['basketValue'], 2, '.', '') . '</h2>';
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