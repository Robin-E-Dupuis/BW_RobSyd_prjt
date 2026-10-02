<?php
require "database.php";
session_start();

    //if already logged in, goes to index
    if (isset($_SESSION["username"])) {
        header("Location: index.php");
        exit();
    }

    $error = "";

    //check if form submitted?
    if (isset($_POST["username"])) {
        $username = trim($_POST["username"]);
        $password = $_POST["password"] ?? "";

        //looks up the account (prepared statement prevents SQL injection)
        $stmt = mysqli_prepare($conn, "SELECT salt, password_hash FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $salt, $hash);

        //salt is added to the password, then checked against the stored hash
        if (mysqli_stmt_fetch($stmt) && password_verify($password . $salt, $hash))  {
            session_regenerate_id(true);
            $_SESSION["username"] = $username;
            mysqli_stmt_close($stmt);
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
        mysqli_stmt_close($stmt);
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Log In - INFX 472 Wiki</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <h1>Log In</h1>

        <?php if ($error): ?>
            <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="post" action="login.php">
            <p>
                <label>Username:<br>
                <input type="text" name="username" required></label>
            </p>
            <p>
                <label>Password:<br>
                <input type="password" name="password" required></label>
            </p>
            <p><input type="submit" name="submit" value="Log In"></p>
        </form>

        <p>No account? <a href="createuser.php">Create one</a> | <a href="index.php">Home</a></p>
    </body>
</html>