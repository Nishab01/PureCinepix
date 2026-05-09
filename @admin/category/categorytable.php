<table id="search-table3">
    <thead>
        <tr>
            <th class="bg-black">
                <span class="flex items-center">ID<svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4"/></svg></span>
            </th>
            <th class="bg-black">
                <span class="flex items-center">Name<svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4"/></svg></span>
            </th>
            <th class="bg-black">
                <span class="flex items-center">Slug<svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4"/></svg></span>
            </th>
            <th class="bg-black">
                <span class="flex items-center">Status<svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4"/></svg></span>
            </th>
            <th class="bg-black">
                <span class="flex items-center">Created<svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4"/></svg></span>
            </th>
            <th class="bg-black">
                <span class="flex items-center">Action<svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4"/></svg></span>
            </th>
        </tr>
    </thead>

    <tbody>
        <?php
            $category_query = "SELECT * FROM types WHERE `section` = 'category' ORDER BY `id` DESC;";
            $categoryrun = $conn->query($category_query);
            if($categoryrun->num_rows > 0){
                while($category = $categoryrun->fetch_assoc()){
                    ?>
                    <tr>
                        <td><?php echo $category['id']; ?></td>
                        <td><?php echo $category['name']; ?></td>
                        <td class="italic"><?php echo $category['slug']; ?></td>
                        <td class="text-center">
                            <?php
                                if($category['status'] == 1){
                                    ?><p class="w-full inline-block px-3 py-1 text-sm font-semibold text-green-400 bg-green-500/20 rounded-full">Active</p><?php
                                }
                                else{
                                    ?><p class="w-full inline-block px-3 py-1 text-sm font-semibold text-red-500 bg-red-500/20 rounded-full">Inactive</p><?php
                                }
                            ?>
                        </td>
                        <td><?php echo date("j-M-Y g:i:sA", strtotime($category['created_at'])); ?></td>
                        <td class="flex items-center justify-start gap-1">
                            <button onclick="openEditModal<?php echo $category['id']; ?>()" class="transition-all duration-100 ease-in-out p-2 rounded-xl bg-blue-500 hover:bg-blue-700"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen-icon lucide-square-pen"><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"/></svg></button>

                            <a class="transition-all duration-100 ease-in-out p-2 rounded-xl bg-red-500 hover:bg-red-700" href="./category/category_delete.php?slug=<?php echo $category['slug']; ?>" onclick="return confirm('Are you sure you want to delete this category?');"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2-icon lucide-trash-2"><path d="M10 11v6"/><path d="M14 11v6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></a>
                        </td>
                    </tr>

                    <div id="editModal<?php echo $category['id']; ?>" class="fixed inset-0 w-full flex items-center justify-center bg-black/80 opacity-0 pointer-events-none transition-opacity duration-300">
                        <div id="editmodalBox<?php echo $category['id']; ?>" class="bg-gray-800 p-6 rounded-xl shadow-lg transform scale-95 transition-all duration-100 w-2/6">
                            <h2 class="text-2xl font-semibold pb-2 mb-2 border-b border-gray-500">Edit Category</h2>
                            
                            <form action="./category/category_update.php" method="POST" class="w-full grid grid-cols-12 gap-4">
                                <input class="w-full rounded-xl text-black" type="text" name="dataid" id="dataid" value="<?php echo $category['id']; ?>" required readonly hidden>

                                <div class="col-span-12 grid grid-cols-12 gap-2 text-left mb-2">
                                    <div class="col-span-8">
                                        <label class="font-bold block" for="name">Name: <span class="font-bold text-red-500">*</span></label>
                                        <input class="w-full rounded-xl text-black" type="text" name="name" id="name" placeholder="Type category name" value="<?php echo $category['name']; ?>" required>
                                    </div>

                                    <div class="col-span-4">
                                        <label class="font-bold block" for="status">Status: <span class="font-bold text-red-500">*</span></label>
                                        <select class="w-full rounded-xl text-black" name="status" id="status" required>
                                            <option class="text-black" value="<?php echo $category['status'];?>" selected hidden><?php echo ($category['status'] == 1) ? "Active" : "Inactive"; ?></option>
                                            <option class="text-black" value="1">Active</option>
                                            <option class="text-black" value="0">Inactive</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-span-12 text-left mb-2 flex flex-col items-center">
                                    <label class="w-full font-bold block" for="banner">Banner:</label>
                                    <?php
                                        if($category['banner'] == NULL){
                                            ?><p class="text-sm text-gray-200 italic">No banner found</p><?php
                                        }
                                        else{
                                            ?><img class="w-auto h-48 rounded-xl" src="<?php echo $category['banner']; ?>" alt=""><?php
                                        }
                                    ?>
                                    <input class="w-full mt-1 w-full rounded-xl text-black" type="text" name="banner" id="banner" placeholder="Enter category banner URL" value="<?php echo $category['banner']; ?>">
                                    <p class="w-full mt-1 text-xs text-gray-200">Recommended image width 1920px</p>
                                </div>

                                <div class="w-full text-left flex flex-col gap-2">
                                    <input class="transition-all duration-100 ease-in-out w-full h-10 cursor-pointer flex items-center justify-center bg-blue-500 text-white rounded-xl font-semibold hover:bg-blue-700" type="submit" name="update" id="update" value="Update">

                                    <a href="#" onclick="closeEditModal<?php echo $category['id']; ?>()" class="transition-all duration-100 ease-in-out h-10 cursor-pointer flex items-center justify-center bg-red-500 text-white rounded-xl font-semibold hover:bg-red-700">Close</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <script>
                        function openEditModal<?php echo $category['id']; ?>() {
                            const modal = document.getElementById('editModal<?php echo $category['id']; ?>');
                            const box = document.getElementById('editmodalBox<?php echo $category['id']; ?>');

                            modal.classList.remove("opacity-0", "pointer-events-none");
                            box.classList.remove("scale-95");
                            box.classList.add("scale-100");
                        }
                        function closeEditModal<?php echo $category['id']; ?>() {
                            const modal = document.getElementById('editModal<?php echo $category['id']; ?>');
                            const box = document.getElementById('editmodalBox<?php echo $category['id']; ?>');

                            modal.classList.add("opacity-0", "pointer-events-none");
                            box.classList.remove("scale-100");
                            box.classList.add("scale-95");
                        }
                        document.getElementById('editModal<?php echo $category['id']; ?>').addEventListener("click", function(e) {
                            if (e.target === this) {
                                closeEditModal<?php echo $category['id']; ?>();
                            }
                        });
                    </script>
                    
                    <?php
                }
                ?>
                <?php
            }
        ?>
    </tbody>
</table>