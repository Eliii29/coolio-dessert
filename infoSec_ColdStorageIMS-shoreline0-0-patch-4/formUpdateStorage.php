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
    unset ($_SESSION['errors']);
    
    $StorageID = $_POST['StorageID'] ?? '';
    $StorageName = $_POST['StorageName'] ?? $_SESSION['StorageName'] ?? '';
    $StorageCapacity = $_POST['StorageCapacity'] ?? $_SESSION['StorageCapacity'] ?? ($_SESSION['StorageCapacity'] ?? '');
    $StorageTemperature = $_POST['StorageTemperature'] ?? $_SESSION['StorageTemperature'] ?? ($_SESSION['StorageTemperature'] ?? '');

    if ($StorageID) {
        $sql = "SELECT * FROM storage WHERE StorageID = '$StorageID'";
        $result = mysqli_query($conn,$sql);
        $storage = mysqli_fetch_assoc($result);

        if (!$storage) {
            echo "No storage found.";
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
            Update Storage
        </title>
    </head>
    <body>
        <div class = "updateStorage">
            <div class = "box1">
                <form method='post' action='updateStorage.php'>
                    <!-- <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">     -->
                    <input type = 'hidden' id='StorageID' name = 'StorageID' value='<?php echo htmlspecialchars($StorageID);?>'/>
                    <label for='StorageName'>
                        Storage Name:
                    </label>
                    <br>
                    <input type = 'text' id='StorageName' name = 'StorageName' value = '<?php echo htmlspecialchars($StorageName); ?>'/><br>
                    <?php if (isset($errors['StorageName'])): ?>
                        <span class="error"> 
                            <?php echo $errors['StorageName']; ?>
                        </span>
                        <br>
                    <?php endif; ?>         
                    <br>
                    <label for='StorageCapacity'>
                        Storage Capacity:
                    </label>
                    <br>
                    <input type = 'number' id='StorageCapacity' name = 'StorageCapacity' value = '<?php echo htmlspecialchars($StorageCapacity); ?>'/>
                    <br>
                    <?php if (isset($errors['StorageCapacity'])): ?>
                        <span class="error"> 
                            <?php echo $errors['StorageCapacity']; ?>
                        </span>
                        <br>
                    <?php endif; ?>
                    <br>
                    <label for='StorageTemperature'>
                        Storage Temperature:
                    </label>
                    <br>
                    <input type = 'number' id='StorageTemperature' name = 'StorageTemperature' value = '<?php echo htmlspecialchars($StorageTemperature); ?>'/>
                    <br>
                    <?php if (isset($errors['StorageTemperature'])): ?>
                        <span class="error"> 
                            <?php echo $errors['StorageTemperature']; ?>
                        </span>
                        <br>
                    <?php endif; ?>
                    <br><br>
                    <button class='updateStorage' type='submit'> 
                        Update Storage
                    </button>
                    <br>
                    <button type="button">
                        <a href="viewStorage.php">
                            Back
                        </a>
                    </button>
                </form>
            </div> 
        </div> 
    </body> 
</html>


