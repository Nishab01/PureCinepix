<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PureCinepix | Contents</title>
    
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
            <div class="w-1/6 h-max rounded-xl p-2 bg-black/20 border-2 border-white/60 shadow-lg">
                <div class="text-left space-y-1 font-semibold flex flex-col">
                    <a href="#" class="select_btn rounded-xl transition-all duration-100 ease-in-out p-2 hover:bg-blue-500 space-x-2 flex flex-row items-center justify-start bg-blue-500" id="select_btn_1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-film-icon lucide-film"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 3v18"/><path d="M3 7.5h4"/><path d="M3 12h18"/><path d="M3 16.5h4"/><path d="M17 3v18"/><path d="M17 7.5h4"/><path d="M17 16.5h4"/></svg>
                        <span>All Contents</span>
                    </a>

                    <a href="#" class="select_btn rounded-xl transition-all duration-100 ease-in-out p-2 hover:bg-blue-500 space-x-2 flex flex-row items-center justify-start" id="select_btn_2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chart-bar-stacked-icon lucide-chart-bar-stacked"><path d="M11 13v4"/><path d="M15 5v4"/><path d="M3 3v16a2 2 0 0 0 2 2h16"/><rect x="7" y="13" width="9" height="4" rx="1"/><rect x="7" y="5" width="12" height="4" rx="1"/></svg>
                        <span>Genre</span>
                    </a>

                    <a href="#" class="select_btn rounded-xl transition-all duration-100 ease-in-out p-2 hover:bg-blue-500 space-x-2 flex flex-row items-center justify-start" id="select_btn_3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shapes-icon lucide-shapes"><path d="M8.3 10a.7.7 0 0 1-.626-1.079L11.4 3a.7.7 0 0 1 1.198-.043L16.3 8.9a.7.7 0 0 1-.572 1.1Z"/><rect x="3" y="14" width="7" height="7" rx="1"/><circle cx="17.5" cy="17.5" r="3.5"/></svg>
                        <span>Category</span>
                    </a>

                    <a href="#" class="select_btn rounded-xl transition-all duration-100 ease-in-out p-2 hover:bg-blue-500 space-x-2 flex flex-row items-center justify-start" id="select_btn_4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-hd-icon lucide-hd"><path d="M10 12H6"/><path d="M10 15V9"/><path d="M14 14.5a.5.5 0 0 0 .5.5h1a2.5 2.5 0 0 0 2.5-2.5v-1A2.5 2.5 0 0 0 15.5 9h-1a.5.5 0 0 0-.5.5z"/><path d="M6 15V9"/><rect x="2" y="5" width="20" height="14" rx="2"/></svg>
                        <span>Quality</span>
                    </a>

                    <a href="#" class="select_btn rounded-xl transition-all duration-100 ease-in-out p-2 hover:bg-blue-500 space-x-2 flex flex-row items-center justify-start" id="select_btn_5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-hash-icon lucide-hash"><line x1="4" x2="20" y1="9" y2="9"/><line x1="4" x2="20" y1="15" y2="15"/><line x1="10" x2="8" y1="3" y2="21"/><line x1="16" x2="14" y1="3" y2="21"/></svg>
                        <span>Trending</span>
                    </a>

                    <a href="#" class="select_btn rounded-xl transition-all duration-100 ease-in-out p-2 hover:bg-blue-500 space-x-2 flex flex-row items-center justify-start" id="select_btn_6">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star-icon lucide-star"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>
                        <span>Featured</span>
                    </a>
                </div>
            </div>

            <div class="w-5/6 h-max shadow-lg">
                <div class="exp_div rounded-xl p-4 bg-black/20 border-2 border-white/60" id="div_1">
                    <div class="flex flex-row justify-between items-center">
                        <p class="text-2xl font-semibold">All Contents</p>
                    
                        <!-- Modal: Add New Content -->
                        <button onclick="openModal1()" class="transition-all duration-100 ease-in-out rounded-xl border-2 border-blue-500 text-blue-500 font-semibold p-2 hover:bg-blue-500 hover:text-white">Add New Content</button>

                        <div id="myModal1" class="fixed inset-0 flex items-center justify-center bg-black/80 opacity-0 pointer-events-none transition-opacity duration-300">
                            <div id="modalBox1" class="bg-gray-900 p-6 rounded-xl shadow-lg transform scale-95 transition-all duration-100 w-3/6">
                                <h2 class="text-lg font-semibold mb-4">Add New Content</h2>

                                <form action="" method="POST">
                                    <input type="text" name="name" placeholder="" class="">

                                    <button type="submit" class="">Submit</button>
                                </form>

                                <button onclick="closeModal1()" class="">Close</button>
                            </div>
                        </div>

                        <script>
                            function openModal1() {
                                const modal = document.getElementById('myModal1');
                                const box = document.getElementById('modalBox1');

                                modal.classList.remove("opacity-0", "pointer-events-none");
                                box.classList.remove("scale-95");
                                box.classList.add("scale-100");
                            }
                            function closeModal1() {
                                const modal = document.getElementById('myModal1');
                                const box = document.getElementById('modalBox1');

                                modal.classList.add("opacity-0", "pointer-events-none");
                                box.classList.remove("scale-100");
                                box.classList.add("scale-95");
                            }
                            document.getElementById('myModal1').addEventListener("click", function(e) {
                                if (e.target === this) {
                                    closeModal1();
                                }
                            });
                        </script>
                    </div>
                    
                    <hr class="my-2 border-1">

                    <table id="search-table1">
                        <thead>
                            <tr>
                                <th class="bg-black/90">
                                    <span class="flex items-center">Name<svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4"/></svg></span>
                                </th>

                                <th class="bg-black/90">
                                    <span class="flex items-center">Release Date<svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4"/></svg></span>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>Flowbite</td>
                                <td>2021/25/09</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="exp_div rounded-xl p-4 bg-black/20 border-2 border-white/60 hidden" id="div_2">
                    <div class="flex flex-row justify-between items-center">
                        <p class="text-2xl font-semibold">Genre</p>
                        
                        <button onclick="openModal2()" class="transition-all duration-100 ease-in-out rounded-xl border-2 border-blue-500 text-blue-500 font-semibold p-2 hover:bg-blue-500 hover:text-white">Add Genre</button>

                        <div id="myModal2" class="fixed inset-0 w-full flex items-center justify-center bg-black/80 opacity-0 pointer-events-none transition-opacity duration-300">
                            <div id="modalBox2" class="bg-gray-800 p-6 rounded-xl shadow-lg transform scale-95 transition-all duration-100 w-3/6">
                                <h2 class="text-xl font-semibold mb-4">Add Genre</h2>
                                
                                <form action="" method="POST" class="grid grid-cols-12 gap-4 w-full">
                                    <div class="w-full col-span-6 text-left">
                                        <label class="font-bold block mb-1" for="g_name">Name: <span class="font-bold text-red-500">*</span></label>
                                        <input class="w-full rounded-xl" type="text" name="g_name" id="g_name" placeholder="Type genre name" required>
                                    </div>

                                    <div class="w-full col-span-6 text-left">
                                        <label class="font-semibold block mb-1" for="g_slug">Slug: <span class="font-bold text-red-500">*</span></label>
                                        <input class="w-full rounded-xl" type="text" name="g_slug" id="g_slug" placeholder="Type genre slug" required>
                                    </div>
                                </form>

                                <button onclick="closeModal2()" class="">Close</button>
                            </div>
                        </div>

                        <script>
                            function openModal2() {
                                const modal = document.getElementById('myModal2');
                                const box = document.getElementById('modalBox2');

                                modal.classList.remove("opacity-0", "pointer-events-none");
                                box.classList.remove("scale-95");
                                box.classList.add("scale-100");
                            }
                            function closeModal2() {
                                const modal = document.getElementById('myModal2');
                                const box = document.getElementById('modalBox2');

                                modal.classList.add("opacity-0", "pointer-events-none");
                                box.classList.remove("scale-100");
                                box.classList.add("scale-95");
                            }
                            document.getElementById('myModal2').addEventListener("click", function(e) {
                                if (e.target === this) {
                                    closeModal2();
                                }
                            });
                        </script>
                    </div>
                    
                    <hr class="my-2 border-1">

                    <table id="search-table2">
                        <thead>
                            <tr>
                                <th class="bg-black/90">
                                    <span class="flex items-center">Name<svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4"/></svg></span>
                                </th>

                                <th class="bg-black/90">
                                    <span class="flex items-center">Release Date<svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4"/></svg></span>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>Flowbite</td>
                                <td>2021/25/09</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="exp_div rounded-xl p-4 bg-black/20 border-2 border-white/60 hidden" id="div_3">
                    <div class="flex flex-row justify-between items-center">
                        <p class="text-2xl font-semibold">Category</p>
                        
                        <button onclick="openModal3()" class="transition-all duration-100 ease-in-out rounded-xl border-2 border-blue-500 text-blue-500 font-semibold p-2 hover:bg-blue-500 hover:text-white">Add Category</button>

                        <div id="myModal3" class="fixed inset-0 flex items-center justify-center bg-black/80 opacity-0 pointer-events-none transition-opacity duration-300">
                            <div id="modalBox3" class="bg-gray-900 p-6 rounded-xl shadow-lg transform scale-95 transition-all duration-100 w-full w-3/6">
                                <h2 class="text-lg font-semibold mb-4">Add Category</h2>

                                <form action="" method="POST">
                                    <input type="text" name="name" placeholder="" class="">

                                    <button type="submit" class="">Submit</button>
                                </form>

                                <button onclick="closeModal3()" class="">Close</button>
                            </div>
                        </div>

                        <script>
                            function openModal3() {
                                const modal = document.getElementById('myModal3');
                                const box = document.getElementById('modalBox3');

                                modal.classList.remove("opacity-0", "pointer-events-none");
                                box.classList.remove("scale-95");
                                box.classList.add("scale-100");
                            }
                            function closeModal3() {
                                const modal = document.getElementById('myModal3');
                                const box = document.getElementById('modalBox3');

                                modal.classList.add("opacity-0", "pointer-events-none");
                                box.classList.remove("scale-100");
                                box.classList.add("scale-95");
                            }
                            document.getElementById('myModal3').addEventListener("click", function(e) {
                                if (e.target === this) {
                                    closeModal3();
                                }
                            });
                        </script>
                    </div>
                    
                    <hr class="my-2 border-1">

                    <table id="search-table3">
                        <thead>
                            <tr>
                                <th class="bg-black/90">
                                    <span class="flex items-center">Name<svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4"/></svg></span>
                                </th>

                                <th class="bg-black/90">
                                    <span class="flex items-center">Release Date<svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4"/></svg></span>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>Flowbite</td>
                                <td>2021/25/09</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="exp_div rounded-xl p-4 bg-black/20 border-2 border-white/60 hidden" id="div_4">
                    <div class="flex flex-row justify-between items-center">
                        <p class="text-2xl font-semibold">Quality</p>
                        
                        <button onclick="openModal4()" class="transition-all duration-100 ease-in-out rounded-xl border-2 border-blue-500 text-blue-500 font-semibold p-2 hover:bg-blue-500 hover:text-white">Add Quality</button>

                        <div id="myModal4" class="fixed inset-0 flex items-center justify-center bg-black/80 opacity-0 pointer-events-none transition-opacity duration-300">
                            <div id="modalBox4" class="bg-gray-900 p-6 rounded-xl shadow-lg transform scale-95 transition-all duration-100 w-full w-3/6">
                                <h2 class="text-lg font-semibold mb-4">Add Quality</h2>

                                <form action="" method="POST">
                                    <input type="text" name="name" placeholder="" class="">

                                    <button type="submit" class="">Submit</button>
                                </form>

                                <button onclick="closeModal4()" class="">Close</button>
                            </div>
                        </div>

                        <script>
                            function openModal4() {
                                const modal = document.getElementById('myModal4');
                                const box = document.getElementById('modalBox4');

                                modal.classList.remove("opacity-0", "pointer-events-none");
                                box.classList.remove("scale-95");
                                box.classList.add("scale-100");
                            }
                            function closeModal4() {
                                const modal = document.getElementById('myModal4');
                                const box = document.getElementById('modalBox4');

                                modal.classList.add("opacity-0", "pointer-events-none");
                                box.classList.remove("scale-100");
                                box.classList.add("scale-95");
                            }
                            document.getElementById('myModal4').addEventListener("click", function(e) {
                                if (e.target === this) {
                                    closeModal4();
                                }
                            });
                        </script>
                    </div>
                    
                    <hr class="my-2 border-1">

                    <table id="search-table4">
                        <thead>
                            <tr>
                                <th class="bg-black/90">
                                    <span class="flex items-center">Name<svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4"/></svg></span>
                                </th>

                                <th class="bg-black/90">
                                    <span class="flex items-center">Release Date<svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4"/></svg></span>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>Flowbite</td>
                                <td>2021/25/09</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="exp_div rounded-xl p-4 bg-black/20 border-2 border-white/60 hidden" id="div_5">
                    <div class="flex flex-row justify-between items-center">
                        <p class="text-2xl font-semibold">Trending</p>
                        <a href="" class="transition-all duration-100 ease-in-out rounded-xl border-2 border-blue-500 text-blue-500 font-semibold p-2 hover:bg-blue-500 hover:text-white">Add Content</a>
                    </div>
                    
                    <hr class="my-2 border-1">

                    <table id="search-table5">
                        <thead>
                            <tr>
                                <th class="bg-black/90">
                                    <span class="flex items-center">Name<svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4"/></svg></span>
                                </th>

                                <th class="bg-black/90">
                                    <span class="flex items-center">Release Date<svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4"/></svg></span>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>Flowbite</td>
                                <td>2021/25/09</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="exp_div rounded-xl p-4 bg-black/20 border-2 border-white/60 hidden" id="div_6">
                    <div class="flex flex-row justify-between items-center">
                        <p class="text-2xl font-semibold">Featured</p>
                        <a href="" class="transition-all duration-100 ease-in-out rounded-xl border-2 border-blue-500 text-blue-500 font-semibold p-2 hover:bg-blue-500 hover:text-white">Add Content</a>
                    </div>
                    
                    <hr class="my-2 border-1">

                    <table id="search-table6">
                        <thead>
                            <tr>
                                <th class="bg-black/90">
                                    <span class="flex items-center">Name<svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4"/></svg></span>
                                </th>

                                <th class="bg-black/90">
                                    <span class="flex items-center">Release Date<svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4"/></svg></span>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>Flowbite</td>
                                <td>2021/25/09</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <script>
        for(let i=1; i<=6; i++){
            const select_btn = document.getElementById('select_btn_'+i);
            const select_div = document.getElementById('div_'+i);

            select_btn.addEventListener('click', function() {
                document.querySelectorAll('.select_btn').forEach(x => x.classList.remove('bg-blue-500'));
                this.classList.add('bg-blue-500');

                document.querySelectorAll('.exp_div').forEach(y => y.classList.add('hidden'));
                select_div.classList.remove('hidden');
            });

            if (document.getElementById("search-table"+i) && typeof simpleDatatables.DataTable !== 'undefined') {
                const dataTable = new simpleDatatables.DataTable("#search-table"+i, {
                    searchable: true,
                    sortable: false
                });
            }
        }
    </script>
</body>
<style>
    #contents{
        border-bottom: 4px solid #3b82f6;;
    }
</style>
</html>