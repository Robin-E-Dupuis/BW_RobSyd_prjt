<?php
session_start();
if (isset($_SESSION['Username'])) {
    header('Location: index.php');
    exit();
}
$db = mysqli_connect('localhost', 'INFX472', 'P*ssword', 'wiki');
if (!$db) {
    http_response_code(500);
    die('Unable to connect to the database.');
}
$username = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Username = trim($_POST['username'] ?? '');
    $Password = $_POST['password'] ?? '';

    if ($Username === '' || $Password === '') {
        $error = 'Enter your username and password.';
    } else {
        $stmt = mysqli_prepare($db, 'SELECT Username, Password FROM users WHERE Username = ? LIMIT 1');

        if (!$stmt) {
            http_response_code(500);
            die('Unable to prepare the login request.');
        }

        mysqli_stmt_bind_param($stmt, 's', $Username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $storedUsername, $storedPassword);

        $userFound = mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        $validPassword = $userFound && hash_equals((string) $storedPassword, $Password);

        if ($validPassword) {
            session_regenerate_id(true);
            $_SESSION['Username'] = $storedUsername;

            header('Location: index.php');
            exit();
        }

        $error = 'The username or password is incorrect.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Wiki HomePage</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Wiki HomePage</h2>
    <main>
    <article>
<p>Welcome to the Wiki HomePage!<br> This is a simple web application that allows users to log in and access various features.<br>Please log in to continue.</p>

    </article>
        <form action="login.php" method="get">
            <button class="start-button" type="submit">Login</button>
        </form>
        <br>
        <form action="createuser.php" method="get">
            <button class="start-button" type="submit">New User</button>
        </form>
        <br>
    </main>
    <footer>&copy; <?= date('Y') ?> Wiki HomePage</footer>
</body>
</html>
