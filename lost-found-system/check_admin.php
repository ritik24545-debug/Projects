<?php
include 'db_connect.php';

echo "<h2>Admin Table Check</h2>";

$result = $conn->query("SELECT * FROM admin");
if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Username</th><th>Password</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td><strong>" . $row['username'] . "</strong></td>";
        echo "<td><strong>" . $row['password'] . "</strong></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No admin found! Creating a new admin...<br>";
    
    // Create new admin
    $username = "admin";
    $password = "admin123";
    $stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);
    
    if ($stmt->execute()) {
        echo "✅ Admin created!<br>";
        echo "Username: <strong>admin</strong><br>";
        echo "Password: <strong>admin123</strong><br>";
    } else {
        echo "❌ Error: " . $stmt->error . "<br>";
    }
}

echo "<br><a href='admin_login.php'>Go to Admin Login</a>";
$conn->close();
?>
