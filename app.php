<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/flowbite.min.css">
    <script src="./js/tailwindcss.js"></script>
    <script src="./js/flowbite.min.js"></script>
</head>
<body>
    <div class="circle"></div>
    <div class="circle"></div>
    <div class="circle"></div>

    <?php
        date_default_timezone_set('Asia/Dhaka');
        
        if(isset($_COOKIE["userid"])){
            if($_COOKIE["userid"] != NULL){
                $userid = $_COOKIE["userid"];
                $conn = new mysqli('localhost', 'root', '', 'purecinepix');
                $loggedin = "SELECT * FROM users WHERE `id` = ".$userid.";";
                $runloggedin = $conn->query($loggedin);
                if($runloggedin->num_rows == 1){
                    $user = $runloggedin->fetch_assoc();
                }
                else{
                    ?>
                    <script>
                        document.cookie = "userid=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;";
                        window.location.href='./';
                    </script>
                    <?php
                    $userid = NULL;
                }
            }
            else{
                ?>
                <script>
                    document.cookie = "userid=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;";
                    window.location.href='./';
                </script>
                <?php
            }
        }
        else{
            $userid = NULL;
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

    <nav class="fixed w-full z-100 top-0 start-0 bg-gray-800 shadow-lg">
        <div class="px-4 max-w-screen-xl flex flex-wrap items-center justify-between mx-auto py-4">
            <a href="./" class="flex items-center space-x-2 rtl:space-x-reverse">
                <img src="./favicon/web-app-manifest-192x192.png" class="h-12" alt="PureCinepix" />
                <span class="self-center text-2xl text-heading font-bold whitespace-nowrap bg-gradient-to-r from-blue-500 via-white-500 to-blue-500 bg-clip-text text-transparent neon-gradient">PURECINEPIX</span>
                <!-- <span class="self-center text-2xl text-heading font-bold whitespace-nowrap">PURECINEPIX</span> -->
            </a>
            
            <button data-collapse-toggle="navbar-dropdown" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center rounded-lg md:hidden hover:bg-neutral-secondary-soft hover:text-heading focus:outline-none focus:ring-2 focus:ring-neutral-tertiary" aria-controls="navbar-dropdown" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="white" stroke-linecap="round" stroke-width="" d="M5 7h14M5 12h14M5 17h14"/></svg>
            </button>
            
            <div class="hidden w-full md:block md:w-auto" id="navbar-dropdown">
                <ul class="flex flex-col p-1 font-semibold md:space-x-1 rtl:space-x-reverse md:flex-row gap-1">
                    <li>
                        <a href="./" class="block py-2 px-3 md:p-2 rounded-lg transition-all duration-100 ease-in-out text-left hover:bg-blue-700" aria-current="page">Home</a>
                    </li>
                    <li>
                        <button id="dropdownNvbarButton2" data-dropdown-toggle="dropdownNavbar2" class="flex items-center justify-between w-full py-2 px-3 md:w-auto md:p-2 transition-all duration-100 ease-in-out rounded-lg hover:bg-blue-700">Genre<svg class="w-auto h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="white" stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="m19 9-7 7-7-7"/></svg></button>
                        <div id="dropdownNavbar2" class="z-10 hidden w-auto">
                            <ul class="p-1 bg-gray-900 border-2 border-blue-500 shadow-xl rounded-lg grid grid-cols-3 md:grid-cols-4 gap-1" aria-labelledby="dropdownNvbarButton2">
                                <li>
                                    <a href="#" class="inline-flex items-center w-full p-2 transition-all duration-100 ease-in-out rounded-lg hover:bg-blue-700">1fafsd fsdddddd</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <button id="dropdownNvbarButton3" data-dropdown-toggle="dropdownNavbar3" class="flex items-center justify-between w-full py-2 px-3 md:w-auto md:p-2 transition-all duration-100 ease-in-out rounded-lg hover:bg-blue-700">Category<svg class="w-auto h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="white" stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="m19 9-7 7-7-7"/></svg></button>
                        <div id="dropdownNavbar3" class="z-10 hidden w-auto">
                            <ul class="p-1 bg-gray-900 border-2 border-blue-500 shadow-xl rounded-lg grid grid-cols-2 md:grid-cols-2 gap-1" aria-labelledby="dropdownNvbarButton3">
                                <li>
                                    <a href="#" class="inline-flex items-center w-full p-2 transition-all duration-100 ease-in-out rounded-lg hover:bg-blue-700">1fafsdfsdaf</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <button id="dropdownNvbarButton4" data-dropdown-toggle="dropdownNavbar4" class="flex items-center justify-between w-full py-2 px-3 md:w-auto md:p-2 transition-all duration-100 ease-in-out rounded-lg hover:bg-blue-700">Quality<svg class="w-auto h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="white" stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="m19 9-7 7-7-7"/></svg></button>
                        <div id="dropdownNavbar4" class="z-10 hidden w-auto">
                            <ul class="p-1 bg-gray-900 border-2 border-blue-500 shadow-xl rounded-lg grid grid-cols-2 md:grid-cols-2 gap-1" aria-labelledby="dropdownNvbarButton4">
                                <li>
                                    <a href="#" class="inline-flex items-center w-full p-2 transition-all duration-100 ease-in-out rounded-lg hover:bg-blue-700">60fps</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="#" class="block py-2 px-3 md:p-2 transition-all duration-100 ease-in-out border-2 border-transparent rounded-lg text-left hover:bg-blue-700">Contact Us</a>
                    </li>
                    <li>
                        <button id="dropdownNvbarButton" data-dropdown-toggle="dropdownNavbar" class="flex items-center justify-between w-full py-2 px-3 md:w-auto md:p-2 transition-all duration-100 ease-in-out rounded-lg border-2 border-blue-500 hover:border-blue-700 hover:bg-blue-700">
                            <?php
                                if($userid == NULL){
                                    echo "User";
                                }
                                else{
                                    echo $user["shortname"];;
                                }
                            ?>
                            <svg class="w-auto h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="white" stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="m19 9-7 7-7-7"/></svg>
                        </button>
                        <div id="dropdownNavbar" class="z-10 hidden w-auto">
                            <ul class="p-1 bg-gray-900 border-2 border-blue-500 shadow-xl rounded-lg" aria-labelledby="dropdownNvbarButton">
                                <?php
                                    if($userid == NULL){
                                        ?>
                                        <li>
                                            <a href="./register.php" class="inline-flex items-center w-full p-2 transition-all duration-100 ease-in-out rounded-lg hover:bg-blue-700">Register</a>
                                        </li>
                                        <li>
                                            <a href="./signout.php" class="inline-flex items-center w-full p-2 transition-all duration-100 ease-in-out rounded-lg hover:bg-blue-700">Sign In</a>
                                        </li>
                                        <?php
                                    }
                                    else{
                                        if($user["role"] == "superadmin" || $user["role"] == "admin"){
                                            ?>
                                            <li>
                                                <a href="./@admin" class="inline-flex items-center w-full p-2 transition-all duration-100 ease-in-out rounded-lg hover:bg-white hover:text-black">Admin Panel</a>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                        <li>
                                            <a href="./profie.php" class="inline-flex items-center w-full p-2 transition-all duration-100 ease-in-out rounded-lg hover:bg-blue-700">My Profile</a>
                                        </li>
                                        <li>
                                            <a href="#" class="inline-flex items-center w-full p-2 transition-all duration-100 ease-in-out rounded-lg hover:bg-blue-700">Watchlist</a>
                                        </li>
                                        <li>
                                            <a href="#" class="inline-flex items-center w-full p-2 transition-all duration-100 ease-in-out rounded-lg hover:bg-blue-700">Request</a>
                                        </li>
                                        <li>
                                            <a href="./signout.php" class="inline-flex items-center w-full p-2 transition-all duration-100 ease-in-out rounded-lg hover:bg-red-700">Sign Out</a>
                                        </li>
                                        <?php
                                    }
                                ?>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

</body>
</html>