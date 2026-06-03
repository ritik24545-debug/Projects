<?php
// Database setup script for password reset functionality
include 'db_connect.php';

echo "<h2>Database Setup for Password Reset</h2>";

// Check if password_reset_tokens table exists
$check_table = "SHOW TABLES LIKE 'password_reset_tokens'";
$result = $conn->query($check_table);

if ($result->num_rows == 0) {
    // Create the table
    $create_table = "
    CREATE TABLE password_reset_tokens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        email VARCHAR(255) NOT NULL,
        otp VARCHAR(6) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        expires_at TIMESTAMP NOT NULL,
        used BOOLEAN DEFAULT FALSE,
        INDEX idx_user_id (user_id),
        INDEX idx_email (email),
        INDEX idx_otp (otp),
        INDEX idx_expires_at (expires_at),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    
    if ($conn->query($create_table)) {
        echo "<p style='color: green;'>✅ password_reset_tokens table created successfully!</p>";
    } else {
        echo "<p style='color: red;'>❌ Error creating table: " . $conn->error . "</p>";
    }
} else {
    echo "<p style='color: blue;'>ℹ️ password_reset_tokens table already exists.</p>";
}

// Check if users table has email column
$check_email_column = "SHOW COLUMNS FROM users LIKE 'email'";
$result = $conn->query($check_email_column);

if ($result->num_rows == 0) {
    // Add email column
    $add_email_column = "ALTER TABLE users ADD COLUMN email VARCHAR(255) UNIQUE";
    if ($conn->query($add_email_column)) {
        echo "<p style='color: green;'>✅ Email column added to users table!</p>";
    } else {
        echo "<p style='color: red;'>❌ Error adding email column: " . $conn->error . "</p>";
    }
} else {
    echo "<p style='color: blue;'>ℹ️ Email column already exists in users table.</p>";
}

// Check PHPMailer availability
echo "<h3>PHPMailer Check</h3>";

$composerAutoload = __DIR__ . '/vendor/autoload.php';
$manualBase = __DIR__ . '/lib/phpmailer/src';

if (file_exists($composerAutoload)) {
    echo "<p style='color: green;'>✅ PHPMailer found via Composer autoload</p>";
} else if (file_exists($manualBase . '/PHPMailer.php')) {
    echo "<p style='color: green;'>✅ PHPMailer found in lib/phpmailer directory</p>";
} else {
    echo "<p style='color: red;'>❌ PHPMailer not found!</p>";
    echo "<p>Please install PHPMailer using one of these methods:</p>";
    echo "<ul>";
    echo "<li><strong>Composer:</strong> Run 'composer require phpmailer/phpmailer' in your project directory</li>";
    echo "<li><strong>Manual:</strong> Download PHPMailer ZIP and extract to lib/phpmailer directory</li>";
    echo "</ul>";
}

echo "<h3>SMTP Configuration</h3>";
$config = include 'smtp_config.php';
if ($config['host'] === 'smtp.example.com') {
    echo "<p style='color: orange;'>⚠️ Please update smtp_config.php with your actual SMTP settings!</p>";
} else {
    echo "<p style='color: green;'>✅ SMTP configuration appears to be set up</p>";
}

echo "<hr>";
echo "<p><a href='login.php'>← Back to Login</a></p>";
?>
