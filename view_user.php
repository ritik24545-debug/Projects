<?php
session_start();
include 'db_connect.php';

// ✅ Admin session check
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// ✅ Check if user_id is passed
if (!isset($_GET['id'])) {
    echo "⚠️ User ID not provided.";
    exit();
}

$user_id = intval($_GET['id']);

// ✅ Fetch user details
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "❌ User not found.";
    exit();
}

$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Details</title>
    
</head>
<body>

    <div class="card">
        <h2>User Details</h2>
        <div class="info"><b>ID:</b> <?php echo $user['id']; ?></div>
        <div class="info"><b>Username:</b> <?php echo $user['username']; ?></div>
<div class="info"><b>Name:</b> <?php echo $user['fullname']; ?></div> 
<div class="info"><b>Email:</b> <?php echo $user['email']; ?></div>
<div class="info"><b>Phone:</b> <?php echo $user['phone']; ?></div>
<div class="info"><b>Adhar No/ID:</b> <?php echo isset($user['aadhar']) ? $user['aadhar'] : 'Not Provided'; ?></div>
<div class="info"><b>Role:</b> <?php echo isset($user['role']) ? $user['role'] : 'User'; ?></div>

        <a href="admin_dashboard.php" class="back-btn">⬅ Back to Dashboard</a>
    </div>

</body>
</html>
