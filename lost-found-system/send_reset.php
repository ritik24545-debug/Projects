<?php
session_start();
include 'db_connect.php';
require 'config_twilio.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = trim($_POST['email_or_phone']);
    $otp = rand(100000, 999999);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR phone = ?");
    $stmt->bind_param("ss", $input, $input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        $_SESSION['reset_user_id'] = $user['id'];
        $_SESSION['reset_otp'] = $otp;
        $_SESSION['reset_expires'] = time() + 900;

        $msg = "";

        if (!empty($user['phone'])) {
            // Try SMS
            try {
                $twilio->messages->create(
                    $user['phone'],
                    [
                        'from' => $from,
                        'body' => "Your OTP for Lost & Found password reset: $otp (valid for 15 minutes)"
                    ]
                );
                $msg = "✅ OTP sent to your registered mobile!";
            } catch (Exception $e) {
                $msg = "⚠️ SMS failed: " . $e->getMessage();
            }
        }

        // Fallback Email
        if (empty($msg) && !empty($user['email'])) {
            $subject = "Lost & Found Password Reset OTP";
            $body = "Your OTP for Lost & Found System is: $otp\n\nThis OTP is valid for 15 minutes.";
            $headers = "From: lostfound@collegeproject.com\r\n";

            if (mail($user['email'], $subject, $body, $headers)) {
                $msg = "✅ OTP sent to your registered email!";
            } else {
                $msg = "⚠️ Could not send email. Use demo OTP: $otp";
            }
        }

        echo "<div style='text-align:center;margin-top:50px;'>
                <h3>$msg</h3>
                <a href='verify_otp.php' style='text-decoration:none;color:#6a1b9a;'>→ Enter OTP</a>
              </div>";
        exit;
    } else {
        $msg = "❌ No user found with that email or phone!";
    }
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Forgot Password</title></head>
<body style="text-align:center;font-family:Arial;background:#f3e5f5;padding:50px;">
<h2>Forgot Password</h2>
<form method="post" style="display:inline-block;background:white;padding:30px;border-radius:10px;">
  <input type="text" name="email_or_phone" placeholder="Email or Phone" required style="padding:10px;width:250px;"><br>
  <button type="submit" style="background:#6a1b9a;color:white;padding:10px 20px;border:none;border-radius:6px;">Send OTP</button>
</form>
<p style="color:red;"><?= $msg ?? '' ?></p>
</body>
</html>
