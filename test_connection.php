<?php
include 'db_connect.php';

echo "<h2>Database Connection Test</h2>";

if ($conn->ping()) {
    echo "✅ Database connection successful!<br>";
} else {
    echo "❌ Database connection failed!<br>";
    exit();
}

// Check if matched_items table exists and show its structure
echo "<h3>Checking matched_items table:</h3>";

$result = $conn->query("SHOW TABLES LIKE 'matched_items'");
if ($result->num_rows > 0) {
    echo "✅ matched_items table exists<br>";
    
    // Show table structure
    $structure = $conn->query("DESCRIBE matched_items");
    echo "<h4>Table Structure:</h4>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $structure->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Show sample data
    $data = $conn->query("SELECT * FROM matched_items LIMIT 5");
    if ($data->num_rows > 0) {
        echo "<h4>Sample Data (First 5 rows):</h4>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Lost Item ID</th><th>Found Item ID</th><th>Status</th><th>Matched On</th></tr>";
        while ($row = $data->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['lost_item_id'] . "</td>";
            echo "<td>" . $row['found_item_id'] . "</td>";
            echo "<td>" . $row['status'] . "</td>";
            echo "<td>" . $row['matched_on'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No data in matched_items table yet.</p>";
    }
    
} else {
    echo "❌ matched_items table does not exist<br>";
}

// Check if users table exists
echo "<h3>Checking users table:</h3>";
$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result->num_rows > 0) {
    echo "✅ users table exists<br>";
} else {
    echo "❌ users table does not exist<br>";
}

// Check if lost_items table exists
echo "<h3>Checking lost_items table:</h3>";
$result = $conn->query("SHOW TABLES LIKE 'lost_items'");
if ($result->num_rows > 0) {
    echo "✅ lost_items table exists<br>";
} else {
    echo "❌ lost_items table does not exist<br>";
}

// Check if found_items table exists
echo "<h3>Checking found_items table:</h3>";
$result = $conn->query("SHOW TABLES LIKE 'found_items'");
if ($result->num_rows > 0) {
    echo "✅ found_items table exists<br>";
} else {
    echo "❌ found_items table does not exist<br>";
}

$conn->close();
echo "<br><a href='dashboard.php'>← Back to Dashboard</a>";
?>
