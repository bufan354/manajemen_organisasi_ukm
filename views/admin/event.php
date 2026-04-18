<div class="flex-1 p-8 pb-12 max-w-7xl mx-auto w-full">
    <!-- Page Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Kegiatan Absensi</h2>
            <p class="text-on-surface-variant text-sm mt-1">Kelola dan pantau seluruh kegiatan absensi digital dalam satu dashboard.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="openImportModal()" class="flex items-center gap-2 bg-white border border-slate-200 text-slate-600 px-5 py-3 rounded-xl font-bold hover:bg-slate-50 transition-all active:scale-95 text-sm">
                <span class="material-symbols-outlined text-lg">upload_file</span>
                Import Data
            </button>
            <a href="index.php?action=export_kegiatan<?= isset($_GET['filter_ukm_id']) && $_GET['filter_ukm_id'] !== '' ? '&ukm_id=' . $_GET['filter_ukm_id'] : '' ?>" class="flex items-center gap-2 bg-white border border-slate-200 text-slate-600 px-5 py-3 rounded-xl font-bold hover:bg-slate-50 transition-all active:scale-95 text-sm">
                <span class="material-symbols-outlined text-lg">download</span>
                Export Excel
            </a>
            <a href="index.php?page=tambah_event" class="flex items-center gap-2 bg-primary-container text-white px-6 py-3 rounded-xl font-bold hover:shadow-lg hover:shadow-primary/30 transition-all active:scale-95 text-sm">
                <span class="material-symbols-outlined text-lg">add_circle</span>
                Buat Kegiatan Baru
            </a>
        </div>
    </div>
    
    <?= renderFlash() ?>

    <!-- Filter Bar -->
    <div class="bg-surface-container-lowest rounded-2xl p-4 flex gap-4 items-center mb-6 shadow-sm border border-white/40">
        <?php if (!empty($ukmList)): ?>
        <form method="GET" class="flex items-center gap-3">
            <input type="hidden" name="page" value="event">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Filter UKM:</label>
            <select name="filter_ukm_id" onchange="this.form.submit()" class="px-3 py-2 bg-surface-container rounded-lg border-none text-sm font-medium cursor-pointer min-w-[160px]">
                <option value="">Semua UKM</option>
                <?php foreach ($ukmList as $u): ?>
                    <option value="<?= $u['id'] ?>" <?= (($_GET['filter_ukm_id'] ?? '') == $u['id']) ? 'selected' : '' ?>><?= htmlspecialchars($u['singkatan']) ?></option>
                <?php endforeach; ?>
            </select>
        </form>
        <?php endif; ?>
    </div>
    
    <?php
    $routines = $eventList['routines'] ?? [];
    $regulars = $eventList['regulars'] ?? [];
    $hariMap = ['0'=>'Minggu', '1'=>'Senin', '2'=>'Selasa', '3'=>'Rabu', '4'=>'Kamis', '5'=>'Jumat', '6'=>'Sabtu'];
    ?>
    
    <!-- Bagian 1: Template Kegiatan Rutin -->
    <div class="mb-8 relative">
        <h3 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-primary">event_repeat</span> Jadwal Rutinitas Induk</h3>
        <p class="text-xs text-slate-500 mb-4 bg-primary/5 p-3 rounded-lg border border-primary/10">Jadwal di bawah ini bertindak sebagai "Mesin Cetak". Mereka akan memproduksi Kegiatan Anak secara otomatis pada hari H pelaksanaannya.</p>
        <div class="bg-surface-container-lowest rounded-3xl overflow-hidden shadow-sm border border-white/60">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-high/50">
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.1em]">Nama Rutinitas</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.1em]">Hari Berulang</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.1em]">Jam Pelaksanaan</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.1em]">Generator</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.1em] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (empty($routines)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-on-surface-variant text-sm">
                            Belum ada jadwal rutinitas (Master).
                        </td>
                    </tr>
                    <?php else: foreach ($routines as $ev): ?>
                    <tr class="hover:bg-surface-container-low/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold"><span class="material-symbols-outlined text-sm">all_inclusive</span></div>
                                <div>
                                    <p class="font-bold text-slate-900"><?= htmlspecialchars($ev['nama']) ?></p>
                                    <p class="text-[10px] text-slate-400 font-medium">UKM: <?= htmlspecialchars($ev['ukm_nama'] ?? '-') ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                <?php 
                                $hArr = explode(',', $ev['hari_rutin'] ?? '');
                                foreach($hArr as $h) {
                                    if(isset($hariMap[$h])) echo '<span class="px-2 py-0.5 bg-slate-100 text-slate-600 font-bold text-[10px] rounded-md">'.$hariMap[$h].'</span>';
                                }
                                ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-slate-600">
                            <?= date('H:i', strtotime($ev['waktu_mulai'])) ?> - <?= date('H:i', strtotime($ev['waktu_selesai'])) ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if (!empty($ev['status_absensi'])): ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-wider">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Menyala
                            </span>
                            <?php else: ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                                Berhenti
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-1">
                                <a href="index.php?page=edit_event&id=<?= $ev['id'] ?>" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all flex items-center justify-center" title="Edit Master">
                                    <span class="material-symbols-outlined text-xl">edit</span>
                                </a>
                                <button onclick="openDeleteModal('<?= addslashes($ev['nama']) ?>', <?= $ev['id'] ?>)" class="p-2 text-slate-400 hover:text-error hover:bg-error-container/20 rounded-lg transition-all" title="Hapus Master (Riwayat anak ikut terhapus)">
                                    <span class="material-symbols-outlined text-xl">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>


    <!-- Bagian 2: Daftar Kegiatan Hari Ini & Masa Lalu -->
    <div>
        <h3 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-primary">format_list_bulleted</span> Kegiatan Tunggal & Hasil Generator</h3>
        <div class="bg-surface-container-lowest rounded-3xl overflow-hidden shadow-sm border border-white/60">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-high/50">
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.1em]">Nama Kegiatan</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.1em]">Waktu Pelaksanaan</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.1em]">Lokasi</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.1em]">Kehadiran</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.1em]">Status Scan</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.1em]">Status Jadwal</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.1em] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (empty($regulars)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-4xl text-outline mb-2 block">event_busy</span>
                            Belum ada riwayat kegiatan.
                        </td>
                    </tr>
                    <?php else: foreach ($regulars as $ev): ?>
                    <tr class="hover:bg-surface-container-low/50 transition-colors group">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <?php if ($ev['parent_id']): ?>
                                    <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold opacity-80" title="Auto-Generated"><span class="material-symbols-outlined text-sm">robot_2</span></div>
                                <?php else: ?>
                                    <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 font-bold"><?= strtoupper(substr($ev['nama'], 0, 1)) ?></div>
                                <?php endif; ?>
                                <div>
                                    <p class="font-bold text-slate-900"><?= htmlspecialchars($ev['nama']) ?></p>
                                    <p class="text-[10px] text-slate-400 font-medium">
                                        <?= !empty($ev['ukm_nama']) ? htmlspecialchars($ev['ukm_nama']) . ' · ' : '' ?>ID: EVT-<?= $ev['id'] ?>
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm">
                                <div class="flex items-center gap-1 text-slate-700 font-medium <?= date('Y-m-d', strtotime($ev['waktu_mulai'])) === date('Y-m-d') ? 'text-primary' : '' ?>">
                                    <span class="material-symbols-outlined text-xs <?= date('Y-m-d', strtotime($ev['waktu_mulai'])) === date('Y-m-d') ? 'text-primary' : 'text-slate-400' ?>">calendar_today</span>
                                    <?= date('d M Y', strtotime($ev['waktu_mulai'])) ?> <?= date('Y-m-d', strtotime($ev['waktu_mulai'])) === date('Y-m-d') ? '<span class="text-[10px] bg-primary/10 text-primary px-1 rounded ml-1">HARI INI</span>' : '' ?>
                                </div>
                                <?php if (!empty($ev['waktu_selesai'])): ?>
                                <div class="text-[10px] text-slate-400 pl-4 mt-0.5">s/d <?= date('H:i', strtotime($ev['waktu_selesai'])) ?></div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-1 text-sm text-slate-600">
                                <span class="material-symbols-outlined text-sm text-slate-400">location_on</span>
                                <?= htmlspecialchars($ev['lokasi'] ?? '-') ?>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <?php 
                                $hadir = $ev['total_hadir'] ?? 0;
                                $total = $ev['total_anggota'] ?? 0;
                                $persen = $total > 0 ? round($hadir / $total * 100) : 0;
                            ?>
                            <div class="flex items-center gap-2">
                                <div class="w-16 bg-slate-100 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full <?= $persen >= 75 ? 'bg-emerald-500' : ($persen >= 50 ? 'bg-amber-500' : 'bg-red-400') ?>" style="width: <?= min($persen, 100) ?>%"></div>
                                </div>
                                <span class="text-xs font-bold text-slate-600"><?= $hadir ?>/<?= $total ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <?php if (!empty($ev['status_absensi'])): ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-secondary-container text-on-secondary-container text-[10px] font-bold uppercase tracking-wider">
                                <span class="w-1 h-1 rounded-full bg-current"></span> Aktif
                            </span>
                            <?php else: ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                                Non-Aktif
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-5">
                            <?php 
                            $status = $ev['status'] ?? 'scheduled';
                            $statusMap = [
                                'scheduled' => ['label' => 'Dijadwalkan', 'class' => 'bg-blue-100 text-blue-700'],
                                'postponed' => ['label' => 'Diundur', 'class' => 'bg-amber-100 text-amber-700'],
                                'cancelled' => ['label' => 'Dibatalkan', 'class' => 'bg-red-100 text-red-700'],
                            ];
                            $s = $statusMap[$status] ?? $statusMap['scheduled'];
                            ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider <?= $s['class'] ?>">
                                <?= $s['label'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex justify-end gap-1 transition-opacity">
                                <a href="index.php?page=detail_event&id=<?= $ev['id'] ?>" class="px-3 py-2 text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-all flex items-center justify-center font-bold text-xs gap-1" title="Lihat Kehadiran">
                                    <span class="material-symbols-outlined text-[16px]">visibility</span> Detail
                                </a>
                                <?php if ($status !== 'cancelled'): ?>
                                <button onclick="openPostponeModal(<?= $ev['id'] ?>, '<?= addslashes($ev['nama'] ?? '') ?>', '<?= date('Y-m-d\TH:i', strtotime($ev['waktu_mulai'])) ?>', '<?= date('Y-m-d\TH:i', strtotime($ev['waktu_selesai'])) ?>')" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="Undur Kegiatan">
                                    <span class="material-symbols-outlined text-xl">event_upcoming</span>
                                </button>
                                <button onclick="openCancelModal(<?= $ev['id'] ?>, '<?= addslashes($ev['nama'] ?? '') ?>')" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Batalkan Kegiatan">
                                    <span class="material-symbols-outlined text-xl">event_busy</span>
                                </button>
                                <?php endif; ?>
                                <a href="index.php?page=edit_event&id=<?= $ev['id'] ?>" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all flex items-center justify-center" title="Edit">
                                    <span class="material-symbols-outlined text-xl">edit</span>
                                </a>
                                <button onclick="openDeleteModal('<?= addslashes($ev['nama']) ?>', <?= $ev['id'] ?>)" class="p-2 text-slate-400 hover:text-error hover:bg-error-container/20 rounded-lg transition-all" title="Hapus">
                                    <span class="material-symbols-outlined text-xl">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
            <div class="px-6 py-4 bg-slate-50/50 flex justify-between items-center border-t border-slate-100">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Menampilkan <?= count($regulars) ?> riwayat</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Delete Confirmation -->
<div id="deleteModal" class="hidden fixed inset-0 z-[110] flex items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="relative bg-white rounded-2xl p-8 max-w-sm w-full shadow-2xl overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="deleteModalContent">
        <div class="w-16 h-16 rounded-full bg-red-100 text-red-600 flex items-center justify-center mb-6 mx-auto">
            <span class="material-symbols-outlined text-[32px]">warning</span>
        </div>
        <h3 class="text-2xl font-black text-slate-900 text-center mb-2">Hapus Kegiatan?</h3>
        <p class="text-slate-500 text-center text-sm leading-relaxed mb-8">
            Apakah Anda yakin ingin menghapus kegiatan <strong id="deleteTargetName" class="text-slate-800"></strong>? Log absensi juga bisa terpengaruh.
        </p>
        <form action="index.php?action=event_delete" method="POST" class="flex gap-4">
    <?= csrf_field() ?>
            
            <input type="hidden" name="id" id="deleteTargetId">
            <button type="button" onclick="closeDeleteModal()" class="flex-1 py-3 px-4 bg-slate-100 text-slate-600 font-bold text-sm rounded-xl hover:bg-slate-200 transition-colors">Batal</button>
            <button type="submit" class="flex-1 py-3 px-4 bg-red-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-red-200 hover:bg-red-700 transition-colors">Ya, Hapus</button>
        </form>
    </div>
</div>

<!-- Modal: Import CSV -->
<div id="importModal" class="hidden fixed inset-0 z-[110] flex items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeImportModal()"></div>
    <div class="relative bg-white rounded-2xl p-8 max-w-md w-full shadow-2xl overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="importModalContent">
        <div class="w-16 h-16 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mb-6 mx-auto">
            <span class="material-symbols-outlined text-[32px]">upload_file</span>
        </div>
        <h3 class="text-2xl font-black text-slate-900 text-center mb-2">Import Data Kegiatan</h3>
        <p class="text-slate-500 text-center text-sm leading-relaxed mb-6">
            Upload file CSV yang berisi daftar kegiatan. Format kolom: Nama Kegiatan, Waktu Mulai (Y-m-d H:i:s), Waktu Selesai (opsional), Lokasi (opsional), Deskripsi (opsional). Baris pertama dianggap sebagai header.
        </p>
        <form action="index.php?action=event_import_csv" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
    <?= csrf_field() ?>
            
            <?php if (Session::get('admin_role') === 'superadmin'): ?>
            <div class="mb-2">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih UKM Target</label>
                <select name="ukm_id" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    <option value="">- Pilih UKM -</option>
                    <?php foreach ($ukmList as $u): ?>
                        <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['singkatan'] . ' - ' . $u['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">File CSV</label>
                <input type="file" name="csv_file" accept=".csv" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none transition-all file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            <div class="flex gap-4">
                <button type="button" onclick="closeImportModal()" class="flex-1 py-3 px-4 bg-slate-100 text-slate-600 font-bold text-sm rounded-xl hover:bg-slate-200 transition-colors">Batal</button>
                <button type="submit" class="flex-1 py-3 px-4 bg-blue-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 transition-colors">Import Sekarang</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Postpone Event -->
<div id="postponeModal" class="hidden fixed inset-0 z-[110] flex items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closePostponeModal()"></div>
    <div class="relative bg-white rounded-2xl p-8 max-w-md w-full shadow-2xl overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="postponeModalContent">
        <div class="w-16 h-16 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mb-6 mx-auto">
            <span class="material-symbols-outlined text-[32px]">event_upcoming</span>
        </div>
        <h3 class="text-2xl font-black text-slate-900 text-center mb-2">Undur Kegiatan</h3>
        <p class="text-slate-500 text-center text-sm leading-relaxed mb-6" id="postponeText"></p>
        <form action="index.php?action=event_postpone" method="POST" class="flex flex-col gap-4">
    <?= csrf_field() ?>
            
            <input type="hidden" name="id" id="postponeId">
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Waktu Mulai Baru</label>
                    <input type="datetime-local" name="waktu_mulai" id="postponeMulai" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Waktu Selesai Baru</label>
                    <input type="datetime-local" name="waktu_selesai" id="postponeSelesai" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                </div>
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Alasan Pengunduran (Opsional)</label>
                <textarea name="alasan" placeholder="Contoh: Bentrok dengan jadwal perkuliahan..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none transition-all h-24 resize-none"></textarea>
            </div>
            <div class="flex gap-4">
                <button type="button" onclick="closePostponeModal()" class="flex-1 py-3 px-4 bg-slate-100 text-slate-600 font-bold text-sm rounded-xl hover:bg-slate-200 transition-colors">Batal</button>
                <button type="submit" class="flex-1 py-3 px-4 bg-amber-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-amber-200 hover:bg-amber-700 transition-colors">Undur Sekarang</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Cancel Event -->
<div id="cancelModal" class="hidden fixed inset-0 z-[110] flex items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeCancelModal()"></div>
    <div class="relative bg-white rounded-2xl p-8 max-w-md w-full shadow-2xl overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="cancelModalContent">
        <div class="w-16 h-16 rounded-full bg-red-100 text-red-600 flex items-center justify-center mb-6 mx-auto">
            <span class="material-symbols-outlined text-[32px]">event_busy</span>
        </div>
        <h3 class="text-2xl font-black text-slate-900 text-center mb-2">Batalkan Kegiatan</h3>
        <p class="text-slate-500 text-center text-sm leading-relaxed mb-6" id="cancelText"></p>
        <form action="index.php?action=event_cancel" method="POST" class="flex flex-col gap-4">
    <?= csrf_field() ?>
            
            <input type="hidden" name="id" id="cancelId">
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Alasan Pembatalan</label>
                <textarea name="alasan" required placeholder="Contoh: Kondisi cuaca buruk / pembicara berhalangan..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none transition-all h-24 resize-none"></textarea>
            </div>
            <div class="flex gap-4">
                <button type="button" onclick="closeCancelModal()" class="flex-1 py-3 px-4 bg-slate-100 text-slate-600 font-bold text-sm rounded-xl hover:bg-slate-200 transition-colors">Batal</button>
                <button type="submit" class="flex-1 py-3 px-4 bg-red-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-red-200 hover:bg-red-700 transition-colors">Ya, Batalkan</button>
            </div>
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
    
    function openImportModal() {
        const modal = document.getElementById('importModal');
        const content = document.getElementById('importModalContent');
        modal.classList.remove('hidden');
        setTimeout(() => { content.classList.remove('scale-95', 'opacity-0'); }, 10);
    }
    function closeImportModal() {
        const content = document.getElementById('importModalContent');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => { document.getElementById('importModal').classList.add('hidden'); }, 300);
    }

    function openPostponeModal(id, name, start, end) {
        document.getElementById('postponeId').value = id;
        document.getElementById('postponeText').innerHTML = `Atur ulang jadwal untuk kegiatan <strong>${name}</strong>. Waktu baru akan diperbarui dan pengumuman akan dibuat otomatis.`;
        document.getElementById('postponeMulai').value = start;
        document.getElementById('postponeSelesai').value = end;
        
        const modal = document.getElementById('postponeModal');
        const content = document.getElementById('postponeModalContent');
        modal.classList.remove('hidden');
        setTimeout(() => { content.classList.remove('scale-95', 'opacity-0'); }, 10);
    }
    function closePostponeModal() {
        const content = document.getElementById('postponeModalContent');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => { document.getElementById('postponeModal').classList.add('hidden'); }, 300);
    }

    function openCancelModal(id, name) {
        document.getElementById('cancelId').value = id;
        document.getElementById('cancelText').innerHTML = `Apakah Anda yakin ingin membatalkan kegiatan <strong>${name}</strong>? Status akan berubah menjadi dibatalkan secara permanen.`;
        
        const modal = document.getElementById('cancelModal');
        const content = document.getElementById('cancelModalContent');
        modal.classList.remove('hidden');
        setTimeout(() => { content.classList.remove('scale-95', 'opacity-0'); }, 10);
    }
    function closeCancelModal() {
        const content = document.getElementById('cancelModalContent');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => { document.getElementById('cancelModal').classList.add('hidden'); }, 300);
    }
</script>

<!-- Modal: Redaksi Otomatis (Muncul setelah Tambah Event) -->
<?php if (isset($_SESSION['redaksi_to_copy'])): ?>
<div id="redaksiModal" class="fixed inset-0 z-[150] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md animate-fade-in" onclick="closeRedaksiModal()"></div>
    <div class="relative bg-white rounded-[2rem] max-w-lg w-full shadow-2xl overflow-hidden transform animate-scale-up" id="redaksiModalContent">
        <!-- Header -->
        <div class="px-8 pt-8 pb-6 flex items-center justify-between border-b border-slate-50">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[28px]">content_copy</span>
                </div>
                <div>
                    <h3 class="text-xl font-black text-slate-900 leading-none">Salin Redaksi</h3>
                    <p class="text-xs text-slate-500 mt-1 font-medium italic">Siap dibagikan ke Grup WhatsApp</p>
                </div>
            </div>
            <button onclick="closeRedaksiModal()" class="w-10 h-10 rounded-full hover:bg-slate-100 transition-colors flex items-center justify-center text-slate-400">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Content -->
        <div class="p-8">
            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200/60 relative group">
                <textarea id="redaksiText" readonly class="w-full h-64 bg-transparent border-none focus:ring-0 text-sm font-medium text-slate-700 leading-relaxed resize-none scrollbar-thin scrollbar-thumb-slate-200"><?= htmlspecialchars($_SESSION['redaksi_to_copy']) ?></textarea>
                
                <!-- Feedback Copied -->
                <div id="copyFeedback" class="absolute inset-0 bg-white/90 backdrop-blur-sm flex flex-col items-center justify-center rounded-2xl opacity-0 pointer-events-none transition-all duration-300">
                    <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mb-2 animate-bounce">
                        <span class="material-symbols-outlined text-3xl">check</span>
                    </div>
                    <p class="text-emerald-700 font-bold">Teks Berhasil Disalin!</p>
                </div>
            </div>
            
            <p class="text-[10px] text-slate-400 mt-4 text-center font-bold uppercase tracking-widest">Klik tombol di bawah untuk menyalin teks secara otomatis</p>
        </div>

        <!-- Footer -->
        <div class="p-8 pt-0 flex flex-col gap-3">
            <!-- WA Web Toggle -->
            <label class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100 cursor-pointer group hover:border-emerald-200 transition-all mb-1">
                <div class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="useWAWeb" class="sr-only peer">
                    <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500"></div>
                </div>
                <div class="flex flex-col">
                    <span class="text-xs font-bold text-slate-700">Gunakan WhatsApp Web</span>
                    <span class="text-[10px] text-slate-400 leading-none">Lewati splash screen (Desktop)</span>
                </div>
            </label>

            <button onclick="shareToWA()" class="w-full py-4 bg-[#25D366] text-white font-bold rounded-2xl shadow-xl shadow-success/20 hover:bg-[#128C7E] transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Kirim ke WhatsApp
            </button>
            <button onclick="copyRedaksi()" id="copyBtn" class="w-full py-4 bg-surface-container-high text-on-surface font-bold rounded-2xl border border-slate-200 hover:bg-surface-container-highest transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-xl">content_copy</span>
                Hanya Salin Teks
            </button>
            <button onclick="closeRedaksiModal()" class="w-full py-3 text-slate-500 font-bold text-sm hover:text-slate-800 transition-colors">
                Tutup Saja
            </button>
        </div>
    </div>
</div>

<script>
    function copyRedaksi() {
        const textArea = document.getElementById('redaksiText');
        const feedback = document.getElementById('copyFeedback');
        const btn = document.getElementById('copyBtn');
        
        textArea.select();
        textArea.setSelectionRange(0, 99999); // Mobile
        
        try {
            navigator.clipboard.writeText(textArea.value).then(() => {
                showSuccess();
            });
        } catch (err) {
            // Fallback
            document.execCommand('copy');
            showSuccess();
        }

        function showSuccess() {
            feedback.classList.remove('opacity-0', 'pointer-events-none');
            btn.innerHTML = '<span class="material-symbols-outlined text-xl">done_all</span> Tersalin!';
            btn.classList.add('bg-emerald-600', 'shadow-emerald-200');
            
            setTimeout(() => {
                feedback.classList.add('opacity-0', 'pointer-events-none');
                setTimeout(() => {
                    closeRedaksiModal();
                }, 1000);
            }, 1000);
        }
    }

    function shareToWA() {
        const textArea = document.getElementById('redaksiText');
        const text = encodeURIComponent(textArea.value);
        const forceWeb = document.getElementById('useWAWeb').checked;
        const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
        
        // Pilih Endpoint
        // Jika mobile, api.whatsapp.com selalu paling aman (deep link)
        // Jika desktop dan forceWeb dicentang, langsung ke web.whatsapp.com
        let baseUrl = 'https://api.whatsapp.com/send';
        
        if (!isMobile && forceWeb) {
            baseUrl = 'https://web.whatsapp.com/send';
        }
        
        window.open(`${baseUrl}?text=${text}`, '_blank');
    }

    // Auto-detect desktop to pre-check the toggle
    window.addEventListener('load', () => {
        const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
        const toggle = document.getElementById('useWAWeb');
        if (toggle && !isMobile) {
            toggle.checked = true; // Auto-check if on desktop for smoother experience
        }
    });

    function closeRedaksiModal() {
        const content = document.getElementById('redaksiModalContent');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            document.getElementById('redaksiModal').remove();
            // Clear session via AJAX (optional) or just let it expire. 
            // In this app, we should ideally clear it so it doesn't reappear on refresh.
            fetch('api/clear_redaksi.php'); // We'll create this small helper
        }, 300);
    }
</script>
<style>
    @keyframes scale-up {
        from { transform: scale(0.95); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    .animate-scale-up { animation: scale-up 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
<?php 
    // Clear session immediately after rendering or via AJAX. 
    // To prevent it reappearing on refresh, we unset it here since it's already rendered.
    unset($_SESSION['redaksi_to_copy']); 
?>
<?php endif; ?>
