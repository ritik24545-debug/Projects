<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}
$stats = [
    'users' => $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0],
    'lost'  => $conn->query("SELECT COUNT(*) FROM lost_items")->fetch_row()[0],
    'found' => $conn->query("SELECT COUNT(*) FROM found_items")->fetch_row()[0],
    'pending' => $conn->query("SELECT COUNT(*) FROM matched_items WHERE status = 0")->fetch_row()[0],
    'accepted' => $conn->query("SELECT COUNT(*) FROM matched_items WHERE status = 1")->fetch_row()[0],
    'rejected' => $conn->query("SELECT COUNT(*) FROM matched_items WHERE status = 2")->fetch_row()[0]
];
$active_tab = $_GET['tab'] ?? 'pending';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        :root {
            --primary: #4f46e5; --primary-dark: #4338ca; --secondary: #06b6d4;
            --white: #ffffff; --text: #0f172a; --text-gray: #64748b;
            --border: #e2e8f0; --bg: #f1f5f9;
            --success: #10b981; --warning: #f59e0b; --danger: #ef4444;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg); min-height: 100vh;
        }
        header {
            background: white; box-shadow:0 1px 3px rgba(0,0,0,0.05);
            position: sticky; top:0; z-index:100;
        }
        .nav {
            max-width: 1280px; margin:0 auto; padding:1.2rem 2rem;
            display:flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;
        }
        .logo {
            display:flex; align-items:center; gap:0.75rem; font-weight:800;
            font-size:1.25rem; color: var(--text);
        }
        .logo i { color: var(--primary); font-size:1.75rem; }
        .nav-actions { display:flex; gap:0.75rem; align-items:center; }
        .btn {
            padding:0.75rem 1.25rem; border-radius:8px; border:none; cursor:pointer;
            font-weight:600; font-size:0.95rem; text-decoration:none; display:inline-flex;
            align-items:center; gap:0.5rem; transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color:white;
            box-shadow:0 4px 6px -1px rgba(79,70,229,0.3);
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow:0 10px 15px -3px rgba(79,70,229,0.4); }
        .btn-danger { background: var(--danger); color:white; }
        .container { max-width:1280px; margin:2rem auto; padding:0 2rem; }
        .stats-grid {
            display:grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap:1.25rem; margin-bottom: 2rem;
        }
        .stat-card {
            background: white; border-radius:12px; padding:1.5rem; box-shadow:0 1px 3px rgba(0,0,0,0.05);
            border-left:5px solid var(--primary); text-align:center; transition: transform 0.3s ease;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow:0 4px 6px -1px rgba(0,0,0,0.1); }
        .stat-number { font-size:2rem; font-weight:800; color: var(--text); margin-bottom:0.25rem; }
        .stat-label { color: var(--text-gray); font-weight:600; font-size:0.95rem; }
        .tabs { display:flex; gap:0.75rem; flex-wrap:wrap; margin-bottom:1.5rem; }
        .tab-btn {
            background:white; color: var(--text-gray); border:2px solid var(--border);
            border-radius:8px; padding:0.6rem 1.25rem; text-decoration:none; font-weight:700;
            transition:all 0.3s ease;
        }
        .tab-btn.active, .tab-btn:hover {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color:white; border-color:transparent;
        }
        .content {
            background:white; border-radius:16px; padding:2rem; box-shadow:0 1px 3px rgba(0,0,0,0.05);
        }
        h2 { font-size:1.5rem; font-weight:800; margin-bottom:1.25rem; color: var(--text); }
        table { width:100%; border-collapse: collapse; }
        th, td { padding:0.85rem 0.75rem; text-align:left; border-bottom:1px solid var(--border); font-size:0.95rem; }
        th {
            background: linear-gradient(135deg,#f8fafc,#e0e7ff);
            font-weight:700; color: var(--primary);
        }
        tr:hover { background:#f8fafc; }
        .status-badge {
            display:inline-block; padding:0.35rem 0.75rem; border-radius:50px;
            font-size:0.85rem; font-weight:700;
        }
        .status-pending { background:#fef3c7; color:#92400e; }
        .status-accepted { background:#d1fae5; color:#065f46; }
        .status-rejected { background:#fee2e2; color:#991b1b; }
        .btn-sm { padding:0.5rem 1rem; font-size:0.875rem; border-radius:8px; border:none; font-weight:700; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:0.35rem; }
        .btn-accept { background:var(--success); color:white; }
        .btn-reject { background:var(--danger); color:white; }
        .empty { text-align:center; color: var(--text-gray); padding:2rem; }
        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr { display:block; }
            thead tr { position: absolute; top:-9999px; left:-9999px; }
            tr { border:1px solid var(--border); border-radius:10px; margin-bottom:1rem; padding:0.75rem; }
            td { border:none; position: relative; padding-left: 50%; padding-top:0.75rem; padding-bottom:0.75rem; }
            td:before {
                position:absolute; top:0.75rem; left:0.75rem; width:45%; padding-right:0.5rem;
                white-space: nowrap; font-weight:700; color: var(--text);
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="nav">
            <div class="logo">
                <i class="fas fa-user-shield"></i>
                Admin Dashboard
            </div>
            <div class="nav-actions">
                <a href="logout.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><i class="fas fa-users"></i> <?php echo $stats['users']; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card" style="border-left-color:#ef4444">
                <div class="stat-number"><i class="fas fa-search-minus"></i> <?php echo $stats['lost']; ?></div>
                <div class="stat-label">Lost Items</div>
            </div>
            <div class="stat-card" style="border-left-color:#10b981">
                <div class="stat-number"><i class="fas fa-search-plus"></i> <?php echo $stats['found']; ?></div>
                <div class="stat-label">Found Items</div>
            </div>
            <div class="stat-card" style="border-left-color:#f59e0b">
                <div class="stat-number"><i class="fas fa-clock"></i> <?php echo $stats['pending']; ?></div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card" style="border-left-color:#10b981">
                <div class="stat-number"><i class="fas fa-check"></i> <?php echo $stats['accepted']; ?></div>
                <div class="stat-label">Accepted</div>
            </div>
            <div class="stat-card" style="border-left-color:#ef4444">
                <div class="stat-number"><i class="fas fa-times"></i> <?php echo $stats['rejected']; ?></div>
                <div class="stat-label">Rejected</div>
            </div>
        </div>

        <div class="tabs">
            <a href="?tab=pending" class="tab-btn <?php echo $active_tab === 'pending' ? 'active' : '' ?>">
                <i class="fas fa-clock"></i> Pending
            </a>
            <a href="?tab=all" class="tab-btn <?php echo $active_tab === 'all' ? 'active' : '' ?>">
                <i class="fas fa-list"></i> All Matches
            </a>
            <a href="?tab=users" class="tab-btn <?php echo $active_tab === 'users' ? 'active' : '' ?>">
                <i class="fas fa-users"></i> Users
            </a>
            <a href="?tab=lost" class="tab-btn <?php echo $active_tab === 'lost' ? 'active' : '' ?>">
                <i class="fas fa-search-minus"></i> Lost Items
            </a>
            <a href="?tab=found" class="tab-btn <?php echo $active_tab === 'found' ? 'active' : '' ?>">
                <i class="fas fa-search-plus"></i> Found Items
            </a>
        </div>

        <div class="content">
            <?php if ($active_tab === 'pending'): ?>
                <h2>Pending Matches</h2>
                <?php
                $matches = $conn->query("SELECT m.id, l.item_name lost_name, f.item_name found_name, l.location, m.status FROM matched_items m JOIN lost_items l ON m.lost_item_id=l.id JOIN found_items f ON m.found_item_id=f.id WHERE m.status = 0");
                if ($matches->num_rows > 0):
                ?>
                    <table>
                        <thead><tr><th>ID</th><th>Lost</th><th>Found</th><th>Location</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            <?php while($row=$matches->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['lost_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['found_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                                    <td><span class="status-badge status-pending">Pending</span></td>
                                    <td>
                                        <a href="accept_match.php?id=<?php echo $row['id']; ?>" class="btn-sm btn-accept"><i class="fas fa-check"></i> Accept</a>
                                        <a href="reject_match.php?id=<?php echo $row['id']; ?>" class="btn-sm btn-reject" style="margin-left:0.5rem;"><i class="fas fa-times"></i> Reject</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="empty">No pending matches!</p>
                <?php endif; ?>

            <?php elseif ($active_tab === 'all'): ?>
                <h2>All Matches</h2>
                <?php
                $matches = $conn->query("SELECT m.id, l.item_name lost_name, f.item_name found_name, m.status FROM matched_items m JOIN lost_items l ON m.lost_item_id=l.id JOIN found_items f ON m.found_item_id=f.id");
                if ($matches->num_rows > 0):
                ?>
                    <table>
                        <thead><tr><th>ID</th><th>Lost</th><th>Found</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            <?php while($row=$matches->fetch_assoc()): ?>
                                <?php
                                    $s = (int)$row['status'];
                                    $class = "status-".['pending','accepted','rejected'][$s];
                                    $label = ["Pending","Accepted","Rejected"][$s];
                                ?>
                                <tr>
                                    <td>#<?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['lost_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['found_name']); ?></td>
                                    <td><span class="status-badge <?php echo $class; ?>"><?php echo $label; ?></span></td>
                                    <td>
                                        <?php if($s === 0): ?>
                                            <a href="accept_match.php?id=<?php echo $row['id']; ?>" class="btn-sm btn-accept"><i class="fas fa-check"></i> Accept</a>
                                            <a href="reject_match.php?id=<?php echo $row['id']; ?>" class="btn-sm btn-reject" style="margin-left:0.5rem;"><i class="fas fa-times"></i> Reject</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="empty">No matches yet!</p>
                <?php endif; ?>

            <?php elseif ($active_tab === 'users'): ?>
                <h2>Registered Users</h2>
                <?php
                $users = $conn->query("SELECT id, fullname, email, phone, created_at FROM users");
                if ($users->num_rows > 0):
                ?>
                    <table>
                        <thead><tr><th>ID</th><th>Full Name</th><th>Email</th><th>Phone</th><th>Joined</th></tr></thead>
                        <tbody>
                            <?php while($row=$users->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="empty">No users yet!</p>
                <?php endif; ?>

            <?php elseif ($active_tab === 'lost'): ?>
                <h2>All Lost Items</h2>
                <?php
                $items = $conn->query("SELECT l.item_name, l.description, l.date_lost, l.location, u.fullname FROM lost_items l JOIN users u ON l.user_id = u.id ORDER BY l.created_at DESC");
                if ($items->num_rows > 0):
                ?>
                    <table>
                        <thead><tr><th>Item</th><th>Description</th><th>Date Lost</th><th>Location</th><th>Reported By</th></tr></thead>
                        <tbody>
                            <?php while($row=$items->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                                    <td><?php echo htmlspecialchars($row['date_lost']); ?></td>
                                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                                    <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="empty">No lost items reported yet!</p>
                <?php endif; ?>

            <?php elseif ($active_tab === 'found'): ?>
                <h2>All Found Items</h2>
                <?php
                $items = $conn->query("SELECT f.item_name, f.description, f.date_found, f.location, u.fullname FROM found_items f JOIN users u ON f.user_id = u.id ORDER BY f.created_at DESC");
                if ($items->num_rows > 0):
                ?>
                    <table>
                        <thead><tr><th>Item</th><th>Description</th><th>Date Found</th><th>Location</th><th>Reported By</th></tr></thead>
                        <tbody>
                            <?php while($row=$items->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                                    <td><?php echo htmlspecialchars($row['date_found']); ?></td>
                                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                                    <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="empty">No found items reported yet!</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>