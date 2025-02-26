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

    $sql = "SELECT * FROM storage";
    $result = mysqli_query($conn,$sql);

    if (
        isset($_POST['StorageID']) &&
        isset($_POST['StorageName']) &&
        isset($_POST['StorageCapacity']) &&
        isset($_POST['StorageTemperature'])
    ) {
        
        $StorageID = $_POST['StorageID'];
        $StorageName = $_POST['StorageName'];
        $StorageCapacity = $_POST['StorageCapacity'];
        $StorageTemperature = $_POST['StorageTemperature'];

        $sql = "DELETE FROM storage WHERE StorageID = $StorageID";

        if (mysqli_query($conn,$sql)) {
            $UserID = $_SESSION['UserID'];
            $TransactionType = "Deleted storage";
            $Details = "ID: ". $StorageID ." - " . $StorageName . " (Capacity: " . $StorageCapacity . " , Temp " . $StorageTemperature . ")";
                    
            $sqlLog = "INSERT INTO transactionlog (TransactionType, UserID, TransactionDate, Details) VALUES (?, ?, NOW(), ?)";
            if ($stmtLog = mysqli_prepare($conn, $sqlLog)) {
                mysqli_stmt_bind_param($stmtLog, "sis", $TransactionType, $UserID, $Details);                            
                mysqli_stmt_execute($stmtLog);
                mysqli_stmt_close($stmtLog);
            }
            header("Location: viewStorage.php?storaget=deleted");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }

    } else {
        echo "Missing parameters.";
    }

    mysqli_close($conn);

?>