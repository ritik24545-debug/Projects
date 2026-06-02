<?php
session_start();
include 'db_connect.php';

$user_id = $_SESSION['user_id'];

// Get matched items where current user is involved
$sql = "SELECT m.id AS match_id, l.item_name AS lost_item, f.item_name AS found_item, 
               m.status, m.matched_on, l.user_id AS lost_user, f.user_id AS found_user
        FROM matched_items m
        JOIN lost_items l ON m.lost_item_id = l.id
        JOIN found_items f ON m.found_item_id = f.id
        WHERE (l.user_id = '$user_id' OR f.user_id = '$user_id') 
        AND m.status = 'Accepted'";
$result = $conn->query($sql);
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

<?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['lost_item'] ?></td>
        <td><?= $row['found_item'] ?></td>
        <td><?= $row['status'] ?></td>
        <td><?= $row['matched_on'] ?></td>
        <td>
            <?php if($row['status'] == 'Accepted'): ?>
                <a href="view_contact.php?match_id=<?= $row['match_id'] ?>">View Contact</a>
            <?php else: ?>
                Pending
            <?php endif; ?>
        </td>
    </tr>
<?php endwhile; ?>
</table>
