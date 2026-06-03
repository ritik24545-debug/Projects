<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db_connect.php';

echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 30px auto; padding: 30px; background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);'>";
echo "<h1 style='color: #6d28d9; text-align: center; margin-bottom: 30px;'>🎯 Inserting Demo Data...</h1>";

// --------------------------
// 1. Insert Demo Admin (if not exists)
// --------------------------
$admin_check = $conn->query("SELECT * FROM admin WHERE username = 'admin'");
if ($admin_check->num_rows === 0) {
    $stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
    $admin_user = 'admin';
    $admin_pass = 'admin123';
    $stmt->bind_param("ss", $admin_user, $admin_pass);
    if ($stmt->execute()) {
        echo "<div style='background: #d1fae5; color: #059669; padding: 15px; border-radius: 10px; margin: 10px 0;'><b>✅ Admin Created:</b> Username = admin, Password = admin123</div>";
    }
    $stmt->close();
} else {
    echo "<div style='background: #fef3c7; color: #d97706; padding: 15px; border-radius: 10px; margin: 10px 0;'><b>ℹ️ Admin Already Exists:</b> Username = admin</div>";
}

// --------------------------
// 2. Insert Demo Users
// --------------------------
$demo_users = [
    ['fullname' => 'Rahul Sharma', 'email' => 'rahul@example.com', 'username' => 'rahul', 'password' => 'password123', 'phone' => '9876543210', 'aadhar' => '123456789012'],
    ['fullname' => 'Priya Patel', 'email' => 'priya@example.com', 'username' => 'priya', 'password' => 'password123', 'phone' => '9876509876', 'aadhar' => '234567890123'],
    ['fullname' => 'Amit Kumar', 'email' => 'amit@example.com', 'username' => 'amit', 'password' => 'password123', 'phone' => '9123456780', 'aadhar' => '345678901234'],
    ['fullname' => 'Sneha Singh', 'email' => 'sneha@example.com', 'username' => 'sneha', 'password' => 'password123', 'phone' => '9988776655', 'aadhar' => '456789012345']
];

$user_ids = [];
foreach ($demo_users as $user) {
    // Check if user already exists
    $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check->bind_param("ss", $user['username'], $user['email']);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows > 0) {
        $existing_user = $result->fetch_assoc();
        $user_ids[] = $existing_user['id'];
        echo "<div style='background: #fef3c7; color: #d97706; padding: 10px 15px; border-radius: 10px; margin: 5px 0;'><b>ℹ️ User Already Exists:</b> " . htmlspecialchars($user['username']) . "</div>";
    } else {
        $hashed_pass = password_hash($user['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (fullname, email, username, password, phone, aadhar) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $user['fullname'], $user['email'], $user['username'], $hashed_pass, $user['phone'], $user['aadhar']);
        
        if ($stmt->execute()) {
            $user_id = $conn->insert_id;
            $user_ids[] = $user_id;
            echo "<div style='background: #d1fae5; color: #059669; padding: 10px 15px; border-radius: 10px; margin: 5px 0;'><b>✅ User Created:</b> " . htmlspecialchars($user['fullname']) . " (" . htmlspecialchars($user['username']) . " / password123)</div>";
        }
        $stmt->close();
    }
    $check->close();
}

// If we didn't get any user IDs from insert, get existing ones to use for items
if (empty($user_ids)) {
    $result = $conn->query("SELECT id FROM users LIMIT 4");
    while ($row = $result->fetch_assoc()) {
        $user_ids[] = $row['id'];
    }
}

// --------------------------
// 3. Insert Demo Lost Items
// --------------------------
$demo_lost = [
    ['item_name' => 'Blue Wallet', 'description' => 'A blue leather wallet with some cash and cards inside', 'date_lost' => date('Y-m-d', strtotime('-3 days')), 'location' => 'College Canteen'],
    ['item_name' => 'Black Backpack', 'description' => 'Black Nike backpack with laptop and books', 'date_lost' => date('Y-m-d', strtotime('-5 days')), 'location' => 'Library'],
    ['item_name' => 'Samsung Phone', 'description' => 'Samsung Galaxy S23, black color, with screen protector', 'date_lost' => date('Y-m-d', strtotime('-1 day')), 'location' => 'Sports Ground']
];

$lost_item_ids = [];
for ($i = 0; $i < count($demo_lost); $i++) {
    $item = $demo_lost[$i];
    $user_id = $user_ids[$i % count($user_ids)];
    
    $stmt = $conn->prepare("INSERT INTO lost_items (user_id, item_name, description, date_lost, location) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $item['item_name'], $item['description'], $item['date_lost'], $item['location']);
    
    if ($stmt->execute()) {
        $lost_item_ids[] = $conn->insert_id;
        echo "<div style='background: #dbeafe; color: #1e40af; padding: 10px 15px; border-radius: 10px; margin: 5px 0;'><b>📌 Lost Item Added:</b> " . htmlspecialchars($item['item_name']) . "</div>";
    }
    $stmt->close();
}

// --------------------------
// 4. Insert Demo Found Items
// --------------------------
$demo_found = [
    ['item_name' => 'Blue Wallet', 'description' => 'Found a blue leather wallet near the canteen', 'date_found' => date('Y-m-d', strtotime('-2 days')), 'location' => 'College Canteen'],
    ['item_name' => 'Black Backpack', 'description' => 'Black backpack found in the library reading room', 'date_found' => date('Y-m-d', strtotime('-4 days')), 'location' => 'Library'],
    ['item_name' => 'Water Bottle', 'description' => 'Milton water bottle, blue color', 'date_found' => date('Y-m-d'), 'location' => 'Computer Lab']
];

$found_item_ids = [];
for ($i = 0; $i < count($demo_found); $i++) {
    $item = $demo_found[$i];
    $user_idx = ($i + 1) % count($user_ids); // Different user from who lost it
    $user_id = $user_ids[$user_idx];
    
    $stmt = $conn->prepare("INSERT INTO found_items (user_id, item_name, description, date_found, location) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $item['item_name'], $item['description'], $item['date_found'], $item['location']);
    
    if ($stmt->execute()) {
        $found_item_ids[] = $conn->insert_id;
        echo "<div style='background: #dcfce7; color: #166534; padding: 10px 15px; border-radius: 10px; margin: 5px 0;'><b>🔍 Found Item Added:</b> " . htmlspecialchars($item['item_name']) . "</div>";
    }
    $stmt->close();
}

// --------------------------
// 5. Insert Demo Matched Items (Pending)
// --------------------------
if (!empty($lost_item_ids) && !empty($found_item_ids)) {
    // Match first lost item with first found item (Blue Wallet)
    if (isset($lost_item_ids[0]) && isset($found_item_ids[0])) {
        $stmt = $conn->prepare("INSERT INTO matched_items (lost_item_id, found_item_id, status) VALUES (?, ?, 0)");
        $stmt->bind_param("ii", $lost_item_ids[0], $found_item_ids[0]);
        
        if ($stmt->execute()) {
            echo "<div style='background: #fef3c7; color: #92400e; padding: 10px 15px; border-radius: 10px; margin: 5px 0;'><b>🤝 Match Created:</b> Blue Wallet (Pending)</div>";
        }
        $stmt->close();
    }
    
    // Match second lost item with second found item (Black Backpack) - status accepted
    if (isset($lost_item_ids[1]) && isset($found_item_ids[1])) {
        $stmt = $conn->prepare("INSERT INTO matched_items (lost_item_id, found_item_id, status) VALUES (?, ?, 1)");
        $stmt->bind_param("ii", $lost_item_ids[1], $found_item_ids[1]);
        
        if ($stmt->execute()) {
            echo "<div style='background: #d1fae5; color: #059669; padding: 10px 15px; border-radius: 10px; margin: 5px 0;'><b>✅ Match Created:</b> Black Backpack (Accepted)</div>";
        }
        $stmt->close();
    }
}

echo "<hr style='border: none; border-top: 2px solid #ede9fe; margin: 30px 0;'>";
echo "<div style='text-align: center;'>";
echo "<h2 style='color: #6d28d9; margin-bottom: 20px;'>🎉 Demo Data Successfully Inserted!</h2>";
echo "<p style='font-size: 16px; color: #475569; margin-bottom: 20px;'>Your system is now populated with sample data to test all features!</p>";
echo "<div style='display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;'>";
echo "<a href='index.html' style='background: linear-gradient(135deg, #6d28d9 0%, #4f46e5 100%); color: white; padding: 12px 25px; border-radius: 10px; text-decoration: none; font-weight: bold;'>🏠 Go to Home Page</a>";
echo "<a href='admin_login.php' style='background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); color: white; padding: 12px 25px; border-radius: 10px; text-decoration: none; font-weight: bold;'>👑 Admin Login</a>";
echo "<a href='login.php' style='background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white; padding: 12px 25px; border-radius: 10px; text-decoration: none; font-weight: bold;'>👤 User Login</a>";
echo "</div>";
echo "<div style='margin-top: 30px; padding: 20px; background: #f1f5f9; border-radius: 15px; text-align: left;'>";
echo "<h3 style='color: #1e293b; margin-bottom: 15px;'>📋 Credentials for Testing:</h3>";
echo "<ul style='list-style: none; padding: 0;'>";
echo "<li style='padding: 8px 0;'><b>Admin:</b> admin / admin123</li>";
echo "<li style='padding: 8px 0;'><b>User 1:</b> rahul / password123</li>";
echo "<li style='padding: 8px 0;'><b>User 2:</b> priya / password123</li>";
echo "<li style='padding: 8px 0;'><b>User 3:</b> amit / password123</li>";
echo "<li style='padding: 8px 0;'><b>User 4:</b> sneha / password123</li>";
echo "</ul>";
echo "</div>";
echo "</div>";
echo "</div>";

$conn->close();
?>