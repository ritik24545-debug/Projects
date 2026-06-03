<?php
function autoMatchItems($conn) {
    // Fetch all lost and found items
    $lost_items = $conn->query("SELECT * FROM lost_items");
    $found_items = $conn->query("SELECT * FROM found_items");

    if (!$lost_items || !$found_items) {
        return 0;
    }

    $matchCount = 0;

    // Helper: clean and normalize text
    function normalizeText($text) {
        $text = strtolower(trim($text));
        $text = preg_replace("/[^a-z0-9\s]/", "", $text);
        return $text;
    }

    // Helper: check if any common words exist between two strings
    function hasCommonWords($str1, $str2) {
        $words1 = explode(" ", $str1);
        $words2 = explode(" ", $str2);
        foreach ($words1 as $w1) {
            $w1 = trim($w1);
            if (strlen($w1) < 2) continue; // skip small words
            foreach ($words2 as $w2) {
                $w2 = trim($w2);
                if (strlen($w2) < 2) continue;
                // Check partial match too (at least 4 chars)
                if (strlen($w1) >= 4 && strlen($w2) >= 4) {
                    similar_text($w1, $w2, $percent);
                    if ($percent > 70) {
                        return true;
                    }
                }
                if ($w1 === $w2) {
                    return true;
                }
            }
        }
        return false;
    }

    while ($lost = $lost_items->fetch_assoc()) {
        $lostName = normalizeText($lost['item_name']);
        $lostDesc = normalizeText($lost['description']);
        $lostLoc  = normalizeText($lost['location']);

        // Compare with every found item
        $found_items->data_seek(0);
        while ($found = $found_items->fetch_assoc()) {
            $foundName = normalizeText($found['item_name']);
            $foundDesc = normalizeText($found['description']);
            $foundLoc  = normalizeText($found['location']);

            // Check matching - SUPER FLEXIBLE NOW!
            similar_text($lostName, $foundName, $namePercent);
            similar_text($lostDesc, $foundDesc, $descPercent);
            similar_text($lostLoc, $foundLoc, $locPercent);

            $nameMatch = ($namePercent > 50 || hasCommonWords($lostName, $foundName));
            $descMatch = ($descPercent > 40 || hasCommonWords($lostDesc, $foundDesc));
            $locMatch  = ($locPercent > 40 || hasCommonWords($lostLoc, $foundLoc));

            // Any 2 of them match OR name matches OR (desc + loc) match
            $matchCriteriaMet = $nameMatch || $descMatch || $locMatch;

            if ($matchCriteriaMet) {
                $check = $conn->prepare("SELECT id FROM matched_items WHERE lost_item_id=? AND found_item_id=?");
                $check->bind_param("ii", $lost['id'], $found['id']);
                $check->execute();
                $check->store_result();

                if ($check->num_rows == 0) {
                    $insert = $conn->prepare("INSERT INTO matched_items (lost_item_id, found_item_id, status, matched_on) VALUES (?, ?, 0, NOW())");
                    $insert->bind_param("ii", $lost['id'], $found['id']);
                    $insert->execute();
                    $insert->close();
                    $matchCount++;
                }
                $check->close();
            }
        }
    }

    return $matchCount;
}
?>