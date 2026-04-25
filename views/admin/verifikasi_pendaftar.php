<main class="flex-1 p-8 min-h-[calc(100vh-64px)] bg-surface-container-low">
    <!-- Content Header & Actions -->
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-1">
            <h2 class="text-3xl font-bold tracking-tight text-on-surface">Verifikasi Pendaftar</h2>
            <p class="text-on-surface-variant body-md">Tinjau dan proses pendaftaran calon anggota <?= h($ENTITY) ?> yang baru masuk.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="index.php?page=anggota" class="flex items-center gap-2 px-5 py-2.5 bg-white text-on-surface-variant font-semibold text-sm rounded-xl border border-outline-variant hover:bg-surface transition-all active:scale-95">
                <span class="material-symbols-outlined text-[20px]" data-icon="arrow_back">arrow_back</span>
                Kembali ke Manajemen Anggota
            </a>
        </div>
    </div>
    
    <?= renderFlash() ?>

    <!-- Table -->
    <div class="bg-surface-container-lowest rounded-2xl shadow-[0_12px_40px_rgba(25,28,30,0.04)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-high">
                    <tr>
                        <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-[0.1em]">Calon Anggota</th>
                        <?php if (Session::get('admin_role') === 'superadmin'): ?>
                        <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-[0.1em]">Asal <?= h($ENTITY) ?></th>
                        <?php endif; ?>
                        <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-[0.1em]">Kontak</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-[0.1em]">Jurusan/Kelas</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-[0.1em]">Tanggal Daftar</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-[0.1em]">Status</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-[0.1em] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/15">
                    <?php if (empty($pendaftaranList)): ?>
                    <tr>
                        <td colspan="<?= Session::get('admin_role') === 'superadmin' ? '7' : '6' ?>" class="px-6 py-12 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-4xl text-outline mb-2 block">inbox</span>
                            Tidak ada data pendaftaran yang perlu diverifikasi.
                        </td>
                    </tr>
                    <?php else: foreach ($pendaftaranList as $p): ?>
                    <tr class="hover:bg-surface-container-low transition-colors group">
                        <td class="px-6 py-5">
                            <p class="text-sm font-bold text-on-surface"><?= htmlspecialchars($p['nama']) ?></p>
                            <?php if(!empty($p['alasan'])): ?>
                            <p class="text-[10px] text-slate-500 mt-1 line-clamp-1 max-w-[200px]" title="<?= htmlspecialchars($p['alasan']) ?>">
                                "<?= htmlspecialchars($p['alasan']) ?>"
                            </p>
                            <?php endif; ?>
                        </td>
                        <?php if (Session::get('admin_role') === 'superadmin'): ?>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-primary/10 text-primary text-[10px] font-bold uppercase rounded-full tracking-wider border border-primary/20">
                                <span class="material-symbols-outlined text-[12px]">domain</span>
                                <?= htmlspecialchars($p['ukm_nama'] ?? '-') ?>
                            </span>
                        </td>
                        <?php endif; ?>
                        <td class="px-6 py-5">
                            <div class="flex flex-col gap-1">
                                <span class="flex items-center gap-1 text-xs text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[14px]">mail</span> <?= htmlspecialchars($p['email']) ?>
                                </span>
                                <span class="flex items-center gap-1 text-xs text-on-surface-variant mt-0.5">
                                    <span class="material-symbols-outlined text-[14px]">call</span> <?= htmlspecialchars($p['no_wa']) ?>
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <p class="text-sm text-slate-800 font-medium"><?= htmlspecialchars($p['jurusan'] ?? '-') ?></p>
                            <p class="text-xs text-slate-500 mt-0.5"><?= htmlspecialchars($p['kelas'] ?? '-') ?></p>
                        </td>
                        <td class="px-6 py-5">
                            <p class="text-sm text-on-surface-variant"><?= date('d M Y', strtotime($p['created_at'])) ?></p>
                        </td>
                        <td class="px-6 py-5">
                            <?php
                                $st = $p['status'] ?? 'pending';
                                $badgeClass = 'bg-amber-100 text-amber-800 border-amber-200';
                                if ($st === 'diterima') $badgeClass = 'bg-emerald-100 text-emerald-800 border-emerald-200';
                                if ($st === 'ditolak') $badgeClass = 'bg-red-100 text-red-800 border-red-200';
                            ?>
                            <span class="px-2 py-1 <?= $badgeClass ?> rounded-md text-[10px] font-black uppercase tracking-widest border">
                                <?= htmlspecialchars($st) ?>
                            </span>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <?php if (!isset($_SESSION['is_active_periode']) || $_SESSION['is_active_periode']): ?>
                            <div class="flex items-center justify-end gap-2">
                                <?php if ($st === 'pending'): ?>
                                <form action="index.php?action=pendaftaran_status" method="POST" class="m-0 inline-block">
    <?= csrf_field() ?>
                                    
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <input type="hidden" name="status" value="diterima">
                                    <button type="submit" onclick="return confirm('Terima pendaftar ini? Data akan dipindahkan ke buku tabel Anggota Utama.')" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all flex items-center justify-center font-bold text-xs" title="Setujui/Terima">
                                        <span class="material-symbols-outlined text-[20px]">check_circle</span>
                                    </button>
                                </form>
                                <form action="index.php?action=pendaftaran_status" method="POST" class="m-0 inline-block" onsubmit="return handleReject(this)">
    <?= csrf_field() ?>
                                    
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <input type="hidden" name="status" value="ditolak">
                                    <input type="hidden" name="alasan_tolak" class="reject-reason" value="">
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all flex items-center justify-center font-bold text-xs" title="Tolak">
                                        <span class="material-symbols-outlined text-[20px]">cancel</span>
                                    </button>
                                </form>
                                <?php else: ?>
                                <form action="index.php?action=pendaftaran_delete" method="POST" class="m-0 inline-block">
    <?= csrf_field() ?>
                                    
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <button type="submit" onclick="return confirm('Hapus permanen arsip pendaftaran ini?')" class="p-2 text-outline hover:text-error hover:bg-error-container rounded-lg transition-all flex items-center justify-center font-bold text-xs" title="Hapus Arsip">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </form>
                                <?php endif; ?>
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
        <!-- Pagination border base -->
        <div class="px-6 py-4 flex items-center justify-between border-t border-outline-variant/15 bg-surface-container-high/20">
            <p class="text-xs font-bold text-outline uppercase tracking-widest">Menampilkan <?= count($pendaftaranList ?? []) ?> Pendaftar</p>
        </div>
    </div>
</main>

<script>
function handleReject(form) {
    const alasan = prompt('Masukkan alasan penolakan (opsional, tekan OK untuk kosongi):');
    if (alasan === null) {
        return false; // User clicked Cancel
    }
    form.querySelector('.reject-reason').value = alasan;
    return true;
}
</script>
