<?php
// Initialize the session
session_start();

// Include config file
require_once "config.php";
 
// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Check if the user is an admin, else redirect to user website
if (!isset($_SESSION["acc_type"]) || $_SESSION["acc_type"] !== 'admin') {
    header("location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Product management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
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
        <a href="reports.php">Reports</a>
        <a class="active" href="product-management.php">Product management</a>
        <a href="stock-order.php">Order stock</a>
        <a href="pred-config.php">Prediction settings</a>
    </div>

    <div style="padding-left:16px">
        <p>
            <?php
            // Attempt select query execution
            $sql = "SELECT * FROM products";
            if ($result = $mysqli->query($sql)) {
                if ($result->num_rows > 0) {
                    echo '<table class="table table-bordered table-striped">';
                        echo "<thead>";
                              echo '<tr style="background-color:#c9dfca">';
                                    echo "<th>Product ID</th>";
                                    echo "<th>Product Name</th>";
                                    echo "<th>Retail Price</th>";
                                    echo "<th>Supplier Price</th>";
                                    echo '<th>Edit / Delete Product <a href="create-product.php" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add New Product</a> </th>';
                            echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                        while ($row = $result->fetch_array()) {
                            echo '<tr>';
                                echo "<td>" . $row['ProdID'] . "</td>";
                                echo "<td>" . $row['ProdName'] . "</td>";
                                echo "<td>£" . number_format((float)$row['RetailPrice'], 2, '.', '') . "</td>";
                                echo "<td>£" .  number_format((float)$row['SupplierPrice'], 2, '.', '') . "</td>";
                                echo "<td>";
                                    echo '<a href="update-product.php?id=' . $row['ProdID'] . '" title="Edit Product" data-toggle="tooltip"><span class="fa fa-pencil"></span></a>';
                                    echo '  ';
                                    echo '<a href="delete-product.php?id=' . $row['ProdID'] . '" title="Delete Product" data-toggle="tooltip"><span class="fa fa-trash"></span></a>';
                                echo "</td>";
                            echo "</tr>";
                        }
                        echo "</tbody>";
                    echo "</table>";
                    // Free result set
                    $result->free();
                } else {
                    echo '<div class="alert alert-danger"><em>No records were found.</em></div>';
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close connection
            $mysqli->close();
            ?>
        </p>
    </div>

</body>

</html>