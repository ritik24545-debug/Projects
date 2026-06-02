<?php
include 'db_connect.php';

$lost_id = $_GET['lost_id'];
$found_id = $_GET['found_id'];

// Lost item details
$lost_sql = "SELECT * FROM lost_items WHERE ID='$lost_id'";
$lost_result = $conn->query($lost_sql);
$lost = $lost_result->fetch_assoc();

// Found item details
$found_sql = "SELECT * FROM found_items WHERE ID='$found_id'";
$found_result = $conn->query($found_sql);
$found = $found_result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Match Details</title>
</head>
<body>
    <h2>Lost Item Report</h2>
    <p><b>Item Name:</b> <?php echo $lost['item_name']; ?></p>
    <p><b>Description:</b> <?php echo $lost['description']; ?></p>
    <p><b>Date Lost:</b> <?php echo $lost['date_lost']; ?></p>
    <p><b>Location:</b> <?php echo $lost['location']; ?></p>

    <hr>

    <h2>Found Item Report</h2>
    <p><b>Item Name:</b> <?php echo $found['item_name']; ?></p>
    <p><b>Description:</b> <?php echo $found['description']; ?></p>
    <p><b>Date Found:</b> <?php echo $found['date_found']; ?></p>
    <p><b>Location:</b> <?php echo $found['location']; ?></p>

    <hr>
    <a href="admin_dashboard.php">⬅ Back to Dashboard</a>
</body>
</html>
