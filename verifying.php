<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PureCinepix | Registration</title>
    
    <link rel="icon" type="image/png" sizes="96x96" href="./favicon/favicon-96x96.png">
    <link rel="icon" type="image/svg+xml" href="./favicon/favicon.svg">
    <link rel="shortcut icon" href="./favicon/favicon.ico">
    <link rel="apple-touch-icon" href="./favicon/apple-touch-icon.png">
    <meta name="apple-mobile-web-app-title" content="PureCinepix">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="./favicon/site.webmanifest">
    <meta name="theme-color" content="#00AAFF">

    <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
    <script>emailjs.init("86UQDxNWkZk0weXtM");</script>
</head>
<body>
    <?php
        include './app.php';

        // $conn = new mysqli('localhost', 'root', '', 'purecinepix');
        
        // Generate OTP 
        function generateopt($length = 6) {
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $otp = '';
            for ($i = 0; $i < $length; $i++) {
                $otp .= $characters[random_int(0, strlen($characters) - 1)];
            }
            return $otp;
        }

        $shortname = $_POST["shortname"];
        $email = $_POST["email"];
        // $password = $_POST["password"];
        $password = fastEncrypt($_POST["password"], $key, $iv);
        $otp = generateopt();
        $created_on = date("Y-m-d H:i:s");

        $_SESSION['otp'] = $otp;

        $emailcheck = "SELECT * FROM users WHERE `email` = '".$email."';";
        $run1 = $conn->query($emailcheck);

        if($run1->num_rows != 0){
            // User already exists
            ?>
                <script>
                    alert("This email is already registered.");
                    window.location.href='./register.php';
                </script>
            <?php
            $conn->close();
            exit();
        }
        else{
            $newuser = "INSERT INTO users
                    (`shortname`, `email`, `password`, `verification`, `role`, `created_on`)
                    VALUES
                    ('".$shortname."', '".$email."', '".$password."', '".$otp."', 'user', '".$created_on."')
            ";
            $run2 = $conn->query($newuser);
        } 
    ?>

    <script>
        let name = "<?php echo $shortname; ?>";
        let email = "<?php echo $email; ?>";
        let otp = "<?php echo $otp; ?>";

        emailjs.send(
            "purecinepix_11",
            "template_kmbco6a",
            {
                user_name: name,
                user_email: email,
                otp_code: otp
            }
        )
        .then(function(response){
            console.log("OTP Sent Successfully.");
        })
        .catch(function(error){
            console.log(error);
            console.log("Failed to send OTP");
        });
    </script>
    
    <div class="px-4 max-w-screen-xl flex flex-col items-center justify-center md:flex-row rounded-xl gap-2">
        <div class="hidden md:block w-1/2">
            <!-- banner -->
            <img src="./img/otp.webp" alt="" class="object-contain">
        </div>

        <div class="lg:w-1/2 md:w-1/2 bg-gray-800/50 rounded-xl shadow-lg">
            <form action="./registering.php" method="POST" class="my-4 text-center flex flex-col items-center justify-center">
                <p class="text-2xl font-bold mb-2">Verification</p>

                <p class="text-sm text-gray-400"><?php echo $email; ?></p>
                
                <div class="my-4 text-left font-semibold w-5/6 lg:w-4/6 md:w-4/6 space-y-4">
                    <div class="space-y-1">
                        <label class="w-full" for="opt">Code:</label>
                        <input class="w-full rounded-xl bg-gray-800 border-2 border-blue-500 font-normal" type="text" name="opt" id="opt" placeholder="Enter verification code" required>

                        <span class="font-normal text-xs text-gray-400">A verification code was sent to your email.</span>

                        <input class="w-full rounded-xl bg-gray-800 border-2 border-blue-500 font-normal" type="text" name="emailvalue" id="emailvalue" value="<?php echo $email; ?>" required readonly hidden>

                        <input class="w-full rounded-xl bg-gray-800 border-2 border-blue-500 font-normal" type="text" name="passvalue" id="passvalue" value="<?php echo $password; ?>" required readonly hidden>
                    </div>

                    <div class="space-y-1 text-center">
                        <input class="p-2 transition-all duration-100 w-full cursor-pointer bg-blue-500 hover:bg-blue-700 hover:shadow-lg rounded-xl font-semibold text-base" type="submit" name="verify" id="verify" value="Verify">
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>