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
        $conn = new mysqli('localhost', 'root', '', 'purecinepix');

        $maintenance = "SELECT * FROM settings WHERE `key` = 'maintenance-mode';";
        $runq = $conn->query($maintenance);
        $mode = $runq->fetch_assoc();
        if($mode['value'] == 1){
            ?>
            <script>
                alert("Maintenance in progress.");
                window.location.href='./maintenence.php';
            </script>
            <?php
        }
        
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

    <nav class="fixed top-0 left-0 z-50 w-full border-b border-white/10 bg-black/30 backdrop-blur-xl">
        <div class="max-w-screen-2xl mx-auto px-4 lg:px-8">
            <!-- Top Nav -->
            <div class="flex items-center justify-between h-20">
                <!-- Left Side -->
                <div class="flex items-center gap-12">
                    <!-- LOGO -->
                    <a href="./" class="flex items-center gap-2 group">
                        <img src="./favicon/web-app-manifest-192x192.png" class="h-12 transition-transform duration-300 group-hover:scale-105" alt="PureCinepix">

                        <div class="flex flex-col leading-none">
                            <span class="text-2xl font-black tracking-wide text-white">PURE<span class="text-blue-500">CINEPIX</span></span>

                            <span class="text-xs tracking-[0.3em] text-blue-400 uppercase">Entertainment</span>
                        </div>
                    </a>

                    <!-- Desktop Menu -->
                    <div class="hidden lg:flex items-center gap-2 text-sm font-semibold">
                        <li>
                            <a href="./" class="px-4 py-2 rounded-xl text-white hover:bg-white/10 hover:text-blue-400 transition-all duration-300">Home</a>
                        </li>

                        <li class="relative group">
                            <button class="flex items-center gap-1 px-4 py-2 rounded-xl text-white hover:bg-white/10 hover:text-blue-400 transition-all duration-300">Genre<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform duration-300 group-hover:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg></button>

                            <div class="absolute top-full left-0 mt-0 hidden group-hover:block w-[600px] rounded-xl border border-white/10 bg-black backdrop-blur-xl shadow-xl p-4">
                                <div class="grid grid-cols-3 gap-2">
                                    <a href="#" class="px-4 py-3 rounded-xl hover:bg-blue-500/20 hover:text-blue-400 transition-all">Action asfsdfa, efaweg</a>

                                    <a href="#" class="px-4 py-3 rounded-xl hover:bg-blue-500/20 hover:text-blue-400 transition-all">Adventure</a>

                                    <a href="#" class="px-4 py-3 rounded-xl hover:bg-blue-500/20 hover:text-blue-400 transition-all">Action</a>

                                    <a href="#" class="px-4 py-3 rounded-xl hover:bg-blue-500/20 hover:text-blue-400 transition-all">Adventure</a>
                                </div>

                                <!-- <div class="mt-4 pt-4 border-t border-white/10">
                                    <a href="#" class="text-sm text-blue-400 hover:text-blue-300">View All</a>
                                </div> -->
                            </div>
                        </li>

                        <li class="relative group">
                            <button class="flex items-center gap-2 px-4 py-2 rounded-xl text-white hover:bg-white/10 hover:text-blue-400 transition-all duration-300">Category<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform duration-300 group-hover:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg></button>

                            <div class="absolute top-full left-0 mt-0 hidden group-hover:block w-[500px] rounded-xl border border-white/10 bg-black backdrop-blur-xl shadow-xl p-4">
                                <div class="grid grid-cols-2 gap-2">
                                    <a href="#" class="px-4 py-3 rounded-xl hover:bg-blue-500/20 hover:text-blue-400 transition-all">garasgewrg</a>

                                    <a href="#" class="px-4 py-3 rounded-xl hover:bg-blue-500/20 hover:text-blue-400 transition-all">fasgvaw</a>

                                    <a href="#" class="px-4 py-3 rounded-xl hover:bg-blue-500/20 hover:text-blue-400 transition-all">gsadgawergf</a>

                                    <a href="#" class="px-4 py-3 rounded-xl hover:bg-blue-500/20 hover:text-blue-400 transition-all">dfasfa</a>
                                </div>

                                <!-- <div class="mt-4 pt-4 border-t border-white/10">
                                    <a href="#" class="text-sm text-blue-400 hover:text-blue-300">View All</a>
                                </div> -->
                            </div>
                        </li>

                        <li class="relative group">
                            <button class="flex items-center gap-2 px-4 py-2 rounded-xl text-white hover:bg-white/10 hover:text-blue-400 transition-all duration-300">Quality<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform duration-300 group-hover:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg></button>
                            
                            <div class="absolute top-full left-0 mt-0 hidden group-hover:block w-[350px] rounded-xl border border-white/10 bg-black backdrop-blur-xl shadow-xl p-4">
                                <div class="grid grid-cols-2 gap-2">
                                    <a href="#" class="px-4 py-3 rounded-xl hover:bg-blue-500/20 hover:text-blue-400 transition-all">4K</a>

                                    <a href="#" class="px-4 py-3 rounded-xl hover:bg-blue-500/20 hover:text-blue-400 transition-all">2K</a>

                                    <a href="#" class="px-4 py-3 rounded-xl hover:bg-blue-500/20 hover:text-blue-400 transition-all">1080p FHD</a>

                                    <a href="#" class="px-4 py-3 rounded-xl hover:bg-blue-500/20 hover:text-blue-400 transition-all">3d</a>
                                </div>

                                <!-- <div class="mt-4 pt-4 border-t border-white/10">
                                    <a href="#" class="text-sm text-blue-400 hover:text-blue-300">View All</a>
                                </div> -->
                            </div>
                        </li>

                        <!-- <li>
                            <a href="#" class="px-4 py-2 rounded-xl text-white hover:bg-white/10 hover:text-blue-400 transition-all duration-300">Contact Us</a>
                        </li> -->
                    </div>
                </div>

                <!-- Right Side -->
                <div class="flex items-center gap-3">
                    <!-- Search -->
                    <div class="hidden md:flex items-center">
                        <div class="flex items-center gap-3 px-4 h-10 w-[200px] rounded-xl border border-white/10 bg-white/5 backdrop-blur-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>

                            <input type="text" placeholder="Search..." class="w-full rounded-xl bg-transparent border-none outline-none text-sm text-white placeholder:text-gray-400">
                        </div>
                    </div>

                    <!-- User -->
                    <div class="relative group hidden md:block">
                        <button class="flex items-center gap-2 px-4 h-10 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 transition-all duration-300">
                            <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center font-bold text-white">
                                <?php
                                    if($userid == NULL){
                                        echo "U";
                                    }
                                    else{
                                        echo strtoupper(substr($user["shortname"], 0, 1));
                                    }
                                ?>
                            </div>

                            <span class="text-sm font-medium text-white">
                                <?php
                                    if($userid == NULL){
                                        echo "User";
                                    }
                                    else{
                                        echo $user["shortname"];
                                    }
                                ?>
                            </span>
                        </button>

                        <!-- USER DROPDOWN -->
                        <div class="absolute right-0 top-full mt-0 w-40 hidden group-hover:block rounded-xl border border-white/10 bg-black/80 backdrop-blur-xl shadow-xl overflow-hidden text-sm">
                            <div class="p-2 space-y-2">
                                <?php
                                    if($userid != NULL){
                                        if($user['role'] == "superadmin" || $user['role'] == "admin"){
                                            ?>
                                            <a href="./@admin/" target="_blank" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-white/10 transition-all">Admin Panel</a>
                                            <?php
                                        }
                                        ?>

                                        <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-white/10 transition-all">My Profile</a>
                                        <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-white/10 transition-all">Watchlist</a>
                                        <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-white/10 transition-all">Request</a>
                                        <a href="./signout.php" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-red-500/20 text-red-400 transition-all">Sign Out</a>
                                        <?php
                                    }
                                    else{
                                        ?>
                                        <a href="./register.php" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-white/10 transition-all">Register</a>

                                        <a href="./signin.php" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-white/10 transition-all">Sign In</a>
                                        <?php
                                    }
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- MOBILE MENU BUTTON -->
                    <button id="mobileMenuButton" class="lg:hidden flex items-center justify-center w-12 h-12 rounded-xl border border-white/10 bg-white/5 text-white"><svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg></button>
                </div>
            </div>
        </div>
    </nav>

    <div id="mobileSidebar" class="fixed top-0 left-[-100%] z-[60] h-screen overflow-y-auto overflow-x-hidden w-[85%] max-w-[340px] bg-[#050816]/95 backdrop-blur-xl border-r border-white/10 transition-all duration-500">
        <!-- Header -->
        <div class="flex items-center justify-between p-5 border-b border-white/10">
            <div class="flex items-center gap-3">
                <img src="./favicon/web-app-manifest-192x192.png" class="h-10" alt=""><span class="text-2xl font-black text-white">PURE<span class="text-blue-500">CINEPIX</span></span>
            </div>

            <button id="closeSidebar" class="w-10 h-10 rounded-xl bg-white/10 text-white">✕</button>
        </div>

        <!-- SEARCH -->
        <div class="p-5">
            <div class="flex items-center gap-3 px-4 h-10 rounded-xl border border-white/10 bg-white/5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>

                <input type="text" placeholder="Search..." class="w-full bg-transparent outline-none border-0 text-white placeholder:text-gray-400 h-8 rounded-xl">
            </div>
        </div>

        <!-- MOBILE MENU -->
        <div class="px-5 pb-10 space-y-1 text-white text-sm">
            <a href="#" class="flex items-center justify-between px-4 py-2 rounded-xl border border-white/[0.03] hover:bg-blue-500/20 hover:text-blue-400 transition-all">Home</a>

            <div class="rounded-xl overflow-hidden border border-white/[0.03]">
                <button class="mobileDropdownBtn w-full flex items-center justify-between px-4 py-2 hover:bg-blue-500/20 hover:text-blue-400 transition-all">
                    <span>Genre</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div class="mobileDropdown hidden px-2 py-2 space-y-1 text-left">
                    <a href="#" class="block px-4 py-2 rounded-xl text-sm hover:bg-blue-500/20 hover:text-blue-400">Action</a>
                    <a href="#" class="block px-4 py-2 rounded-xl text-sm hover:bg-blue-500/20 hover:text-blue-400">Adventure</a>
                    <a href="#" class="block px-4 py-2 rounded-xl text-sm hover:bg-blue-500/20 hover:text-blue-400">Action</a>
                    <a href="#" class="block px-4 py-2 rounded-xl text-sm hover:bg-blue-500/20 hover:text-blue-400">Adventure</a>
                    <a href="#" class="block px-4 py-2 rounded-xl text-sm hover:bg-blue-500/20 hover:text-blue-400">Action</a>
                    <a href="#" class="block px-4 py-2 rounded-xl text-sm hover:bg-blue-500/20 hover:text-blue-400">Adventure</a>
                    <a href="#" class="block px-4 py-2 rounded-xl text-sm hover:bg-blue-500/20 hover:text-blue-400">Action</a>
                    <a href="#" class="block px-4 py-2 rounded-xl text-sm hover:bg-blue-500/20 hover:text-blue-400">Adventure</a>
                    <a href="#" class="block px-4 py-2 rounded-xl text-sm hover:bg-blue-500/20 hover:text-blue-400">Action</a>
                    <a href="#" class="block px-4 py-2 rounded-xl text-sm hover:bg-blue-500/20 hover:text-blue-400">Adventure</a>
                    <a href="#" class="block px-4 py-2 rounded-xl text-sm hover:bg-blue-500/20 hover:text-blue-400">Action</a>
                    <a href="#" class="block px-4 py-2 rounded-xl text-sm hover:bg-blue-500/20 hover:text-blue-400">Adventure</a>
                    <a href="#" class="block px-4 py-2 rounded-xl text-sm hover:bg-blue-500/20 hover:text-blue-400">Action</a>
                    <a href="#" class="block px-4 py-2 rounded-xl text-sm hover:bg-blue-500/20 hover:text-blue-400">Adventure</a>
                </div>
            </div>

            <div class="rounded-xl overflow-hidden border border-white/[0.03]">
                <button class="mobileDropdownBtn w-full flex items-center justify-between px-4 py-2 hover:bg-blue-500/20 hover:text-blue-400 transition-all">
                    <span>Category</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div class="mobileDropdown hidden px-2 py-2 space-y-1 text-left">
                    <a href="#" class="block px-4 py-2 rounded-xl text-sm hover:bg-blue-500/20 hover:text-blue-400">Action</a>
                    <a href="#" class="block px-4 py-2 rounded-xl text-sm hover:bg-blue-500/20 hover:text-blue-400">Adventure</a>
                </div>
            </div>

            <div class="p-3 rounded-xl overflow-hidden"><hr class="border border-white/10"></div>

            <div class="rounded-xl overflow-hidden border border-white/[0.03]">
                <button class="mobileDropdownBtn w-full flex items-center justify-between px-4 py-2 bg-white/[0.03] hover:bg-blue-500/20 hover:text-blue-400 transition-all">
                    <div class="flex flex-row gap-4">
                        <span class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center font-bold text-white">
                            <?php
                                if($userid == NULL){
                                    echo "U";
                                }
                                else{
                                    echo strtoupper(substr($user["shortname"], 0, 1));
                                }
                            ?>
                        </span>
                        <span class="text-sm font-medium text-white flex items-center">
                            <?php
                                if($userid == NULL){
                                    echo "User";
                                }
                                else{
                                    echo $user["shortname"];
                                }
                            ?>
                        </span>



     
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div class="mobileDropdown hidden px-2 py-2 space-y-1 text-left">
                    <?php
                        if($userid == NULL){
                            ?>
                            <a href="./register.php" class="block px-4 py-2 rounded-xl text-sm hover:bg-blue-500/20 hover:text-blue-400">Register</a>
                            <a href="./signin.php" class="block px-4 py-2 rounded-xl text-sm hover:bg-blue-500/20 hover:text-blue-400">Sign In</a>
                            <?php
                        }
                        else{
                            if($user['role'] == "superadmin" || $user['role'] == "admin"){
                                ?>
                                    <a href="./@admin/" target="_blank" class="block px-4 py-2 rounded-xl text-sm hover:bg-blue-500/20 hover:text-blue-400">Admin Panel</a>
                                <?php
                            }
                            ?>
                            <a href="#" class="block px-4 py-2 rounded-xl text-sm hover:bg-blue-500/20 hover:text-blue-400">My Profile</a>
                            <a href="#" class="block px-4 py-2 rounded-xl text-sm hover:bg-blue-500/20 hover:text-blue-400">Watchlist</a>
                            <a href="#" class="block px-4 py-2 rounded-xl text-sm hover:bg-blue-500/20 hover:text-blue-400">Request</a>
                            <a href="./signout.php" class="block px-4 py-2 rounded-xl text-sm hover:bg-red-500/20 hover:text-red-400">Sign Out</a>
                            <?php
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- OVERLAY -->
    <div id="sidebarOverlay" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm hidden"></div>

    <!-- JAVASCRIPT -->
    <script>
        const mobileMenuButton = document.getElementById("mobileMenuButton");
        const mobileSidebar = document.getElementById("mobileSidebar");
        const closeSidebar = document.getElementById("closeSidebar");
        const sidebarOverlay = document.getElementById("sidebarOverlay");

        mobileMenuButton.addEventListener("click", () => {
            mobileSidebar.style.left = "0";
            sidebarOverlay.classList.remove("hidden");
            document.body.style.overflow = "hidden";
        });

        closeSidebar.addEventListener("click", closeMenu);
        sidebarOverlay.addEventListener("click", closeMenu);

        function closeMenu() {
            mobileSidebar.style.left = "-100%";
            sidebarOverlay.classList.add("hidden");
            document.body.style.overflow = "auto";
        }

        const mobileDropdownBtns = document.querySelectorAll(".mobileDropdownBtn");

        mobileDropdownBtns.forEach((btn) => {
            btn.addEventListener("click", () => {
                const currentDropdown = btn.nextElementSibling;
                const currentIcon = btn.querySelector("svg");

                // Close all other dropdowns
                document.querySelectorAll(".mobileDropdown").forEach((dropdown) => {
                    if (dropdown !== currentDropdown) {
                        dropdown.classList.add("hidden");
                    }
                });
                // Reset all other icons
                document.querySelectorAll(".mobileDropdownBtn svg").forEach((icon) => {
                    if (icon !== currentIcon) {
                        icon.classList.remove("rotate-180");
                    }
                });

                // Toggle current dropdown
                currentDropdown.classList.toggle("hidden");
                // Rotate current icon
                currentIcon.classList.toggle("rotate-180");
            });
        });
    </script>
</body>
</html>