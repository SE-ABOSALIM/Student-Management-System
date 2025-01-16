<?php
    session_start();
    include_once("./database/connection.php");

    $message = ''; // Mesaj değişkeni

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'] ?? null;
        $password = $_POST['password'] ?? null;
        
        if ($username && $password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $created_date = date('Y-m-d H:i:s'); // Şu anki tarih ve saat

            // Aynı kullanıcı adının kontrolü
            $checkStmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $checkStmt->execute([$username]);
            
            if ($checkStmt->rowCount() > 0) {
                $message = "<div class='uae-error-message'>User already exists</div>";
            } else {
                // Kullanıcıyı ekleme
                $stmt = $pdo->prepare("INSERT INTO users (username, password, created_date) VALUES (?, ?, ?)");
                $stmt->execute([$username, $hashed_password, $created_date]);
                header("Location: after_sign_up.php");
            }
        }
    }
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="../style/sign_up.css">
    <style>
        .uae-error-message {
            color: red;
            text-align: center;
            margin-bottom: 15px;
            font-size: 16px;
            position: relative;
        }
    </style>
</head>

<body>

<div class="container">
        <form class="sign-up-form" id="signUpForm" method="POST" novalidate>
            <?php if (!empty($message)) { echo $message; } ?>

            <div class="form-group">
                <input 
                    type="text" 
                    class="form-control" 
                    id="username"
                    name="username" 
                    placeholder="Username" 
                    required 
                    autocomplete="off"
                >
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
                >
                <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                    <div class="eye"></div>
                </button>
            </div>
            <button type="submit" class="btn-sign-up">Sign Up</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('signUpForm');
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
                    const submitButton = form.querySelector('.btn-sign-up');
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
