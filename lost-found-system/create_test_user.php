<?php
include 'db_connect.php';

// Test user details
$fullname = "Ritik Kumar";
$email = "ritik@test.com";
$username = "ritik";
$password = "123456"; // Password will be hashed
$phone = "9876543210";
$aadhar = "123456789012";

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if user already exists
$check = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
$check->bind_param("ss", $email, $username);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo "⚠️ User already exists!<br>";
    echo "Login with: Username = <strong>ritik</strong>, Password = <strong>123456</strong><br><br>";
} else {
    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (fullname, email, username, password, phone, aadhar) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $fullname, $email, $username, $hashed_password, $phone, $aadhar);
    
    if ($stmt->execute()) {
        echo "✅ Test user created successfully!<br><br>";
        echo "Login Details:<br>";
        echo "Username: <strong>ritik</strong><br>";
        echo "Password: <strong>123456</strong><br><br>";
    } else {
        echo "❌ Error: " . $stmt->error . "<br>";
    }
}

echo "<a href='login.php'>Go to Login Page</a>";
$conn->close();
?>
