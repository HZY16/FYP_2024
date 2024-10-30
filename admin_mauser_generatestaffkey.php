<?php
    require('database.php');
    require('securityauth.php');
    restrictAccessByRole(['admin']);

    //$db = new PDO('mysql:host=localhost;dbname=fyp', 'root', '');
    $encryption_key = bin2hex(random_bytes(32));
    $staffkey= bin2hex(random_bytes(8));


    //$insert1 = $db->prepare("INSERT INTO `usersstaffkey` (staffkey, used, used_date, generate_date) VALUES (?, ?, NULL, NOW())");
    $insert1 = $db->prepare("INSERT INTO `usersstaffkey` (staffkey, used, generate_date) VALUES (?, ?, NOW())");

    $insert1->execute([$staffkey, '0']);
    if ($insert1) {
        echo "<script>
                alert('Generate Staff Token Successfully. The token number is $staffkey' );
                window.location.href='admin_manageuser.php';
            </script>";
            exit();
    } else {
        "<script>
                alert('There was an error registering the user.');
                window.location.href='admin_manageuser.php';
        </script>";
    }
    
?>
