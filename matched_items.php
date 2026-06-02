<?php
include 'db_connect.php';

// Lost items fetch
$lost_items_res = $conn->query("SELECT * FROM lost_items");
$lost_items = $lost_items_res->fetch_all(MYSQLI_ASSOC);

// Found items fetch
$found_items_res = $conn->query("SELECT * FROM found_items");
$found_items = $found_items_res->fetch_all(MYSQLI_ASSOC);

foreach ($lost_items as $lost) {
    foreach ($found_items as $found) {
        // normalize text -> lowercase
        $lost_name = strtolower($lost['item_name']);
        $found_name = strtolower($found['item_name']);
        $lost_desc = strtolower($lost['description']);
        $found_desc = strtolower($found['description']);

        // simple match check -> agar item_name ya description similar hai
        if (
            strpos($found_name, $lost_name) !== false || 
            strpos($lost_name, $found_name) !== false ||
            strpos($found_desc, $lost_desc) !== false || 
            strpos($lost_desc, $found_desc) !== false
        ) {
            // Insert into matched_items agar pehle se nahi hai
            $check = $conn->prepare("SELECT * FROM matched_items WHERE lost_item_id=? AND found_item_id=?");
            $check->bind_param("ii", $lost['id'], $found['id']);
            $check->execute();
            $result = $check->get_result();

            if ($result->num_rows == 0) {
                $stmt = $conn->prepare("INSERT INTO matched_items (lost_item_id, found_item_id, status, matched_on) VALUES (?, ?, 'pending', NOW())");
                $stmt->bind_param("ii", $lost['id'], $found['id']);
                $stmt->execute();
            }
        }
    }
}
echo "Matching process completed!";
?>
