<?php
session_start();
include 'db_connect.php';

if (isset($_POST['login'])) {
    // Trim extra spaces
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Prepare and execute query
   // Change from 'admins' to 'admin'
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ? AND password = ?");

    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Login successful
        $admin = $result->fetch_assoc();
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];

        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
</head>
<body>
<h1>Admin Login</h1>

<?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>

<form method="post">
    <label>Username:</label><br>
    <input type="text" name="username" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit" name="login">Login</button>
</form>
</body>
</html>
