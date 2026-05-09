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