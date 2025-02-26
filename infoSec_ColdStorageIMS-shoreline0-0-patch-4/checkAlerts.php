<?php

include 'security.php';
session_start(); // Start the session after setting parameters


    include 'dbconn.php';

    function checkStorageAlert($StorageID, $conn) {
        $storageCheck = "
            SELECT s.StorageID, s.StorageName,
                SUM(p.CurrentStock) AS StorageUsedCapacity, s.StorageMaxCapacity
            FROM storage s
        
            LEFT JOIN product p ON s.StorageID = p.StorageID
            WHERE s.StorageID = $StorageID
            GROUP BY s.StorageID
            HAVING StorageUsedCapacity >= (s.StorageMaxCapacity * 0.8)";
        
            $storageResult = mysqli_query($conn, $storageCheck);

            if($storage = mysqli_fetch_assoc($storageResult)) {
                $AlertName = "Storage warning - '{$storage['StorageName']}'";
                $AlertName = mysqli_real_escape_string($conn, $AlertName);

                $Notes = "Storage '{$storage['StorageName']}' is almost full.";
                $Notes = mysqli_real_escape_string($conn, $Notes);

                $checkExistingAlert = "SELECT * FROM alerts WHERE AlertType = 'Storage'
                    AND RelatedID = {$storage['StorageID']}
                    AND AlertName = '$AlertName'
                    AND Notes = '$Notes'";

                $existingAlertResult = mysqli_query($conn, $checkExistingAlert);

                if (mysqli_num_rows($existingAlertResult) == 0) {
                    $insertAlert = "INSERT INTO alerts (AlertType, AlertName, Notes, RelatedID) 
                                    VALUES ('Storage', '$AlertName', '$Notes', {$storage['StorageID']})";         
                    mysqli_query($conn, $insertAlert);
                }

            }
    }

    function checkProductAlert($ProductID, $conn) {
        $ProductCheck = "
            SELECT ProductID, ProductName, ProductExpiryDate
            FROM product
            WHERE ProductID = $ProductID
            AND ProductExpiryDate <= CURDATE() + INTERVAL 3 DAY";

            $productResult = mysqli_query($conn, $ProductCheck);

            if($product = mysqli_fetch_assoc($productResult)) {
                $expiryDate = $product['ProductExpiryDate'];

                $AlertName = "Expiration warning - '{$product['ProductName']}'";
                $AlertName = mysqli_real_escape_string($conn, $AlertName);

                $Notes = (strtotime($expiryDate) < time()) 
                    ? "Product '{$product['ProductName']}' is expired."
                    : "Product '{$product['ProductName']}' is near expiry.";

                $Notes = mysqli_real_escape_string($conn, $Notes);

                $checkExistingAlert = "SELECT * FROM alerts WHERE AlertType = 'Product'
                    AND RelatedID = {$product['ProductID']}
                    AND AlertName = '$AlertName'
                    AND Notes = '$Notes'";

                $existingAlertResult = mysqli_query($conn, $checkExistingAlert);

                if (mysqli_num_rows($existingAlertResult) == 0) {
                    $insertAlert = "INSERT INTO alerts (AlertType, AlertName, Notes, RelatedID)
                                    VALUES ('Product', '$AlertName', '$Notes', {$product['ProductID']})";         
                    mysqli_query($conn, $insertAlert);
                }

            }
    }
?>
