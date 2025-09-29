<?php 

session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location: index.php');
    exit();
}

$email = $_SESSION['user_email'];
$user_file = __DIR__ ."/data/users.txt";
$user = null;

if (file_exists($user_file)) {
    $lines = file($user_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $data = json_decode($line, true);
        if ($data && $data['email'] === $email ) {
            $user = $data;
        }

    }
}

if (!$user) {
    session_destroy();
    die('User not found');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['exit_account'])) {
        
        session_destroy();

        header('Location: index.php?message=Account+exit');
        exit();
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
        <h2>Welcom, <?=$user['userName']?></h2>
        <p>Email: <?=$user['email']?></p>
        <a href="edit_profile.php?email=<?= urlencode($user['email']) ?>">Edit profile</a>
        <form method="POST">
            <button type="submit" >Exit account</button>
            <input type="hidden" name="exit_account" value="1">
        </form>
    </div>
</body>
</html>