<main class="flex-1 p-8 min-h-[calc(100vh-64px-112px)] bg-surface-container-low">
    <!-- Content Header & Actions -->
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-1">
            <h2 class="text-3xl font-bold tracking-tight text-on-surface">Manajemen Berita</h2>
            <p class="text-on-surface-variant body-md">Kelola arsip berita, aktivitas, dan pengumuman untuk publikasi.</p>
        </div>
        <?php if (!isset($_SESSION['is_active_periode']) || $_SESSION['is_active_periode']): ?>
        <div class="flex items-center gap-3">
            <a href="index.php?page=tambah_berita" class="flex items-center gap-2 px-6 py-2.5 bg-primary text-white font-semibold text-sm rounded-xl shadow-lg shadow-primary/20 hover:bg-primary-container transition-all active:scale-95">
                <span class="material-symbols-outlined text-[20px]" data-icon="post_add">post_add</span>
                Tulis Berita Baru
            </a>
        </div>
        <?php endif; ?>
    </div>
    
    <?= renderFlash() ?>

    <!-- Filter & Search Bento Card -->
    <div class="bg-surface-container-lowest rounded-2xl p-6 mb-6 shadow-[0_12px_40px_rgba(25,28,30,0.04)] flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-[300px] relative">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline text-[20px]" data-icon="search">search</span>
            <input class="w-full pl-12 pr-4 py-3 bg-surface-container rounded-xl border-none focus:ring-2 focus:ring-primary/20 text-sm placeholder:text-outline" placeholder="Cari berdasarkan judul berita..." type="text"/>
        </div>
        <div class="flex items-center gap-4">
            <?php if (Session::get('admin_role') === 'superadmin' && !empty($ukmList)): ?>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-outline uppercase tracking-wider">UKM:</span>
                <select onchange="window.location.href='index.php?page=berita&filter_ukm_id='+this.value" class="bg-surface-container border-none rounded-xl text-sm font-medium py-3 px-4 focus:ring-2 focus:ring-primary/20 cursor-pointer">
                    <option value="">Semua UKM</option>
                    <?php foreach ($ukmList as $u): ?>
                    <option value="<?= $u['id'] ?>" <?= (isset($_GET['filter_ukm_id']) && $_GET['filter_ukm_id'] == $u['id']) ? 'selected' : '' ?>><?= htmlspecialchars($u['singkatan'] ?? $u['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-outline uppercase tracking-wider">Kategori:</span>
                <select class="bg-surface-container border-none rounded-xl text-sm font-medium py-3 px-4 focus:ring-2 focus:ring-primary/20 cursor-pointer">
                    <option>Semua Kategori</option>
                    <option>Pengumuman</option>
                    <option>Prestasi</option>
                    <option>Kegiatan</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Table -->
    <div class="bg-surface-container-lowest rounded-2xl shadow-[0_12px_40px_rgba(25,28,30,0.04)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-high">
                    <tr>
                        <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-[0.1em]">Artikel</th>
                        <?php if (Session::get('admin_role') === 'superadmin'): ?>
                        <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-[0.1em]">Asal UKM</th>
                        <?php endif; ?>
                        <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-[0.1em]">Kategori</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-[0.1em]">Status</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-[0.1em] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/15">
                    <?php if (empty($beritaList)): ?>
                    <tr>
                        <td colspan="<?= Session::get('admin_role') === 'superadmin' ? '5' : '4' ?>" class="px-6 py-12 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-4xl text-outline mb-2 block">article</span>
                            Belum ada berita.
                        </td>
                    </tr>
                    <?php else: foreach ($beritaList as $b): ?>
                    <tr class="hover:bg-surface-container-low transition-colors group">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4 max-w-[400px]">
                                <div class="w-16 h-12 rounded-lg overflow-hidden bg-slate-100 flex-shrink-0">
                                    <?php if (!empty($b['gambar_path'])): ?>
                                        <img class="w-full h-full object-cover" alt="Thumbnail" src="<?= htmlspecialchars($b['gambar_path']) ?>"/>
                                    <?php else: ?>
                                        <div class="w-full h-full bg-surface-container flex items-center justify-center">
                                            <span class="material-symbols-outlined text-outline text-sm">image</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-on-surface line-clamp-1"><?= htmlspecialchars($b['judul']) ?></p>
                                    <p class="text-xs text-on-surface-variant mt-1"><?= date('d M Y', strtotime($b['created_at'])) ?></p>
                                </div>
                            </div>
                        </td>
                        <?php if (Session::get('admin_role') === 'superadmin'): ?>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-primary/10 text-primary text-[10px] font-bold uppercase rounded-full tracking-wider border border-primary/20">
                                <span class="material-symbols-outlined text-[12px]">domain</span>
                                <?= htmlspecialchars($b['ukm_nama'] ?? 'General') ?>
                            </span>
                        </td>
                        <?php endif; ?>
                        <td class="px-6 py-5">
                            <?php
                                $kat = $b['kategori'] ?? 'Umum';
                                $katColors = [
                                    'Prestasi' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                    'Pengumuman' => 'bg-amber-100 text-amber-800 border-amber-200',
                                    'Kegiatan' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'Informasi' => 'bg-purple-100 text-purple-800 border-purple-200',
                                ];
                                $katClass = $katColors[$kat] ?? 'bg-slate-100 text-slate-800 border-slate-200';
                            ?>
                            <span class="px-3 py-1 <?= $katClass ?> rounded-md text-[10px] font-black uppercase tracking-widest border"><?= htmlspecialchars($kat) ?></span>
                        </td>
                        <td class="px-6 py-5">
                            <?php $isPublished = ($b['status'] ?? '') === 'published'; ?>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full <?= $isPublished ? 'bg-secondary' : 'bg-surface-dim' ?>"></span>
                                <span class="text-sm font-semibold <?= $isPublished ? 'text-secondary' : 'text-outline' ?>"><?= $isPublished ? 'Terbit' : 'Draf' ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <?php if (!isset($_SESSION['is_active_periode']) || $_SESSION['is_active_periode']): ?>
                            <div class="flex items-center justify-end gap-2">
                                <a href="index.php?page=edit_berita&id=<?= $b['id'] ?>" class="p-2 text-outline hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all flex items-center justify-center" title="Edit Article">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </a>
                                <button onclick="openDeleteModal('<?= addslashes($b['judul']) ?>', <?= $b['id'] ?>)" class="p-2 text-outline hover:text-error hover:bg-error-container rounded-lg transition-all" title="Delete">
                                    <span class="material-symbols-outlined text-[20px]" data-icon="delete">delete</span>
                                </button>
                            </div>
                            <?php else: ?>
                            <span class="text-xs text-outline italic">Read-Only</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="px-6 py-4 flex items-center justify-between border-t border-outline-variant/15">
            <p class="text-xs font-bold text-outline uppercase tracking-widest">Menampilkan <?= count($beritaList ?? []) ?> Berita</p>
        </div>
    </div>
</main>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="relative bg-white rounded-2xl p-8 max-w-sm w-full shadow-2xl overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="deleteModalContent">
        <div class="w-16 h-16 rounded-full bg-red-100 text-red-600 flex items-center justify-center mb-6 mx-auto">
            <span class="material-symbols-outlined text-[32px]">delete</span>
        </div>
        <h3 class="text-2xl font-black text-slate-900 text-center mb-2">Hapus Artikel?</h3>
        <p class="text-slate-500 text-center text-sm leading-relaxed mb-8">
            Apakah Anda yakin ingin menghapus publikasi <strong id="deleteTargetName" class="text-slate-800"></strong> secara permanen?
        </p>
        <form action="index.php?action=berita_delete" method="POST" class="flex gap-4">
            <input type="hidden" name="id" id="deleteTargetId">
            <button type="button" onclick="closeDeleteModal()" class="flex-1 py-3 px-4 bg-slate-100 text-slate-600 font-bold text-sm rounded-xl hover:bg-slate-200 transition-colors">Batal</button>
            <button type="submit" class="flex-1 py-3 px-4 bg-red-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-red-200 hover:bg-red-700 transition-colors">Ya, Hapus</button>
        </form>
    </div>
</div>

<script>
    function openDeleteModal(name, id) {
        document.getElementById('deleteTargetName').innerText = name;
        document.getElementById('deleteTargetId').value = id;
        const modal = document.getElementById('deleteModal');
        const content = document.getElementById('deleteModalContent');
        modal.classList.remove('hidden');
        setTimeout(() => { content.classList.remove('scale-95', 'opacity-0'); }, 10);
    }
    function closeDeleteModal() {
        const content = document.getElementById('deleteModalContent');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => { document.getElementById('deleteModal').classList.add('hidden'); }, 300);
    }
</script>
