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

    $sql = "SELECT * FROM inventory";
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
            Inventory
        </title>
    </head>
    <body>
        <div class = "inventory">
            <table class = "tableStyle">
                <thead>
                    <tr>
                        <th> Inventory ID </th>
                        <th> Product ID  </th>
                        <th> Storage ID </th>
                        <th> Batch ID </th>    
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                    echo "<td>" . $row["InventoryID"] . "</td>";
                                    echo "<td>" . $row["ProductID"] . "</td>";
                                    echo "<td>" . $row["StorageID"] . "</td>";
                                    echo "<td>" . $row["BatchID"] . "</td>";
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
