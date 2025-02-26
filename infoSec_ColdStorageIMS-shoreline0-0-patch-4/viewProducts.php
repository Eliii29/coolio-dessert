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

    $sql = "SELECT p.ProductID, p.ProductName, p.CurrentStock, p.ProductExpiryDate, s.StorageName AS StorageName 
        FROM product p
        LEFT JOIN storage s ON p.StorageID = s.StorageID";

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
            Products
        </title>
    </head>
    <body>
    <div class = "product">
            <table class = "tableStyle">
                <thead>
                    <tr>
                        <th> Product ID </th>
                        <th> Name </th>    
                        <th> Current Stock </th>
                        <th> Expiry Date </th>
                        <th> Storage </th>
                        <th>
                            <form method='post' action='formCreateProduct.php'>
                                <button type='submit'>
                                    Add Product
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
                                    echo "<td>" . $row["ProductID"] . "</td>";
                                    echo "<td>" . $row["ProductName"] . "</td>";
                                    echo "<td>" . $row["CurrentStock"] . "</td>";
                                    echo "<td>" . $row["ProductExpiryDate"] . "</td>";
                                    echo "<td>" . $row["StorageName"] . "</td>";
                                    echo "<td> 
                                            <form method='post' action='formUpdateProduct.php'>
                                                <input type = 'hidden' name = 'ProductID' value = '". $row['ProductID']. "'/>
                                                <input type = 'hidden' name = 'ProductName' value = '". $row['ProductName']. "'/>      
                                                <input type = 'hidden' name = 'CurrentStock' value = '". $row['CurrentStock']. "'/>
                                                <input type = 'hidden' name = 'ProductExpiryDate' value = '". $row['ProductExpiryDate']. "'/>
                                                <input type='hidden' name='StorageID' value='". $row['StorageName']. "'/>
                                                <button type='submit'>
                                                    Update
                                                </button>
                                            </form>
                                            <br>
                                            <form method='post' action='deleteProduct.php'>
                                                <input type = 'hidden' name = 'ProductID' value = '". $row['ProductID']. "'/>
                                                <input type = 'hidden' name = 'ProductName' value = '". $row['ProductName']. "'/>      
                                                <input type = 'hidden' name = 'CurrentStock' value = '". $row['CurrentStock']. "'/>
                                                <input type = 'hidden' name = 'ProductExpiryDate' value = '". $row['ProductExpiryDate']. "'/>
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
