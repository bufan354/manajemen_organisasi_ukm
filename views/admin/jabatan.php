<?php
/**
 * View: Kelola Jabatan Kustom
 * page=jabatan
 * Variabel yang dipass dari router:
 *   $ukm            — data UKM (jika superadmin+ukm_id dipilih)
 *   $jabatanList    — jabatan kustom milik UKM
 *   $ukmList        — semua UKM (hanya superadmin)
 *   $selectedUkmId  — UKM yang sedang dipilih
 */
$isSuperAdmin  = (Session::get('admin_role') === 'superadmin');
$ukmNama       = $ukm['nama'] ?? (Session::get('ukm_nama') ?? $ENTITY . ' Anda');
$selectedUkmId = $selectedUkmId ?? 0;

$levelOptions = [
    2 => 'Level BPH (Ketua / Wakil)',
    3 => 'Level BPH Lanjut (Sekret/Bend)',
    4 => 'Level Koordinator / Divisi',
    5 => 'Level Staf / Anggota',
];
?>
<div class="p-8 max-w-7xl mx-auto w-full">

    <!-- Header -->
    <div class="mb-8 flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="index.php?page=anggota" class="w-10 h-10 rounded-full flex items-center justify-center bg-white border border-outline-variant hover:bg-surface transition-colors">
                <span class="material-symbols-outlined text-outline">arrow_back</span>
            </a>
            <div>
                <h2 class="text-3xl font-bold tracking-tight text-on-surface">Kelola Jabatan</h2>
                <p class="text-on-surface-variant body-md">Atur jabatan kustom yang tersedia untuk dropdown anggota.</p>
            </div>
        </div>

        <!-- Tombol Tambah -->
        <button onclick="openAddModal()" class="flex items-center gap-2 px-5 py-2.5 bg-primary text-white font-bold rounded-xl hover:bg-primary/90 transition-all shadow-lg shadow-primary/20 active:scale-95">
            <span class="material-symbols-outlined text-sm">add</span>
            Tambah Jabatan
        </button>
    </div>

    <?= renderFlash() ?>

    <!-- Superadmin: Pilih UKM -->
    <?php if ($isSuperAdmin): ?>
    <div class="mb-6 max-w-sm">
        <form method="GET" action="index.php">
            <input type="hidden" name="page" value="jabatan">
            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Pilih <?= h($ENTITY) ?></label>
            <div class="relative">
                <select name="ukm_id" onchange="this.form.submit()"
                        class="w-full px-4 py-3 bg-surface-container rounded-xl border border-outline-variant/40 text-sm font-medium appearance-none cursor-pointer focus:ring-2 focus:ring-primary/20">
                    <option value="">— Pilih <?= h($ENTITY) ?> —</option>
                    <?php foreach ($ukmList as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= ($selectedUkmId == $u['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($u['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant">expand_more</span>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <?php if ($isSuperAdmin && !$selectedUkmId): ?>
    <!-- Belum pilih UKM -->
    <div class="max-w-2xl mx-auto text-center py-24 text-on-surface-variant">
        <span class="material-symbols-outlined text-6xl mb-4 block opacity-30">badge</span>
        <p class="font-bold text-lg">Pilih <?= h($ENTITY) ?> terlebih dahulu</p>
        <p class="text-sm mt-1">Gunakan dropdown di atas untuk memilih <?= h($ENTITY) ?> yang ingin dikelola jabatannya.</p>
    </div>
    <?php else: ?>

    <!-- Card utama -->
    <div class="max-w-4xl bg-surface-container-lowest rounded-2xl shadow-[0_12px_40px_rgba(25,28,30,0.04)] overflow-hidden">

        <!-- Jabatan Standar (read-only) -->
        <div class="px-6 pt-6 pb-4 border-b border-outline-variant/20">
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-on-surface-variant mb-3">
                Jabatan Standar (tidak dapat diedit)
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2">
                <?php foreach (JabatanKustom::JABATAN_STANDAR as $std): ?>
                <div class="px-3 py-2 bg-surface-container rounded-xl border border-outline-variant/30 text-center">
                    <p class="text-xs font-bold text-on-surface"><?= htmlspecialchars($std['label']) ?></p>
                    <p class="text-[10px] text-on-surface-variant mt-0.5">Level <?= $std['level'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Jabatan Kustom -->
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-on-surface-variant">
                    Jabatan Kustom <?= $ukm ? '— ' . htmlspecialchars($ukmNama) : '' ?>
                </h3>
                <span class="text-xs text-on-surface-variant"><?= count($jabatanList) ?> jabatan</span>
            </div>

            <?php if (empty($jabatanList)): ?>
            <div class="text-center py-16 text-on-surface-variant">
                <span class="material-symbols-outlined text-5xl mb-3 block opacity-30">badge</span>
                <p class="font-bold">Belum ada jabatan kustom</p>
                <p class="text-sm mt-1">Klik <strong>"Tambah Jabatan"</strong> untuk membuat jabatan pertama.</p>
            </div>
            <?php else: ?>
            <div class="space-y-2" id="jabatan-list">
                <?php foreach ($jabatanList as $j): ?>
                <div class="flex items-center justify-between px-4 py-3.5 bg-surface-container rounded-xl hover:bg-surface-container-high transition-colors group"
                     id="jabatan-row-<?= $j['id'] ?>">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary text-sm">badge</span>
                        </div>
                        <div>
                            <p class="font-bold text-sm text-on-surface"><?= htmlspecialchars($j['nama_jabatan']) ?></p>
                            <p class="text-[10px] text-on-surface-variant">
                                <?= isset($levelOptions[$j['level']]) ? $levelOptions[$j['level']] : "Level {$j['level']}" ?>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button onclick="openEditModal(<?= $j['id'] ?>, '<?= htmlspecialchars(addslashes($j['nama_jabatan'])) ?>', <?= $j['level'] ?>)"
                                class="px-3 py-1.5 text-xs font-bold text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-colors flex items-center gap-1">
                            <span class="material-symbols-outlined text-xs">edit</span> Edit
                        </button>
                        <button onclick="confirmDelete(<?= $j['id'] ?>, '<?= htmlspecialchars(addslashes($j['nama_jabatan'])) ?>')"
                                class="px-3 py-1.5 text-xs font-bold text-error bg-error/10 rounded-lg hover:bg-error/20 transition-colors flex items-center gap-1">
                            <span class="material-symbols-outlined text-xs">delete</span> Hapus
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php endif; ?>
</div>

<!-- ====== Modal: Tambah Jabatan ====== -->
<div id="modal-add" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest rounded-2xl shadow-2xl w-full max-w-md p-6 animate-fade-in">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-on-surface">Tambah Jabatan Baru</h3>
            <button onclick="closeModal('modal-add')" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-surface-container transition-colors">
                <span class="material-symbols-outlined text-on-surface-variant">close</span>
            </button>
        </div>
        <form action="index.php?action=jabatan_store" method="POST" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="ukm_id" value="<?= $selectedUkmId ?: (int)Session::get('ukm_id') ?>">

            <div>
                <label class="block text-sm font-bold text-on-surface mb-2">Nama Jabatan <span class="text-error">*</span></label>
                <input type="text" name="nama_jabatan" id="add-nama"
                       placeholder="Misal: Koordinator Hardware, Kabid Humas..."
                       class="w-full px-4 py-3 bg-surface-container rounded-xl border border-outline-variant/40 focus:ring-2 focus:ring-primary/20 focus:outline-none text-sm font-medium"
                       required autofocus>
            </div>

            <div>
                <label class="block text-sm font-bold text-on-surface mb-2">Level Jabatan</label>
                <div class="relative">
                    <select name="level" class="w-full px-4 py-3 bg-surface-container rounded-xl border border-outline-variant/40 focus:ring-2 focus:ring-primary/20 focus:outline-none text-sm font-medium appearance-none cursor-pointer">
                        <?php foreach ($levelOptions as $val => $lbl): ?>
                            <option value="<?= $val ?>" <?= $val === 4 ? 'selected' : '' ?>><?= $lbl ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant text-base">expand_more</span>
                </div>
                <p class="text-[10px] text-on-surface-variant mt-1.5">Level menentukan posisi di bagan struktur organisasi.</p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-add')" class="flex-1 px-4 py-3 bg-surface-container-high text-on-surface font-bold rounded-xl hover:bg-surface-container-highest transition-colors text-sm">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primary/90 transition-all shadow-lg shadow-primary/20 active:scale-95 text-sm">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ====== Modal: Edit Jabatan ====== -->
<div id="modal-edit" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest rounded-2xl shadow-2xl w-full max-w-md p-6 animate-fade-in">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-on-surface">Edit Jabatan</h3>
            <button onclick="closeModal('modal-edit')" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-surface-container transition-colors">
                <span class="material-symbols-outlined text-on-surface-variant">close</span>
            </button>
        </div>
        <form action="index.php?action=jabatan_update" method="POST" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="edit-id">

            <div>
                <label class="block text-sm font-bold text-on-surface mb-2">Nama Jabatan <span class="text-error">*</span></label>
                <input type="text" name="nama_jabatan" id="edit-nama"
                       class="w-full px-4 py-3 bg-surface-container rounded-xl border border-outline-variant/40 focus:ring-2 focus:ring-primary/20 focus:outline-none text-sm font-medium"
                       required>
            </div>

            <div>
                <label class="block text-sm font-bold text-on-surface mb-2">Level Jabatan</label>
                <div class="relative">
                    <select name="level" id="edit-level" class="w-full px-4 py-3 bg-surface-container rounded-xl border border-outline-variant/40 focus:ring-2 focus:ring-primary/20 focus:outline-none text-sm font-medium appearance-none cursor-pointer">
                        <?php foreach ($levelOptions as $val => $lbl): ?>
                            <option value="<?= $val ?>"><?= $lbl ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant text-base">expand_more</span>
                </div>
                <p class="text-[10px] text-on-surface-variant mt-1.5">⚠ Mengganti nama jabatan akan otomatis memperbarui data semua anggota yang memiliki jabatan ini.</p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-edit')" class="flex-1 px-4 py-3 bg-surface-container-high text-on-surface font-bold rounded-xl hover:bg-surface-container-highest transition-colors text-sm">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-4 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primary/90 transition-all shadow-lg shadow-primary/20 active:scale-95 text-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ====== Modal: Konfirmasi Hapus ====== -->
<div id="modal-delete" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest rounded-2xl shadow-2xl w-full max-w-sm p-6 animate-fade-in">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-error/10 rounded-full flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-error">warning</span>
            </div>
            <h3 class="text-lg font-bold text-on-surface">Hapus Jabatan?</h3>
        </div>
        <p class="text-sm text-on-surface-variant mb-1">Jabatan <strong id="delete-jabatan-nama" class="text-on-surface"></strong> akan dihapus permanen.</p>
        <p class="text-xs text-on-surface-variant mb-6">Jabatan tidak dapat dihapus jika masih digunakan oleh anggota.</p>
        <form action="index.php?action=jabatan_delete" method="POST" class="flex gap-3">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="delete-id">
            <button type="button" onclick="closeModal('modal-delete')" class="flex-1 px-4 py-3 bg-surface-container-high text-on-surface font-bold rounded-xl hover:bg-surface-container-highest transition-colors text-sm">
                Batal
            </button>
            <button type="submit" class="flex-1 px-4 py-3 bg-error text-white font-bold rounded-xl hover:bg-error/90 transition-all active:scale-95 text-sm">
                Ya, Hapus
            </button>
        </form>
    </div>
</div>

<style>
@keyframes fade-in { from { opacity: 0; transform: scale(0.95) translateY(8px); } to { opacity: 1; transform: none; } }
.animate-fade-in { animation: fade-in 0.2s ease; }
</style>

<script>
function openAddModal() {
    document.getElementById('add-nama').value = '';
    document.getElementById('modal-add').classList.remove('hidden');
    setTimeout(() => document.getElementById('add-nama').focus(), 50);
}
function openEditModal(id, nama, level) {
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-nama').value = nama;
    document.getElementById('edit-level').value = level;
    document.getElementById('modal-edit').classList.remove('hidden');
    setTimeout(() => document.getElementById('edit-nama').focus(), 50);
}
function confirmDelete(id, nama) {
    document.getElementById('delete-id').value = id;
    document.getElementById('delete-jabatan-nama').textContent = nama;
    document.getElementById('modal-delete').classList.remove('hidden');
}
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}
// Close on backdrop click
['modal-add','modal-edit','modal-delete'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) closeModal(id);
    });
});
// Close on Esc
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        ['modal-add','modal-edit','modal-delete'].forEach(id => closeModal(id));
    }
});
</script>
