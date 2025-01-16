<?php

    include_once("./database/connection.php");

    $message = ''; // Başarı veya hata mesajı için değişken

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'] ?? null;
        $newPassword = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

        if ($username && $newPassword) {
            // Şifreyi güncellerken last_password_change tarihini de güncelleyelim
            $stmt = $pdo->prepare("UPDATE users SET password = ?, last_password_change = NOW() WHERE username = ?");
            $stmt->execute([$newPassword, $username]);

            if ($stmt->rowCount() > 0) {
                $message = "Password successfully changed!";
            } else {
                $message = "User not found.";
            }
        }
    }

?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="../style/sign_up.css">

    <style>

        .done-message {
            color: green;
            position: relative;
            text-align: center;
            font-size: 16px;
            padding: 10px 0;
        }

        .not-found-message {
            color: red;
            position: relative;
            text-align: center;
            font-size: 16px;
            padding: 10px 0;
        }

    </style>

</head>

<body>

    <div class="container">
        <form class="sign-up-form" id="signUpForm" novalidate method="POST">
            <?php if ($message): ?>
                <div class="<?= strpos($message, 'başarıyla') !== false ? 'done-message' : 'not-found-message' ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
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
                    aria-label="Username"
                >
            </div>
            <div class="form-group">
                <input 
                    type="password" 
                    class="form-control" 
                    id="password"
                    name="password" 
                    placeholder="New Password" 
                    required 
                    autocomplete="new-password"
                    aria-label="Password"
                >
                <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                    <div class="eye"></div>
                </button>
            </div>
            <button type="submit" class="btn-sign-up">Reset Password</button>
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