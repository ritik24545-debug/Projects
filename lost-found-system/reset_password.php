<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified']) {
    header("Location: forgot_password.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $uid = $_SESSION['reset_user_id'];

    $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
    $stmt->bind_param("si", $new_pass, $uid);
    if ($stmt->execute()) {
        session_destroy();
        $msg = "✅ Password reset successful! <a href='login.php'>Login Now</a>";
    } else {
        $msg = "❌ Something went wrong!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Reset Password</title></head>
<body style="font-family:Arial;text-align:center;background:#f3e5f5;padding:50px;">
<h2>Reset Password</h2>
<form method="post" style="background:white;padding:30px;border-radius:10px;display:inline-block;">
  <input type="password" name="password" placeholder="Enter new password" required><br>
  <button type="submit">Reset Password</button>
</form>
<p><?= $msg ?? '' ?></p>
</body>
</html>
