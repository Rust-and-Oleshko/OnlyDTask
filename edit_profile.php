<?php 

session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location: index.php');
    exit();
}

$user_email = $_SESSION['user_email'];
$users_file = __DIR__ . '/data/users.txt';
$message = '';
$current_user = null;
$users = [];

//  loading user

if (file_exists($users_file)) {
    $lines = file($users_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        $data = json_decode($line, true);
        if (is_array($data) && isset($data['email'], $data['password'])) {
            if ($data['email'] === $user_email) {
                $current_user = $data;
            }
            $users[] = $data;
        }
    }
}

if (!$current_user) {
    session_destroy();
    die('Account not found');    
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];
    $new_email = trim($_POST['new_email'] ?? '');

    if (empty($current_password)) {
        $message = 'empty password';
    }
    
    elseif (!password_verify($current_password, $current_user['password'])) {
        $message = 'Wrong current password';
    }
    
    elseif ($new_email && !filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $message = 'invalide email';
    }
    
    elseif ($new_email && $new_email !== $user_email) {
        $email_taken = false;
        foreach ($users as $u) {
            if ($u['email'] == $new_email) {
                $email_taken = true;
                break;
            }
        }
        if ($email_taken) {
            $message = 'that emal taken';
        }
    }
    
    elseif ($new_password && strlen($new_password) < 6) {
        $messege = 'Password must be at least 6 characters long';
    }
    
    elseif ($new_password && $new_password !== $confirm_new_password) {
        $messege = 'Passwords don\'t match';
    }

    if (!$message) {
        $update_user = $current_user;

        if ($new_email && $new_email !== $user_email) {
            $update_user['email'] = $new_email;
            $_SESSION['user_email'] = $new_email;
            $user_email = $new_email;
        }

        if ($new_password) {
            $update_user['password'] = password_hash($new_password, PASSWORD_DEFAULT);
        }

        $new_content = '';
        foreach ($users as $u) {
            if ($u['email'] === $current_user['email']) {
                $new_content .= json_encode($update_user) . PHP_EOL;
            }
            
            else {
                $new_content .= json_encode($update_user) . PHP_EOL;
            }
        }

        file_put_contents($users_file, $new_content, LOCK_EX);
        $message = 'Profile updated successfully';
        $current_user = $update_user;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit profile</title>
</head>
<body>

    <?php if ($message): ?>
        <?= $message ?>
    <?php endif; ?>

    <h1>Edit profile</h1>

    <form method="POST">
        <label>Current password (required)</label>
        <input type="password" name="current_password" required>

        <label>new email(required)</label>
        <input type="email" name="new_email" value="<?= htmlspecialchars($current_user['email'] ?? '') ?>">

        <label>New password</label>
        <input type="password" name="new_password" id="">

        <label >Repest new password</label>
        <input type="password" name="confirm_new_password" id="">

        <button type="submit">Save Changes</button>
    </form>

    <a href="profile.php">Back</a>
</body>
</html>