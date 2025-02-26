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

    $sortOrder = isset($_GET['sort']) && $_GET['sort'] == 'asc' ? 'ASC' : 'DESC';
    $nextSortOrder = ($sortOrder == 'ASC') ? 'desc' : 'asc';

    $sql = "SELECT * FROM alerts ORDER BY AlertID $sortOrder";
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
            Alerts
        </title>
    </head>
    <body>
        <div class = "alerts">
            <table class = "tableStyle">
                <thead>
                    <tr>
                        <th> Alert ID 
                            <a href="?sort=<?php echo $nextSortOrder; ?>">
                                <?php echo ($sortOrder == 'ASC') ? '⬇️' : '⬆️'; ?>
                            </a>
                        </th>
                        <th> Type </th>    
                        <th> Related ID  </th>
                        <th> Name </th>
                        <th> Time </th>                            
                        <th> Notes </th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>" . $row["AlertID"] . "</td>";
                                echo "<td>" . $row["AlertType"] . "</td>";                                        
                                echo "<td>" . $row["RelatedID"] . "</td>";
                                echo "<td>" . $row["AlertName"] . "</td>";
                                echo "<td>" . $row["AlertTime"] . "</td>";
                                echo "<td>" . $row["Notes"] . "</td>";
                            }
                        } else {
                            echo "<tr><td>No records.</td></tr>";
                        }
                        mysqli_close($conn);
                    ?>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div> 
    </body> 
</html>
