<?php
// Data dinamis dari router
$ukmNama      = htmlspecialchars($ukm['nama'] ?? 'UKM');
$ukmId        = $ukm['id'] ?? 0;
$totalAnggota = $totalAnggota ?? 0;
$totalEvent   = $totalEvent   ?? 0;
$anggotaList  = $anggotaList  ?? [];
$eventList    = $eventList    ?? [];
$eventStats   = $eventStats   ?? [];
$totalKehadiran = $totalKehadiran ?? 0;
$rataKehadiran  = $rataKehadiran  ?? 0;

// Filter aktif
$filterEventId = (int)($_GET['event_id'] ?? 0);
?>
<link href="assets/public/css/statistik-ukm.css" rel="stylesheet"/>

<?php include __DIR__ . '/components/ukm_subnav.php'; ?>

<div class="max-w-7xl mx-auto px-6 py-12">
  <!-- Hero Section -->
  <section class="mb-12">
    <h1 class="text-4xl md:text-5xl font-black tracking-tighter text-slate-900 mb-4">Statistik Kehadiran</h1>
    <p class="text-slate-600 max-w-2xl text-lg leading-relaxed">
        Analisis mendalam performa dan partisipasi anggota <strong><?= $ukmNama ?></strong> dalam periode akademik berjalan.
    </p>
  </section>

  <!-- Summary Cards -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-12">
    <!-- Total Anggota -->
    <div class="bg-white border border-slate-100 p-6 rounded-2xl transition-all hover:-translate-y-1 hover:shadow-xl hover:border-blue-100">
      <span class="material-symbols-outlined text-primary mb-4 block">groups</span>
      <div class="text-4xl font-black text-slate-900"><?= $totalAnggota ?></div>
      <div class="text-xs font-bold uppercase tracking-widest text-slate-500 mt-2">Total Anggota</div>
    </div>
    
    <!-- Total Event -->
    <div class="bg-white border border-slate-100 p-6 rounded-2xl transition-all hover:-translate-y-1 hover:shadow-xl hover:border-blue-100">
      <div class="flex justify-between items-start mb-4">
        <span class="material-symbols-outlined text-secondary">event_available</span>
        <span class="text-[10px] bg-blue-100 text-blue-800 px-2.5 py-1 rounded-full font-bold uppercase tracking-wider">Kegiatan</span>
      </div>
      <div class="text-4xl font-black text-slate-900"><?= $totalEvent ?></div>
      <div class="text-xs font-bold uppercase tracking-widest text-slate-500 mt-2">Total Kegiatan</div>
    </div>

    <!-- Total Kehadiran -->
    <div class="bg-white border border-slate-100 p-6 rounded-2xl transition-all hover:-translate-y-1 hover:shadow-xl hover:border-emerald-100">
      <div class="flex justify-between items-start mb-4">
        <span class="material-symbols-outlined text-emerald-600">how_to_reg</span>
        <span class="text-[10px] bg-emerald-100 text-emerald-800 px-2.5 py-1 rounded-full font-bold uppercase tracking-wider">Hadir</span>
      </div>
      <div class="text-4xl font-black text-slate-900"><?= $totalKehadiran ?></div>
      <div class="text-xs font-bold uppercase tracking-widest text-slate-500 mt-2">Total Kehadiran</div>
    </div>

    <!-- Rata-rata Kehadiran -->
    <div class="bg-white border border-slate-100 p-6 rounded-2xl transition-all hover:-translate-y-1 hover:shadow-xl hover:border-amber-100">
      <div class="flex justify-between items-start mb-4">
        <span class="material-symbols-outlined text-amber-600">trending_up</span>
      </div>
      <div class="text-4xl font-black text-slate-900"><?= $rataKehadiran ?>%</div>
      <div class="text-xs font-bold uppercase tracking-widest text-slate-500 mt-2">Rata-rata Kehadiran</div>
    </div>
  </div>

  <!-- Main Content Grid -->
  <div class="asymmetric-grid">
    <!-- Rekap Kehadiran per Event -->
    <div class="space-y-6">
      <div class="bg-white border border-slate-100 p-8 rounded-[2rem] shadow-sm">
        <h2 class="text-xl font-black text-slate-900 mb-6">Rekap Kehadiran per Kegiatan</h2>
        <?php if (empty($eventStats)): ?>
        <div class="text-center py-10 text-slate-400">
            <span class="material-symbols-outlined text-4xl mb-3 block">event_busy</span>
            <p class="font-bold">Belum ada data kehadiran.</p>
        </div>
        <?php else: ?>
        <div class="space-y-3 max-h-[600px] overflow-y-auto pr-2">
            <?php foreach ($eventStats as $ev): ?>
            <div class="p-4 rounded-xl bg-surface hover:bg-slate-50 transition-colors border border-slate-100 <?= $filterEventId == $ev['id'] ? 'ring-2 ring-primary/30 bg-blue-50/50' : '' ?>">
                <div class="flex items-center gap-4 mb-3">
                    <div class="flex-shrink-0 w-12 h-12 bg-primary/10 rounded-2xl flex flex-col items-center justify-center">
                        <span class="text-primary font-black text-sm leading-none"><?= date('d', strtotime($ev['waktu_mulai'])) ?></span>
                        <span class="text-primary/60 font-bold text-[10px] uppercase"><?= date('M', strtotime($ev['waktu_mulai'])) ?></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-900 text-sm truncate"><?= htmlspecialchars($ev['nama']) ?></p>
                        <p class="text-xs text-slate-500"><?= date('H:i', strtotime($ev['waktu_mulai'])) ?> – <?= date('H:i', strtotime($ev['waktu_selesai'])) ?></p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span class="text-lg font-black <?= $ev['persentase'] >= 75 ? 'text-emerald-600' : ($ev['persentase'] >= 50 ? 'text-amber-600' : 'text-red-500') ?>"><?= $ev['persentase'] ?>%</span>
                        <p class="text-[10px] text-slate-400"><?= $ev['total_hadir'] ?>/<?= $ev['total_anggota'] ?></p>
                    </div>
                </div>
                <!-- Progress Bar -->
                <div class="w-full bg-slate-100 rounded-full h-1.5">
                    <div class="h-1.5 rounded-full transition-all <?= $ev['persentase'] >= 75 ? 'bg-emerald-500' : ($ev['persentase'] >= 50 ? 'bg-amber-500' : 'bg-red-400') ?>" style="width: <?= min($ev['persentase'], 100) ?>%"></div>
                </div>

            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
    
    <!-- Sidebar: Detail Kehadiran Event yang Dipilih / Daftar Anggota -->
    <div class="space-y-6">

      <!-- Daftar Anggota -->
      <div class="bg-white border border-slate-100 p-8 rounded-[2rem] shadow-sm">
        <div class="flex items-center justify-between mb-8">
          <h2 class="text-xl font-black text-slate-900">Daftar Anggota</h2>
          <a href="index.php?page=kepengurusan_ukm&ukm_id=<?= $ukmId ?>" class="text-primary text-xs font-bold hover:underline uppercase tracking-wider">
              Lihat Kepengurusan →
          </a>
        </div>
        
        <?php if (empty($anggotaList)): ?>
        <div class="text-center py-10 text-slate-400">
            <span class="material-symbols-outlined text-4xl mb-3 block">group_off</span>
            <p class="font-bold">Belum ada anggota terdaftar.</p>
        </div>
        <?php else: ?>
        <div class="space-y-3">
          <?php foreach (array_slice($anggotaList, 0, 12) as $i => $anggota): ?>
          <div class="flex items-center gap-4 p-4 rounded-xl <?= $i === 0 ? 'bg-blue-50/50 border border-blue-100' : 'hover:bg-slate-50 border border-transparent hover:border-slate-100' ?> transition-colors">
            <span class="w-6 text-sm font-black <?= $i === 0 ? 'text-primary' : 'text-slate-400' ?>">
                <?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?>
            </span>
            <div class="w-10 h-10 rounded-full overflow-hidden bg-slate-200 shrink-0">
              <?php if (!empty($anggota['foto_path'])): ?>
                  <img src="<?= htmlspecialchars($anggota['foto_path']) ?>" class="w-full h-full object-cover" alt="">
              <?php else: ?>
                  <div class="w-full h-full flex items-center justify-center bg-primary/10 text-primary font-bold text-sm">
                      <?= strtoupper(substr($anggota['nama'], 0, 1)) ?>
                  </div>
              <?php endif; ?>
            </div>
            <div class="flex-grow min-w-0">
              <div class="text-sm font-bold text-slate-900 truncate"><?= htmlspecialchars($anggota['nama']) ?></div>
              <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest"><?= htmlspecialchars($anggota['jabatan'] ?? 'Anggota') ?></div>
            </div>
            <span class="flex-shrink-0 text-[10px] px-2.5 py-1 rounded-full font-bold uppercase <?= ($anggota['status'] ?? '') === 'aktif' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>">
                <?= htmlspecialchars($anggota['status'] ?? 'aktif') ?>
            </span>
          </div>
          <?php endforeach; ?>
          <?php if (count($anggotaList) > 12): ?>
          <p class="text-center text-xs text-slate-400 font-bold pt-2">+ <?= count($anggotaList) - 12 ?> anggota lainnya</p>
          <?php endif; ?>
        </div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>
