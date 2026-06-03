<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// accept either POST (form) or GET (link) to be robust
$id = null;
if (isset($_POST['id'])) {
    $id = (int)$_POST['id'];
} elseif (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
}

if (!$id) {
    header("Location: admin_dashboard.php");
    exit();
}

$stmt = $conn->prepare("DELETE FROM lost_items WHERE id = ?");
if ($stmt) {
    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) {
        error_log("delete_lost.php - DB delete failed for id={$id}: " . $stmt->error);
    }
    $stmt->close();
} else {
    error_log("delete_lost.php - prepare failed: " . $conn->error);
}

header("Location: admin_dashboard.php");
exit();
?>