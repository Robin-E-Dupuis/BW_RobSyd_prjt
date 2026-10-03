<?php
require "database.php";
session_start();

    if (!isset($_SESSION["username"])) {
        header("Location: login.php");
        exit();
    }

    $error = "";

    if (isset($_POST["short_title"])) {
        $short_title = mysqli_real_escape_string($conn, trim($_POST["short_title"]));
        $title       = mysqli_real_escape_string($conn, trim($_POST["title"]));
        $body        = mysqli_real_escape_string($conn, trim($_POST["body"]));
        $user        = mysqli_real_escape_string($conn, $_SESSION["username"]);

        if ($short_title == "" || $title == "" || $body == "")
        {
            $error = "All fields are required.";
        } 
        else 
        {
            //check if article is already there
            $check = mysqli_query($conn, "SELECT id FROM articles WHERE short_title = '$short_title'");

            if (mysqli_num_rows($check) > 0)
            {
                $error = "That short title is already in use. Pick another.";
            } else {
                mysqli_query($conn, "INSERT INTO articles (short_title, title, body, created_by, updated_by, created_at, updated_at)
                                    VALUES ('$short_title', '$title', '$body', '$user', '$user', NOW(), NOW())");

                header("Location: wiki.php?short_title=" . urlencode(trim($_POST["short_title"])));
                exit();
            }
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Add Article - INFX 472 Wiki</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>

        <h1>Add Article</h1>
        <p><a href="index.php">Home</a> | <a href="index.php?logout=1">Logout</a></p>

        <?php
        if ($error != "")
        {
          echo "<p style=\"color:red;\">" . htmlspecialchars($error) . "</p>";
        }
        ?>

        <form method="post" action="addarticle.php">
            <p>Short title (used in the URL, e.g. INFX472Article):<br>
            <input type="text" name="short_title"></p>
            <p>Title:<br>
            <input type="text" name="title"></p>
            <p>Body:<br>
            <textarea name="body" rows="12" cols="70"></textarea></p>
            <p><input type="submit" name="submit" value="Add Article"></p>
        </form>
    </body>
</html>