<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];
$fullname = $_SESSION['fullname'];

// Fetch Items
$lost_items = $conn->prepare("SELECT * FROM lost_items WHERE user_id = ?");
$lost_items->bind_param("i", $user_id);
$lost_items->execute();
$lost = $lost_items->get_result();

$found_items = $conn->prepare("SELECT * FROM found_items WHERE user_id = ?");
$found_items->bind_param("i", $user_id);
$found_items->execute();
$found = $found_items->get_result();

// Fetch Matches
$matches = $conn->prepare("SELECT m.id, l.item_name lost, f.item_name found, m.status 
                          FROM matched_items m 
                          JOIN lost_items l ON m.lost_item_id = l.id
                          JOIN found_items f ON m.found_item_id = f.id
                          WHERE l.user_id = ? OR f.user_id = ?");
$matches->bind_param("ii", $user_id, $user_id);
$matches->execute();
$matched = $matches->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Lost & Found</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --primary: #4f46e5; --primary-dark: #4338ca;
            --secondary: #06b6d4;
            --success: #10b981; --warning: #f59e0b; --danger: #ef4444;
            --white: #fff; --text: #0f172a; --gray: #64748b;
            --border: #e2e8f0; --bg: #f1f5f9; --card-bg: #fafafa;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            min-height: 100vh;
        }
        /* HEADER */
        header {
            background: var(--white);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            position: sticky; top: 0; z-index: 100;
        }
        .navbar {
            max-width: 1280px; margin: 0 auto;
            padding: 1rem 2rem; display: flex; align-items: center; justify-content: space-between;
        }
        .logo {
            display: flex; align-items: center; gap: .75rem; font-weight: 800; font-size: 1.25rem;
            color: var(--text);
        }
        .logo i { color: var(--primary); font-size: 1.5rem; }
        .nav-actions { display: flex; gap: .75rem; align-items: center; flex-wrap: wrap; }
        .btn {
            padding: .75rem 1.25rem; border-radius: 8px; border: none; cursor: pointer;
            font-weight: 600; font-size: .95rem; text-decoration: none; display: inline-flex;
            align-items: center; gap: .5rem; transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.3);
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4); }
        .btn-danger { background: var(--danger); color: white; }

        /* MAIN */
        .container {
            max-width: 1280px; margin: 2rem auto; padding: 0 2rem;
        }
        .welcome-card {
            background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
            color: white; padding: 2rem; border-radius: 16px; margin-bottom: 2rem;
            box-shadow: 0 10px 25px -5px rgba(79,70,229,0.3);
        }
        .welcome-card h1 { font-size: 1.75rem; font-weight: 800; margin-bottom: .25rem; }
        .welcome-card p { opacity: 0.9; }

        /* SECTIONS */
        .section { background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0, 0,0,0.05); padding: 1.75rem; margin-bottom: 2rem; }
        .section h2 { font-size: 1.25rem; color: var(--text); font-weight: 700; margin-bottom: 1.25rem; display: flex; align-items: center; gap: .5rem; }
        .section h2 i { color: var(--primary); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: .9rem .75rem; text-align: left; border-bottom: 1px solid var(--border); }
        th { background: #f8fafc; font-weight: 700; color: var(--text); border-radius: 8px 8px 0 0; }
        .status-badge {
            padding: .35rem .75rem; border-radius: 20px; font-size: .8rem; font-weight: 700;
            display: inline-block;
        }
        .status-0 { background: #fef3c7; color: #92400e; }
        .status-1 { background: #d1fae5; color: #065f46; }
        .status-2 { background: #fee2e2; color: #991b1b; }

        .empty { text-align: center; color: var(--gray); padding: 2rem; }

        @media (max-width: 768px) {
            .navbar { flex-direction: column; gap: 1rem; text-align: center; }
            table, thead, tbody, th, td, tr { display: block; }
            thead tr { position: absolute; top: -9999px; left: -9999px; }
            tr { border: 1px solid var(--border); border-radius: 8px; margin-bottom: 1rem; }
            td { border: none; position: relative; padding-left: 50%; padding-top: .75rem; padding-bottom: .75rem; }
            td:before {
                position: absolute; top: 12px; left: 12px; width: 45%;
                padding-right: 10px; white-space: nowrap; font-weight: 700; color: var(--text);
            }
            td:nth-of-type(1):before { content: "Item Name"; }
            td:nth-of-type(2):before { content: "Description"; }
            td:nth-of-type(3):before { content: "Date"; }
            td:nth-of-type(4):before { content: "Location"; }
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <i class="fas fa-search-location"></i>
                <span>Lost & Found</span>
            </div>
            <div class="nav-actions">
                <a href="lost_item.php" class="btn btn-primary">
                    <i class="fas fa-search-minus"></i> Report Lost Item
                </a>
                <a href="found_item.php" class="btn btn-primary">
                    <i class="fas fa-search-plus"></i> Report Found Item
                </a>
                <a href="logout.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="welcome-card">
            <h1><i class="fas fa-user-circle"></i> Hello, <?php echo htmlspecialchars($fullname); ?></h1>
            <p>Welcome back to your dashboard!</p>
        </div>

        <!-- Lost Items -->
        <section class="section">
            <h2><i class="fas fa-search-minus"></i> Your Lost Items</h2>
            <?php if ($lost->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Description</th>
                            <th>Date Lost</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $lost->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td><?php echo htmlspecialchars($row['date_lost']); ?></td>
                                <td><?php echo htmlspecialchars($row['location']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="empty">No lost items reported yet! <a href="lost_item.php" style="color: var(--primary); font-weight:700">Report one</a></p>
            <?php endif; ?>
        </section>

        <!-- Found Items -->
        <section class="section">
            <h2><i class="fas fa-search-plus"></i> Your Found Items</h2>
            <?php if ($found->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Description</th>
                            <th>Date Found</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $found->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td><?php echo htmlspecialchars($row['date_found']); ?></td>
                                <td><?php echo htmlspecialchars($row['location']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="empty">No found items reported yet! <a href="found_item.php" style="color: var(--primary); font-weight:700">Report one</a></p>
            <?php endif; ?>
        </section>

        <!-- Matches -->
        <section class="section">
            <h2><i class="fas fa-handshake"></i> Matched Items</h2>
            <?php if ($matched->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Lost Item</th>
                            <th>Found Item</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $matched->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['lost']); ?></td>
                                <td><?php echo htmlspecialchars($row['found']); ?></td>
                                <td>
                                    <?php 
                                        $status = (int)$row['status'];
                                        $text = ["Pending", "Accepted", "Rejected"][$status];
                                        echo "<span class='status-badge status-$status'>$text</span>"; 
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="empty">No matches yet!</p>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>