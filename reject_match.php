<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $match_id = $_GET['id'];

    // Update status to 'rejected' and clear matched_on timestamp
    $sql = "UPDATE matched_items SET status='rejected', matched_on=NULL WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $match_id);
    
    if (!$stmt->execute()) {
        die("Error updating matched_items: " . $conn->error);
    }
    
    $stmt->close();
    header("Location: admin_dashboard.php");
    exit();
}
?>
