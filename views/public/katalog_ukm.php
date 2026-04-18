<div class="max-w-7xl mx-auto px-6 py-16">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 border-b border-slate-200/50 pb-8">
        <div>
            <span class="text-xs font-bold text-primary uppercase tracking-[0.2em] mb-2 block">PLATFORM DIGITAL IOT</span>
            <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tighter">SEMUA <?= h(mb_strtoupper($ENTITY)) ?></h1>
        </div>
        <p class="text-slate-600 max-w-md mt-4 md:mt-0 text-sm leading-relaxed">
            Pantau aktivitas dan absensi real-time seluruh <?= h($ENTITY) ?> yang terintegrasi dengan ekosistem IoT cerdas kami.
        </p>
    </div>

    <!-- Grid List UKM -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($ukmList)): ?>
        <div class="col-span-full text-center py-16 text-slate-400">
            <span class="material-symbols-outlined text-5xl mb-4 block">corporate_fare</span>
            <p class="text-lg font-bold">Belum ada <?= h($ENTITY) ?> terdaftar</p>
            <p class="text-sm mt-1">Hubungi administrator untuk mendaftarkan <?= h($ENTITY) ?>.</p>
        </div>
        <?php else: foreach ($ukmList as $ukm): ?>
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex flex-col transition-all hover:shadow-xl hover:border-blue-100 group">
            <div class="flex justify-between items-start mb-6">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center overflow-hidden">
                    <?php if (!empty($ukm['logo_path'])): ?>
                        <img src="<?= htmlspecialchars($ukm['logo_path']) ?>" class="w-full h-full object-cover" alt="">
                    <?php else: ?>
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">groups</span>
                    <?php endif; ?>
                </div>
                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-[10px] font-bold uppercase rounded-full">Aktif</span>
            </div>
            
            <h3 class="text-xl font-bold text-slate-900 mb-2"><?= htmlspecialchars($ukm['singkatan'] ?? $ukm['nama']) ?></h3>
            <p class="text-sm text-slate-500 mb-6 flex-1">
                <?= htmlspecialchars(mb_substr($ukm['deskripsi'] ?? 'Organisasi mahasiswa terintegrasi IoT.', 0, 100)) ?><?= mb_strlen($ukm['deskripsi'] ?? '') > 100 ? '...' : '' ?>
            </p>
            
            <div class="flex items-center gap-4 mb-6">
                <span class="text-xs text-slate-400 font-medium"><?= htmlspecialchars($ukm['kategori'] ?? '-') ?></span>
            </div>
            
            <a href="index.php?page=detail_ukm&id=<?= $ukm['id'] ?>" class="w-full py-3 bg-slate-50 text-slate-700 font-bold rounded-xl text-sm text-center group-hover:bg-primary group-hover:text-white transition-colors block">Detail</a>
        </div>
        <?php endforeach; endif; ?>
    </div>
</div>
