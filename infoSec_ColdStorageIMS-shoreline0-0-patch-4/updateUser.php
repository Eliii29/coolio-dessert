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

    $errors = [];
    $FirstName = $LastName = $Email = $Status = $Role = "";

    $sql = "SELECT * FROM storage";
    $result = mysqli_query($conn,$sql);

    if (
        isset($_POST['UserID']) &&
        isset($_POST['FirstName']) &&
        isset($_POST['LastName']) &&
        isset($_POST['Email']) &&
        isset($_POST['Status']) &&
        isset($_POST['Role'])
    ) {
        $UserID = $_POST['UserID'];
        $FirstName = htmlspecialchars($_POST['FirstName'], ENT_QUOTES, 'UTF-8');
        $LastName = htmlspecialchars($_POST['LastName'], ENT_QUOTES, 'UTF-8');
        $Email = htmlspecialchars($_POST['Email'], ENT_QUOTES, 'UTF-8');
        $Status = htmlspecialchars($_POST['Status'], ENT_QUOTES, 'UTF-8');
        $Role = htmlspecialchars($_POST['Role'], ENT_QUOTES, 'UTF-8');

        if (empty($FirstName)) {
            $errors['FirstName'] = "First name required.";
        } elseif (strlen($FirstName) > 50) {
            $errors['FirstName'] = "First name too long.";
        } elseif (!preg_match("/^[a-zA-Z\s]+$/", $FirstName)) {
            $errors['FirstName'] = "Invalid name.";
        }
    
        if (empty($LastName)) {
            $errors['LastName'] = "Last name required.";
        } elseif (strlen($LastName) > 50) {
            $errors['LastName'] = "Last name too long.";
        } elseif (!preg_match("/^[a-zA-Z\s]+$/", $LastName)) {
            $errors['LastName'] = "Invalid name.";
        }
    
        if (empty($Email)) {
            $errors['Email'] = "Email required.";
        } elseif (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
            $errors['Email'] = "Invalid email.";
        }

        if (empty($Status)) {
            $errors['Status'] = "Role required.";
        } elseif (!preg_match("/^[a-zA-Z\s]+$/", $Status)) {
            $errors['Status'] = "Invalid status.";
        }

        if (empty($Role)) {
            $errors['Role'] = "Role required.";
        } elseif (!preg_match("/^[a-zA-Z\s]+$/", $Role)) {
            $errors['Role'] = "Invalid role.";
        }

        $sql = "SELECT * FROM users WHERE Email = ? AND UserID != ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "si", $Email, $UserID);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($result) > 0) {
                $errors['Email'] = "Account already exists with this email!";
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Database error: " . mysqli_error($conn);
        }
    
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['FirstName'] = $FirstName;
            $_SESSION['LastName'] = $LastName;
            $_SESSION['Email'] = $Email;
            $_SESSION['Status'] = $Status;
            $_SESSION['Role'] = $Role;

            header("Location: formEditUser.php");
            exit();
        } else {
            $sql = 
            "UPDATE users 
            SET 
                FirstName = ?, 
                LastName = ?,
                Email = ?,
                Status = ?, 
                Role = ?
            WHERE UserID = ?";
            
            $stmt = mysqli_prepare($conn, $sql);
    
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sssssi", $FirstName, $LastName, $Email, $Status, $Role, $UserID);
                if (mysqli_stmt_execute($stmt)) {
                    $UserID = mysqli_insert_id($conn);
                    $SAdminUserID = $_SESSION['UserID'];
                    $TransactionType = "Updated user";
                    $Details = "ID: ". $UserID ." - " . $FirstName . " "  . $LastName . "";
                    
                    $sqlLog = "INSERT INTO transactionlog (TransactionType, UserID, TransactionDate, Details) VALUES (?, ?, NOW(), ?)";
                    if ($stmtLog = mysqli_prepare($conn, $sqlLog)) {
                        mysqli_stmt_bind_param($stmtLog, "sis", $TransactionType, $SAdminUserID, $Details);                            
                        mysqli_stmt_execute($stmtLog);
                        mysqli_stmt_close($stmtLog);
                    }
                    header("Location: viewUsers.php?user=updated");
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