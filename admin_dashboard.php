<?php
session_start();

include 'matched_items.php';

include 'db_connect.php';       

// 1️⃣ Admin session check
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// 2️⃣ Fetch Lost & Found Items
$lost_items_res = $conn->query("SELECT * FROM lost_items");
$found_items_res = $conn->query("SELECT * FROM found_items");

// 3️⃣ Fetch Possible Matches
$matches_res = $conn->query("
    SELECT l.id AS lost_id, f.id AS found_id, l.item_name, l.location
    FROM lost_items l
    JOIN found_items f ON l.item_name = f.item_name AND l.location = f.location
    LEFT JOIN matched_items m ON l.id = m.lost_item_id AND f.id = m.found_item_id
    WHERE m.id IS NULL
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { background: #007bff; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; border: none; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        .status-pending { color: #856404; background-color: #fff3cd; padding: 3px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .status-accepted { color: #155724; background-color: #d4edda; padding: 3px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .status-rejected { color: #721c24; background-color: #f8d7da; padding: 3px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; }
    </style>
</head>
<body>
<h1>Admin Dashboard</h1>
<a href="logout.php">Logout</a>

<h2>Lost Items</h2>
<?php if ($lost_items_res->num_rows > 0) { ?>
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>User ID</th>
        <th>Item Name</th>
        <th>Description</th>
        <th>Date Lost</th>
        <th>Location</th>
        <th>Actions</th>
    </tr>
    
    <?php while($row = $lost_items_res->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['user_id']; ?></td>
        <td><?php echo $row['item_name']; ?></td>
        <td><?php echo $row['description']; ?></td>
        <td><?php echo $row['date_lost']; ?></td>
        <td><?php echo $row['location']; ?></td>
        <td>
            <a href="view_user.php?id=<?php echo $row['user_id']; ?>">👤 View User</a> |
            
            <a href="update_lost.php?id=<?php echo $row['id']; ?>">📝 Update</a> |
            <a href="delete_lost.php?id=<?php echo $row['id']; ?>" 
               onclick="return confirm('Are you sure to delete this report?')">❌ Delete</a>
        </td>
    </tr>
    <?php } ?>
</table>
<?php } else { echo "<p>No lost items found.</p>"; } ?>

<h2>Found Items</h2>
<?php if ($found_items_res->num_rows > 0) { ?>
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>User ID</th>
        <th>Item Name</th>
        <th>Description</th>
        <th>Date Found</th>
        <th>Location</th>
        <th>Actions</th>
    </tr>
    <?php while($row = $found_items_res->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['user_id']; ?></td>
        <td><?php echo $row['item_name']; ?></td>
        <td><?php echo $row['description']; ?></td>
        <td><?php echo $row['date_found']; ?></td>
        <td><?php echo $row['location']; ?></td>
        <td>
            <a href="view_user.php?id=<?php echo $row['user_id']; ?>">👤 View User</a> |
        
            <a href="update_found.php?id=<?php echo $row['id']; ?>">📝 Update</a> |
            <a href="delete_found.php?id=<?php echo $row['id']; ?>" 
               onclick="return confirm('Are you sure to delete this report?')">❌ Delete</a>
        </td>
    </tr>
    <?php } ?>
</table>
<?php } else { echo "<p>No found items found.</p>"; } ?>

<h3>Possible Matches</h3>
<table border="1" cellpadding="10">
    <tr>
        <th>Lost Item</th>
        <th>Found Item</th>
        <th>Status</th>
        <th>Action</th>
        <th>Details</th> <!-- New column -->
    </tr>
    <?php
   $sql = "SELECT m.id AS match_id, m.status, 
   l.id AS lost_id, l.item_name AS lost_item, l.description AS lost_desc, l.date_lost, l.location AS lost_location, l.user_id AS lost_user,
   f.id AS found_id, f.item_name AS found_item, f.description AS found_desc, f.date_found, f.location AS found_location, f.user_id AS found_user
FROM matched_items m
JOIN lost_items l ON m.lost_item_id = l.id
JOIN found_items f ON m.found_item_id = f.id";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // Normalize status (NULL or missing -> 0/Pending)
            // Support both numeric and string statuses
            $raw_status = isset($row['status']) ? $row['status'] : null;
            if ($raw_status === null) {
                $status = 0;
            } else if (is_numeric($raw_status)) {
                $status = (int)$raw_status;
            } else {
                $status_map = [
                    'pending' => 0,
                    'accepted' => 1,
                    'rejected' => 2,
                ];
                $key = strtolower(trim($raw_status));
                $status = isset($status_map[$key]) ? $status_map[$key] : 0;
            }
            // Convert status to readable text
            $status_text = '';
            $status_class = '';
            switch($status) {
                case 0: $status_text = 'Pending'; $status_class = 'status-pending'; break;
                case 1: $status_text = 'Accepted'; $status_class = 'status-accepted'; break;
                case 2: $status_text = 'Rejected'; $status_class = 'status-rejected'; break;
                default: $status_text = 'Pending'; $status_class = 'status-pending'; break;
            }
            
            echo "<tr>
                    <td>".$row['lost_item']."</td>
                    <td>".$row['found_item']."</td>
                    <td><span class='".$status_class."'>".$status_text."</span></td>
                    <td>";
            
            // Only show accept/reject buttons for pending matches
            if ($status === 0) {
                echo "<a href='accept_match.php?id={$row['match_id']}'>✅ Accept</a> | 
                      <a href='reject_match.php?id={$row['match_id']}'>❌ Reject</a>";
            } else {
                echo "<span style='color: #666;'>".$status_text."</span>";
            }
            
            echo "</td>
                    <td>
                        <a href='view_match_details.php?lost_id=".$row['lost_id']."&found_id=".$row['found_id']."'>👀 View Reports</a>
                    </td>
                  </tr>
                  ";
        }
    } else {
        echo "<tr><td colspan='5'>No matches found</td></tr>";
    }
    ?>
</table>


</body>
</html>
