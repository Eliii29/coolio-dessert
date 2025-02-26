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

    $errors = $_SESSION['errors'] ?? "";
    $FirstName = $_SESSION['FirstName'] ?? "";
    $LastName = $_SESSION['LastName'] ?? "";
    $Email = $_SESSION['Email'] ?? "";

    unset (
        $_SESSION['errors'],
        $_SESSION['FirstName'],
        $_SESSION['LastName'],
        $_SESSION['Email']
    );
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
                <form method='post' action='addUser.php'>
                    <!-- <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">     -->
                    <input type = 'hidden' id='UserID' name = 'UserID' />
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
                    <label for='Password'>
                        Password:
                    </label>
                    <br>
                    <input type = 'password' id='Password' name = 'Password' />
                    <br>
                    <?php if (isset($errors['Password'])): ?>
                        <span class="error"> 
                            <?php echo $errors['Password']; ?>
                        </span>
                        <br>
                    <?php endif; ?>
                    <br>
                    <label for="Role">
                        Select role:
                    </label>
                    <select id="Role" name="Role" required>
                        <option value="" disabled selected>
                            Select here
                        </option>
                        <option value="admin">Admin</option>
                        <option value="superAdmin">Super Admin</option>
                    </select>
                    <br><br>
                    <button class='addUser' type='submit'> 
                        Add User
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


