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


?>
 
<!DOCTYPE html>
<html>
<head>
<title>Reports</title>
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

</style>
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
        <h1>Generate report</h1><br>

        <h3>Report type:</h3>
        <form method="post" action="view-report.php">
            <input type="radio" id="moneySales" name="reportType" value="moneySales">
            <label for="moneySales">Sales(£)</label><br>
            <input type="radio" id="prodSales" name="reportType" value="prodSales">
            <label for="prodSales">Product sales(Quantity sold)</label><br>
            <input type="number" name="prodID" >
            <label for="prodID">Product ID</label><br>
            <input type="radio" id="predAcc" name="reportType" value="predAcc">
            <label for="predAcc">Prediction's accuracy(%)</label><br>
            <input type="radio" id="predMSE" name="reportType" value="predMSE">
            <label for="predMSE">Prediction's accuracy (MSE)</label><br>
        
            <h3>Report's format:</h3>
            <input type="radio" id="table" name="format" value="table">
            <label for="table">Table</label><br>
            <input type="radio" id="graph" name="format" value="graph">
            <label for="graph">Graph</label><br>

            <h3>Specify period(select one):</h3>
        
            <label for="from">Month</label>
            <input type="month" name="month"><br>
            <label for="to">Week</label>
            <input type="week" name="week"><br>
        
            <h3>Comparison options:</h3>
            <input type="checkbox" name="prediction">
            <label for="prediction">vs. prediction</label><br>
            <input type="checkbox" name="previous">
            <label for="previous">vs. last month</label><br>

            <input type="submit" value="Generate Report">
            </form>
        </center>
    </p>
</div>

</body>
</html>
