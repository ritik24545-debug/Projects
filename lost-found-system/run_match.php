<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db_connect.php';
include 'auto_match.php';

echo "<style>
    body {
        font-family: 'Inter', Arial, sans-serif;
        background: linear-gradient(135deg, #f1f5f9 0%, #e0e7ff 100%);
        padding: 3rem;
    }
    .container {
        max-width: 1000px;
        margin: 0 auto;
        background: white;
        padding: 2.5rem;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
    }
    h1 { color: #4f46e5; text-align: center; margin-bottom: 2rem; }
    h2 { color: #0f172a; margin-top: 2rem; margin-bottom: 1rem; border-left: 5px solid #4f46e5; padding-left: 1rem; }
    .success { color: #065f46; background: #d1fae5; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; font-weight: 600; }
    .info { color: #1e40af; background: #dbeafe; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; font-weight: 600; }
    table { width: 100%; border-collapse: collapse; margin-top: 1.5rem; }
    th, td { padding: 0.8rem; border: 1px solid #e2e8f0; text-align: left; }
    th { background: #4f46e5; color: white; }
    .btn {
        display: inline-block;
        background: linear-gradient(135deg, #4f46e5, #4338ca);
        color: white;
        padding: 0.8rem 1.5rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        margin-top: 1rem;
        border: none;
        cursor: pointer;
        font-size: 1rem;
    }
    .btn:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(79,70,229,0.3); }
    .btn-success { background: linear-gradient(135deg, #10b981, #059669); }
    .btn-danger { background: linear-gradient(135deg, #ef4444, #dc2626); }
    select, button { padding: 0.6rem; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 1rem; }
    .manual-match { background: #f8fafc; padding: 1.5rem; border-radius: 8px; margin-top: 1rem; }
</style>";

echo "<div class='container'>";
echo "<h1>🔍 Auto & Manual Matching Tool</h1>";

// Handle manual match
if (isset($_POST['manual_match'])) {
    $lost_id = intval($_POST['lost_item']);
    $found_id = intval($_POST['found_item']);

    $check = $conn->prepare("SELECT id FROM matched_items WHERE lost_item_id=? AND found_item_id=?");
    $check->bind_param("ii", $lost_id, $found_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows == 0) {
        $insert = $conn->prepare("INSERT INTO matched_items (lost_item_id, found_item_id, status, matched_on) VALUES (?, ?, 0, NOW())");
        $insert->bind_param("ii", $lost_id, $found_id);
        if ($insert->execute()) {
            echo "<div class='success'>✅ Success! Manually matched!</div>";
        } else {
            echo "<div class='info'>Error: " . $insert->error . "</div>";
        }
        $insert->close();
    } else {
        echo "<div class='info'>ℹ️ This match already exists!</div>";
    }
    $check->close();
}

// Show current items
echo "<h2>Current Lost Items:</h2>";
$lost = $conn->query("SELECT * FROM lost_items");
$lost_items_arr = [];
if ($lost->num_rows > 0) {
    echo "<table><tr><th>ID</th><th>Item</th><th>Description</th><th>Location</th></tr>";
    while ($row = $lost->fetch_assoc()) {
        $lost_items_arr[] = $row;
        echo "<tr><td>{$row['id']}</td><td>".htmlspecialchars($row['item_name'])."</td><td>".htmlspecialchars($row['description'])."</td><td>".htmlspecialchars($row['location'])."</td></tr>";
    }
    echo "</table>";
} else {
    echo "<div class='info'>No lost items reported yet.</div>";
}

echo "<h2>Current Found Items:</h2>";
$found = $conn->query("SELECT * FROM found_items");
$found_items_arr = [];
if ($found->num_rows > 0) {
    echo "<table><tr><th>ID</th><th>Item</th><th>Description</th><th>Location</th></tr>";
    while ($row = $found->fetch_assoc()) {
        $found_items_arr[] = $row;
        echo "<tr><td>{$row['id']}</td><td>".htmlspecialchars($row['item_name'])."</td><td>".htmlspecialchars($row['description'])."</td><td>".htmlspecialchars($row['location'])."</td></tr>";
    }
    echo "</table>";
} else {
    echo "<div class='info'>No found items reported yet.</div>";
}

// Manual Match Form
if (!empty($lost_items_arr) && !empty($found_items_arr)) {
    echo "<h2>✋ Manual Match</h2>";
    echo "<div class='manual-match'><form method='POST'>";
    echo "<label for='lost_item'>Select Lost Item: </label>";
    echo "<select name='lost_item' id='lost_item' required>";
    foreach ($lost_items_arr as $item) {
        echo "<option value='{$item['id']}'>[{$item['id']}] ".htmlspecialchars($item['item_name'])."</option>";
    }
    echo "</select>";

    echo " <label for='found_item' style='margin-left:1rem;'>Select Found Item: </label>";
    echo "<select name='found_item' id='found_item' required>";
    foreach ($found_items_arr as $item) {
        echo "<option value='{$item['id']}'>[{$item['id']}] ".htmlspecialchars($item['item_name'])."</option>";
    }
    echo "</select>";

    echo " <button type='submit' name='manual_match' class='btn btn-success'>➕ Match Items!</button>";
    echo "</form></div>";
}

// Run matching
echo "<h2>🤖 Auto Matching Results:</h2>";
$matchCount = autoMatchItems($conn);

if ($matchCount > 0) {
    echo "<div class='success'>✅ Success! $matchCount new match(es) found and added!</div>";
} else {
    echo "<div class='info'>ℹ️ No new auto matches found. Try manual matching above!</div>";
}

// Show All Matches
echo "<h2>📋 All Matches:</h2>";
$matches = $conn->query("
    SELECT m.id, l.item_name lost_name, f.item_name found_name, l.location, 
    CASE m.status WHEN 0 THEN 'Pending' WHEN 1 THEN 'Accepted' ELSE 'Rejected' END status
    FROM matched_items m
    JOIN lost_items l ON m.lost_item_id = l.id
    JOIN found_items f ON m.found_item_id = f.id
");
if ($matches->num_rows > 0) {
    echo "<table><tr><th>ID</th><th>Lost</th><th>Found</th><th>Location</th><th>Status</th></tr>";
    while ($row = $matches->fetch_assoc()) {
        echo "<tr><td>{$row['id']}</td><td>".htmlspecialchars($row['lost_name'])."</td><td>".htmlspecialchars($row['found_name'])."</td><td>".htmlspecialchars($row['location'])."</td><td>".htmlspecialchars($row['status'])."</td></tr>";
    }
    echo "</table>";
} else {
    echo "<div class='info'>No matches yet.</div>";
}

echo "<div style='margin-top:2rem;'>
    <a href='admin_dashboard.php' class='btn'>🏠 Go to Admin Dashboard</a>
    <a href='index.html' class='btn' style='margin-left:1rem;'>🏠 Home</a>
</div>";

echo "</div>";
$conn->close();
?>