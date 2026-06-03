<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php';

echo "<h1>Simple Test</h1>";

echo "<h2>Connected to Database: " . $database . "</h2>";

// Show all tables
echo "<h2>Tables in Database:</h2>";
$tables = $conn->query("SHOW TABLES");
while ($row = $tables->fetch_array()) {
    echo "- " . htmlspecialchars($row[0]) . "<br>";
}

// Show admin table contents
echo "<h2>Admin Table Content:</h2>";
$result = $conn->query("SELECT * FROM admin");
if ($result) {
    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='10'>";
        $row = $result->fetch_assoc();
        echo "<tr>";
        foreach (array_keys($row) as $key) {
            echo "<th>" . htmlspecialchars($key) . "</th>";
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
        echo "<p>Admin table is EMPTY!</p>";
    }
} else {
    echo "<p>Admin table doesn't exist! Error: " . $conn->error . "</p>";
}

$conn->close();
?>
