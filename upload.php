<?php

include("db.php");

$filename = "IMG_" . time() . ".jpg";

file_put_contents(
    "uploads/" . $filename,
    file_get_contents("php://input")
);

mysqli_query(
    $conn, "INSERT INTO (photo_name) VALUES('$filename')"
);

echo"Uploaded";

?>