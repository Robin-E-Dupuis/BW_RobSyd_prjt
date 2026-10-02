<?php
require "database.php";
session_start();

    //logout handling
    if (isset($_GET["logout"])) {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit();
    }

    $loggedIn = isset($_SESSION["username"]);
    $result = null;

    //only query articles if the user is authenticated
    if ($loggedIn) {
        $result = mysqli_query($conn, "SELECT short_title, title FROM articles ORDER BY title ASC");
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>INFX 472 Wiki</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <h1>INFX 472 Wiki</h1>

        <?php if (!$loggedIn): ?>
            <p>Welcome! Please log in or create an account to view articles.</p>
            <p><a href="login.php">Log In</a> | <a href="createuser.php">Create Account</a></p>

        <?php else: ?>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!
            | <a href="addarticle.php">Add Article</a>
            | <a href="index.php?logout=1">Logout</a></p>

        <h2>Articles</h2>
        <ul>
        <?php
        if ($result && mysqli_num_rows($result) > 0) 
        {
            while ($row = mysqli_fetch_assoc($result)) 
            {
                $short = urlencode($row["short_title"]);
                $title = htmlspecialchars($row["title"]);
                echo "<li><a href=\"wiki.php?short_title=$short\">$title</a></li>";
            }
        } 
        else 
        {
            echo "<li>No articles yet. Add the first one!</li>";
        }
        ?>
        </ul>
    <?php endif; ?>

    </body>
</html>