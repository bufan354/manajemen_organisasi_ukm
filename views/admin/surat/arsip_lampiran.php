<?php
?>

<div class="p-6 max-w-5xl mx-auto space-y-10">
    <!-- Superadmin UKM Selector -->
    <?php if ($isSuperAdmin): ?>
        <div class="mb-8 bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-50 dark:bg-amber-900/30 rounded-2xl flex items-center justify-center text-amber-600">
                    <span class="material-symbols-outlined text-3xl">corporate_fare</span>
                </div>
                <div>
                    <h2 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">Arsip Lampiran Organisasi</h2>
                    <p class="text-xs text-slate-500 font-medium">Pilih UKM/HMP untuk melihat riwayat lampiran peminjaman mereka.</p>
                </div>
            </div>
            <div class="w-full md:w-72">
                <select onchange="window.location.href='index.php?page=arsip_lampiran&ukm_id='+this.value" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-amber-500 font-bold transition-all cursor-pointer text-slate-700 dark:text-white shadow-inner">
                    <option value="0" disabled <?= $ukm_id === 0 ? 'selected' : '' ?>>-- Pilih UKM/Organisasi --</option>
                    <?php 
                    $ukmList = (new Ukm())->getAll();
                    foreach ($ukmList as $u): 
                    ?>
                        <option value="<?= $u['id'] ?>" <?= $ukm_id == $u['id'] ? 'selected' : '' ?>><?= h($u['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($isSuperAdmin && $ukm_id === 0): ?>
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 p-8 rounded-[2.5rem] flex items-center gap-6">
            <div class="w-16 h-16 bg-amber-100 dark:bg-amber-800 rounded-3xl flex items-center justify-center text-amber-600 dark:text-amber-400">
                <span class="material-symbols-outlined text-3xl">info</span>
            </div>
            <div>
                <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Pilih <?= h($ENTITY) ?> Terlebih Dahulu</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Silakan pilih UKM/HMP melalui dropdown di atas untuk melihat arsip lampiran.</p>
            </div>
        </div>
    <?php else: ?>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div class="flex items-center gap-3">
            <div class="p-3 bg-amber-500 rounded-2xl shadow-lg shadow-amber-500/20">
                <span class="material-symbols-outlined text-white text-3xl">archive</span>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Arsip Lampiran</h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Riwayat peminjaman barang yang telah digenerate.</p>
            </div>
        </div>
        
        <a href="index.php?page=cetak_lampiran&ukm_id=<?= $ukm_id ?>" class="flex items-center gap-3 px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-xl shadow-emerald-600/20 active:scale-95">
            <span class="material-symbols-outlined text-sm">add_circle</span>
            Buat Lampiran Baru
        </a>
    </div>

    <!-- Arsip Table -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">No</th>
                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Nama Acara & Detail</th>
                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 text-center">Jumlah Barang</th>
                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                <?php if (empty($arsip)): ?>
                    <tr>
                        <td colspan="4" class="px-8 py-20 text-center">
                            <span class="material-symbols-outlined text-4xl text-slate-200 mb-2">folder_open</span>
                            <p class="text-sm text-slate-400 italic">Belum ada arsip lampiran.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($arsip as $idx => $a): 
                        $items = json_decode($a['barang_json'], true) ?: [];
                        $totalQty = array_sum(array_column($items, 'jumlah'));
                    ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                            <td class="px-8 py-6 text-xs font-black text-slate-300"><?= str_pad($idx + 1, 2, '0', STR_PAD_LEFT) ?></td>
                            <td class="px-8 py-6">
                                <div class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-blue-600 transition-colors"><?= h($a['nama_acara']) ?></div>
                                <div class="flex items-center gap-2 mt-1.5">
                                    <span class="material-symbols-outlined text-xs text-slate-400">calendar_today</span>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider"><?= h($a['tanggal_kegiatan']) ?></span>
                                    <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Dibuat <?= date('d M Y', strtotime($a['created_at'])) ?></span>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-full text-[10px] font-black uppercase tracking-widest">
                                    <?= count($items) ?> Jenis
                                    <span class="w-1 h-1 bg-blue-200 dark:bg-blue-800 rounded-full"></span>
                                    <?= $totalQty ?> Total
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="views/admin/surat/print_lampiran.php?id=<?= $a['id'] ?>" target="_blank" 
                                       class="p-2.5 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl transition-all shadow-sm" title="Cetak PDF">
                                        <span class="material-symbols-outlined text-lg">print</span>
                                    </a>
                                    <form action="index.php?action=lampiran_pinjam_delete" method="POST" onsubmit="return confirm('Hapus arsip ini?')" class="inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $a['id'] ?>">
                                        <button type="submit" class="p-2.5 text-slate-300 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-all">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
