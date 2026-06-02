<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$fullname = $_SESSION['fullname'];

// Fetch user's lost items
$lost_items_res = $conn->prepare("SELECT * FROM lost_items WHERE user_id = ?");
$lost_items_res->bind_param("i", $user_id);
$lost_items_res->execute();
$lost_items = $lost_items_res->get_result();

// Fetch user's found items
$found_items_res = $conn->prepare("SELECT * FROM found_items WHERE user_id = ?");
$found_items_res->bind_param("i", $user_id);
$found_items_res->execute();
$found_items = $found_items_res->get_result();

// Fetch matched items with match ID
$matched_res = $conn->prepare("
    SELECT m.id AS match_id, l.item_name AS lost_item, f.item_name AS found_item, 
           m.status, m.matched_on, l.user_id AS lost_user_id, f.user_id AS found_user_id
    FROM matched_items m
    JOIN lost_items l ON m.lost_item_id = l.id
    JOIN found_items f ON m.found_item_id = f.id
    WHERE l.user_id = ? OR f.user_id = ?
");
$matched_res->bind_param("ii", $user_id, $user_id);
$matched_res->execute();
$matched_items = $matched_res->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { background: #007bff; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; border: none; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        .status-accepted { color: green; font-weight: bold; }
        .status-pending { color: orange; font-weight: bold; }
        .status-rejected { color: red; font-weight: bold; }
    </style>
</head>
<body>
<h1>Welcome, <?php echo htmlspecialchars($fullname); ?></h1>
<nav>
    <a href="lost_item.php">Report Lost Item</a> |
    <a href="found_item.php">Report Found Item</a> | 
    <a href="logout.php">Logout</a> 
</nav>

<h2>Your Lost Items</h2>
<?php if ($lost_items->num_rows > 0) { ?>
    <table>
        <tr>
            <th>Item Name</th>
            <th>Description</th>
            <th>Date Lost</th>
            <th>Location</th>
        </tr>
        <?php while ($row = $lost_items->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['item_name']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td><?php echo htmlspecialchars($row['date_lost']); ?></td>
            <td><?php echo htmlspecialchars($row['location']); ?></td>
        </tr>
        <?php } ?>
    </table>
<?php } else { echo "<p>No lost items reported yet.</p>"; } ?>

<h2>Your Found Items</h2>
<?php if ($found_items->num_rows > 0) { ?>
    <table>
        <tr>
            <th>Item Name</th>
            <th>Description</th>
            <th>Date Found</th>
            <th>Location</th>
        </tr>
        <?php while ($row = $found_items->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['item_name']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td><?php echo htmlspecialchars($row['date_found']); ?></td>
            <td><?php echo htmlspecialchars($row['location']); ?></td>
        </tr>
        <?php } ?>
    </table>
<?php } else { echo "<p>No found items reported yet.</p>"; } ?>

<h2>Matched Items Status</h2>
<?php if ($matched_items->num_rows > 0) { ?>
<table>
    <tr>
        <th>Lost Item</th>
        <th>Found Item</th>
        <th>Status</th>
        <th>Matched On</th>
        <th>Action</th>
    </tr>
    <?php while($row = $matched_items->fetch_assoc()) { ?>
    <?php
        $raw_status = $row['status'];
        if ($raw_status === null) { $status_val = 0; }
        else if (is_numeric($raw_status)) { $status_val = (int)$raw_status; }
        else {
            $map = ['pending'=>0,'accepted'=>1,'rejected'=>2];
            $status_val = $map[strtolower(trim($raw_status))] ?? 0;
        }
    ?>
    <tr <?php if($status_val === 1) echo 'style="background-color: #d4edda;"'; ?>>
        <td><?php echo htmlspecialchars($row['lost_item']); ?></td>
        <td><?php echo htmlspecialchars($row['found_item']); ?></td>
        <td>
            <?php 
            $status_class = '';
            $status_text = '';
            switch($status_val) {
                case 0: $status_class = 'status-pending'; $status_text = 'Pending'; break;
                case 1: $status_class = 'status-accepted'; $status_text = '✅ Accepted'; break;
                case 2: $status_class = 'status-rejected'; $status_text = '❌ Rejected'; break;
                default: $status_class = 'status-pending'; $status_text = 'Pending'; break;
            }
            ?>
            <span class="<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
        </td>
        <td><?php echo htmlspecialchars($row['matched_on']); ?></td>
        <td>
            <?php if($status_val === 1) { ?>
                <a href="view_contact.php?match_id=<?php echo $row['match_id']; ?>" >Get Contact Info</a>
            <?php } else { ?>
                <a href="view_contact.php?match_id=<?php echo $row['match_id']; ?>" >View Contact</a>
            <?php } ?>
        </td>
    </tr>
    <?php } ?>
</table>


<?php } else { echo "<p>No matched items yet.</p>"; } ?>

</body>
</html>
