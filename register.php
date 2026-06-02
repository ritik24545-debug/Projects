<?php
include 'db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email    = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];
    $phone    = $_POST['phone'];
    $aadhar   = $_POST['aadhar'];

    if ($password !== $confirm) {
        $message = "❌ Passwords do not match!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check duplicate email or username
        $check = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $check->bind_param("ss", $email, $username);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $message = "⚠️ Email or Username already registered!";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (fullname, email, username, password, phone, aadhar) 
                                    VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $fullname, $email, $username, $hashed_password, $phone, $aadhar);

            if ($stmt->execute()) {
                $message = "✅ Registration successful! <a href='login.php'>Login Here</a>";
            } else {
                $message = "❌ Error: " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Registration</title>
</head>
<body>
  <header>
    <h1>User Registration</h1>
    <nav>
      <a href="index.html">Home</a> |
      <a href="login.php">Login</a> |
      <a href="admin_login.php">Admin Login</a>
    </nav>
  </header>

  <?php if (!empty($message)) echo "<p>$message</p>"; ?>

  <form action="" method="post">
    <label>Full Name</label>
    <input type="text" name="fullname" required><br><br>

    <label>Email</label>
    <input type="email" name="email" required><br><br>

    <label>Username</label>
    <input type="text" name="username" required><br><br>

    <label>Password</label>
    <input type="password" name="password" required><br><br>

    <label>Confirm Password</label>
    <input type="password" name="confirm_password" required><br><br>

    <label>Phone Number</label>
    <input type="tel" name="phone" required><br><br>

    <label>Aadhar/ID Number</label>
    <input type="text" name="aadhar" required><br><br>

    <button type="submit">Register</button>
  </form>
</body>
</html>
