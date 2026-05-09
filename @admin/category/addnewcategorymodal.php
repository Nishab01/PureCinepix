<div id="myModal3" class="fixed inset-0 w-full flex items-center justify-center bg-black/80 opacity-0 pointer-events-none transition-opacity duration-300">
    <div id="modalBox3" class="bg-gray-800 p-6 rounded-xl shadow-lg transform scale-95 transition-all duration-100 w-2/6">
        <h2 class="text-2xl font-semibold pb-2 mb-2 border-b border-gray-500">Add New Category</h2>
        
        <form action="./category/category_add.php" method="POST" class="grid grid-cols-12 gap-2 w-full space-y-1">
            <div class="w-full col-span-12 text-left space-y-1">
                <label class="font-bold block" for="name">Name: <span class="font-bold text-red-500">*</span></label>
                <input class="w-full rounded-xl text-black" type="text" name="name" id="name" placeholder="Type category name" required>
            </div>

            <div class="w-full col-span-12 text-left space-y-1">
                <label class="font-bold block" for="status">Status: <span class="font-bold text-red-500">*</span></label>
                <select class="w-full rounded-xl text-black" name="status" id="status" required>
                    <option class="text-black" value="1" selected>Active</option>
                    <option class="text-black" value="0">Inactive</option>
                </select>
            </div>

            <div class="w-full col-span-12 text-left space-y-1">
                <label class="font-semibold block" for="banner">Banner:</label>
                <input class="w-full rounded-xl text-black" type="text" name="banner" id="banner" placeholder="Enter category banner URL">
                <p class="text-xs text-gray-200">Recommended image width 1920px</p>
            </div>

            <div class="w-full col-span-12 grid grid-cols-12 gap-2">
                <input class="col-span-6 h-10 cursor-pointer flex items-center justify-center bg-blue-500 text-white rounded-xl font-semibold hover:bg-blue-700" type="submit" name="submit" id="submit" value="Submit">

                <a href="#" onclick="closeModal3()" class="col-span-6 h-10 cursor-pointer flex items-center justify-center bg-red-500 text-white rounded-xl font-semibold hover:bg-red-700">Close</a>
            </div>
        </form>
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