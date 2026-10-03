<?php
require "database.php";
session_start();

    if (!isset($_SESSION["username"])) {
        header("Location: login.php");
        exit();
    }

    if (!isset($_GET["short_title"])) {
        header("Location: index.php");
        exit();
    }

    $raw_short_title = $_GET["short_title"];
    $short_title = mysqli_real_escape_string($conn, $raw_short_title);
    $user = mysqli_real_escape_string($conn, $_SESSION["username"]);
    $message = "";
    $error = "";

    //haandles any updates
    if (isset($_POST["body"])) {
        $newBody = mysqli_real_escape_string($conn, trim($_POST["body"]));

        if ($newBody == "")
        {
            $error = "The body can't be empty.";
        }
        else
        {
            mysqli_query($conn, "UPDATE articles 
                                SET body = '$newBody', updated_by = '$user', updated_at = NOW()
                                WHERE short_title = '$short_title'");
            header("Location: wiki.php?short_title=" . urlencode($raw_short_title) . "&updated=1");
            exit();
        }
    }

    if (isset($_GET["updated"])) {
        $message = "Article updated.";
    }

    //loads the article
    $result = mysqli_query($conn, "SELECT * FROM articles WHERE short_title = '$short_title'");
    $article = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Article - INFX 472 Wiki</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
    <p><a href="index.php">Home</a> | <a href="addarticle.php">Add Article</a> | <a href="index.php?logout=1">Logout</a></p>

        <?php
            if (!$article)
            {
                echo "<h1>Article not found</h1>";
            }
            else
            {
                echo "<h1>" . htmlspecialchars($article["title"]) . "</h1>";
                echo "<p><small>Created by " . htmlspecialchars($article["created_by"]) . " on " . $article["created_at"];
                echo " | Last updated by " . htmlspecialchars($article["updated_by"]) . " on " . $article["updated_at"] . "</small></p>";

                if ($message != "") {
                    echo "<p style=\"color:green;\">" . htmlspecialchars($message) . "</p>";
                }
                if ($error != "") {
                    echo "<p style=\"color:red;\">" . htmlspecialchars($error) . "</p>";
                }

                echo "<div class=\"article-body\">" . nl2br(htmlspecialchars($article["body"])) . "</div>";
            ?>

            <h2>Update this article</h2>
            <form method="post" action="wiki.php?short_title=<?php echo urlencode($raw_short_title); ?>">
                <p><textarea name="body" rows="12" cols="70"><?php echo htmlspecialchars($article["body"]); ?></textarea></p>
                <p><input type="submit" name="submit" value="Update Article"></p>
            </form>

        <?php
        }
    ?>
    </body>
</html>