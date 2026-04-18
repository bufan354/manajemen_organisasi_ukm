<div class="p-8 max-w-7xl mx-auto w-full">
    <!-- Header Section -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="text-3xl font-extrabold text-on-surface tracking-tight">Semua <?= h($ENTITY) ?></h2>
            <p class="text-on-surface-variant mt-1">Kelola dan tinjau seluruh entitas <?= h($ENTITY) ?> yang terdaftar di sistem.</p>
        </div>
    </div>

    <?= renderFlash() ?>

    <!-- Quick Stats Bento -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-primary-container text-on-primary-container p-6 rounded-3xl relative overflow-hidden shadow-sm">
            <div class="relative z-10">
                <p class="text-xs font-black uppercase tracking-[0.1em] opacity-80 mb-2">Total <?= h($ENTITY) ?> Terdaftar</p>
                <h3 class="text-5xl font-black"><?= count($ukmList ?? []) ?></h3>
            </div>
            <span class="material-symbols-outlined absolute -right-4 -bottom-6 text-8xl opacity-10 rotate-12" style="font-variation-settings: 'FILL' 1;">hub</span>
        </div>
        <div class="bg-secondary-container text-on-secondary-container p-6 rounded-3xl relative overflow-hidden shadow-sm">
            <div class="relative z-10">
                <p class="text-xs font-black uppercase tracking-[0.1em] opacity-80 mb-2"><?= h($ENTITY) ?> Aktif</p>
                <h3 class="text-5xl font-black"><?= count($ukmList ?? []) ?></h3>
            </div>
            <span class="material-symbols-outlined absolute -right-4 -bottom-6 text-8xl opacity-10 rotate-12" style="font-variation-settings: 'FILL' 1;">check_circle</span>
        </div>
        <div class="bg-surface-container-high text-on-surface p-6 rounded-3xl relative overflow-hidden shadow-sm flex flex-col justify-center">
            <div class="relative z-10 border-l-4 border-primary pl-4">
                <h3 class="text-lg font-bold">Akses Superadmin</h3>
                <p class="text-sm font-medium text-on-surface-variant mt-1">Pilih salah satu <?= h($ENTITY) ?> di bawah untuk masuk ke mode konfigurasi khusus (Override Mode).</p>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="flex items-center justify-between gap-4 mb-6">
        <div class="relative flex-1 max-w-md">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">search</span>
            <input class="w-full bg-white border-none pl-12 pr-4 py-3 rounded-xl text-sm font-medium text-on-surface shadow-sm focus:ring-2 focus:ring-primary transition-all" placeholder="Cari nama <?= h($ENTITY) ?> atau Kategori..." type="text"/>
        </div>
    </div>

    <!-- Grid List UKM -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($ukmList)): ?>
        <div class="col-span-full text-center py-12 text-on-surface-variant">
            <span class="material-symbols-outlined text-4xl text-outline mb-2 block">corporate_fare</span>
            Belum ada <?= h($ENTITY) ?> terdaftar.
        </div>
        <?php else: foreach ($ukmList as $ukm): ?>
        <div class="flex flex-col bg-surface-container-lowest border border-outline-variant/10 rounded-3xl p-6 shadow-xl shadow-slate-200/50 hover:shadow-primary/10 hover:border-primary/30 transition-all duration-300 relative overflow-hidden h-full">
            <div class="flex-1">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center overflow-hidden border border-slate-200">
                        <?php if (!empty($ukm['logo_path'])): ?>
                            <img class="w-full h-full object-cover" src="<?= htmlspecialchars($ukm['logo_path']) ?>" alt="Logo <?= h($ENTITY) ?>">
                        <?php else: ?>
                            <img class="w-full h-full object-cover" src="https://ui-avatars.com/api/?name=<?= urlencode($ukm['singkatan'] ?? $ukm['nama']) ?>&background=0D8ABC&color=fff&size=128" alt="Logo <?= h($ENTITY) ?>">
                        <?php endif; ?>
                    </div>
                    <?php if (($ukm['status'] ?? 'aktif') === 'aktif'): ?>
                    <span class="px-2.5 py-1 bg-secondary-container/50 text-on-secondary-container text-[10px] font-bold uppercase rounded-full tracking-wider border border-secondary-container">
                        Aktif
                    </span>
                    <?php else: ?>
                    <span class="px-2.5 py-1 bg-error-container/50 text-error text-[10px] font-bold uppercase rounded-full tracking-wider border border-error-container">
                        Nonaktif
                    </span>
                    <?php endif; ?>
                </div>
                
                <h3 class="text-xl font-bold text-on-surface mb-1"><?= htmlspecialchars($ukm['singkatan'] ?? $ukm['nama']) ?></h3>
                <p class="text-sm font-medium text-on-surface-variant mb-6"><?= htmlspecialchars(mb_substr($ukm['deskripsi'] ?? '', 0, 80)) ?><?= mb_strlen($ukm['deskripsi'] ?? '') > 80 ? '...' : '' ?></p>
            </div>
            
            <div class="flex items-center justify-between gap-2 text-sm border-t border-surface-container pt-4 mt-auto">
                <a href="index.php?page=profil&ukm_id=<?= $ukm['id'] ?>" class="flex-1 py-2 bg-primary/10 text-primary font-bold rounded-xl text-center hover:bg-primary hover:text-white transition-colors text-xs flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">settings</span> Konfigurasi
                </a>
                <a href="index.php?page=kelola_periode&ukm_id=<?= $ukm['id'] ?>" class="flex-1 py-2 bg-amber-50 text-amber-700 font-bold rounded-xl text-center hover:bg-amber-600 hover:text-white transition-colors text-xs flex items-center justify-center gap-1 border border-amber-200">
                    <span class="material-symbols-outlined text-[14px]">history</span> Kelola Periode
                </a>

                <div class="flex gap-2 border-l border-outline-variant/30 pl-2">
                    <form action="index.php?action=ukm_toggle_status" method="POST" class="inline m-0">
    <?= csrf_field() ?>
                        
                        <input type="hidden" name="id" value="<?= $ukm['id'] ?>">
                        <?php if (($ukm['status'] ?? 'aktif') === 'aktif'): ?>
                        <button type="submit" class="w-8 h-8 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center hover:bg-orange-600 hover:text-white transition-colors" title="Nonaktifkan" onclick="return confirm('Nonaktifkan <?= h($ENTITY) ?> ini? <?= h($ENTITY) ?> tidak akan tampil di halaman publik.')">
                            <span class="material-symbols-outlined text-[16px]">visibility_off</span>
                        </button>
                        <?php else: ?>
                        <button type="submit" class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-colors" title="Aktifkan" onclick="return confirm('Aktifkan <?= h($ENTITY) ?> ini kembali?')">
                            <span class="material-symbols-outlined text-[16px]">visibility</span>
                        </button>
                        <?php endif; ?>
                    </form>

                    <form action="index.php?action=ukm_delete" method="POST" class="inline m-0">
    <?= csrf_field() ?>
                        
                        <input type="hidden" name="id" value="<?= $ukm['id'] ?>">
                        <button type="submit" class="w-8 h-8 rounded-xl bg-error/10 text-error flex items-center justify-center hover:bg-error hover:text-white transition-colors" title="Hapus (Soft Delete)" onclick="return confirm('Anda yakin ingin menghapus <?= h($ENTITY) ?> ini? Data masih tersimpan secara soft-delete di database.')">
                            <span class="material-symbols-outlined text-[16px]">delete</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; endif; ?>

        <!-- Add New UKM Card placeholder -->
        <a href="index.php?page=tambah_ukm" class="block bg-surface-container/30 border-2 border-dashed border-outline-variant/30 rounded-3xl p-6 hover:bg-surface-container hover:border-primary/50 transition-all duration-300 group cursor-pointer flex flex-col items-center justify-center min-h-[250px]">
            <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center mb-4 shadow-sm group-hover:scale-110 transition-transform text-outline group-hover:text-primary">
                <span class="material-symbols-outlined text-3xl">add</span>
            </div>
            <h3 class="text-lg font-bold text-on-surface-variant group-hover:text-primary transition-colors">Daftarkan <?= h($ENTITY) ?></h3>
            <p class="text-xs font-medium text-slate-400 mt-1 text-center">Buat entitas <?= h($ENTITY) ?> baru ke dalam sistem absensi</p>
        </a>
    </div>
</div>
