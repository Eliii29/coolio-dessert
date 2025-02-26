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

    $errors = $_SESSION['errors'] ?? [];
    unset($_SESSION['errors']);

    $UserID = $_POST['UserID'] ?? ($_SESSION['FirstName'] ?? '');
    $FirstName = $_POST['FirstName'] ?? ($_SESSION['FirstName'] ?? '');
    $LastName = $_POST['LastName'] ?? ($_SESSION['LastName'] ?? '');
    $Email = $_POST['Email'] ?? ($_SESSION['Email'] ?? '');
    $Status = $_POST['Status'] ?? ($_SESSION['Status'] ?? '');
    $Role = $_POST['Role'] ?? ($_SESSION['Role'] ?? '');

    if ($UserID) {
        $sql = "SELECT * FROM users WHERE UserID = '$UserID'";
        $result = mysqli_query($conn,$sql);
        $users = mysqli_fetch_assoc($result);

        if (!$users) {
            echo "No user found.";
        }
    }

?>

<!DOCTYPE html>

<html>
    <meta http-equiv = "refresh" content = "300; url = index.php">
    <head>
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
            Add User
        </title>
    </head>
    <body>
        <div class = "addProduct">
            <div class = "box1">
                <form method='post' action='updateUser.php'>
                    <!-- <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">     -->
                    <input type = 'hidden' id='UserID' name = 'UserID' value='<?php echo htmlspecialchars($UserID);?>'/>
                    <label for='FirstName'>
                        First Name:
                    </label>
                    <br>
                    <input type = 'text' id='FirstName' name = 'FirstName' value = '<?php echo htmlspecialchars($FirstName); ?>'/><br>
                    <?php if (isset($errors['FirstName'])): ?>
                        <span class="error"> 
                            <?php echo $errors['FirstName']; ?>
                        </span>
                        <br>
                    <?php endif; ?>
                    <br>
                    <label for='LastName'>
                        Last Name:
                    </label>
                    <br>
                    <input type = 'text' id='LastName' name = 'LastName' value = '<?php echo htmlspecialchars($LastName); ?>'/><br>
                    <?php if (isset($errors['LastName'])): ?>
                        <span class="error"> 
                            <?php echo $errors['LastName']; ?>
                        </span>
                        <br>
                    <?php endif; ?>         
                    <br>
                    <label for='Email'>
                        Email:
                    </label>
                    <br>
                    <input type = 'text' id='Email' name = 'Email' value = '<?php echo htmlspecialchars($Email); ?>'/>
                    <br>
                    <?php if (isset($errors['Email'])): ?>
                        <span class="error"> 
                            <?php echo $errors['Email']; ?>
                        </span>
                        <br>
                    <?php endif; ?>
                    <br>
                    <label for='Status'>
                        Status:
                    </label>
                    <br>
                    <input type = 'text' id='Status' name = 'Status' value = '<?php echo htmlspecialchars($Status); ?>'/>
                    <br>
                    <?php if (isset($errors['Status'])): ?>
                        <span class="error"> 
                            <?php echo $errors['Status']; ?>
                        </span>
                        <br>
                    <?php endif; ?>
                    <br>
                    <br>
                    <label for="Role">
                        Role:
                    </label>
                    <select id="Role" name="Role" required>
                        <option value="" disabled selected>
                            Select here
                        </option>
                        <option value="admin" <?php if ($Role == "Admin") echo "selected"; ?>>Admin</option>
                        <option value="superAdmin" <?php if ($Role == "Super Admin") echo "selected"; ?>>Super Admin</option>
                    </select>
                    <br><br>
                    <button class='updateUser' type='submit'> 
                        Update User
                    </button>
                    <br>
                    <button type="button">
                        <a href="viewUsers.php">
                            Back
                        </a>
                    </button>
                </form>
            </div> 
        </div> 
    </body> 
</html>


