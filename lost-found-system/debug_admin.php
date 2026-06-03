<?php
include 'db_connect.php';

echo "<h1>Admin Debug Info</h1>";

// Show admin table structure
echo "<h2>Admin Table Structure:</h2>";
$structure = $conn->query("DESCRIBE admin");
echo "<table border='1' cellpadding='8'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
while ($row = $structure->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
    echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
    echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
    echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
    echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
    echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
    echo "</tr>";
}
echo "</table>";

// Show all admins
echo "<h2>Existing Admin(s):</h2>";
$result = $conn->query("SELECT * FROM admin");
if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='8'>";
    echo "<tr>";
    $fields = $result->fetch_fields();
    foreach ($fields as $field) {
        echo "<th>" . htmlspecialchars($field->name) . "</th>";
    }
    echo "</tr>";
    
    $result->data_seek(0);
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No admins found!</p>";
}

// Truncate and insert fresh admin
echo "<h2>Resetting Admin...</h2>";
$conn->query("TRUNCATE TABLE admin");

$username = "admin";
$password = "admin123";
$stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $password);

if ($stmt->execute()) {
    echo "✅ Admin reset done!<br><br>";
    echo "<strong>Login Details:</strong><br>";
    echo "Username: <strong>admin</strong><br>";
    echo "Password: <strong>admin123</strong><br><br>";
    echo "<a href='admin_login.php'>Go to Admin Login</a>";
} else {
    echo "❌ Error: " . $conn->error . "<br>";
}

$conn->close();
?>
