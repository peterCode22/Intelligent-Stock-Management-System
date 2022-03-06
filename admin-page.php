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


// Check if prediciton period has passed
// if next day has a prediction or null
$tomorrow = new DateTime('tomorrow');
$predSQL = "SELECT Predicted FROM sales WHERE DayT = ?";
if($stmt = $mysqli->prepare($predSQL)){
    // Bind variables to the prepared statement as parameters
    $stmt->bind_param("s", $tmrStr);
    $tmrStr = $tomorrow->format('Y-m-d');
    // Attempt to execute the prepared statement
    if($stmt->execute()){
        // Records created successfully. Redirect to landing page
        $result = $stmt->get_result();
              
        if($result->num_rows == 0){ //Prediction period has passed
            shell_exec('python python/predict.py');
            $accurate = shell_exec('python python/test.py');
            if ($accurate === False){
                $message = "Last prediction period has exceeded inaccuracy threshold. Model re-training recommended!";
                echo "<script type='text/javascript'>alert('$message');</script>";
            }
        }
    }
}
 
// Close statement
$stmt->close();


?>
 
<!DOCTYPE html>
<html>
<head>
<title>Admin home</title>
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
    <a class="active" href="admin-page.php">Home</a>
    <a href="stock-count.php">Stock count</a>
    <a href="sales.php">Sales summary</a>
    <a href="product-management.php">Product management</a>
    <a href="stock-order.php">Order stock</a>
    <a href="pred-config.php">Prediction settings</a>
</div>

<div style="padding-left:16px">
  <?php echo '<h2>Welcome to admin home page, '. $_SESSION['username'] . ' !  </h2>'; ?>
  <p>   
		<a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
        <a href="logout.php" class="btn btn-danger ml-3">Sign Out of Your Account</a>
		</p>
</div>

</body>
</html>
