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
    <?php include './app2.php'; ?>

    <div class="w-full flex justify-center">
        <div class="w-full max-w-screen-xl flex flex-row gap-2 p-2">
            <div class="w-1/6 h-max rounded-lg p-2 bg-black/20 border-2 border-white/60 shadow-lg">
                <div class="text-left space-y-1 font-semibold flex flex-col">
                    <a href="#" class="select_btn rounded-lg transition-all duration-100 ease-in-out p-2 hover:bg-blue-500 space-x-2 flex flex-row items-center justify-start bg-blue-500" id="select_btn_1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-panel-left-icon lucide-layout-panel-left"><rect width="7" height="18" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/></svg>
                        <span>Display Content</span>
                    </a>

                    <a href="#" class="select_btn rounded-lg transition-all duration-100 ease-in-out p-2 hover:bg-blue-500 space-x-2 flex flex-row items-center justify-start" id="select_btn_2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail-icon lucide-mail"><path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"/><rect x="2" y="4" width="20" height="16" rx="2"/></svg>
                        <span>Contact & Email</span>
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
                <div class="exp_div rounded-lg p-4 bg-black/20 border-2 border-white/60" id="div_1"></div>

                <div class="exp_div rounded-lg p-4 bg-black/20 border-2 border-white/60 hidden" id="div_2"></div>

                <div class="exp_div rounded-lg p-4 bg-black/20 border-2 border-white/60 hidden" id="div_3"></div>

                <div class="exp_div rounded-lg p-4 bg-black/20 border-2 border-white/60 hidden" id="div_4"></div>

                <div class="exp_div rounded-lg p-4 bg-black/20 border-2 border-white/60 hidden" id="div_5"></div>
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