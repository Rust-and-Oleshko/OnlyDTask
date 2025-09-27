<?php
$message = '';
$userName = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userName = trim($_POST['userName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    // validation
    if (empty($userName) || empty($email) || empty($password)) {
        $message = '<p style="color: red;">All fields must be filled in.</p>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '<p style="color: red;">Incorrect email format.</p>';
    } elseif (strlen($password) < 6) {
        $message = '<p style="color: red;">Password must be at least 6 characters long.</p>';
    } elseif ($password !== $confirmPassword) {
        $message = '<p style="color: red;">Passwords do not match.</p>';
    } else {
        $user_file = __DIR__ . '/data/users.txt';

        if (!is_dir(__DIR__ . '/data')) {
            mkdir(__DIR__ . '/data', 0755, true);
        }

        // loadind users
        $users = [];
        if (file_exists($user_file)) {
            $lines = file($user_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $user_data = json_decode($line, true);
                if (is_array($user_data) && isset($user_data['email'], $user_data['userName'])) {
                    $users[] = $user_data;
                }
            }
        }

        $email_exists = false;
        $userName_exists = false;

        foreach ($users as $user) {
            if ($user['email'] === $email) {
                $email_exists = true;
            }
            if ($user['userName'] === $userName) {
                $userName_exists = true;
            }
        }

        if ($email_exists) {
            $message = '<p style="color: red;">Email is already registered.</p>';
        } elseif ($userName_exists) {
            $message = '<p style="color: red;">Username is already taken.</p>';
        } else {
            // hashed password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $new_user = [
                'userName' => $userName,
                'email' => $email,
                'password' => $hashed_password,
            ];

            file_put_contents($user_file, json_encode($new_user) . PHP_EOL, FILE_APPEND | LOCK_EX);
            $message = '<p style="color: green;">Registration was successful!</p>';

            $userName = '';
            $email = '';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>

    <?php if ($message): ?>
        <?= $message ?>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="userName">Username:</label>
            <input type="text" id="userName" name="userName" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="password">Password (min 6 characters):</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" id="confirmPassword" name="confirmPassword" required>
        </div>

        <button type="submit">Register</button>
    </form>

    <a href="index.html">← Back to main page</a>
</body>
</html>