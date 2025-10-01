<?php

session_start();

// if user already register
if (isset($_SESSION["user_email"])) {
    header('Location: profile.php');
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $message = 'fill email or';
    } 
    
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Invalid email or password';
    } 
    
    else {
        // loading users
        $user_file = __DIR__ .'/data/users.txt';
        $user = null;

        if (file_exists($user_file)) {
            $lines = file($user_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $data = json_decode($line, true);
                if ($data && isset($data['email'], $data['password']) && $data['email'] == $email)  {
                    $user = $data;
                    break;
                }
            }
        }

        if (!$user) {
            $message = 'Invalid email or password';
        }

        elseif (!password_verify($password, $user['password'])) {
            $message = 'Invalid email or password';
        }

        else {
            $_SESSION['user_email'] = $user['email'];
            header('Location: profile.php');
            exit();
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
    
    <?php if ($message): ?>
        <?= $message ?>
    <?php endif; ?>

    <h2>Welcom to login!</h2>
    <form method="post">
        <p>Email</p>
        <input type="email" name="email" id="">
        <p>password</p>
        <input type="password" name="password" id="">
        <button type="submit">Login</button>
    </form>
</body>
</html>