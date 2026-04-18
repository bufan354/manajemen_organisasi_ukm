<div class="p-8 max-w-7xl mx-auto w-full">

<div class="mb-8 flex lg:flex-row flex-col lg:justify-between lg:items-center gap-4">
    <div>
        <h2 class="text-2xl font-black tracking-tight text-on-surface">Manajemen Periode Kepengurusan</h2>
        <p class="text-sm font-medium text-on-surface-variant mt-1">Mengelola siklus periode aktif untuk <?= h($ENTITY) ?> <?= htmlspecialchars($ukm['nama']) ?></p>
    </div>
    
    <div class="flex gap-3">
        <a href="index.php?page=ukm" class="px-5 py-2.5 bg-surface-container-high hover:bg-surface-variant text-on-surface font-bold text-xs uppercase tracking-wider rounded-xl transition-all shadow-sm active:scale-95 flex items-center gap-2 border border-outline-variant/30">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kembali
        </a>
        <button onclick="document.getElementById('modalTambah').classList.remove('hidden')" class="px-5 py-2.5 bg-primary hover:bg-primary-container text-on-primary hover:text-on-primary-container font-bold text-xs uppercase tracking-wider rounded-xl transition-all shadow-md shadow-primary/20 active:scale-95 flex items-center gap-2">
            <span class="material-symbols-outlined text-[16px]">add</span> Tambah Periode
        </button>
    </div>
</div>

<?php if (isset($_SESSION['flash_success'])): ?>
    <div class="bg-success/10 border border-success/20 text-success px-4 py-3 rounded-xl mb-6 flex items-center gap-3">
        <span class="material-symbols-outlined">check_circle</span>
        <p class="text-sm font-bold"><?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></p>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['flash_error'])): ?>
    <div class="bg-error/10 border border-error/20 text-error px-4 py-3 rounded-xl mb-6 flex items-center gap-3">
        <span class="material-symbols-outlined">error</span>
        <p class="text-sm font-bold"><?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></p>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['flash_info'])): ?>
    <div class="bg-secondary/10 border border-secondary/20 text-secondary px-4 py-3 rounded-xl mb-6 flex items-center gap-3">
        <span class="material-symbols-outlined">info</span>
        <p class="text-sm font-bold"><?= $_SESSION['flash_info']; unset($_SESSION['flash_info']); ?></p>
    </div>
<?php endif; ?>

<div class="bg-white rounded-3xl shadow-sm border border-surface-container overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low border-b border-outline-variant/20 text-on-surface-variant text-[11px] uppercase tracking-widest">
                    <th class="p-5 font-black">Status</th>
                    <th class="p-5 font-black">Nama Periode</th>
                    <th class="p-5 font-black">Tahun Berlaku</th>
                    <th class="p-5 font-black">Deskripsi</th>
                    <th class="p-5 font-black w-1/4">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10 text-sm">
                <?php if (empty($periodeList)): ?>
                <tr>
                    <td colspan="5" class="p-12 text-center text-on-surface-variant">
                        <span class="material-symbols-outlined text-6xl mb-4 block opacity-20">history</span>
                        <p class="font-medium text-lg">Belum Ada Periode</p>
                        <p class="text-xs mt-1">Belum ada periode kepengurusan untuk <?= h($ENTITY) ?> ini.</p>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($periodeList as $pr): ?>
                    <tr class="hover:bg-surface-container-lowest transition-colors <?= $pr['is_active'] ? 'bg-primary/5' : '' ?>">
                        <td class="p-5">
                            <?php if ($pr['is_active']): ?>
                                <span class="inline-flex items-center gap-1.5 bg-primary/10 text-primary px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider border border-primary/20">
                                    <span class="material-symbols-outlined text-[14px]">check_circle</span> Aktif
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 bg-surface-container-high text-on-surface-variant px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider border border-outline-variant/30">
                                    <span class="material-symbols-outlined text-[14px]">history</span> Riwayat
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="p-5 font-bold text-on-surface text-base"><?= htmlspecialchars($pr['nama']) ?></td>
                        <td class="p-5">
                            <span class="bg-surface-container-high px-2.5 py-1 rounded-md text-xs font-mono font-bold text-on-surface-variant">
                                <?= htmlspecialchars($pr['tahun_mulai']) ?> - <?= htmlspecialchars($pr['tahun_selesai']) ?>
                            </span>
                        </td>
                        <td class="p-5 text-on-surface-variant/80 text-xs italic max-w-xs truncate">
                            <?= htmlspecialchars($pr['deskripsi'] ?? '-') ?>
                        </td>
                        <td class="p-5 flex gap-2">
                            <?php if (!$pr['is_active']): ?>
                            <form action="index.php?action=periode_set_active" method="POST" class="inline" onsubmit="return confirm('Jadikan periode ini sebagai yang aktif untuk publik?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= $pr['id'] ?>">
                                <input type="hidden" name="ukm_id" value="<?= $ukm['id'] ?>">
                                <button type="submit" class="px-3 py-2 bg-success/10 text-success hover:bg-success hover:text-on-primary rounded-xl font-bold text-[10px] uppercase tracking-wider border border-success/20 transition-all flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[14px]">check</span> Set Aktif
                                </button>
                            </form>
                            <?php endif; ?>
                            
                            <button onclick='editPeriode(<?= json_encode($pr) ?>)' class="px-3 py-2 bg-secondary-container/50 text-on-secondary-container hover:bg-secondary-container rounded-xl font-bold text-[10px] uppercase tracking-wider border border-secondary-container transition-all flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[14px]">edit</span> Edit
                            </button>
                            
                            <form action="index.php?action=periode_delete" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus periode ini? Periode yang sudah ada isinya akan kehilangan datanya (Cascading Delete). Lanjutkan?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= $pr['id'] ?>">
                                <input type="hidden" name="ukm_id" value="<?= $ukm['id'] ?>">
                                <button type="submit" class="px-3 py-2 bg-error/10 text-error hover:bg-error hover:text-white rounded-xl font-bold text-[10px] uppercase tracking-wider border border-error/20 transition-all flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[14px]">delete</span> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Periode -->
<div id="modalTambah" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex justify-center items-center">
    <div class="relative p-8 border w-[450px] shadow-2xl rounded-3xl bg-white transform transition-all">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-black tracking-tight text-on-surface">Tambah Periode Baru</h3>
            <button onclick="document.getElementById('modalTambah').classList.add('hidden')" class="w-8 h-8 flex items-center justify-center rounded-full bg-surface-container hover:bg-surface-variant text-on-surface-variant transition-colors">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
        <form action="index.php?action=periode_store" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="ukm_id" value="<?= $ukm['id'] ?>">
            
            <div class="mb-5">
                <label class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Nama Periode</label>
                <input type="text" name="nama" required placeholder="Contoh: Kabinet Inovasi" class="w-full bg-surface-container-highest border-none rounded-xl px-4 py-3 text-sm focus:ring-0 focus:border-b-2 border-b-2 border-transparent focus:border-b-primary transition-all">
            </div>
            <div class="grid grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Tahun Mulai</label>
                    <input type="number" name="tahun_mulai" required value="<?= date('Y') ?>" class="w-full bg-surface-container-highest border-none rounded-xl px-4 py-3 text-sm focus:ring-0 focus:border-b-2 border-b-2 border-transparent focus:border-b-primary transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Tahun Selesai</label>
                    <input type="number" name="tahun_selesai" required value="<?= date('Y') + 1 ?>" class="w-full bg-surface-container-highest border-none rounded-xl px-4 py-3 text-sm focus:ring-0 focus:border-b-2 border-b-2 border-transparent focus:border-b-primary transition-all">
                </div>
            </div>
            <div class="mb-6">
                <label class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Deskripsi Visi / Tagline (Opsional)</label>
                <textarea name="deskripsi" rows="3" placeholder="Deskripsi singkat arah gerak periode..." class="w-full bg-surface-container-highest border-none rounded-xl px-4 py-3 text-sm focus:ring-0 focus:border-b-2 border-b-2 border-transparent focus:border-b-primary transition-all"></textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="w-full px-6 py-3 bg-primary text-on-primary rounded-xl hover:bg-primary-container hover:text-on-primary-container font-black uppercase tracking-wider text-[11px] shadow-md shadow-primary/20 transition-all active:scale-95 border border-primary/20">Buat Periode</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Periode -->
<div id="modalEdit" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex justify-center items-center">
    <div class="relative p-8 border w-[450px] shadow-2xl rounded-3xl bg-white transform transition-all">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-black tracking-tight text-on-surface">Edit Periode</h3>
            <button onclick="document.getElementById('modalEdit').classList.add('hidden')" class="w-8 h-8 flex items-center justify-center rounded-full bg-surface-container hover:bg-surface-variant text-on-surface-variant transition-colors">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
        <form action="index.php?action=periode_update" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="edit_id">
            <input type="hidden" name="ukm_id" value="<?= $ukm['id'] ?>">
            
            <div class="mb-5">
                <label class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Nama Periode</label>
                <input type="text" name="nama" id="edit_nama" required class="w-full bg-surface-container-highest border-none rounded-xl px-4 py-3 text-sm focus:ring-0 focus:border-b-2 border-b-2 border-transparent focus:border-b-primary transition-all">
            </div>
            <div class="grid grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Tahun Mulai</label>
                    <input type="number" name="tahun_mulai" id="edit_tahun_mulai" required class="w-full bg-surface-container-highest border-none rounded-xl px-4 py-3 text-sm focus:ring-0 focus:border-b-2 border-b-2 border-transparent focus:border-b-primary transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Tahun Selesai</label>
                    <input type="number" name="tahun_selesai" id="edit_tahun_selesai" required class="w-full bg-surface-container-highest border-none rounded-xl px-4 py-3 text-sm focus:ring-0 focus:border-b-2 border-b-2 border-transparent focus:border-b-primary transition-all">
                </div>
            </div>
            <div class="mb-6">
                <label class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Deskripsi</label>
                <textarea name="deskripsi" id="edit_deskripsi" rows="3" class="w-full bg-surface-container-highest border-none rounded-xl px-4 py-3 text-sm focus:ring-0 focus:border-b-2 border-b-2 border-transparent focus:border-b-primary transition-all"></textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="w-full px-6 py-3 bg-secondary-container text-on-secondary-container rounded-xl hover:bg-secondary transition-all font-black uppercase tracking-wider text-[11px] shadow-md border border-secondary/20">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function editPeriode(pr) {
    document.getElementById('edit_id').value = pr.id;
    document.getElementById('edit_nama').value = pr.nama;
    document.getElementById('edit_tahun_mulai').value = pr.tahun_mulai;
    document.getElementById('edit_tahun_selesai').value = pr.tahun_selesai;
    document.getElementById('edit_deskripsi').value = pr.deskripsi || '';
    document.getElementById('modalEdit').classList.remove('hidden');
}
</script>

</div>
