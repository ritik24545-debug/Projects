<?php
session_start();
include 'db_connect.php';

// Agar id pass nahi hui
if(!isset($_GET['id'])){
    die("Invalid Request!");
}

$lost_id = $_GET['id'];

// Fetch report details
$stmt = $conn->prepare("SELECT * FROM lost_items WHERE id = ?");
$stmt->bind_param("i", $lost_id);
$stmt->execute();
$result = $stmt->get_result();
$lost = $result->fetch_assoc();

if(!$lost){
    die("Report not found!");
}

// Form submit hone par
if(isset($_POST['update'])){
    $item_name = $_POST['item_name'];
    $description = $_POST['description'];
    $date_lost = $_POST['date_lost'];
    $location = $_POST['location'];

    $update = $conn->prepare("UPDATE lost_items SET item_name=?, description=?, date_lost=?, location=? WHERE id=?");
    $update->bind_param("ssssi", $item_name, $description, $date_lost, $location, $lost_id);

    if($update->execute()){
        header("Location: admin_dashboard.php?msg=Report Updated Successfully");
        exit;
    } else {
        echo "Error updating report!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Lost Report</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        form { max-width: 500px; margin: auto; }
        input, textarea { width: 100%; padding: 8px; margin: 8px 0; }
        button { padding: 10px 20px; cursor: pointer; }
        .back { margin-top: 20px; display: inline-block; }
    </style>
</head>
<body>
    <h2>Update Lost Report</h2>
    <form method="post">
        <label>Item Name:</label>
        <input type="text" name="item_name" value="<?php echo htmlspecialchars($lost['item_name']); ?>" required>

        <label>Description:</label>
        <textarea name="description" required><?php echo htmlspecialchars($lost['description']); ?></textarea>

        <label>Date Lost:</label>
        <input type="date" name="date_lost" value="<?php echo $lost['date_lost']; ?>" required>

        <label>Location:</label>
        <input type="text" name="location" value="<?php echo htmlspecialchars($lost['location']); ?>" required>

        <button type="submit" name="update">Update</button>
    </form>

    <a class="back" href="admin_dashboard.php">⬅ Back to Dashboard</a>
</body>
</html>
