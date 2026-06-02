<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    die("❌ Please login first.");
}

$user_id = $_SESSION['user_id'];

// Check if match_id is provided
if (!isset($_GET['match_id'])) {
    die("❌ No match ID provided.");
}

$match_id = (int)$_GET['match_id'];

// Fetch match details with both users' contact information
$sql = "SELECT m.id AS match_id, m.status, m.matched_on,
               l.item_name AS lost_item, l.description AS lost_description, l.date_lost, l.location AS lost_location,
               f.item_name AS found_item, f.description AS found_description, f.date_found, f.location AS found_location,
               u1.id AS lost_user_id, u1.fullname AS lost_user_name, u1.email AS lost_user_email, u1.phone AS lost_user_phone,
               u2.id AS found_user_id, u2.fullname AS found_user_name, u2.email AS found_user_email, u2.phone AS found_user_phone
        FROM matched_items m
        JOIN lost_items l ON m.lost_item_id = l.id
        JOIN users u1 ON l.user_id = u1.id
        JOIN found_items f ON m.found_item_id = f.id
        JOIN users u2 ON f.user_id = u2.id
        WHERE m.id = ? AND (l.user_id = ? OR f.user_id = ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $match_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // Show contact information regardless of status
    echo "<!DOCTYPE html>";
    echo "<html lang='en'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<title>Contact Information</title>";
    echo "<style>";
    echo "body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }";
    echo ".container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
    echo ".header { text-align: center; border-bottom: 2px solid #007bff; padding-bottom: 15px; margin-bottom: 20px; }";
    echo ".item-section { display: flex; gap: 20px; margin-bottom: 20px; }";
    echo ".item-card { flex: 1; border: 2px solid #ddd; border-radius: 8px; padding: 15px; }";
    echo ".lost-item { border-color: #dc3545; }";
    echo ".found-item { border-color: #28a745; }";
    echo ".contact-section { background: #e7f3ff; padding: 20px; border-radius: 8px; margin-bottom: 20px; }";
    echo ".contact-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }";
    echo ".contact-card { background: white; padding: 15px; border-radius: 5px; border-left: 4px solid #007bff; }";
    echo ".btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px; }";
    echo ".btn:hover { background: #0056b3; }";
    echo ".back-btn { background: #6c757d; }";
    echo ".back-btn:hover { background: #545b62; }";
    echo ".status-badge { padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: bold; }";
    echo ".status-pending { background: #ffc107; color: #000; }";
    echo ".status-accepted { background: #28a745; color: white; }";
    echo ".status-rejected { background: #dc3545; color: white; }";
    echo ".accepted-highlight { background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border: 2px solid #28a745; }";
    echo ".contact-highlight { background: linear-gradient(135deg, #e7f3ff 0%, #d1ecf1 100%); border: 2px solid #007bff; }";
    echo ".success-message { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; border: 1px solid #c3e6cb; margin-bottom: 20px; text-align: center; font-size: 16px; }";
    echo "</style>";
    echo "</head>";
    echo "<body>";
    
    echo "<div class='container'>";
    echo "<div class='header'>";
    echo "<h1>📞 Contact Information</h1>";
    echo "<p>Match ID: #" . $row['match_id'] . " | Matched on: " . $row['matched_on'] . "</p>";
    
    // Show status badge
    $status_class = '';
    $status_text = '';
    switch($row['status']) {
        case 0: $status_class = 'status-pending'; $status_text = 'Pending'; break;
        case 1: $status_class = 'status-accepted'; $status_text = 'Accepted'; break;
        case 2: $status_class = 'status-rejected'; $status_text = 'Rejected'; break;
        default: $status_class = 'status-pending'; $status_text = 'Pending'; break;
    }
    echo "<p><span class='status-badge " . $status_class . "'>Status: " . $status_text . "</span></p>";
    
    // Add prominent success message for accepted matches
    if ($row['status'] == 1) {
        echo "<div class='success-message'>";
        echo "🎉 <strong>Congratulations! This match has been accepted!</strong><br>";
        echo "You can now contact each other to arrange the item return.";
        echo "</div>";
    }
    
    // Add helpful note based on status
    if ($row['status'] == 0) {
        echo "<p style='text-align: center; background: #fff3cd; padding: 10px; border-radius: 5px; border: 1px solid #ffeaa7; color: #856404;'>";
        echo "💡 <strong>Note:</strong> This match is pending. You can contact the other user to discuss the item details and arrange pickup.";
        echo "</p>";
    } elseif ($row['status'] == 1) {
        echo "<p style='text-align: center; background: #d4edda; padding: 10px; border-radius: 5px; border: 1px solid #c3e6cb; color: #155724;'>";
        echo "✅ <strong>Great!</strong> This match has been accepted. You can now contact each other to arrange the item return.";
        echo "</p>";
    } elseif ($row['status'] == 2) {
        echo "<p style='text-align: center; background: #f8d7da; padding: 10px; border-radius: 5px; border: 1px solid #f5c6cb; color: #721c24;'>";
        echo "❌ <strong>Note:</strong> This match was rejected. You can still contact each other if you need to discuss anything.";
        echo "</p>";
    }
    
    echo "</div>";
    
    // Item Details Section
    echo "<div class='item-section'>";
    echo "<div class='item-card lost-item'>";
    echo "<h3>📱 Lost Item</h3>";
    echo "<p><strong>Name:</strong> " . htmlspecialchars($row['lost_item']) . "</p>";
    echo "<p><strong>Description:</strong> " . htmlspecialchars($row['lost_description']) . "</p>";
    echo "<p><strong>Date Lost:</strong> " . htmlspecialchars($row['date_lost']) . "</p>";
    echo "<p><strong>Location:</strong> " . htmlspecialchars($row['lost_location']) . "</p>";
    echo "</div>";
    
    echo "<div class='item-card found-item'>";
    echo "<h3>🔍 Found Item</h3>";
    echo "<p><strong>Name:</strong> " . htmlspecialchars($row['found_item']) . "</p>";
    echo "<p><strong>Description:</strong> " . htmlspecialchars($row['found_description']) . "</p>";
    echo "<p><strong>Date Found:</strong> " . htmlspecialchars($row['date_found']) . "</p>";
    echo "<p><strong>Location:</strong> " . htmlspecialchars($row['found_location']) . "</p>";
    echo "</div>";
    echo "</div>";
    
    // Contact Information Section - Enhanced for accepted matches
    $contact_class = ($row['status'] == 1) ? 'contact-highlight' : '';
    echo "<div class='contact-section " . $contact_class . "'>";
    echo "<h2>👥 Contact Details</h2>";
    
    if ($row['status'] == 1) {
        echo "<p style='text-align: center; font-size: 18px; color: #155724; margin-bottom: 20px;'>";
        echo "🔑 <strong>Contact information is now available!</strong>";
        echo "</p>";
    }
    
    echo "<div class='contact-grid'>";
    
    // Lost User Contact
    echo "<div class='contact-card'>";
    echo "<h3>📱 Lost Item Owner</h3>";
    echo "<p><strong>Name:</strong> " . htmlspecialchars($row['lost_user_name']) . "</p>";
    echo "<p><strong>Email:</strong> <a href='mailto:" . htmlspecialchars($row['lost_user_email']) . "'>" . htmlspecialchars($row['lost_user_email']) . "</a></p>";
    echo "<p><strong>Phone:</strong> <a href='tel:" . htmlspecialchars($row['lost_user_phone']) . "'>" . htmlspecialchars($row['lost_user_phone']) . "</a></p>";
    echo "</div>";
    
    // Found User Contact
    echo "<div class='contact-card'>";
    echo "<h3>🔍 Found Item Owner</h3>";
    echo "<p><strong>Name:</strong> " . htmlspecialchars($row['found_user_name']) . "</p>";
    echo "<p><strong>Email:</strong> <a href='mailto:" . htmlspecialchars($row['found_user_email']) . "'>" . htmlspecialchars($row['found_user_email']) . "</a></p>";
    echo "<p><strong>Phone:</strong> <a href='tel:" . htmlspecialchars($row['found_user_phone']) . "'>" . htmlspecialchars($row['found_user_phone']) . "</a></p>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    
    // Navigation Buttons
    echo "<div style='text-align: center;'>";
    echo "<a href='dashboard.php' class='btn back-btn'>← Back to Dashboard</a>";
    echo "<a href='matched_items.php' class='btn'>View All Matches</a>";
    echo "</div>";
    
    echo "</div>";
    echo "</body>";
    echo "</html>";
    
} else {
    echo "<div style='text-align: center; margin: 50px;'>";
    echo "<h2>❌ Match Not Found</h2>";
    echo "<p>This match could not be found or you don't have permission to view it.</p>";
    echo "<a href='dashboard.php' class='btn back-btn'>← Back to Dashboard</a>";
    echo "</div>";
}

$stmt->close();
$conn->close();
?>
