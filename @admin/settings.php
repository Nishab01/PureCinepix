<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PureCinepix | Settings</title>
    
    <link rel="icon" type="image/png" sizes="96x96" href="../favicon/favicon-96x96.png">
    <link rel="icon" type="image/svg+xml" href="../favicon/favicon.svg">
    <link rel="shortcut icon" href="../favicon/favicon.ico">
    <link rel="apple-touch-icon" href="../favicon/apple-touch-icon.png">
    <meta name="apple-mobile-web-app-title" content="PureCinepix">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="../favicon/site.webmanifest">
    <meta name="theme-color" content="#00AAFF">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@9.0.3"></script>
</head>
<body>
    <?php
        include './app2.php';

        $query1 = "SELECT * FROM settings WHERE `section` = 'display-content';";
        $query2 = "SELECT * FROM settings WHERE `section` = 'email-contact';";
        $query3 = "SELECT * FROM settings WHERE `section` = 'social-media';";
        $query4 = "SELECT * FROM settings WHERE `section` = 'footer';";
        $query5 = "SELECT * FROM settings WHERE `section` = 'miscellaneous';";

        $run1 = $conn->query($query1);
        $run2 = $conn->query($query2);
        $run3 = $conn->query($query3);
        $run4 = $conn->query($query4);
        $run5 = $conn->query($query5);
    ?>

    <div class="w-full flex justify-center">
        <div class="w-full max-w-screen-xl flex flex-row gap-2 p-2">
            <div class="w-1/6 h-max rounded-lg p-2 bg-black/20 border-2 border-white/60 shadow-lg">
                <div class="text-left space-y-1 font-semibold flex flex-col">
                    <a href="#" class="select_btn rounded-lg transition-all duration-100 ease-in-out p-2 hover:bg-blue-500 space-x-2 flex flex-row items-center justify-start bg-blue-500" id="select_btn_1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-monitor-cog-icon lucide-monitor-cog"><path d="M12 17v4"/><path d="m14.305 7.53.923-.382"/><path d="m15.228 4.852-.923-.383"/><path d="m16.852 3.228-.383-.924"/><path d="m16.852 8.772-.383.923"/><path d="m19.148 3.228.383-.924"/><path d="m19.53 9.696-.382-.924"/><path d="m20.772 4.852.924-.383"/><path d="m20.772 7.148.924.383"/><path d="M22 13v2a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7"/><path d="M8 21h8"/><circle cx="18" cy="6" r="3"/></svg>
                        <span>Display Content</span>
                    </a>

                    <a href="#" class="select_btn rounded-lg transition-all duration-100 ease-in-out p-2 hover:bg-blue-500 space-x-2 flex flex-row items-center justify-start" id="select_btn_2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail-icon lucide-mail"><path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"/><rect x="2" y="4" width="20" height="16" rx="2"/></svg>
                        <span>Email & Contact</span>
                    </a>

                    <a href="#" class="select_btn rounded-lg transition-all duration-100 ease-in-out p-2 hover:bg-blue-500 space-x-2 flex flex-row items-center justify-start" id="select_btn_3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-fading-plus-icon lucide-circle-fading-plus"><path d="M12 2a10 10 0 0 1 7.38 16.75"/><path d="M12 8v8"/><path d="M16 12H8"/><path d="M2.5 8.875a10 10 0 0 0-.5 3"/><path d="M2.83 16a10 10 0 0 0 2.43 3.4"/><path d="M4.636 5.235a10 10 0 0 1 .891-.857"/><path d="M8.644 21.42a10 10 0 0 0 7.631-.38"/></svg>
                        <span>Social Media</span>
                    </a>

                    <a href="#" class="select_btn rounded-lg transition-all duration-100 ease-in-out p-2 hover:bg-blue-500 space-x-2 flex flex-row items-center justify-start" id="select_btn_4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-panel-bottom-dashed-icon lucide-panel-bottom-dashed"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M14 15h1"/><path d="M19 15h2"/><path d="M3 15h2"/><path d="M9 15h1"/></svg>
                        <span>Footer</span>
                    </a>

                    <a href="#" class="select_btn rounded-lg transition-all duration-100 ease-in-out p-2 hover:bg-blue-500 space-x-2 flex flex-row items-center justify-start" id="select_btn_5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-ellipsis-icon lucide-circle-ellipsis"><circle cx="12" cy="12" r="10"/><path d="M17 12h.01"/><path d="M12 12h.01"/><path d="M7 12h.01"/></svg>
                        <span>Miscellaneous</span>
                    </a>
                </div>
            </div>

            <div class="w-5/6 h-max shadow-lg">
                <form action="./updatesettings.php" method="POST" class="rounded-lg p-4 bg-black/20 border-2 border-white/60 gap-4 grid">
                    <div class="exp_div w-full grid grid-cols-12 gap-4" id="div_1">
                        <?php
                            while($display = $run1->fetch_assoc()){
                                if($display['key'] == "trending-count"){
                                    ?>
                                    <div class="w-full col-span-6 text-left">
                                        <label for="trending_count" class="font-semibold">Trending Content Count: <span class="font-bold text-red-500">*</span></label>
                                        <input class="w-full bg-white text-black rounded-lg" type="number" name="trending_count" id="trending_count" value="<?php echo $display['value']; ?>" required>
                                    </div>
                                    <?php
                                }

                                if($display['key'] == "featured-count"){
                                    ?>
                                    <div class="w-full col-span-6 text-left">
                                        <label for="featured_count" class="font-semibold">Featured Content Count: <span class="font-bold text-red-500">*</span></label>
                                        <input class="w-full bg-white text-black rounded-lg" type="number" name="featured_count" id="featured_count" value="<?php echo $display['value']; ?>" required>
                                    </div>
                                    <?php
                                }

                                if($display['key'] == "latest-count"){
                                    ?>
                                    <div class="w-full col-span-6 text-left">
                                        <label for="latest_count" class="font-semibold">Latest Content Count: <span class="font-bold text-red-500">*</span></label>
                                        <input class="w-full bg-white text-black rounded-lg" type="number" name="latest_count" id="latest_count" value="<?php echo $display['value']; ?>" required>
                                    </div>
                                    <?php
                                }

                                if($display['key'] == "genrewise-count"){
                                    ?>
                                    <div class="w-full col-span-6 text-left">
                                        <label for="genrewise_count" class="font-semibold">Genre-wise Content Count: <span class="font-bold text-red-500">*</span></label>
                                        <input class="w-full bg-white text-black rounded-lg" type="number" name="genrewise_count" id="genrewise_count" value="<?php echo $display['value']; ?>" required>
                                    </div>
                                    <?php
                                }
                            }
                        ?>
                    </div>

                    <div class="exp_div w-full grid grid-cols-12 gap-4 hidden" id="div_2">
                        <?php
                            while($contact = $run2->fetch_assoc()){
                                if($contact['key'] == "website-email"){
                                    ?>
                                    <div class="w-full col-span-12 text-left">
                                        <label for="website_email" class="font-semibold">Website Email Address: <span class="font-bold text-red-500">*</span></label>
                                        <input class="w-full bg-white text-black rounded-lg" type="text" name="website_email" id="website_email" value="<?php echo $contact['value']; ?>" required>
                                    </div>
                                    <?php
                                }

                                if($contact['key'] == "website-about"){
                                    ?>
                                    <div class="w-full col-span-12 text-left">
                                        <label for="website_about" class="font-semibold">Website About:</label>
                                        <textarea class="w-full bg-white text-black rounded-lg" name="website_about" id="website_about"><?php echo $contact['value']; ?></textarea>
                                    </div>
                                    <?php
                                }
                            }
                        ?>
                    </div>

                    <div class="exp_div w-full grid grid-cols-12 gap-4 hidden" id="div_3">
                        <?php
                            while($social = $run3->fetch_assoc()){
                                if($social['key'] == "facebook"){
                                    ?>
                                    <div class="w-full col-span-6 text-left">
                                        <label for="facebook_url" class="font-semibold">Facebook URL:</label>
                                        <input class="w-full bg-white text-black rounded-lg" type="text" name="facebook_url" id="facebook_url" value="<?php echo $social['value']; ?>">
                                    </div>

                                    <div class="w-full col-span-6 text-left">
                                        <label for="facebook_icon" class="font-semibold">Facebook SVG Icon:</label>
                                        <input class="w-full bg-white text-black rounded-lg" type="text" name="facebook_icon" id="facebook_icon" value="<?php echo $social['icon']; ?>">
                                    </div>
                                    <?php
                                }

                                if($social['key'] == "instagram"){
                                    ?>
                                    <div class="w-full col-span-6 text-left">
                                        <label for="instagram_url" class="font-semibold">Instagram URL:</label>
                                        <input class="w-full bg-white text-black rounded-lg" type="text" name="instagram_url" id="instagram_url" value="<?php echo $social['value']; ?>">
                                    </div>

                                    <div class="w-full col-span-6 text-left">
                                        <label for="instagram_icon" class="font-semibold">Instagram SVG Icon:</label>
                                        <input class="w-full bg-white text-black rounded-lg" type="text" name="instagram_icon" id="instagram_icon" value="<?php echo $social['icon']; ?>">
                                    </div>
                                    <?php
                                }
                            }
                        ?>
                    </div>

                    <div class="exp_div w-full grid grid-cols-12 gap-4 hidden" id="div_4">
                        <?php
                            while($footer = $run4->fetch_assoc()){
                                if($footer['key'] == "copyright-text"){
                                    ?>
                                    <div class="w-full col-span-12 text-left">
                                        <label for="copyright_text" class="font-semibold">Copyright Text: <span class="font-bold text-red-500">*</span></label>
                                        <textarea class="w-full bg-white text-black rounded-lg" name="copyright_text" id="copyright_text" required><?php echo $footer['value']; ?></textarea>
                                    </div>
                                    <?php
                                }

                                if($footer['key'] == "footer-text"){
                                    ?>
                                    <div class="w-full col-span-12 text-left">
                                        <label for="footer_text" class="font-semibold">Footer Text:</label>
                                        <textarea class="w-full bg-white text-black rounded-lg" name="footer_text" id="footer_text"><?php echo $footer['value']; ?></textarea>
                                    </div>
                                    <?php
                                }

                                if($footer['key'] == "footer-description"){
                                    ?>
                                    <div class="w-full col-span-12 text-left">
                                        <label for="footer_description" class="font-semibold">Footer Description:</label>
                                        <textarea class="w-full bg-white text-black rounded-lg" name="footer_description" id="footer_description"><?php echo $footer['value']; ?></textarea>
                                    </div>
                                    <?php
                                }
                            }
                        ?>
                    </div>

                    <div class="exp_div w-full grid grid-cols-12 gap-4 hidden" id="div_5">
                        <?php
                            while($misc = $run5->fetch_assoc()){
                                if($misc['key'] == "maintenance-mode"){
                                    ?>
                                    <div class="w-full col-span-12 text-left">
                                        <div class="flex items-center gap-3">
                                            <span class="font-semibold text-white">Maintenance Mode:</span>

                                            <!-- Hidden Input -->
                                            <input type="checkbox" id="maintenance_mode" name="maintenance_mode" class="hidden">

                                            <!-- Toggle -->
                                            <div id="toggle" onclick="toggleSwitch()" class="w-14 h-7 rounded-full relative cursor-pointer transition-all duration-300 bg-gray-500">
                                                <div id="circle" class="w-5 h-5 bg-white rounded-full absolute top-1 left-1 transition-all duration-300"></div>
                                            </div>
                                        </div>

                                        <script>
                                            const initialState = <?= ($misc['value'] == 1) ? 'true' : 'false' ?>;

                                            const checkbox = document.getElementById("maintenance_mode");
                                            const toggle = document.getElementById("toggle");
                                            const circle = document.getElementById("circle");

                                            checkbox.checked = initialState;

                                            function updateToggle(){
                                                if (checkbox.checked) {
                                                    toggle.classList.remove("bg-gray-500");
                                                    toggle.classList.add("bg-blue-500");
                                                    circle.style.left = "2rem";
                                                }
                                                else{
                                                    toggle.classList.remove("bg-blue-500");
                                                    toggle.classList.add("bg-gray-500");
                                                    circle.style.left = "0.25rem";
                                                }
                                            }
                                            function toggleSwitch(){
                                                checkbox.checked = !checkbox.checked;
                                                updateToggle();
                                            }
                                            // run AFTER syncing DB value
                                            updateToggle();
                                        </script>
                                    </div>
                                    <?php
                                }
                            }
                        ?>
                    </div>

                    <div class="w-full grid grid-cols-12 gap-4">
                        <input class="rounded-lg bg-blue-500 text-white px-4 py-2 font-bold" type="submit" name="save" id="save" value="Save">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        for(let i=1; i<=5; i++){
            const select_btn = document.getElementById('select_btn_'+i);
            const select_div = document.getElementById('div_'+i);

            select_btn.addEventListener('click', function() {
                document.querySelectorAll('.select_btn').forEach(x => x.classList.remove('bg-blue-500'));
                this.classList.add('bg-blue-500');

                document.querySelectorAll('.exp_div').forEach(y => y.classList.add('hidden'));
                select_div.classList.remove('hidden');
            });
        }
    </script>
</body>
<style>
    #settings{
        border-bottom: 4px solid #3b82f6;;
    }
</style>
</html>