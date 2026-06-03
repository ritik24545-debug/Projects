<?php
session_start();
include 'db_connect.php';

/*
------------------------------------------
  Case 1: Admin clicking Accept / Reject
------------------------------------------
*/

if (isset($_SESSION['admin_id']) && isset($_GET['lost_id'], $_GET['found_id'], $_GET['action'])) {

    $lost_id  = (int)$_GET['lost_id'];
    $found_id = (int)$_GET['found_id'];
    $action   = strtolower(trim($_GET['action']));
    $status   = ($action === 'accept') ? 'accepted' : 'rejected';

    // Try to update existing matched record
    $update = $conn->prepare("
        UPDATE matched_items 
        SET status = ?, matched_on = CASE WHEN ? = 'accepted' THEN NOW() ELSE NULL END 
        WHERE lost_item_id = ? AND found_item_id = ?
    ");
    if ($update) {
        $update->bind_param("ssii", $status, $status, $lost_id, $found_id);
        $update->execute();

        // If record doesn't exist, insert a new one
        if ($update->affected_rows === 0) {
            $stmt = $conn->prepare("
                INSERT INTO matched_items (lost_item_id, found_item_id, status, matched_on)
                VALUES (?, ?, ?, CASE WHEN ? = 'accepted' THEN NOW() ELSE NULL END)
            ");
            $stmt->bind_param("iiss", $lost_id, $found_id, $status, $status);
            $stmt->execute();
            $stmt->close();
        }
        $update->close();
    } else {
        // fallback insert if prepare failed
        $stmt = $conn->prepare("INSERT INTO matched_items (lost_item_id, found_item_id, status) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $lost_id, $found_id, $status);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: admin_dashboard.php");
    exit();
}

/*
------------------------------------------
  Case 2: Admin or User clicking “View”
------------------------------------------
*/

if (isset($_GET['id'])) {
    $match_id = (int)$_GET['id'];

    // Admin: open contact view (both users visible)
    if (isset($_SESSION['admin_id'])) {
        header("Location: view_contact.php?match_id=" . $match_id);
        exit();
    }

    // Normal User: open read-only match view
    elseif (isset($_SESSION['user_id'])) {
        header("Location: view_match_details.php?match_id=" . $match_id);
        exit();
    }

    // No session -> send to login
    else {
        header("Location: login.php");
        exit();
    }
}

/*
------------------------------------------
  Default fallback
------------------------------------------
*/

header("Location: admin_dashboard.php");
exit();
?>
