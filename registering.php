<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PureCinepix | Processing...</title>
    
    <link rel="icon" type="image/png" sizes="96x96" href="./favicon/favicon-96x96.png">
    <link rel="icon" type="image/svg+xml" href="./favicon/favicon.svg">
    <link rel="shortcut icon" href="./favicon/favicon.ico">
    <link rel="apple-touch-icon" href="./favicon/apple-touch-icon.png">
    <meta name="apple-mobile-web-app-title" content="PureCinepix">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="./favicon/site.webmanifest">
    <meta name="theme-color" content="#00AAFF">
</head>
<body>
    <?php 
        include './app.php';

        // $conn = new mysqli('localhost', 'root', '', 'purecinepix');

        $otp = $_POST["opt"];
        $emailvalue = $_POST["emailvalue"];
        $passvalue = $_POST["passvalue"];
        
        $fetchuser = "SELECT * FROM users WHERE `email` = '".$emailvalue."' AND `password` = '".$passvalue."';";
        $run3 = $conn->query($fetchuser);

        if($run3->num_rows == 1){
            // User found
            $getuser = $run3->fetch_assoc();
            $userid = $getuser["id"];

            if($otp == $getuser['verification'] || $getuser['verification'] == "Verified"){
                $currtime = date("Y-m-d H:i:s");
                $createdtime = $getuser["created_on"];

                $interval = strtotime($currtime) - strtotime($createdtime);

                if($interval <= 300){
                    $verified = "UPDATE users
                            SET `verification` = 'Verified'
                            WHERE `id` = '".$userid."'
                        ";
                    $run4 = $conn->query($verified);

                    ?>
                    <script>
                        document.cookie = "userid=<?php echo $userid; ?>; max-age=604800; path=/";
                        alert("Verification Successful");
                        window.location.href='./';
                    </script>
                    <?php
                }
                else{
                    $expired = "DELETE FROM users WHERE `id` = '".$userid."';";
                    $run5 = $conn->query($expired);
                    $conn->close();
                    ?>
                    <script>
                        alert("Verification code expired. Register again.");
                        window.location.href='./register.php';
                    </script>
                    <?php
                    $conn->close();
                    exit();
                }
            }
            else{
                ?>
                    <script>
                        alert("Wrong verification code. Please try again.");
                    </script>
                <?php
            }
        }
        else{
            ?>
                <script>
                    alert("No user found. Please register.");
                    window.location.href='./register.php';
                </script>
            <?php
            $conn->close();
            exit();
        } 
    ?>
</body>
</html>