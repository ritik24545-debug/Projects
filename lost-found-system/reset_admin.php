<?php
include 'db_connect.php';

echo "<h2>Reset Admin Password</h2>";

// Delete old admin if exists
$conn->query("DELETE FROM admin WHERE id >= 1");

// Create new admin with simple password
$username = "admin";
$password = "admin"; // Very simple password for testing
$stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $password);

if ($stmt->execute()) {
    echo "✅ Admin password reset successfully!<br><br>";
    echo "Admin Login Details:<br>";
    echo "Username: <strong>admin</strong><br>";
    echo "Password: <strong>admin</strong><br><br>";
    echo "<a href='admin_login.php'>Go to Admin Login</a>";
} else {
    echo "❌ Error: " . $stmt->error . "<br>";
}

$conn->close();
?>
