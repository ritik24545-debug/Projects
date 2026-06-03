<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = $_POST['otp'];
    if (isset($_SESSION['reset_otp']) && $_SESSION['reset_otp'] == $otp && time() < $_SESSION['reset_expires']) {
        $_SESSION['otp_verified'] = true;
        header("Location: reset_password.php");
        exit;
    } else {
        $msg = "❌ Invalid or expired OTP!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><title>Verify OTP</title>
<style>
body { font-family: Arial; text-align:center; background: #f3e5f5; padding:50px; }
form { background:white; padding:30px; border-radius:10px; display:inline-block; }
input { padding:10px; width:200px; margin:10px; }
button { background:#6a1b9a; color:white; padding:10px 20px; border:none; border-radius:5px; cursor:pointer; }
</style>
</head>
<body>
<h2>Verify OTP</h2>
<form method="post">
  <input type="text" name="otp" placeholder="Enter OTP" required><br>
  <button type="submit">Verify</button>
</form>
<p style="color:red;"><?= $msg ?? '' ?></p>
</body>
</html>
