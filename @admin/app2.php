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
        $conn = new mysqli('localhost', 'root', '', 'purecinepix');
        
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
                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <!-- <img src="../favicon/web-app-manifest-192x192.png" class="lg:h-12 md:h-12 h-8" alt="PureCinepix" /> -->
                    <div class="space-x-2">
                        <a href="./" class="flex items-center gap-2 group">
                            <img src="../favicon/web-app-manifest-192x192.png" class="h-12 transition-transform duration-300 group-hover:scale-105" alt="PureCinepix">

                            <div class="flex flex-col leading-none">
                                <span class="text-2xl font-black tracking-wide text-white">PURE<span class="text-blue-500">CINEPIX</span></span>

                                <span class="text-mg tracking-[0.3em] text-blue-400">Admin Panel</span>
                            </div>
                        </a>
                    </div>
                </div>

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

                    <a href="../signout.php" class="p-2 transition-all duration-100 ease-in-out rounded-xl text-base font-semibold border-transparent hover:text-white hover:bg-red-700">Sign Out</a>
                </div>
            </div>
        </nav>

        <nav class="bg-neutral-secondary-soft border-y-2 border-white/60">
            <div class="px-4 max-w-screen-xl px-4 py-3 mx-auto">
                <div class="flex items-center">
                    <ul class="flex flex-row mt-0 space-x-2 rtl:space-x-reverse text-sm font-semibold">
                        <li>
                            <a href="../" target="_blank" class="px-2 py-1 transition-all duration-100 ease-in-out flex flex-row items-center gap-2 border-b-4 border-transparent hover:border-blue-700" aria-current="page">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house-icon lucide-house"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                                <span>Home</span>
                            </a>
                        </li>
                        <li>
                            <a href="./" class="px-2 py-1 transition-all duration-100 ease-in-out flex flex-row items-center gap-2 border-b-4 border-transparent hover:border-blue-700" id="dashboard" aria-current="page">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-dashboard-icon lucide-layout-dashboard"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>    
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="./contents.php" class="px-2 py-1 transition-all duration-100 ease-in-out flex flex-row items-center gap-2 border-b-4 border-transparent hover:border-blue-700" id="contents" aria-current="page">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-fullscreen-icon lucide-fullscreen"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><rect width="10" height="8" x="7" y="8" rx="1"/></svg>   
                                <span>Contents</span>
                            </a>
                        </li>
                        <li>
                            <a href="./manageusers.php" class="px-2 py-1 transition-all duration-100 ease-in-out flex flex-row items-center gap-2 border-b-4 border-transparent hover:border-blue-700" id="manageusers" aria-current="page">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-round-pen-icon lucide-user-round-pen"><path d="M2 21a8 8 0 0 1 10.821-7.487"/><path d="M21.378 16.626a1 1 0 0 0-3.004-3.004l-4.01 4.012a2 2 0 0 0-.506.854l-.837 2.87a.5.5 0 0 0 .62.62l2.87-.837a2 2 0 0 0 .854-.506z"/><circle cx="10" cy="8" r="5"/></svg>
                                <span>Manage Users</span>
                            </a>
                        </li>
                        <li>
                            <a href="./requests.php" class="px-2 py-1 transition-all duration-100 ease-in-out flex flex-row items-center gap-2 border-b-4 border-transparent hover:border-blue-700" id="requests" aria-current="page">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square-warning-icon lucide-message-square-warning"><path d="M22 17a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 21.286V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2z"/><path d="M12 15h.01"/><path d="M12 7v4"/></svg>
                                <span>Requests</span>
                            </a>
                        </li>
                        <li>
                            <a href="./settings.php" class="px-2 py-1 transition-all duration-100 ease-in-out flex flex-row items-center gap-2 border-b-4 border-transparent hover:border-blue-700" id="settings" aria-current="page">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings-icon lucide-settings"><path d="M9.671 4.136a2.34 2.34 0 0 1 4.659 0 2.34 2.34 0 0 0 3.319 1.915 2.34 2.34 0 0 1 2.33 4.033 2.34 2.34 0 0 0 0 3.831 2.34 2.34 0 0 1-2.33 4.033 2.34 2.34 0 0 0-3.319 1.915 2.34 2.34 0 0 1-4.659 0 2.34 2.34 0 0 0-3.32-1.915 2.34 2.34 0 0 1-2.33-4.033 2.34 2.34 0 0 0 0-3.831A2.34 2.34 0 0 1 6.35 6.051a2.34 2.34 0 0 0 3.319-1.915"/><circle cx="12" cy="12" r="3"/></svg>
                                <span>Settings</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>

</body>
</html>