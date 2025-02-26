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

    $role = $_SESSION['Role'];
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
        </ul>
        <link rel = "stylesheet" href = "style.css">
        <title> 
            Home
        </title>
    </head>
    <body>
        <div class = "home">
            <div class = "container">
                <div class = "box2">
                    <img src = "images/alert.png" class = "icons">
                    <button type = "button">
                        <a href = "viewAlerts.php">
                            Alerts
                        </a>
                    </button> 
                </div>
                <div class = "box2">
                    <img src = "images/products.png" class = "icons">
                    <button type = "button">
                        <a href = "viewProducts.php">
                            Product
                        </a>
                    </button> 
                </div> 
                <div class = "box2">
                    <img src = "images/storage.png" class = "icons">
                    <button type = "button">
                        <a href = "viewStorage.php">
                            Storage
                        </a>
                    </button> 
                </div> 
                <?php if ($role == "Super Admin"): ?>
                <div class = "box2">
                    <img src = "images/transactions.png" class = "icons">
                    <button type = "button">
                        <a href = "viewTransactions.php">
                            Transactions
                        </a>
                    </button> 
                </div> 
                <div class = "box2">
                    <img src = "images/user.png" class = "icons">
                    <button type = "button">
                        <a href = "viewUsers.php">
                            Users
                        </a>
                    </button> 
                </div> 
                <?php endif; ?>
            </div>
        </div> 
    </body> 
</html>
