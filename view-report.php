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

        $pyStr = "python python/report.py";
        
        if(isset($_POST['previous'])){
            $pyArgv['previous'] = True;
        }

        if(isset($_POST['prediction'])){
            $pyArgv['prediction'] = True;
        }

        $pyJson = json_encode($pyArgv);
        //echo $pyJson;
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
<style>
body {
  margin: 0;
  font-family: Arial, Helvetica, sans-serif;
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

</style>
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

                if($format == 'table'){
                    echo $output;
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
