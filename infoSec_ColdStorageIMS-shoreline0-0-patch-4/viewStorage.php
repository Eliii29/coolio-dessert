<?php
// 1️⃣ Content Security Policy (CSP) - Prevent XSS Attacks
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self' data:; frame-ancestors 'none';");

// 2️⃣ Clickjacking Protection (X-Frame-Options)
header("X-Frame-Options: SAMEORIGIN");

// 3️⃣ Prevent MIME Sniffing (X-Content-Type-Options)
header("X-Content-Type-Options: nosniff");

// 4️⃣ Enforce HTTPS (Strict-Transport-Security)
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");

// 5️⃣ Secure Cookies (Set Flags for Sessions)
session_set_cookie_params([
    'httponly' => true,    // Prevents JavaScript from accessing cookies
    'secure' => isset($_SERVER['HTTPS']), // Only send cookies over HTTPS
    'samesite' => 'Strict' // Prevents CSRF attacks
]);
session_start(); // Start the session after setting parameters

// 6️⃣ Set Cookies Securely
setcookie("MySecureCookie", "value", [
    'expires' => time() + 3600, // 1 hour expiry
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => isset($_SERVER['HTTPS']), // Only over HTTPS
    'httponly' => true, // Prevent JavaScript access
    'samesite' => 'Strict' // CSRF Protection
]);

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

    $sql = "SELECT s.StorageID, s.StorageName, s.StorageMaxCapacity, s.StorageTemperature,
        COALESCE(SUM(p.CurrentStock), 0) AS StorageUsedCapacity, 
        (s.StorageMaxCapacity - COALESCE(SUM(p.CurrentStock), 0)) AS AvailableCapacity
        FROM storage s
        LEFT JOIN product p ON s.StorageID = p.StorageID
        GROUP BY s.StorageID;";
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
            Storage
        </title>
    </head>
    <body>
    <div class = "storage">
            <table class = "tableStyle">
                <thead>
                    <tr>
                        <th> Storage ID </th>
                        <th> Name </th>    
                        <th> Max Capacity </th>
                        <th> Used Capacity </th>
                        <th> Temperature (°C) </th>
                        <th>
                            <form method='post' action='formCreateStorage.php'>
                                <button type='submit'>
                                    Add Storage
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
                                    echo "<td>" . $row["StorageID"] . "</td>";
                                    echo "<td>" . $row["StorageName"] . "</td>";
                                    echo "<td>" . $row["StorageMaxCapacity"] . "</td>";
                                    echo "<td>" . $row["StorageUsedCapacity"] . "</td>";
                                    echo "<td>" . $row["StorageTemperature"] . "</td>";
                                    echo "<td> 
                                            <form method='post' action='formUpdateStorage.php'>
                                                <input type = 'hidden' name = 'StorageID' value = '". $row['StorageID']. "'/>
                                                <input type = 'hidden' name = 'StorageName' value = '". $row['StorageName']. "'/>      
                                                <input type = 'hidden' name = 'StorageCapacity' value = '". $row['StorageMaxCapacity']. "'/>
                                                <input type = 'hidden' name = 'StorageCapacity' value = '". $row['StorageUsedCapacity']. "'/>
                                                <input type = 'hidden' name = 'StorageTemperature' value = '". $row['StorageTemperature']. "'/>
                                                <button type='submit'>
                                                    Update
                                                </button>
                                            </form>
                                            <br>
                                            <form method='post' action='deleteStorage.php'>
                                                <input type = 'hidden' name = 'StorageID' value = '". $row['StorageID']. "'/>
                                                <input type = 'hidden' name = 'StorageName' value = '". $row['StorageName']. "'/>      
                                                <input type = 'hidden' name = 'StorageCapacity' value = '". $row['StorageMaxCapacity']. "'/>
                                                <input type = 'hidden' name = 'StorageCapacity' value = '". $row['StorageUsedCapacity']. "'/>
                                                <input type = 'hidden' name = 'StorageTemperature' value = '". $row['StorageTemperature']. "'/>
                                                <button type='submit'>
                                                    Delete
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
