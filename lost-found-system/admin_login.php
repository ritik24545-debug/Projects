<?php
session_start();
include 'db_connect.php';
$error = "";
if (isset($_POST['login'])) {
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $_POST['username'], $_POST['password']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password!";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        :root {
            --primary: #4f46e5; --primary-dark: #4338ca; --secondary: #06b6d4;
            --white: #ffffff; --text-dark: #0f172a; --text-gray: #64748b;
            --border: #e2e8f0; --bg: #f1f5f9;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f1f5f9 0%, #e0e7ff 100%);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 2rem 1rem;
        }
        .wrapper { width:100%; max-width:450px; }
        .card {
            background: white; border-radius: 16px; padding:2.5rem;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        }
        .back-link {
            display: inline-flex; align-items: center; gap:0.5rem;
            color: var(--text-gray); text-decoration: none; font-weight: 600;
            margin-bottom: 1.5rem; transition: color 0.3s ease;
        }
        .back-link:hover { color:var(--primary); text-decoration: underline; }
        .header { text-align: center; margin-bottom:2rem; }
        .logo {
            display: inline-flex; align-items: center; gap:0.75rem;
            font-weight:800; font-size:1.25rem; margin-bottom:1rem;
        }
        .logo i {
            font-size:1.75rem;
            background: linear-gradient(135deg,var(--primary),var(--secondary));
            -webkit-background-clip:text;
            -webkit-text-fill-color:transparent;
            background-clip:text;
        }
        h1 {
            font-size: 2rem; font-weight:800; color: var(--text-dark);
        }
        .alert {
            background: #fee2e2; color: #991b1b; padding:1rem; border-radius: 8px;
            margin-bottom:1.5rem; font-weight: 600; display:flex; align-items: center; gap:0.5rem;
        }
        .form-group { margin-bottom:1.5rem; }
        .form-group label {
            display:block; margin-bottom:0.5rem; font-weight:600; color: var(--text-dark);
        }
        .input-wrapper { position:relative; }
        .form-group input {
            width:100%; padding: 0.9rem 1rem; border:2px solid var(--border); border-radius: 8px;
            background:white; font-size:1rem; outline:none; transition: all 0.3s ease;
        }
        .form-group input:focus {
            border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79,70,229, 0.1);
        }
        .toggle-password {
            position:absolute; right:1rem; top:50%; transform:translateY(-50%);
            color: var(--text-gray); cursor: pointer; transition: color 0.3s ease;
        }
        .toggle-password:hover { color: var(--primary); }
        .btn {
            width:100%; padding:1rem; border-radius:8px; border:none; cursor:pointer;
            font-weight:700; font-size:1rem; display: flex; align-items: center; justify-content: center;
            gap: 0.5rem; transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color:white; box-shadow: 0 4px 6px -1px rgba(79,70,229, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-1px); box-shadow: 0 10px 15px -3px rgba(79,70,229, 0.4);
        }
        @media (max-width: 480px) { .card { padding:2rem 1.5rem; } }
    </style>
</head>
<body>
    <div class="wrapper">
        <a href="index.html" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>

        <div class="card">
            <div class="header">
                <div class="logo">
                    <i class="fas fa-user-shield"></i>
                    Admin Panel
                </div>
                <h1>Admin Login</h1>
            </div>

            <?php if ($error): ?>
                <div class="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Enter username" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" placeholder="Enter password" required>
                        <i class="fas fa-eye toggle-password" id="toggle"></i>
                    </div>
                </div>
                <button type="submit" name="login" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Login to Admin Panel
                </button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('toggle').addEventListener('click', function() {
            const input = document.getElementById('password');
            const isPass = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPass ? 'text' : 'password');
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>