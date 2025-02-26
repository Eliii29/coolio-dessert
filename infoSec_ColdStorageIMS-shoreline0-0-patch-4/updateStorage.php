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
    $StorageName = $StorageCapacity = $StorageTemperature = "";

    $sql = "SELECT * FROM storage";
    $result = mysqli_query($conn,$sql);

    if (
        isset($_POST['StorageID']) &&
        isset($_POST['StorageName']) &&
        isset($_POST['StorageCapacity']) &&
        isset($_POST['StorageTemperature'])
    ) {
        $StorageID = $_POST['StorageID'];
        $StorageName = htmlspecialchars($_POST['StorageName'], ENT_QUOTES, 'UTF-8');
        $StorageCapacity = filter_input(INPUT_POST, 'StorageCapacity', FILTER_VALIDATE_INT);
        $StorageTemperature = filter_input(INPUT_POST, 'StorageTemperature', FILTER_VALIDATE_FLOAT);

        if (empty($StorageName)) {
            $errors['StorageName'] = "Storage name required.";
        } elseif (strlen($StorageName) > 50) {
            $errors['StorageName'] = "Storage name too long.";
        }
    
        if (empty($StorageCapacity)) {
            $errors['StorageCapacity'] = "Capacity required.";
        } elseif ($StorageCapacity < 0) {
            $errors['StorageCapacity'] = "Invalid capacity.";
        }

        if ($StorageTemperature > 100) {
            $errors['StorageTemperature'] = "Invalid temperature.";
        }
    
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['StorageName'] = $StorageName;
            $_SESSION['StorageCapacity'] = $StorageCapacity;
            $_SESSION['StorageTemperature'] = $StorageTemperature;

            header("Location: formUpdateStorage.php");
            exit();
        } else {
            $sql = 
            "UPDATE storage 
            SET 
                StorageName = ?, 
                StorageCapacity = ?,
                StorageTemperature = ?
            WHERE StorageID = ?";
            
            $stmt = mysqli_prepare($conn, $sql);
    
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "siid", $StorageName, $StorageCapacity, $StorageTemperature, $StorageID);
                if (mysqli_stmt_execute($stmt)) {
                    $UserID = $_SESSION['UserID'];
                    $TransactionType = "Updated storage";
                    $Details = "ID: ". $StorageID ." - " . $StorageName . " (Capacity: " . $StorageCapacity . " , Temp " . $StorageTemperature . ")";
                    
                    $sqlLog = "INSERT INTO transactionlog (TransactionType, UserID, TransactionDate, Details) VALUES (?, ?, NOW(), ?)";
                    if ($stmtLog = mysqli_prepare($conn, $sqlLog)) {
                        mysqli_stmt_bind_param($stmtLog, "sis", $TransactionType, $UserID, $Details);                            
                        mysqli_stmt_execute($stmtLog);
                        mysqli_stmt_close($stmtLog);
                    }
                    header("Location: viewStorage.php?storage=updated");
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