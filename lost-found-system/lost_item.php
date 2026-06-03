<?php
session_start();
include 'db_connect.php';
include 'auto_match.php';
autoMatchItems($conn);
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $stmt = $conn->prepare("INSERT INTO lost_items (user_id, item_name, description, date_lost, location) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $_SESSION['user_id'], $_POST['item_name'], $_POST['description'], $_POST['date_lost'], $_POST['location']);
    if ($stmt->execute()) {
        $msg = "success";
    } else {
        $msg = "error";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Lost Item</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --primary: #4f46e5; --primary-dark: #4338ca; --secondary: #06b6d4;
            --white: #ffffff; --text-dark: #0f172a; --text-gray: #64748b;
            --border: #e2e8f0; --bg: #f1f5f9;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f1f5f9 0%, #e0e7ff 100%);
            min-height: 100vh; padding: 2rem 1rem;
        }
        .wrapper { max-width: 520px; margin: 0 auto; }
        .card {
            background: white; border-radius: 16px; padding: 2.5rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .back-link {
            display: inline-flex; align-items: center; gap: 0.5rem;
            color: var(--text-gray); text-decoration: none; font-weight: 600; margin-bottom: 1.5rem;
            transition: color 0.3s ease;
        }
        .back-link:hover { color: var(--primary); text-decoration: underline; }
        .header { margin-bottom: 2rem; }
        .header h1 {
            font-size: 1.75rem; font-weight: 800; color: var(--text-dark);
        }
        .alert {
            padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;
            display: flex; align-items: center; gap: 0.5rem; font-weight: 600;
        }
        .alert-success { background: #d1fae5; color: #065f46; }
        .alert-error { background: #fee2e2; color: #991b1b; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label {
            display: block; margin-bottom: .5rem; color: var(--text-dark);
            font-weight: 600; font-size: .95rem;
        }
        .form-group input, .form-group textarea {
            width: 100%; padding: .9rem 1rem; font-size: 1rem;
            border: 2px solid var(--border); border-radius: 8px; background: white;
            outline: none; transition: all 0.3s ease; font-family: inherit;
        }
        .form-group input:focus, .form-group textarea:focus {
            border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79,70,229, 0.1);
        }
        textarea { resize: vertical; min-height: 120px; }
        .btn {
            width: 100%; padding: 1rem; border: none; border-radius: 8px;
            font-weight: 700; font-size: 1rem; cursor: pointer; display: inline-flex;
            align-items: center; justify-content: center; gap: .5rem;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white; box-shadow: 0 4px 6px -1px rgba(79,70,229, 0.3);
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 10px 15px -3px rgba(79,70,229, 0.4); }
        @media (max-width: 480px) { .card { padding: 2rem 1.5rem; } }
    </style>
</head>
<body>
    <div class="wrapper">
        <a href="dashboard.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>

        <div class="card">
            <div class="header">
                <h1><i class="fas fa-search-minus" style="color:var(--primary)"></i> Report Lost Item</h1>
            </div>

            <?php if ($msg === "success"): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Lost Item reported successfully!
                </div>
            <?php elseif ($msg === "error"): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> Error: Could not report!
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Item Name</label>
                    <input type="text" name="item_name" placeholder="e.g. Blue Wallet" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" placeholder="Describe your item in detail" required></textarea>
                </div>
                <div class="form-group">
                    <label>Date Lost</label>
                    <input type="date" name="date_lost" required>
                </div>
                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" placeholder="e.g. College Canteen" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Report Lost Item
                </button>
            </form>
        </div>
    </div>
</body>
</html>