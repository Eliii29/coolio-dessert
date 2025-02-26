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

    $sql = "SELECT * FROM product";
    $result = mysqli_query($conn,$sql);

    if (
        isset($_POST['ProductID']) &&
        isset($_POST['ProductName']) &&
        isset($_POST['CurrentStock']) &&
        isset($_POST['ProductExpiryDate'])
    ) {
        
        $ProductID = $_POST['ProductID'];
        $ProductName = $_POST['ProductName'];
        $CurrentStock = $_POST['CurrentStock'];

        $sql = "DELETE FROM product WHERE ProductID = $ProductID";

        if (mysqli_query($conn,$sql)) {
            $UserID = $_SESSION['UserID'];
            $TransactionType = "Deleted product";
            $Details = "ID: ". $ProductID ." - " . $ProductName . " (Quantity: " . $CurrentStock . ")";
                    
            $sqlLog = "INSERT INTO transactionlog (TransactionType, UserID, TransactionDate, Details) VALUES (?, ?, NOW(), ?)";
            if ($stmtLog = mysqli_prepare($conn, $sqlLog)) {
                mysqli_stmt_bind_param($stmtLog, "sis", $TransactionType, $UserID, $Details);                            
                mysqli_stmt_execute($stmtLog);
                mysqli_stmt_close($stmtLog);
            }
            header("Location: viewProducts.php?product=deleted");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }

    } else {
        echo "Missing parameters.";
    }

    mysqli_close($conn);

?>