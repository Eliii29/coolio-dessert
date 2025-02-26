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

//starts here

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
    include 'checkAlerts.php';

    $productID = $_POST['ProductID'];
    $storageID = $_POST['StorageID'];

    $errors = [];
    $ProductName = $CurrentStock = $ProductExpiryDate = $StorageID = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $ProductName = htmlspecialchars($_POST['ProductName'], ENT_QUOTES, 'UTF-8');
        $CurrentStock = filter_input(INPUT_POST, 'CurrentStock', FILTER_VALIDATE_INT);
        $ProductExpiryDate = filter_input(INPUT_POST, 'ProductExpiryDate', FILTER_SANITIZE_STRING);
        $StorageID = (int)$_POST['StorageID'];

        if (empty($ProductName)) {
            $errors['ProductName'] = "Product name required.";
        } elseif (strlen($ProductName) > 50) {
            $errors['ProductName'] = "Product name too long.";
        }
    
        if ($CurrentStock === false || $CurrentStock < 0) {
            $errors['CurrentStock'] = "Invalid stock.";
        }

        if (empty($ProductExpiryDate)) {
            $errors['ProductExpiryDate'] = "Expiry date required.";
        }
    
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['ProductName'] = $ProductName;
            $_SESSION['CurrentStock'] = $CurrentStock;
            $_SESSION['ProductExpiryDate'] = $ProductExpiryDate;
            
            header("Location: formCreateProduct.php");
            exit();
    
        } else {
            $sql = "INSERT INTO product (ProductName, CurrentStock, ProductExpiryDate, StorageID) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
    
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sisi", $ProductName, $CurrentStock, $ProductExpiryDate, $StorageID);

                if (mysqli_stmt_execute($stmt)) {
                    $ProductID = mysqli_insert_id($conn);
                    $UserID = $_SESSION['UserID'];
                    $TransactionType = "Added product";
                    $Details = "ID: ". $ProductID ." - " . $ProductName . " (Quantity: " . $CurrentStock . ")";
                    
                    $sqlLog = "INSERT INTO transactionlog (TransactionType, UserID, TransactionDate, Details) VALUES (?, ?, NOW(), ?)";
                    if ($stmtLog = mysqli_prepare($conn, $sqlLog)) {
                        mysqli_stmt_bind_param($stmtLog, "sis", $TransactionType, $UserID, $Details);                            
                        mysqli_stmt_execute($stmtLog);
                        mysqli_stmt_close($stmtLog);
                    }

                    checkProductAlert($productID, $conn);
                    checkStorageAlert($storageID, $conn);
                    
                    header("Location: viewProducts.php?product=created");
                    exit();
                } else {
                    echo "Error: " . mysqli_stmt_error($stmt);
                }
                mysqli_stmt_close($stmt);
            } else {
                echo "Error preparing statement.";
            }
        }    
    }
?>