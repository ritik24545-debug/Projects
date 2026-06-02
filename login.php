<?php
session_start();
include 'db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, fullname, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            header("Location: dashboard.php"); // ya koi home page
            exit();
        } else {
            $message = "❌ Invalid password!";
        }
    } else {
        $message = "❌ Username not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Login</title>
</head>
<body>
  <h1>User Login</h1>
  <?php if (!empty($message)) echo "<p>$message</p>"; ?>
  <form method="post" action="">
    <label>Username</label>
    <input type="text" name="username" required><br><br>
    <label>Password</label>
    <input type="password" name="password" required><br><br>
    <button type="submit">Login</button>
  </form>
  <p>Don't have an account? <a href="register.php">Register here</a></p>
</body>
</html>
