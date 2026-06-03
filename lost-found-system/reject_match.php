<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("No match ID provided.");
}

$match_id = (int)$_GET['id'];

// Update status to 'rejected' and clear matched_on
$stmt = $conn->prepare("UPDATE matched_items SET status = 'rejected', matched_on = NULL WHERE id = ?");
$stmt->bind_param("i", $match_id);
if (!$stmt->execute()) {
    error_log("reject_match.php - update failed: " . $stmt->error);
}
$stmt->close();

header("Location: admin_dashboard.php");
exit();
?>
