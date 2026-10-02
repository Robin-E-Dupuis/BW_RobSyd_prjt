<?php

$conn = mysqli_connect("localhost", "INFX472", "P*ssword", "wiki");
if (!$conn) {
    exit("Database connection failed: " . mysqli_connect_error());
}

?>