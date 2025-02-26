<?php   
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "coldstoragedb";

    $conn = mysqli_connect(
        $servername,
        $username,
        $password,
        $dbname
    );

    if ($conn == false) {
        die("Error: Could not connect to server. " . mysqli_connect_error());
    }
?>