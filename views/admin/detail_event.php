<?php 
$ev = $event ?? [];
$kehadiranList = $kehadiranList ?? [];
$anggotaList = $anggotaList ?? [];
$stats = $stats ?? ['total_hadir' => 0, 'total_anggota' => 0, 'persentase' => 0];

$hadirMap = [];
foreach ($kehadiranList as $k) {
    if(isset($k['anggota_id'])) {
        $hadirMap[$k['anggota_id']] = $k;
    }
}
?>
<main class="flex-1 p-8 min-h-[calc(100vh-64px-112px)] bg-surface-container-low">
    <!-- Header -->
    <div class="mb-8 flex items-center gap-4">
        <a href="index.php?page=event" class="w-10 h-10 rounded-full flex items-center justify-center bg-white border border-outline-variant hover:bg-surface transition-colors">
            <span class="material-symbols-outlined text-outline">arrow_back</span>
        </a>
        <div class="flex-1">
            <h2 class="text-3xl font-bold tracking-tight text-on-surface">Detail Kehadiran Kegiatan</h2>
            <p class="text-on-surface-variant body-md"><?= htmlspecialchars($ev['nama'] ?? 'Kegiatan') ?></p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="openImportModal()" class="flex items-center gap-2 bg-white border border-slate-200 text-slate-600 px-5 py-2.5 rounded-xl font-bold hover:bg-slate-50 transition-all active:scale-95 text-sm">
                <span class="material-symbols-outlined text-sm">upload_file</span>
                Import Kehadiran
            </button>
            <a href="index.php?action=export_detail_kegiatan&id=<?= $ev['id'] ?? 0 ?>" class="flex items-center gap-2 bg-emerald-600 text-white px-5 py-2.5 rounded-xl font-bold hover:shadow-lg hover:shadow-emerald-600/30 transition-all active:scale-95 text-sm">
                <span class="material-symbols-outlined text-sm">download</span>
                Export ke Excel
            </a>
        </div>
    </div>

    <!-- Event Info & Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <!-- Event Info -->
        <div class="md:col-span-2 bg-surface-container-lowest rounded-2xl p-6 shadow-sm border border-white/60">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 flex flex-col items-center justify-center flex-shrink-0">
                    <span class="text-blue-600 font-black text-lg leading-none"><?= date('d', strtotime($ev['waktu_mulai'] ?? 'now')) ?></span>
                    <span class="text-blue-400 font-bold text-[10px] uppercase"><?= date('M', strtotime($ev['waktu_mulai'] ?? 'now')) ?></span>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-slate-900"><?= htmlspecialchars($ev['nama'] ?? '-') ?></h3>
                    <div class="flex items-center gap-2 mt-1">
                        <p class="text-sm text-slate-500">
                            <span class="material-symbols-outlined text-xs align-middle">schedule</span>
                            <?= date('d M Y, H:i', strtotime($ev['waktu_mulai'] ?? 'now')) ?> – <?= date('H:i', strtotime($ev['waktu_selesai'] ?? 'now')) ?>
                        </p>
                        <?php
                        if (!empty($ev['status_absensi'])) {
                            $now = time();
                            $start = strtotime($ev['waktu_mulai'] ?? 'now');
                            $end = !empty($ev['waktu_selesai']) ? strtotime($ev['waktu_selesai']) : null;
                            
                            if ($now < $start) {
                                echo '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700 uppercase">Menunggu Jam</span>';
                            } elseif ($end && $now > $end) {
                                echo '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500 uppercase">Waktu Habis</span>';
                            } else {
                                echo '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 uppercase"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse mr-1"></span> Absen Terbuka</span>';
                            }
                        } else {
                            echo '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500 uppercase">Non-Aktif</span>';
                        }
                        ?>
                    </div>
                    <?php if (!empty($ev['lokasi'])): ?>
                    <p class="text-sm text-slate-500">
                        <span class="material-symbols-outlined text-xs align-middle">location_on</span>
                        <?= htmlspecialchars($ev['lokasi']) ?>
                    </p>
                    <?php endif; ?>
                    <?php if (!empty($ev['deskripsi'])): ?>
                    <p class="text-xs text-slate-400 mt-2"><?= htmlspecialchars($ev['deskripsi']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Hadir -->
        <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border border-white/60 flex flex-col justify-between">
            <span class="material-symbols-outlined text-emerald-600 mb-2" style="font-variation-settings: 'FILL' 1;">how_to_reg</span>
            <div class="text-3xl font-black text-slate-900"><?= $stats['total_hadir'] ?></div>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1">Anggota Hadir</p>
        </div>

        <!-- Persentase -->
        <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border border-white/60 flex flex-col justify-between">
            <span class="material-symbols-outlined text-amber-600 mb-2" style="font-variation-settings: 'FILL' 1;">percent</span>
            <div class="text-3xl font-black <?= $stats['persentase'] >= 75 ? 'text-emerald-600' : ($stats['persentase'] >= 50 ? 'text-amber-600' : 'text-red-500') ?>">
                <?= $stats['persentase'] ?>%
            </div>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1"><?= $stats['total_hadir'] ?> / <?= $stats['total_anggota'] ?> Anggota</p>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="bg-surface-container-lowest rounded-3xl overflow-hidden shadow-sm border border-white/60">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-high/50">
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.1em]">#</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.1em]">Anggota</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.1em]">NIM</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.1em]">Jabatan</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.1em]">Waktu Hadir</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.1em]">Metode</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if (empty($anggotaList)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-on-surface-variant">
                        <span class="material-symbols-outlined text-4xl text-outline mb-2 block">group_off</span>
                        Belum ada anggota terdaftar.
                    </td>
                </tr>
                <?php else: foreach ($anggotaList as $i => $ang): 
                    $isHadir = isset($hadirMap[$ang['id']]);
                    $kh = $isHadir ? $hadirMap[$ang['id']] : null;
                ?>
                <tr class="hover:bg-surface-container-low/50 transition-colors">
                    <td class="px-6 py-4 text-sm font-bold text-slate-400"><?= $i + 1 ?></td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full overflow-hidden bg-slate-200 flex-shrink-0">
                                <?php if (!empty($ang['foto_path'])): ?>
                                    <img src="<?= htmlspecialchars($ang['foto_path']) ?>" class="w-full h-full object-cover" alt="">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center bg-primary/10 text-primary font-bold text-xs">
                                        <?= strtoupper(substr($ang['nama'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <span class="font-bold text-sm text-slate-900"><?= htmlspecialchars($ang['nama']) ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600"><?= htmlspecialchars($ang['nim'] ?? '-') ?></td>
                    <td class="px-6 py-4 text-sm text-slate-600"><?= htmlspecialchars($ang['jabatan'] ?? 'Anggota') ?></td>
                    <td class="px-6 py-4 text-sm text-slate-600">
                        <?php if ($isHadir): ?>
                            <div class="font-medium text-slate-700"><?= date('H:i:s', strtotime($kh['waktu_hadir'])) ?></div>
                            <div class="text-[10px] text-emerald-600 font-bold uppercase tracking-wider mt-0.5">Sudah Absen</div>
                        <?php else: ?>
                            <div class="text-slate-400 font-medium">-</div>
                            <div class="text-[10px] text-amber-500 font-bold uppercase tracking-wider mt-0.5">Belum Absen</div>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php if ($isHadir): ?>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                            <?= $kh['metode'] === 'fingerprint' ? 'bg-blue-100 text-blue-700' : ($kh['metode'] === 'rfid' ? 'bg-violet-100 text-violet-700' : 'bg-slate-100 text-slate-600') ?>">
                            <?= ucfirst($kh['metode']) ?>
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold text-slate-400 bg-slate-100 uppercase tracking-wider">
                            -
                        </span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
        <div class="px-6 py-4 bg-slate-50/50 flex justify-between items-center border-t border-slate-100">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total: <?= count($anggotaList ?? []) ?> anggota</p>
        </div>
    </div>
</main>

<!-- Modal: Import CSV Kehadiran -->
<div id="importModal" class="hidden fixed inset-0 z-[110] flex items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeImportModal()"></div>
    <div class="relative bg-white rounded-2xl p-8 max-w-md w-full shadow-2xl overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="importModalContent">
        <div class="w-16 h-16 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mb-6 mx-auto">
            <span class="material-symbols-outlined text-[32px]">upload_file</span>
        </div>
        <h3 class="text-2xl font-black text-slate-900 text-center mb-2">Import Kehadiran</h3>
        <p class="text-slate-500 text-center text-sm leading-relaxed mb-6">
            Upload file CSV yang berisi NIM anggota untuk mencatat kehadiran secara manual. Baris pertama dianggap sebagai header. Kehadiran akan tersimpan dengan metode 'Manual'.
        </p>
        <form action="index.php?action=kehadiran_import_csv" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
    <?= csrf_field() ?>
            
            <input type="hidden" name="event_id" value="<?= $ev['id'] ?>">
            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">File CSV (Kolom 1: NIM)</label>
                <input type="file" name="csv_file" accept=".csv" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none transition-all file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            <div class="flex gap-4">
                <button type="button" onclick="closeImportModal()" class="flex-1 py-3 px-4 bg-slate-100 text-slate-600 font-bold text-sm rounded-xl hover:bg-slate-200 transition-colors">Batal</button>
                <button type="submit" class="flex-1 py-3 px-4 bg-blue-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 transition-colors">Import Kehadiran</button>
            </div>
        </form>
    </div>
</div>

<script>
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
</script>
