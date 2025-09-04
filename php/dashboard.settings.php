<?php
    session_start();
    
    // Oturum kontrolü
    if (!isset($_SESSION['admin_id'])) {
        echo "<script>
            alert('Login to see your account details!');
            window.location.href = 'login.php';
        </script>";
        exit();
    }

    include_once("./database/connection.php");

    // Kullanıcı bilgilerini çek
    $stmt = $pdo->prepare("SELECT username, created_date, last_password_change, password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $error_message = '';
    $success_message = '';

    if (isset($_SESSION['error_message'])) {
        $error_message = $_SESSION['error_message'];
        unset($_SESSION['error_message']);
    }

    if (isset($_SESSION['success_message'])) {
        $success_message = $_SESSION['success_message'];
        unset($_SESSION['success_message']);
    }

    // Form gönderildiğinde
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['update_password'])) {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            if (password_verify($current_password, $user['password'])) {
                if ($new_password === $confirm_password) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    
                    $update_stmt = $pdo->prepare("UPDATE users SET password = ?, last_password_change = NOW() WHERE id = ?");
                    if($update_stmt->execute([$hashed_password, $_SESSION['admin_id']])) {
                        $_SESSION['success_message'] = "Password updated successfully!";
                    } else {
                        $_SESSION['error_message'] = "Error updating password. Please try again.";
                    }
                } else {
                    $_SESSION['error_message'] = "New passwords don't match!";
                }
            } else {
                $_SESSION['error_message'] = "Current password is incorrect!";
            }
            
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../style/dashboard.settings.css">
    <title>Settings</title>
</head>
<body>
    <?php include("../compoents/sidebar.php")?>
    
    <div class="main-wrapper" id="mainWrapper">
        <div class="container">
            <h1>Settings</h1>

            <!-- Account Details Card -->
            <div class="settings-card">
                <h2 id="acc-details-h2">Account Details</h2>
                <div class="profile-info">
                    <div class="label">Username:</div>
                    <div class="db-label"><?php echo htmlspecialchars($user['username']); ?></div>
                    
                    <div class="label">Account Created at:</div>
                    <div class="db-label"><?php echo date('d/m/Y', strtotime($user['created_date'])); ?></div>
                    
                    <div class="label">Last Password Change at:</div>
                    <div class="db-label"><?php echo date('d/m/Y H:i', strtotime($user['last_password_change'])); ?></div>
                </div>
            </div>
            
            <!-- Change Password Card -->
            <h2 id="change-pass-h2">Change Password</h2>
            <div class="settings-card">
                <?php if($error_message): ?>
                <div class="alert alert-error" id="errorAlert">
                    <i class="fas fa-exclamation-circle"></i>
                    <span class="alert-message"><?php echo htmlspecialchars($error_message); ?></span>
                    <button class="alert-close" onclick="closeAlert('errorAlert')">&times;</button>
                </div>
                <?php endif; ?>

                <?php if($success_message): ?>
                <div class="alert alert-success" id="successAlert">
                    <i class="fas fa-check-circle"></i>
                    <span class="alert-message"><?php echo htmlspecialchars($success_message); ?></span>
                    <button class="alert-close" onclick="closeAlert('successAlert')">&times;</button>
                </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>

                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">New Password (Again)</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>

                    <button type="submit" name="update_password" class="btn">Update Password</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Alert kapatma fonksiyonu
        function closeAlert(alertId) {
            document.getElementById(alertId).style.display = 'none';
        }

        // Otomatik alert kapatma (5 saniye sonra)
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.display = 'none';
            });
        }, 5000);
    </script>
</body>
</html>