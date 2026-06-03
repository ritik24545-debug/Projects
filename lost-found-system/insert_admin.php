<?php
include 'db_connect.php';

echo "<h1>Inserting Admin...</h1>";

$username = "admin";
$password = "admin123";

$stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $password);

if ($stmt->execute()) {
    echo "✅ Admin inserted successfully!<br><br>";
    echo "<strong>Login Details:</strong><br>";
    echo "Username: <strong>admin</strong><br>";
    echo "Password: <strong>admin123</strong><br><br>";
    echo "<a href='admin_login.php'>Go to Admin Login</a>";
} else {
    echo "❌ Error: " . $stmt->error . "<br>";
}

$conn->close();
?>
