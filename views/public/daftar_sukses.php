<nav class="bg-white border-b border-slate-200 w-full py-4 px-6 z-50 sticky top-0">
    <div class="max-w-4xl mx-auto flex items-center justify-center">
        <a class="flex items-center gap-1 text-slate-500 hover:text-blue-500 transition-colors shrink-0" href="index.php?page=home">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span class="text-sm font-bold uppercase tracking-wider">Kembali</span>
        </a>
        <div class="font-black text-xl text-slate-900 tracking-tight"><?= h($APP_NAME) ?></div>
    </div>
</nav>

<main class="min-h-[calc(100vh-65px)] bg-slate-50 py-12 px-6 flex items-center justify-center">
    <div class="max-w-xl w-full">
        <!-- Success Container -->
        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200/60 overflow-hidden relative">
            <div class="absolute top-0 left-0 right-0 h-2 bg-emerald-500"></div>
            <div class="p-8 lg:p-16 text-center flex flex-col items-center justify-center">
                <div class="w-24 h-24 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mb-6 ring-8 ring-emerald-50">
                    <span class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                </div>
                <h3 class="text-3xl font-black text-slate-900 mb-4">Pendaftaran Berhasil Dirancang!</h3>
                <?= renderFlash() ?>
                <p class="text-slate-600 max-w-md mx-auto leading-relaxed mt-2 mb-8">Data pendaftaran Anda telah diserahkan. Silakan tunggu verifikasi lebih lanjut dari pihak Admin <?= h($ENTITY) ?>. Kami mungkin akan menghubungi Anda ke nomor WhatsApp yang terdaftar.</p>
                <a href="index.php?page=katalog_ukm" class="w-full sm:w-auto px-8 py-4 bg-emerald-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-200 hover:bg-emerald-700 active:scale-95 transition-all text-center">
                    Tutup dan Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</main>
