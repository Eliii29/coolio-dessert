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

    $sql = "SELECT * FROM product";
    $result = mysqli_query($conn,$sql);

    if (
        isset($_POST['ProductID']) &&
        isset($_POST['ProductName']) &&
        isset($_POST['CurrentStock']) &&
        isset($_POST['ProductExpiryDate'])
    ) {
        
        $ProductID = $_POST['ProductID'];
        $ProductName = $_POST['ProductName'];
        $CurrentStock = $_POST['CurrentStock'];

        $sql = "DELETE FROM product WHERE ProductID = $ProductID";

        if (mysqli_query($conn,$sql)) {
            $UserID = $_SESSION['UserID'];
            $TransactionType = "Deleted product";
            $Details = "ID: ". $ProductID ." - " . $ProductName . " (Quantity: " . $CurrentStock . ")";
                    
            $sqlLog = "INSERT INTO transactionlog (TransactionType, UserID, TransactionDate, Details) VALUES (?, ?, NOW(), ?)";
            if ($stmtLog = mysqli_prepare($conn, $sqlLog)) {
                mysqli_stmt_bind_param($stmtLog, "sis", $TransactionType, $UserID, $Details);                            
                mysqli_stmt_execute($stmtLog);
                mysqli_stmt_close($stmtLog);
            }
            header("Location: viewProducts.php?product=deleted");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }

    } else {
        echo "Missing parameters.";
    }

    mysqli_close($conn);

?>