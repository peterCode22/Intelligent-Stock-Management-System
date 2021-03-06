<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../../Actions/login.php");
    exit;
}

// Check if the user is an admin, else redirect to user website
if(!isset($_SESSION["acc_type"]) || $_SESSION["acc_type"] !== 'admin'){
	header("location: ../../index.php");
    exit;
}

require_once "../../Config/config.php";

$sql = "SELECT MIN(DayT), MAX(DayT) FROM sales;";
if($result = $mysqli->query($sql)){
    if($result->num_rows == 1){
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $minDate = $row['MIN(DayT)'];
        $maxDate = $row['MAX(DayT)'];
    }
}

//Store configuration settings in trainConfig.json
if($_SERVER['REQUEST_METHOD'] == "POST"){
    $jsonString = file_get_contents('../../MachineLearning/trainConfig.json');
    $data = json_decode($jsonString, true);
    if(isset($_POST['deliveryFreq'])){
        $data['delivery'] = $_POST['deliveryFreq'];
        $newJsonString = json_encode($data);
        file_put_contents('../../MachineLearning/trainConfig.json', $newJsonString);
    }
    if (isset($_POST['error'])){
        if ($_POST['error'] > 0){
            $data['MSE'] = floatval($_POST['error']);
            $newJsonString = json_encode($data);
            file_put_contents('../../MachineLearning/trainConfig.json', $newJsonString);
        }
    }

    if (isset($_POST['from']) && isset($_POST['to'])){
        $from = $_POST['from'];
        $to = $_POST['to'];
        shell_exec("python ../../MachineLearning/trainModel.py $from $to");
    }

}

?>
 
<!DOCTYPE html>
<html>
<head>
<title>Prediction settings</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="../../style.css">
</head>
<body>

<div class="topnav">
    <a href="admin-page.php">Home</a>
    <a href="stock-count.php">Stock count</a>
    <a href="reports.php">Reports</a>
    <a href="product-management.php">Product management</a>
    <a href="stock-order.php">Order stock</a>
    <a class="active" href="pred-config.php">Prediction settings</a>
</div>

<div style="padding-left:16px">
    <p>
        <center>
        <h1>Prediction settings:</h1><br>

        <h3>How often are product deliveries?</h3>
        <p> This setting specifies which future time period the algorithm should predict for.</p>    
        <form method="post">
            <input type="radio" id="weekly" name="deliveryFreq" value="weekly">
            <label for="weekly">Weekly</label><br>
            <input type="radio" id="monthly" name="deliveryFreq" value="monthly">
            <label for="monthly">Monthly</label><br>
        
        <h3>What is the desired maximum Mean Squared Error (MSE)?</h3>
        <p> If the algorithm's MSE is higher than specified value, the user will be notified. </p>
            <label for="error">Mean Squared Error:</label>
            <input type="number" id="error" name="error" min="0" step="0.01">
            <br>
            <input type="submit" value="Apply changes">
        </form>
        <br>
        <h1>Train the model:</h1>
        <p>The model can be trained based on currently stored sales data.</p><br>

        <h3>Specify desired time period:</h3>
        <?php
        echo '<form method="post">';
            echo '<label for="from">From:</label>';
            echo '<input type="date" name="from" min="' . $minDate .'" max="' . $maxDate . '"><br>';
            echo '<label for="to">To:</label>';
            echo '<input type="date" name="to" min="' . $minDate .'" max="' . $maxDate . '"><br>';
            echo '<input type="submit" value="Train">';
        echo '</form>';
        ?>
        </center>
    </p>
</div>

</body>
</html>
