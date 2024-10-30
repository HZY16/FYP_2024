<?php
    require('database.php');

    if(isset($_GET['vkey'])){
        //Process Verification
        $vkey = $_GET['vkey'];
        $userid = $_GET['userid'];

        $query = "SELECT used, vkey FROM usersactivate WHERE used = 0 AND vkey = '$vkey' AND userid = '$userid' LIMIT 1";
        $resultSet = mysqli_query($con, $query);
        $rows = mysqli_num_rows($resultSet);

        if($rows == 1){
            //Validate The Email
            $update = mysqli_query($con, "UPDATE usersactivate SET used = 1, used_date= NOW() WHERE vkey = '$vkey' AND userid = '$userid' LIMIT 1");
            $update1 = mysqli_query($con, "UPDATE users SET acc_activated = 1 WHERE  user_id = '$userid' LIMIT 1");
            if($update && $update1){
                echo "<script>
                        alert('Your account has been verified');
                        window.location.href='index.html';
                    </script>";
                exit();
            }else{
                echo "<script>
                        alert('System Error, Cannot update your status!');
                        window.location.href='index.html';
                    </script>";
                exit();
            }
        }else{
            echo "<script>
                    //alert('Your Verify Key is expired, please contact us to assist you.');
                    alert('Welcome Onboard MEDVault. If any query please feel free ask us.');
                    window.location.href='index.html';
                </script>";
            exit();
        }
    }else{
        echo "<script>
                alert('Something went wrong.');
                window.location.href='index.html';
            </script>";
        exit();
    }
?>