<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db_connect.php';

echo "<h1>Checking All Tables in Database: " . $database . "</h1>";

$tables = ['users', 'lost_items', 'found_items', 'matched_items', 'admin'];

foreach ($tables as $table) {
    echo "<h2>Table: $table</h2>";
    $result = $conn->query("DESCRIBE $table");
    if ($result) {
        echo "<table border='1' cellpadding='8'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>Error: " . $conn->error . "</p>";
    }
}

echo "<h1>Existing Admin Records:</h1>";
$admin_result = $conn->query("SELECT * FROM admin");
if ($admin_result && $admin_result->num_rows > 0) {
    echo "<table border='1' cellpadding='8'><tr><th>ID</th><th>Username</th><th>Password</th></tr>";
    while ($row = $admin_result->fetch_assoc()) {
        echo "<tr><td>" . $row['id'] . "</td><td><strong>" . $row['username'] . "</strong></td><td><strong>" . $row['password'] . "</strong></td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>No admin found! Inserting new admin: username=admin, password=admin123...</p>";
    $stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);
    $username = "admin";
    $password = "admin123";
    if ($stmt->execute()) {
        echo "<p>✅ Admin inserted successfully! Now you can login.</p>";
    } else {
        echo "<p style='color: red;'>❌ Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

$conn->close();
?>
