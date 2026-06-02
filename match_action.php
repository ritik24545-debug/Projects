<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit();
}

if (isset($_GET['lost_id'], $_GET['found_id'], $_GET['action'])) {
    $lost_id  = $_GET['lost_id'];
    $found_id = $_GET['found_id'];
    $status   = ($_GET['action'] === 'accept') ? 'Accepted' : 'Rejected';

    $stmt = $conn->prepare("INSERT INTO matched_items (lost_item_id, found_item_id, status) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $lost_id, $found_id, $status);
    $stmt->execute();

    header("Location: admin_dashboard.php");
    exit();
}
?>
