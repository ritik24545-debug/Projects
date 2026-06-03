<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Forgot Password | Lost & Found System</title>
<style>
body {
  font-family: Arial, sans-serif;
  background: linear-gradient(to right, #f3e5f5, #e1bee7);
  text-align: center;
  padding: 50px;
}
.container {
  background: white;
  border-radius: 10px;
  padding: 30px;
  width: 350px;
  margin: 0 auto;
  box-shadow: 0 0 15px rgba(0,0,0,0.1);
}
input {
  width: 90%;
  padding: 10px;
  margin: 10px 0;
  border-radius: 6px;
  border: 1px solid #ccc;
}
button {
  background: #6a1b9a;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 6px;
  cursor: pointer;
}
button:hover { background: #8e24aa; }
a { color: #6a1b9a; text-decoration: none; }
</style>
</head>
<body>
<div class="container">
<h2>Forgot Password?</h2>
<p>Enter your registered <strong>Email</strong> or <strong>Phone</strong> to receive an OTP.</p>
<form action="send_reset.php" method="post">
  <input type="text" name="email_or_phone" placeholder="Email or Phone" required><br>
  <button type="submit">Send OTP</button>
</form>
<p><a href="login.php">← Back to Login</a></p>
</div>
</body>
</html>
