<?php
// matched_items.php
// Provides a function run_matching($conn) that finds possible matches
// and inserts matched_items rows when not present. Does NOT echo or print.

function run_matching($conn) {
    // Fetch lost and found items
    $lost_items_res = $conn->query("SELECT * FROM lost_items");
    if (!$lost_items_res) return false;
    $lost_items = $lost_items_res->fetch_all(MYSQLI_ASSOC);

    $found_items_res = $conn->query("SELECT * FROM found_items");
    if (!$found_items_res) return false;
    $found_items = $found_items_res->fetch_all(MYSQLI_ASSOC);

    foreach ($lost_items as $lost) {
        foreach ($found_items as $found) {
            // normalize and guard against empty values
            $lost_name = strtolower(trim($lost['item_name'] ?? ''));
            $found_name = strtolower(trim($found['item_name'] ?? ''));
            $lost_desc = strtolower(trim($lost['description'] ?? ''));
            $found_desc = strtolower(trim($found['description'] ?? ''));

            if ($lost_name === '' && $found_name === '' && $lost_desc === '' && $found_desc === '') {
                continue;
            }

            // simple match heuristics
            $is_match = false;
            if ($lost_name !== '' && $found_name !== '') {
                if (strpos($found_name, $lost_name) !== false || strpos($lost_name, $found_name) !== false) {
                    $is_match = true;
                }
            }
            if (!$is_match && $lost_desc !== '' && $found_desc !== '') {
                if (strpos($found_desc, $lost_desc) !== false || strpos($lost_desc, $found_desc) !== false) {
                    $is_match = true;
                }
            }

            if ($is_match) {
                // check existing matched_items
                $check = $conn->prepare("SELECT id FROM matched_items WHERE lost_item_id = ? AND found_item_id = ?");
                if (!$check) continue;
                $check->bind_param("ii", $lost['id'], $found['id']);
                $check->execute();
                $res = $check->get_result();

                if ($res && $res->num_rows == 0) {
                    $stmt = $conn->prepare("INSERT INTO matched_items (lost_item_id, found_item_id, status, matched_on) VALUES (?, ?, 'pending', NOW())");
                    if ($stmt) {
                        $stmt->bind_param("ii", $lost['id'], $found['id']);
                        $stmt->execute();
                        $stmt->close();
                    }
                } else {
                    // optional: if row exists but status is NULL, set to pending
                    $existing = $res ? $res->fetch_assoc() : null;
                    if ($existing && isset($existing['id'])) {
                        // nothing for now
                    }
                }

                $check->close();
            }
        }
    }

    return true;
}
?>
