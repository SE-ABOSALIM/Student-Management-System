<?php
session_start();
include_once("./database/connection.php");

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($_POST['username']) ? $_POST['username'] : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        header("Location: ../dashboard.php");
        exit();
    } else {
        $error_message = "Incorrect Username or Password!";
        header("Location: login.php?error=" . urlencode($error_message));
        exit();
    }
}

if (isset($_GET['error'])) {
    $error_message = htmlspecialchars($_GET['error']);
}
?>


<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../style/login.css">

    <style>
        .incorrect-message {
            color: red;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }
    </style>

</head>

<body>

    <div class="container">
        <div class="sidebar">
            <svg class="sidebar-icon" viewBox="0 0 24 24" fill="white">
                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
            </svg>
            <h1>Login</h1>
        </div>

        <div class="main-content">
            <form class="login-form" id="loginForm" method="POST" novalidate>
                <h2 class="welcome-text">Welcome Back User!</h2>

                <!-- Hata mesajını burada gösteriyoruz -->
                <?php if (!empty($error_message)): ?>
                    <p class="incorrect-message"><?php echo htmlspecialchars($error_message); ?></p>
                <?php endif; ?>

                <div class="form-group">
                    <input
                        type="text"
                        class="form-control"
                        id="username"
                        name="username"
                        placeholder="Username"
                        required
                        autocomplete="off"
                        aria-label="Username">
                    <div class="error-message">Please enter your username</div>
                </div>
                <div class="form-group">
                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        placeholder="Password"
                        required
                        autocomplete="current-password"
                        aria-label="Password">
                    <div class="error-message">Please enter your password</div>
                    <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                        <div class="eye"></div>
                    </button>
                </div>
                <button type="submit" class="btn-login">Login</button>
                <a class="forgot-password" href="forgot_password.php">Forgot password?</a>
                <a class="Sign-Up" href="sign_up.php">Sign Up</a>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('loginForm');
            const passwordToggle = document.querySelector('.password-toggle');
            const passwordInput = document.getElementById('password');

            // Toggle password visibility with improved animation
            passwordToggle.addEventListener('click', () => {
                const type = passwordInput.type === 'password' ? 'text' : 'password';
                passwordInput.type = type;
                passwordToggle.classList.toggle('show');
            });

            // Form validation and submission
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const isValid = validateForm();

                if (isValid) {
                    const submitButton = form.querySelector('.btn-login');
                    submitButton.classList.add('loading');
                    form.submit();
                    // Simulate API call
                    setTimeout(() => {
                        submitButton.classList.remove('loading');
                        // Add your actual form submission logic here
                    }, 2000);
                }
            });

            function validateForm() {
                let isValid = true;
                const inputs = form.querySelectorAll('.form-control');

                inputs.forEach(input => {
                    if (!input.value.trim()) {
                        input.classList.add('error');
                        isValid = false;
                    } else {
                        input.classList.remove('error');
                    }
                });

                return isValid;
            }

            // Remove error state on input
            const inputs = form.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('input', () => {
                    input.classList.remove('error');
                });

                // Add focus animation to password toggle
                input.addEventListener('focus', () => {
                    if (input.type === 'password') {
                        passwordToggle.classList.add('focused');
                    }
                });

                input.addEventListener('blur', () => {
                    if (input.type === 'password') {
                        passwordToggle.classList.remove('focused');
                    }
                });
            });
        });
    </script>

</body>

</html>