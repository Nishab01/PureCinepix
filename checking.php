<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PureCinepix</title>
    
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
        date_default_timezone_set('Asia/Dhaka');
        $conn = new mysqli('localhost', 'root', '', 'purecinepix');

        $email = $_POST["email"];
        $password = fastEncrypt($_POST["password"], $key, $iv);

        // Check email
        $checkemail = "SELECT * FROM users WHERE `email` = '".$email."';";
        $run1 = $conn->query($checkemail);
        if($run1 -> num_rows == 1){
            // Check email & password
            $checkpassword = "SELECT * FROM users WHERE `email` = '".$email."' AND `password` = '".$password."';";
            $run2 = $conn->query($checkpassword);

            if($run2 ->num_rows == 1){
                // Credentials valid
                $loggedinuser = $run2->fetch_assoc();
                $userid = $loggedinuser["id"];
                ?>
                <script>
                    document.cookie = "userid=<?php echo $userid; ?>; max-age=604800; path=/";
                    window.location.href='./';
                </script>
                <?php
            }
            else{
                // Wrong password
                ?>
                <script>
                    alert("Wrong Password");
                    window.location.href='./signin.php';
                </script>
                <?php
            }
        }
        else{
            // Wrong email
            ?>
            <script>
                alert("Email is not registered.");
                window.location.href='./signin.php';
            </script>
            <?php
        }
    ?>
    
</body>
</html>