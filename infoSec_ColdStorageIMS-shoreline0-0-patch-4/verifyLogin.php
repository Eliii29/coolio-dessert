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

    include 'dbconn.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $errors = [];
        $Email = "";

        $Email = filter_var($_POST["Email"], FILTER_SANITIZE_EMAIL);
        $loginPW = htmlspecialchars(trim($_POST["Password"]), ENT_QUOTES, "UTF-8");
        $hashedInput = hash("sha256", $loginPW);

        if (empty($Email)) {
            $errors["Email"] = "Email required.";
        } elseif (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
            $errors["Email"] = "Invalid Email.";
        }

        if (empty($loginPW)) {
            $errors["Password"] = "Password required.";
        }

        if(!empty($errors)) {
            $_SESSION["errors"] = $errors;
            $_SESSION["Email"] = $Email;

            header("Location: login.php");
            exit();
        }

        $sql = "SELECT * FROM users WHERE Email = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $Email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
        
            if (mysqli_num_rows($result) == 1) {
                $user = mysqli_fetch_assoc($result);
                $hashedPW = $user['Password'];

                $access = 'login';
        
                if ($hashedInput == $hashedPW) {
                    $_SESSION['UserID'] = $user['UserID'];
                    $_SESSION['FirstName'] = $user['FirstName'];
                    $_SESSION['Role'] = $user['Role'];

                    header("Location: home.php");
                } else {
                    $errors['Password'] = "Incorrect Password.";
                    if (!empty($errors)) {
                        $_SESSION['errors'] = $errors;
                        $_SESSION['Email'] = $Email;
                        
                        header("Location: login.php");               
                        exit();
                    }                  
                }
            } else {
                $errors['Email'] = "Account does not exist!";     
                if (!empty($errors)) {
                    $_SESSION['errors'] = $errors;
                    $_SESSION['Email'] = $Email;
                    
                    header("Location: login.php");               
                    exit();
                }    
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Database error: " . mysqli_error($conn);
        }
    }
?>
