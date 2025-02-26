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

    $errors = $_SESSION['errors'] ?? "";
    $Email = $_SESSION['Email'] ?? "";

    unset(
        $_SESSION['errors'],
        $_SESSION['Email']
    );
?>

<!DOCTYPE html>

<html>
    <head>
        <link rel="stylesheet" href="style.css">
        <title> Log-in </title>
    </head>
    <body>
        <div class = "login">
            <h1>
                Log-in 
            </h1>
            <div>
                <?php if (isset($errors["general"])): ?>
                    <p class = "error">
                        <?php echo $errors['general']; ?>
                    </p>
                <?php endif; ?>
            </div>
            <div class = "box1">
                <form method = "post" action = "verifyLogin.php">
                    <label for = "Email">
                        Email:
                    </label>
                    <br>
                    <input type = "text" id = "Email" name = "Email" value = "<?php echo htmlspecialchars($Email); ?>"/>
                    <br>
                    <?php if (isset($errors["Email"])): ?>
                        <span class = "error">
                            <?php echo $errors["Email"]; ?>
                        </span>
                    <?php endif; ?>
                    <br> <br>
                    <label for = "Password">
                        Password:
                    </label>
                    <br>
                    <input type = "Password" id = "Password" name = "Password" />
                    <br>
                    <?php if (isset($errors["Password"])): ?>
                        <span class = "error">
                            <?php echo $errors["Password"]; ?>
                        </span>
                    <?php endif; ?>
                    <br> <br>
                    <button class = "verifyLogin" type = "submit">
                        Login 
                    </button>
                    <br> <br>
                    <button type = "button">
                        <a href="index.php">
                            Back 
                        </a>
                    </button>
                </form>
            </div>
        </div> 
    </body> 
</html>
