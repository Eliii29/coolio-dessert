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

    $storageQuery = "SELECT StorageID, StorageName FROM storage";
    $storageResult = mysqli_query($conn, $storageQuery);

    $errors = $_SESSION['errors'] ?? [];
    unset ($_SESSION['errors']);

    $ProductID = $_POST['ProductID'] ?? '';
    $ProductName = $_POST['ProductName'] ?? ($_SESSION['ProductName'] ?? '');
    $CurrentStock = $_POST['CurrentStock'] ?? ($_SESSION['CurrentStock'] ?? '');
    $ProductExpiryDate = $_POST['ProductExpiryDate'] ?? ($_SESSION['ProductExpiryDate'] ?? '');

    if ($ProductID) {
        $sql = "SELECT * FROM product WHERE ProductID = '$ProductID'";
        $result = mysqli_query($conn,$sql);
        $product = mysqli_fetch_assoc($result);

        if (!$product) {
            echo "No product found.";
        } else {
            $StorageID = $product['StorageID'];
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
            Update Product
        </title>
    </head>
    <body>
        <div class = "updateProduct">
            <div class = "box1">
                <form method='post' action='updateProduct.php'>
                    <!-- <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">     -->
                    <input type = 'hidden' id='ProductID' name = 'ProductID' value='<?php echo htmlspecialchars($ProductID);?>'/>
                    <label for='ProductName'>
                        Product Name:
                    </label>
                    <br>
                    <input type = 'text' id='ProductName' name = 'ProductName' value = '<?php echo htmlspecialchars($ProductName); ?>'/><br>
                    <?php if (isset($errors['ProductName'])): ?>
                        <span class="error"> 
                            <?php echo $errors['ProductName']; ?>
                        </span>
                        <br>
                    <?php endif; ?>         
                    <br>
                    <label for='CurrentStock'>
                        Current Stock:
                    </label>
                    <br>
                    <input type = 'number' id='CurrentStock' name = 'CurrentStock' value = '<?php echo htmlspecialchars($CurrentStock); ?>'/>
                    <br>
                    <?php if (isset($errors['CurrentStock'])): ?>
                        <span class="error"> 
                            <?php echo $errors['CurrentStock']; ?>
                        </span>
                        <br>
                    <?php endif; ?>
                    <br>
                    <label for='ProductExpiryDate'>
                    ProductExpiryDate:
                    </label>
                    <br>
                    <input type = 'date' id='ProductExpiryDate' name = 'ProductExpiryDate' value = '<?php echo htmlspecialchars($ProductExpiryDate); ?>' min='<?php echo date('Y-m-d'); ?>'/>
                    <br>
                    <?php if (isset($errors['ProductExpiryDate'])): ?>
                        <span class="error"> 
                            <?php echo $errors['ProductExpiryDate']; ?>
                        </span>
                        <br>
                    <?php endif; ?>
                    <br>
                    <label for="StorageID">
                        Storage:
                    </label>
                    <select id="StorageID" name="StorageID" required>
                        <option value="" disabled>
                            Select Storage
                        </option>
                        <?php
                        while ($storage = mysqli_fetch_assoc($storageResult)) {
                            $selected = (isset($StorageID) && $storage['StorageID'] == $StorageID) ? "selected" : "";
                            echo "<option value='" . $storage['StorageID'] . "' $selected>" . $storage['StorageName'] . "</option>";
                        }
                        ?>
                    </select>
                    <br><br>
                    <button class='updateProduct' type='submit'> 
                        Update Product
                    </button>
                    <br>
                    <button type="button">
                        <a href="viewProducts.php">
                            Back
                        </a>
                    </button>
                </form>
            </div> 
        </div> 
    </body> 
</html>


