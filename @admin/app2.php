<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./@css/style2.css">
    <link rel="stylesheet" href="./@css/flowbite.min.css">
    <script src="../js/tailwindcss.js"></script>
    <script src="../js/flowbite.min.js"></script>
</head>
<body>
    <?php
        date_default_timezone_set('Asia/Dhaka');
        
        if(isset($_COOKIE["userid"]) && $_COOKIE["userid"] != NULL){
            $userid = $_COOKIE["userid"];
            $conn = new mysqli('localhost', 'root', '', 'purecinepix');
            $loggedin = "SELECT * FROM users WHERE `id` = ".$userid.";";
            $runloggedin = $conn->query($loggedin);
            $user = $runloggedin->fetch_assoc();

            if($user["role"] == "superadmin" || $user["role"] == "admin"){
                
            }
            else{
                ?>
                <script>
                    alert("Access Denied");
                    document.cookie = "userid=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;";
                    window.location.href='../signout.php';
                </script>
                <?php
            }
        }
        else{
            ?>
                <script>
                    alert("Please login first.");
                    document.cookie = "userid=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;";
                    window.location.href='../signout.php';
                </script>
                <?php
        }

        // A Fixed 16-character IV (Must be exactly 16 bytes for AES-128)
        $iv = "1234567890123456"; 
        $key = "PureCinepix_Key_2026"; 
        function fastEncrypt($data, $key, $iv) {
            return openssl_encrypt($data, "AES-128-CBC", $key, 0, $iv);
        }
        function fastDecrypt($data, $key, $iv) {
            return openssl_decrypt($data, "AES-128-CBC", $key, 0, $iv);
        }
    ?>

    <div class="w-full bg-black/50">
        <nav class="bg-neutral-primary">
            <div class="flex flex-wrap justify-between items-center mx-auto px-4 max-w-screen-xl py-4">
                <a href="./" class="flex items-center space-x-3 rtl:space-x-reverse">
                    <img src="../favicon/web-app-manifest-192x192.png" class="lg:h-12 md:h-12 h-8" alt="PureCinepix" />
                    <div class="space-x-2">
                        <span class="self-center lg:text-2xl md:text-2xl text-xl text-heading font-bold whitespace-nowrap">PureCinepix</span>
                        <span class="self-center text-sm text-heading font-bold whitespace-nowrap text-blue-200">Admin Panel</span>
                    </div>
                </a>

                <div class="flex flex-row items-center space-x-2 rtl:space-x-reverse">
                    <div class="px-4 flex flex-col gap-0 text-right border-r-2 border-white/60">
                        <p class="text-md text-gray-400 font-bold border-b-4 border-transparent"><?php echo $user["shortname"]; ?></p>

                        <p class="text-xs text-gray-400 font-bold border-b-4 border-transparent"><?php
                            if($user["role"] == "superadmin"){echo "Super Admin";}
                            else if($user["role"] == "admin"){echo "Admin";}
                            else{
                                header("Location: ../signout.php");
                                exit;
                            }
                        ?></p>
                    </div>

                    <a href="../signout.php" class="p-2 transition-all duration-100 ease-in-out rounded-lg text-base font-semibold border-transparent hover:text-white hover:bg-red-500">Sign Out</a>
                </div>
            </div>
        </nav>

        <nav class="bg-neutral-secondary-soft border-y-2 border-white/60">
            <div class="px-4 max-w-screen-xl px-4 py-3 mx-auto">
                <div class="flex items-center">
                    <ul class="flex flex-row mt-0 space-x-4 rtl:space-x-reverse font-semibold">
                        <li>
                            <a href="../" target="_blank" class="transition-all duration-100 ease-in-out px-2 py-2 hover:border-b-4 border-blue-500 border-blue-500" aria-current="page">Home</a>
                        </li>
                        <li>
                            <a href="./" class="transition-all duration-100 ease-in-out px-2 py-2 hover:border-b-4 border-blue-500" id="dashboard" aria-current="page">Dashboard</a>
                        </li>
                        <li>
                            <a href="./contents.php" class="transition-all duration-100 ease-in-out px-2 py-2 hover:border-b-4 border-blue-500" id="contents" aria-current="page">Contents</a>
                        </li>
                        <li>
                            <a href="./manageusers.php" class="transition-all duration-100 ease-in-out px-2 py-2 hover:border-b-4 border-blue-500" id="manageusers" aria-current="page">Manage Users</a>
                        </li>
                        <li>
                            <a href="./requests.php" class="transition-all duration-100 ease-in-out px-2 py-2 hover:border-b-4 border-blue-500" id="requests" aria-current="page">Requests</a>
                        </li>
                        <li>
                            <a href="./settings.php" class="transition-all duration-100 ease-in-out px-2 py-2 hover:border-b-4 border-blue-500" id="settings" aria-current="page">Settings</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>

    <br>
</body>
</html>