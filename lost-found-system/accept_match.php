<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['id'])) {
    $match_id = $_GET['id'];

    // 1. Get lost_item_id & found_item_id from matched_items
    $sql = "SELECT lost_item_id, found_item_id FROM matched_items WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $match_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        die("Error in SELECT query: " . $conn->error);
    }

    $match = $result->fetch_assoc();

    if ($match) {
        $lost_id = $match['lost_item_id'];
        $found_id = $match['found_item_id'];

        // 2. Update matched_items status to 'accepted' and set matched_on timestamp
        $update_sql = "UPDATE matched_items SET status='accepted', matched_on=NOW() WHERE id=?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("i", $match_id);
        
        if (!$update_stmt->execute()) {
            die("Error updating matched_items: " . $conn->error);
        }
        
        $update_stmt->close();
    } else {
        die("Match record not found for id=$match_id");
    }

    $stmt->close();
    header("Location: admin_dashboard.php");
    exit();
} else {
    die("No match ID provided!");
}
?>
