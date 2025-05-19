<?php if (!isset($no_footer)): ?>
    </main>
    
    <footer class="bg-white border-t mt-8">
        <div class="container mx-auto px-4 py-4 text-center text-gray-600 text-sm">
            &copy; <?= date('Y') ?> Bank Sampah - All Rights Reserved
        </div>
    </footer>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        // Profile dropdown toggle
        $(document).ready(function() {
            $('#profileDropdownBtn').click(function() {
                $('#profileDropdown').toggleClass('hidden');
            });
            
            // Close dropdown when clicking outside
            $(document).click(function(event) {
                if (!$(event.target).closest('#profileDropdownBtn, #profileDropdown').length) {
                    $('#profileDropdown').addClass('hidden');
                }
            });
        });
        // Toggle dropdown profile
    document.getElementById('userDropdown').addEventListener('click', function() {
        document.getElementById('dropdownContent').classList.toggle('hidden');
    });

    // Close dropdown ketika klik di luar
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#userDropdown') && !e.target.closest('#dropdownContent')) {
            document.getElementById('dropdownContent').classList.add('hidden');
        }
    });
    </script>
</body>
</html>
<?php endif; ?>