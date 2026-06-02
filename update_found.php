<?php
session_start();
include 'db_connect.php';

// Validate id param
if(!isset($_GET['id'])){
    die("Invalid Request!");
}

$found_id = $_GET['id'];

// Fetch found report details
$stmt = $conn->prepare("SELECT * FROM found_items WHERE id = ?");
$stmt->bind_param("i", $found_id);
$stmt->execute();
$result = $stmt->get_result();
$found = $result->fetch_assoc();

if(!$found){
    die("Report not found!");
}

// Handle form submit
if(isset($_POST['update'])){
    $item_name = $_POST['item_name'];
    $description = $_POST['description'];
    $date_found = $_POST['date_found'];
    $location = $_POST['location'];

    $update = $conn->prepare("UPDATE found_items SET item_name=?, description=?, date_found=?, location=? WHERE id=?");
    $update->bind_param("ssssi", $item_name, $description, $date_found, $location, $found_id);

    if($update->execute()){
        header("Location: admin_dashboard.php?msg=Found Report Updated Successfully");
        exit;
    } else {
        echo "Error updating report!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Found Report</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        form { max-width: 500px; margin: auto; }
        input, textarea { width: 100%; padding: 8px; margin: 8px 0; }
        button { padding: 10px 20px; cursor: pointer; }
        .back { margin-top: 20px; display: inline-block; }
    </style>
    </head>
<body>
    <h2>Update Found Report</h2>
    <form method="post">
        <label>Item Name:</label>
        <input type="text" name="item_name" value="<?php echo htmlspecialchars($found['item_name']); ?>" required>

        <label>Description:</label>
        <textarea name="description" required><?php echo htmlspecialchars($found['description']); ?></textarea>

        <label>Date Found:</label>
        <input type="date" name="date_found" value="<?php echo $found['date_found']; ?>" required>

        <label>Location:</label>
        <input type="text" name="location" value="<?php echo htmlspecialchars($found['location']); ?>" required>

        <button type="submit" name="update">Update</button>
    </form>

    <a class="back" href="admin_dashboard.php">⬅ Back to Dashboard</a>
</body>
</html>


