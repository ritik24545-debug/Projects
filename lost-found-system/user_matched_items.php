<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// Fetch matched items for this user (use prepared statement)
$sql = "
    SELECT m.id AS match_id,
           l.item_name AS lost_item,
           f.item_name AS found_item,
           COALESCE(m.status, 'pending') AS status,
           m.matched_on
    FROM matched_items m
    JOIN lost_items l ON m.lost_item_id = l.id
    JOIN found_items f ON m.found_item_id = f.id
    WHERE l.user_id = ? OR f.user_id = ?
    ORDER BY m.matched_on DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Matched Items Status</h2>
<table border="1" cellpadding="10">
    <tr>
        <th>Lost Item</th>
        <th>Found Item</th>
        <th>Status</th>
        <th>Matched On</th>
        <th>Action</th>
    </tr>

<?php while($row = $result->fetch_assoc()): 
    $status_raw = strtolower(trim($row['status'] ?? 'pending'));
    $status_text = ($status_raw === 'accepted') ? 'Accepted' : (($status_raw === 'rejected') ? 'Rejected' : 'Pending');
?>
    <tr>
        <td><?= htmlspecialchars($row['lost_item']) ?></td>
        <td><?= htmlspecialchars($row['found_item']) ?></td>
        <td><?= $status_text ?></td>
        <td><?= htmlspecialchars($row['matched_on']) ?></td>
        <td>
            <a href="view_match_details.php?lost_id=<?= urlencode($row['match_id']) ?>">View</a>
            <?php if ($status_raw === 'accepted'): ?>
                | <a href="view_contact.php?match_id=<?= $row['match_id'] ?>">View Contact</a>
            <?php endif; ?>
        </td>
    </tr>
<?php endwhile; ?>
</table>

<?php
$stmt->close();
?>
