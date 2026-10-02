<?php
require "database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"];
    $password = $_POST["password"];

    if (empty($username) || empty($password)) {

        $message = "Please enter a username and password.";

    } else {

        
        $sql = "SELECT id FROM users WHERE username = ?";
        $check = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($check, "s", $username);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {

            $message = "Username already exists.";

        } else {

            //salt
            $salt = bin2hex(random_bytes(16));

           
            $password_hash = password_hash($password . $salt, PASSWORD_DEFAULT);

            
            $sql = "INSERT INTO users (username, salt, password_hash)
                    VALUES (?, ?, ?)";

            $insert = mysqli_prepare($conn, $sql);

            mysqli_stmt_bind_param(
                $insert,
                "sss",
                $username,
                $salt,
                $password_hash
            );

            if (mysqli_stmt_execute($insert)) {

                header("Location: login.php");
                exit();

            } else {

                $message = "Error creating account.";
            }

            mysqli_stmt_close($insert);
        }

        mysqli_stmt_close($check);
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Create Account</title>
    </head>
    <body>
        <h1>Create Account</h1>

        <?php
        if (!empty($message)) {
            echo "<p>" . $message . "</p>";
        }
        ?>
        <form method="POST" action="createuser.php">
            <label>Username:</label>
            <input type="text" name="username">

            <br><br>

            <label>Password:</label>
            <input type="password" name="password">

            <br><br>

            <input type="submit" value="Create Account">
        </form>

        <br>

        <a href="login.php">Back to Login</a>

    </body>
</html>