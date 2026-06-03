<?php
session_start();
include 'db_connect.php';

// Only admin or logged-in user
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['match_id'])) {
    echo "<p style='color:red;'>❌ Match ID missing!</p>";
    exit();
}

$match_id = intval($_GET['match_id']);

// Fetch match details
$sql = "
SELECT 
    m.id AS match_id, m.status, m.matched_on,
    l.item_name AS lost_item, l.description AS lost_desc, l.location AS lost_loc, l.date_lost,
    f.item_name AS found_item, f.description AS found_desc, f.location AS found_loc, f.date_found,
    u1.fullname AS lost_user, u1.email AS lost_email, u1.phone AS lost_phone,
    u2.fullname AS found_user, u2.email AS found_email, u2.phone AS found_phone
FROM matched_items m
JOIN lost_items l ON m.lost_item_id = l.id
JOIN found_items f ON m.found_item_id = f.id
JOIN users u1 ON l.user_id = u1.id
JOIN users u2 ON f.user_id = u2.id
WHERE m.id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $match_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<p style='color:red;'>❌ No match found for ID: $match_id</p>";
    exit();
}

$data = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Match Contact Info</title>
<style>
body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: linear-gradient(to right, #f7e6ff, #e3f2fd);
    color: #333;
    margin: 0;
    padding: 20px;
}
.container {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    padding: 20px 30px;
    max-width: 800px;
    margin: 40px auto;
}
h2 {
    color: #6a1b9a;
    text-align: center;
}
section {
    margin-top: 20px;
}
.info {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 10px 15px;
    margin-bottom: 15px;
}
label { font-weight: bold; color: #4a148c; }
a.btn {
    display: inline-block;
    background: #6a1b9a;
    color: white;
    padding: 8px 14px;
    border-radius: 6px;
    text-decoration: none;
}
a.btn:hover { background: #8e24aa; }
</style>
</head>
<body>
<div class="container">
    <h2>Match Details & Contact Info</h2>
    
    <section>
        <h3>🧾 Match Summary</h3>
        <div class="info">
            <label>Status:</label> <?= htmlspecialchars($data['status']) ?><br>
            <label>Matched On:</label> <?= htmlspecialchars($data['matched_on']) ?>
        </div>
    </section>

    <section>
        <h3>📦 Lost Item Details</h3>
        <div class="info">
            <label>Item Name:</label> <?= htmlspecialchars($data['lost_item']) ?><br>
            <label>Description:</label> <?= htmlspecialchars($data['lost_desc']) ?><br>
            <label>Date Lost:</label> <?= htmlspecialchars($data['date_lost']) ?><br>
            <label>Location:</label> <?= htmlspecialchars($data['lost_loc']) ?>
        </div>

        <h4>👤 Lost Item Owner</h4>
        <div class="info">
            <label>Name:</label> <?= htmlspecialchars($data['lost_user']) ?><br>
            <label>Email:</label> <?= htmlspecialchars($data['lost_email']) ?><br>
            <label>Phone:</label> <?= htmlspecialchars($data['lost_phone']) ?>
        </div>
    </section>

    <section>
        <h3>🎁 Found Item Details</h3>
        <div class="info">
            <label>Item Name:</label> <?= htmlspecialchars($data['found_item']) ?><br>
            <label>Description:</label> <?= htmlspecialchars($data['found_desc']) ?><br>
            <label>Date Found:</label> <?= htmlspecialchars($data['date_found']) ?><br>
            <label>Location:</label> <?= htmlspecialchars($data['found_loc']) ?>
        </div>

        <h4>👤 Found Item Reporter</h4>
        <div class="info">
            <label>Name:</label> <?= htmlspecialchars($data['found_user']) ?><br>
            <label>Email:</label> <?= htmlspecialchars($data['found_email']) ?><br>
            <label>Phone:</label> <?= htmlspecialchars($data['found_phone']) ?>
        </div>
    </section>

    <center><a href="admin_dashboard.php" class="btn">⬅ Back to Dashboard</a></center>
</div>
</body>
</html>

