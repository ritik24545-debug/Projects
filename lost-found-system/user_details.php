<?php
session_start();
include 'db_connect.php';

// ✅ Only admin can access
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// ✅ Get user ID from query
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<h3>Invalid User ID</h3>");
}
$user_id = (int)$_GET['id'];

// ✅ Fetch user info
$stmt = $conn->prepare("SELECT id, fullname, username, email, phone, aadhar FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("<h3>User not found</h3>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Details | Admin Panel</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body {
  margin: 0;
  font-family: 'Segoe UI', Arial, sans-serif;
  background: linear-gradient(to right, #f7e6ff, #e3f2fd);
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}
.container {
  background: rgba(255,255,255,0.95);
  box-shadow: 0 8px 25px rgba(0,0,0,0.1);
  border-radius: 15px;
  padding: 30px 40px;
  width: 420px;
}
h1 {
  text-align: center;
  color: #6a1b9a;
  margin-bottom: 20px;
}
.info {
  line-height: 1.8;
  font-size: 15px;
}
label {
  font-weight: bold;
  color: #4a148c;
}
.back-btn {
  display: inline-block;
  margin-top: 20px;
  background: #6a1b9a;
  color: white;
  padding: 8px 15px;
  border-radius: 6px;
  text-decoration: none;
}
.back-btn:hover {
  background: #8e24aa;
}
</style>
</head>
<body>
<div class="container">
  <h1>User Details</h1>
  <div class="info">
    <p><label>User ID:</label> <?= htmlspecialchars($user['id']); ?></p>
    <p><label>Full Name:</label> <?= htmlspecialchars($user['fullname']); ?></p>
    <p><label>Username:</label> <?= htmlspecialchars($user['username']); ?></p>
    <p><label>Email:</label> <?= htmlspecialchars($user['email']); ?></p>
    <p><label>Phone:</label> <?= htmlspecialchars($user['phone']); ?></p>
    <p><label>Aadhar / ID:</label> <?= htmlspecialchars($user['aadhar']); ?></p>
  </div>
  <a href="admin_dashboard.php" class="back-btn">← Back to Dashboard</a>
</div>
</body>
</html>
