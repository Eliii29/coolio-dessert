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

    $sql = "SELECT * FROM users";
    $result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>

<html>
    <meta http-equiv = "refresh" content = "300; url = index.php">
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
            Users
        </title>
    </head>
    <body>
    <div class = "users">
            <table class = "tableStyle">
                <thead>
                    <tr>
                        <th> User ID </th>
                        <th> Name </th>    
                        <th> Email  </th>
                        <th> Status </th>
                        <th> Role </th>
                        <th>
                            <form method='post' action='formCreateUser.php'>
                                <button type='submit'>
                                    Add User
                                </button> 
                            </form>
                        </th>
                    </tr>                    
                </thead>
                <tbody>
                    <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                    echo "<td>" . $row["UserID"] . "</td>";
                                    echo "<td>" . $row["FirstName"] . " " . $row["LastName"] . " </td>";
                                    echo "<td>" . $row["Email"] . "</td>";
                                    echo "<td>" . $row["Status"] . "</td>";
                                    echo "<td>" . $row["Role"] . "</td>";
                                    echo "<td> 
                                            <form method='post' action='formUpdateUser.php'>
                                                <input type = 'hidden' name = 'UserID' value = '". $row['UserID']. "'/>
                                                <input type = 'hidden' name = 'FirstName' value = '". $row['FirstName']. "'/>      
                                                <input type = 'hidden' name = 'LastName' value = '". $row['LastName']. "'/>
                                                <input type = 'hidden' name = 'Email' value = '". $row['Email']. "'/>
                                                <input type = 'hidden' name = 'Status' value = '". $row['Status']. "'/>
                                                <input type = 'hidden' name = 'Role' value = '". $row['Role']. "'/>
                                                <button type='submit'>
                                                    Update
                                                </button>
                                            </form>
                                        </td>";
                                echo "<tr>";
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
