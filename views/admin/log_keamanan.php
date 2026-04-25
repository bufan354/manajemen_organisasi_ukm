<div class="p-8 max-w-7xl mx-auto w-full">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Log Aktivitas Keamanan</h2>
            <p class="text-sm font-medium text-slate-500 mt-1">Sistem mencatat aktivitas sensitif secara otomatis setiap harinya.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="index.php?action=log_keamanan_export_csv&search=<?= urlencode($search) ?>&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>" class="flex items-center gap-2 px-4 py-2 bg-white text-slate-700 font-bold text-sm rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors shadow-sm">
                <span class="material-symbols-outlined text-sm">border_all</span> Export Excel
            </a>
            <a href="index.php?action=log_keamanan_export_json&search=<?= urlencode($search) ?>&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>" class="flex items-center gap-2 px-4 py-2 bg-white text-slate-700 font-bold text-sm rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors shadow-sm">
                <span class="material-symbols-outlined text-sm">code</span> JSON
            </a>
        </div>
    </div>

    <!-- Stats & Information Alert -->
    <div class="bg-blue-50 text-blue-800 p-4 rounded-2xl mb-8 flex items-start gap-4 border border-blue-100">
        <span class="material-symbols-outlined text-blue-600 mt-0.5">info</span>
        <div>
            <h4 class="font-bold text-sm">Informasi Auto-Purge</h4>
            <p class="text-sm opacity-90 mt-1">Demi menjaga performa, log yang berumur lebih dari 7 hari akan dihapus otomatis oleh sistem setiap harinya (pukul 01:00 AM).</p>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 mb-8">
        <form method="GET" action="index.php" class="flex flex-col md:flex-row gap-4 items-end">
            <input type="hidden" name="page" value="log_keamanan">
            
            <div class="flex-1 w-full">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Pencarian</label>
                <div class="relative">
                    <input type="text" name="search" value="<?= h($search) ?>" placeholder="Kata kunci, email, atau IP..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 pl-11 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    <span class="material-symbols-outlined absolute left-4 top-3 text-slate-400">search</span>
                </div>
            </div>

            <div class="w-full md:w-auto">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Dari Tanggal</label>
                <input type="date" name="start_date" value="<?= h($startDate) ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
            </div>

            <div class="w-full md:w-auto">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Sampai Tanggal</label>
                <input type="date" name="end_date" value="<?= h($endDate) ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
            </div>

            <div class="w-full md:w-auto flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-xl font-bold text-sm hover:bg-blue-700 transition shadow-md shadow-blue-500/20">
                    Filter
                </button>
                <a href="index.php?page=log_keamanan" class="bg-slate-100 text-slate-600 px-6 py-3 rounded-xl font-bold text-sm hover:bg-slate-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-slate-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500 border-b border-slate-200">Waktu</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500 border-b border-slate-200">Aktivitas</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500 border-b border-slate-200">User / Aktor</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500 border-b border-slate-200">IP & Device</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500 border-b border-slate-200">Detail JSON</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                            <span class="material-symbols-outlined text-4xl mb-3 block text-slate-300">security_update_warning</span>
                            <p class="font-medium">Tidak ada log yang ditemukan.</p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm font-bold text-slate-900"><?= date('d M Y', strtotime($log['waktu'])) ?></p>
                                <p class="text-xs font-medium text-slate-500"><?= date('H:i:s', strtotime($log['waktu'])) ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-slate-900 bg-blue-50 text-blue-700 px-3 py-1 rounded-full border border-blue-100"><?= h($log['aktivitas']) ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($log['user_email']): ?>
                                    <p class="text-sm font-bold text-slate-900"><?= h($log['user_nama']) ?></p>
                                    <p class="text-xs font-medium text-slate-500"><?= h($log['user_email']) ?></p>
                                <?php else: ?>
                                    <span class="text-sm text-slate-400 italic">Sistem / Anonim</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 max-w-[200px]">
                                <p class="text-sm font-bold font-mono text-slate-600 bg-slate-100 inline-block px-2 py-0.5 rounded"><?= h($log['ip_address'] ?? 'Unknown') ?></p>
                                <p class="text-xs text-slate-500 truncate mt-1" title="<?= h($log['user_agent'] ?? '') ?>"><?= h($log['user_agent'] ?? '-') ?></p>
                            </td>
                            <td class="px-6 py-4 max-w-[200px]">
                                <?php if (!empty($log['detail'])): ?>
                                    <button onclick="alert('<?= htmlspecialchars(addslashes($log['detail'])) ?>')" class="text-xs font-bold text-blue-600 hover:text-blue-800 underline">Lihat Detail</button>
                                <?php else: ?>
                                    <span class="text-xs text-slate-400">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        <?php if ($totalPages > 1): ?>
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
            <p class="text-sm text-slate-500 leading-5">
                Menampilkan halaman <span class="font-medium"><?= $pageNo ?></span> dari <span class="font-medium"><?= $totalPages ?></span>
            </p>
            <div class="flex gap-1">
                <?php if ($pageNo > 1): ?>
                    <a href="index.php?page=log_keamanan&p=<?= $pageNo - 1 ?>&search=<?= urlencode($search) ?>&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>" class="px-3 py-1 bg-white border border-slate-300 rounded-lg text-sm text-slate-600 hover:bg-slate-50">
                        Sebeleumnya
                    </a>
                <?php endif; ?>
                <?php if ($pageNo < $totalPages): ?>
                    <a href="index.php?page=log_keamanan&p=<?= $pageNo + 1 ?>&search=<?= urlencode($search) ?>&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>" class="px-3 py-1 bg-white border border-slate-300 rounded-lg text-sm text-slate-600 hover:bg-slate-50">
                        Selanjutnya
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
