<?php
include 'db_connect.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $phone = trim($_POST['phone']);
    $aadhar = trim($_POST['aadhar']);

    if ($password !== $confirm) {
        $message = "error";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $check->bind_param("ss", $email, $username);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $message = "error";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (fullname, email, username, password, phone, aadhar) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $fullname, $email, $username, $hashed, $phone, $aadhar);
            if ($stmt->execute()) {
                $message = "success";
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Lost & Found System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --secondary: #06b6d4;
            --success: #10b981;
            --danger: #ef4444;
            --white: #ffffff;
            --text-dark: #0f172a;
            --text-gray: #64748b;
            --border: #e2e8f0;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f1f5f9 0%, #e0e7ff 100%);
            min-height: 100vh;
            padding: 2rem 1rem;
        }
        .register-wrapper {
            width: 100%;
            max-width: 520px;
            margin: 0 auto;
        }
        .register-card {
            background: var(--white);
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .register-logo {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.25rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }
        .register-logo i {
            font-size: 1.75rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        h1 {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-dark);
        }
        .register-header p {
            color: var(--text-gray);
            margin-top: 0.5rem;
        }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }
        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
        }
        .alert-success {
            background: #d1fae5;
            color: #065f46;
        }
        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }
        .input-wrapper {
            position: relative;
        }
        .form-group input {
            width: 100%;
            padding: 0.875rem 1rem;
            font-size: 1rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            background: var(--white);
            transition: all 0.3s ease;
        }
        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        .toggle-password {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-gray);
            cursor: pointer;
            transition: color 0.3s ease;
        }
        .toggle-password:hover {
            color: var(--primary);
        }
        .btn {
            width: 100%;
            padding: 0.875rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4);
        }
        .links {
            margin-top: 1.5rem;
            text-align: center;
        }
        .links a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }
        .links a:hover {
            text-decoration: underline;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            color: var(--text-gray);
            text-decoration: none;
            font-weight: 500;
        }
        .back-link:hover {
            color: var(--primary);
        }
        @media (max-width: 600px) {
            .register-card {
                padding: 2rem 1.5rem;
            }
            .row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="register-wrapper">
        <a href="index.html" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>

        <div class="register-card">
            <div class="register-header">
                <div class="register-logo">
                    <i class="fas fa-search-location"></i>
                    <span>Lost & Found</span>
                </div>
                <h1>Create Account</h1>
                <p>Get started with your free account</p>
            </div>

            <?php if ($message === "success"): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    Registration successful! <a href="login.php" style="color: #065f46; font-weight: 700;">Login now</a>
                </div>
            <?php elseif ($message === "error"): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    Error: Email or username already exists or passwords don't match
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" id="fullname" name="fullname" required placeholder="Enter your full name">
                </div>

                <div class="row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required placeholder="Enter your email">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" required placeholder="Enter your phone">
                    </div>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required placeholder="Choose a username">
                </div>

                <div class="row">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="password" name="password" required placeholder="Create a password">
                            <i class="fas fa-eye toggle-password" id="togglePass1"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm password">
                            <i class="fas fa-eye toggle-password" id="togglePass2"></i>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="aadhar">Aadhar/ID Number</label>
                    <input type="text" id="aadhar" name="aadhar" required placeholder="Enter your Aadhar number">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Register Now
                </button>
            </form>

            <div class="links">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>

    <script>
        function setupToggle(btnId, inputId) {
            const btn = document.getElementById(btnId);
            const input = document.getElementById(inputId);
            btn.addEventListener('click', function() {
                const isPass = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPass ? 'text' : 'password');
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
        setupToggle('togglePass1', 'password');
        setupToggle('togglePass2', 'confirm_password');
    </script>
</body>
</html>