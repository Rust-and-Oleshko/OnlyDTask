<?php

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userName = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ??'');
    $password = $_POST['password'] ?? '';

    // validation
    if (empty($userName) || empty($password) || empty($tmail)) {
        $message = '<p style = "color: red;">All fields must be filled in</p>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '<p style = "color: red;">incorrest email</p>';
    } elseif (strlen($password) < 6) {
        $message = '<p style = "color: red;">The password must be more than 6 characters long</p>';
    } else {
        $user_file = 'users.txt';
        $users = file_exists($user_file) ? file($user_file, FILE_IGNORE_NEW_LINES) : [];
    
        $email_exists = false;
        foreach ($users as $user_line) {
            $user_data = json_decode($user_line, true);
            if ($user_data && $user_email['email'] === $email) {
                $email_exists = true;
                break;
            }
        }
        if (!$email_exists) {
            $message = '<p style = "color: red;">email already registered</p>';
        } 
        else {
        
            // hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // user save
            $new_user = [
                'userName' => $userName,
                'email' => $email,
                'password'=> $hashed_password,
            ];

            file_put_contents($users_file, json_encode($new_user) . PHP_EOL, FILE_APPEND | LOCK_EX);

            $message = '<p style = "color: green;">registration was successful</p>';

        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div>

        <?php if ($message): ?>
            <?= $message ?>
        <?php endif; ?>

        <form method="POST">
            <h2>Name</h2>
            <input type="test" name="userName" placeholder="Name">
            <h2>Email</h2>
            <input type="email" name="email" placeholder="Email">
            <h2>Password</h2>
            <input type="password" name="password" placeholder="Password">
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>