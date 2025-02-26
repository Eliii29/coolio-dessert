<?php
include 'security.php';
session_start(); // Start the session after setting parameters


    if (!isset($_SESSION['UserID'])) {
        header('Location: logout.php');
        exit();
    }

    $timeout_duration = 300;

    if (isset($_SESSION['LAST_ACTIVITY'])) {
        $elapsed_time = time() - $_SESSION['LAST_ACTIVITY'];
        if ($elapsed_time > $timeout_duration) {
            session_unset();
            session_destroy();
            header('Location: logout.php');
            exit();
        }
    }

    $_SESSION['LAST_ACTIVITY'] = time();

    include 'dbconn.php';

    $sql = "SELECT * FROM batch";
    $result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>

<html>
    <head>
    <h1>
            Coolio Dessert
        </h1>
        <ul>
            <li>
                <a href = "index.php">
                    Log out 
                </a> 
            </li>
            <li>
                <a href = "home.php">
                    Home
                </a> 
            </li>
        </ul>
        <link rel = "stylesheet" href = "style.css">
        <title> 
            Batch
        </title>
    </head>
    <body>
        <div class = "batch">
            <table class = "tableStyle">
                <thead>
                    <tr>
                        <th> Batch ID </th>
                        <th> Batch Date </th>    
                        <th> Product Total  </th>
                        <th> Product Available </th>
                        <th> Status </th>                        
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                    echo "<td>" . $row["BatchID"] . "</td>";
                                    echo "<td>" . $row["BatchDate"] . "</td>";                                        
                                    echo "<td>" . $row["ProductTotal"] . "</td>";
                                    echo "<td>" . $row["ProductAvailable"] . "</td>";
                                    echo "<td>" . $row["Status"] . "</td>";
                            }
                        } else {
                            echo "<tr><td>No records.</td></tr>";
                        }
                        mysqli_close($conn);
                    ?>
                </tbody>
            </table>
        </div> 
    </body> 
</html>
