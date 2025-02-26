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