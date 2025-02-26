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

    $errors = [];
    $FirstName = $LastName = $Email = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $FirstName = htmlspecialchars($_POST['FirstName'], ENT_QUOTES, 'UTF-8');
        $LastName = htmlspecialchars($_POST['LastName'], ENT_QUOTES, 'UTF-8');
        $Email = filter_var($_POST['Email'], FILTER_SANITIZE_EMAIL);
        $Password = htmlspecialchars(trim($_POST['Password']), ENT_QUOTES, 'UTF-8');
    
        if (empty($FirstName)) {
            $errors['FirstName'] = "First name required.";
        } elseif (strlen($FirstName) > 50) {
            $errors['FirstName'] = "First name too long.";
        }
    
        if (empty($LastName)) {
            $errors['LastName'] = "Last name required.";
        } elseif (strlen($LastName) > 50) {
            $errors['LastName'] = "Last name too long.";
        }
    
        if (empty($Email)) {
            $errors['Email'] = "Email required.";
        } elseif (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
            $errors['Email'] = "Invalid email format.";
        }
     
        $sql = "SELECT * FROM users WHERE Email = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $Email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($result) > 0) {
                $errors['Email'] = "Account already exists with this email!";
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Database error: " . mysqli_error($conn);
        }
    
        if (empty($Password)) {
            $errors['Password'] = "Password required.";
        } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $Password)) {
            $errors['Password'] = "Password must be at least 8 characters long, contain an uppercase letter, a lowercase letter, a number, and a special character.";
        } 
    
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['FirstName'] = $FirstName;
            $_SESSION['LastName'] = $LastName;
            $_SESSION['Email'] = $Email;
            
            header("Location: formCreateUser.php");
            exit();
    
        } else {
            $hashedPassword = hash('sha256', $Password);
            $sql = "INSERT INTO users (FirstName, LastName, Email, Password) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
    
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssss", $FirstName, $LastName, $Email, $hashedPassword);
                if (mysqli_stmt_execute($stmt)) {
                    $UserID = mysqli_insert_id($conn);
                    $SAdminUserID = $_SESSION['UserID'];
                    $TransactionType = "Added user";
                    $Details = "ID: ". $UserID ." - " . $FirstName . " "  . $LastName . "";
                    
                    $sqlLog = "INSERT INTO transactionlog (TransactionType, UserID, TransactionDate, Details) VALUES (?, ?, NOW(), ?)";
                    if ($stmtLog = mysqli_prepare($conn, $sqlLog)) {
                        mysqli_stmt_bind_param($stmtLog, "sis", $TransactionType, $SAdminUserID, $Details);                            
                        mysqli_stmt_execute($stmtLog);
                        mysqli_stmt_close($stmtLog);
                    }
                    header("Location: viewUsers.php?user=created");
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