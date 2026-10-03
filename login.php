<?php
    require "database.php";
    session_start();

    if (isset($_SESSION["username"])) {
        header("Location: index.php");
        exit();
    }

    $error = "";

    if (isset($_POST["username"])) {
        $username = mysqli_real_escape_string($conn, trim($_POST["username"]));
        $password = $_POST["password"];

        $result = mysqli_query($conn, "SELECT salt, password_hash FROM users WHERE username = '$username'");

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);

            //salt is added to the password, matching createuser.php
            if (password_verify($password . $row["salt"], $row["password_hash"])) {
                $_SESSION["username"] = $_POST["username"];
                header("Location: index.php");
                exit();
            }
        }
        $error = "Invalid username or password.";
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

        <?php
        if ($error != "") 
        {
            echo "<p style=\"color:red;\">" . htmlspecialchars($error) . "</p>";
        }
        ?>

        <form method="post" action="login.php">
        <p>Username:<br><input type="text" name="username"></p>
        <p>Password:<br><input type="password" name="password"></p>
        <p><input type="submit" name="submit" value="Log In"></p>
        </form>

        <p>No account? <a href="createuser.php">Create one</a> | <a href="index.php">Home</a></p>
    </body>
</html>