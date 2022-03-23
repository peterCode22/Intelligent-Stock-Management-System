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

if($_SERVER['REQUEST_METHOD'] == "POST"){
    if(isset($_POST['reportType']) && isset($_POST['format'])){
        $type = $_POST['reportType'];
        $format = $_POST['format'];
        $pyArgv = array(
        "type" => $type,
        'format' => $format,
        'month' => $_POST['month'],
        'week' => $_POST['week'],
        'prodID' => $_POST['prodID']);
        
        if ($type == 'prodSales' && empty($_POST['prodID'])){
            //error
        }

        $pyStr = "python python/report.py";
        
        if(isset($_POST['previous'])){
            $pyArgv['previous'] = True;
        }

        if(isset($_POST['prediction'])){
            $pyArgv['prediction'] = True;
        }

        $pyJson = json_encode($pyArgv);
        
        if(empty($_POST['month']) && empty($_POST['week'])){ //no time period specified
            header("location: reports.php");
            exit;
        }

        if(!empty($_POST['month']) && !empty($_POST['week'])){ //if both month and week set
            header("location: reports.php");
            exit;
        }

        $output = shell_exec("python python/report.py " . json_encode(json_encode($pyArgv)));
        //}
        echo json_encode($pyArgv);
    }
    else{ //form submitted with missing fields
        //header("location: reports.php");
        //exit;
    }
}
else{ //no form submitted
    header("location: reports.php");
    exit;
}

?>
 
<!DOCTYPE html>
<html>
<head>
<title>Report view</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="style.css">
<meta charset="utf-8"> 
</head>
<body>

<div class="topnav">
    <a href="admin-page.php">Home</a>
    <a href="stock-count.php">Stock count</a>
    <a class="active" href="reports.php">Reports</a>
    <a href="product-management.php">Product management</a>
    <a href="stock-order.php">Order stock</a>
    <a href="pred-config.php">Prediction settings</a>
</div>

<div style="padding-left:16px">
    <p>
        <center>
            <?php

                if($format == 'table' || $format == 'acc'){
                    echo '<table class="table table-bordered table-striped">';
                    echo $output;
                    if ($output == null){
                        echo '<h1>No prediction and/or sales data for selected period.</h1>';
                    }
                }
                else{
                    echo '<figure><img src="python/graph.jpg?refresh"></figure>';
                }
            ?>

            <br><br>
            <a href="reports.php" id = "return">
            <button class="returnButton">Return</button> 
            </a>
        </center>
    </p>
</div>

</body>
</html>
