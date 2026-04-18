</main>

<!-- Global Footer -->
<footer class="bg-slate-50 dark:bg-slate-950 border-t border-slate-200/15 w-full mt-12">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 w-full px-8 py-12 max-w-7xl mx-auto">
        <!-- Brand Info -->
        <div class="flex flex-col gap-4 md:col-span-2 pr-8">
            <div class="font-bold text-slate-900 dark:text-slate-100 text-lg"><?= h($APP_NAME) ?></div>
            <p class="text-xs text-slate-500 leading-relaxed max-w-sm">
                Editorial IoT Management platform dirancang untuk memberikan transparansi data absensi dan aktivitas organisasi secara real-time dengan sentuhan desain modern.
            </p>
            <div class="flex gap-4 mt-2">
                <a class="text-slate-400 hover:text-blue-500 transition-colors" href="#"><span class="material-symbols-outlined text-xl">language</span></a>
                <a class="text-slate-400 hover:text-blue-500 transition-colors" href="#"><span class="material-symbols-outlined text-xl">share</span></a>
            </div>
        </div>
        
        <!-- Quick Links -->
        <div class="flex flex-col gap-4">
            <div class="text-[10px] font-bold uppercase tracking-widest text-slate-900">Navigasi</div>
            <nav class="flex flex-col gap-3">
                <a class="text-slate-500 hover:text-blue-500 text-xs transition-colors" href="index.php?page=home">Beranda</a>
                <a class="text-slate-500 hover:text-blue-500 text-xs transition-colors" href="index.php?page=katalog_ukm"><?= h(getEntityLabel()) ?></a>
                <a class="text-slate-500 hover:text-blue-500 text-xs transition-colors" href="index.php?page=tentang">Tentang Kami</a>
            </nav>
        </div>
        
        <!-- Legal & Links -->
        <div class="flex flex-col gap-4">
            <div class="text-[10px] font-bold uppercase tracking-widest text-slate-900">Legal</div>
            <nav class="flex flex-col gap-3">
                <a class="text-slate-500 hover:text-blue-500 text-xs transition-colors" href="#">Privacy Policy</a>
                <a class="text-slate-500 hover:text-blue-500 text-xs transition-colors" href="#">Documentation</a>
            </nav>
        </div>
    </div>
    
    <div class="border-t border-slate-200/50 py-6 px-8 flex justify-between items-center max-w-7xl mx-auto flex-col md:flex-row gap-4">
         <div class="flex items-center gap-2 text-secondary font-semibold text-xs transition-colors duration-300 hover:text-primary cursor-default mr-auto">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-secondary opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-secondary"></span>
                </span>
                Sistem Online
            </div>
            
         <div class="flex gap-4">
             <a class="text-slate-400 hover:text-blue-500 text-xs transition-colors" href="#">Instagram</a>
             <a class="text-slate-400 hover:text-blue-500 text-xs transition-colors" href="#">LinkedIn</a>
         </div>
        <p class="text-[10px] text-slate-400">© <?= date('Y') ?> <?= h($APP_NAME) ?>. Editorial Bufan Fadhilah.</p>
    </div>
</footer>

<script src="assets/public/js/footer.js"></script>

</body>
</html>
