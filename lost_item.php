<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id     = $_SESSION['user_id'];
    $item_name   = $_POST['item_name'];
    $description = $_POST['description'];
    $date_lost   = $_POST['date_lost'];
    $location    = $_POST['location'];

    $stmt = $conn->prepare("INSERT INTO lost_items (user_id, item_name, description, date_lost, location) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $item_name, $description, $date_lost, $location);

    if ($stmt->execute()) {
        $message = "✅ Lost item reported successfully!";
    } else {
        $message = "❌ Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Lost Item</title>
</head>
<body>
<a href="dashboard.php" style="text-decoration:none; font-size:10px;">&#8592; Back to Dashboard</a>

<h1>Report Lost Item</h1>
<?php if (!empty($message)) echo "<p>$message</p>"; ?>
<form method="POST" action="">
    <label>Item Name:</label>
    <input type="text" name="item_name" required><br><br>

    <label>Description:</label>
    <textarea name="description" required></textarea><br><br>

    
    <label>Date Lost:</label>
    <input type="date" name="date_lost" required><br><br>

    <label>Location:</label>
    <input type="text" name="location" required><br><br>

    
    <button type="submit" name="submit">Report Lost Item</button>
    <button type="button" onclick="window.location.href='dashboard.php'">Cancel</button>

</form>
</body>
</html>
