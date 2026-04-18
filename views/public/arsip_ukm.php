<?php include __DIR__ . '/components/ukm_subnav.php'; ?>
<div class="max-w-7xl mx-auto px-6 py-16">
    <!-- Header -->
    <div class="mb-12 border-b border-slate-200/50 pb-8">
        <span class="text-xs font-bold text-primary uppercase tracking-[0.2em] mb-2 block">SEJARAH ORGANISASI</span>
        <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tighter">Arsip Kepengurusan</h1>
        <p class="text-slate-600 max-w-lg mt-4 text-sm leading-relaxed">
            Dokumentasi lengkap kabinet kepengurusan dari masa ke masa.
        </p>
    </div>

    <!-- Timeline -->
    <?php if (empty($periodeList)): ?>
    <div class="text-center py-16 text-slate-400">
        <span class="material-symbols-outlined text-5xl mb-4 block">folder_off</span>
        <p class="text-lg font-bold">Belum ada arsip kepengurusan</p>
        <p class="text-sm mt-1">Data arsip belum tersedia.</p>
    </div>
    <?php else: ?>
    <div class="relative">
        <!-- Timeline Line -->
        <div class="absolute left-8 md:left-1/2 top-0 bottom-0 w-px bg-gradient-to-b from-primary/40 via-primary/20 to-transparent"></div>
        
        <div class="space-y-12">
            <?php foreach ($periodeList as $i => $kp): ?>
            <div class="relative flex items-start gap-8 <?= $i % 2 === 0 ? 'md:flex-row' : 'md:flex-row-reverse' ?>">
                <!-- Timeline Dot -->
                <div class="absolute left-8 md:left-1/2 -translate-x-1/2 w-4 h-4 rounded-full <?= $kp['is_active'] ? 'bg-green-500 ring-green-500/30' : 'bg-primary ring-primary/20' ?> ring-4  z-10"></div>
                
                <!-- Content Card -->
                <div class="ml-16 md:ml-0 md:w-[calc(50%-2rem)] bg-white rounded-3xl p-8 shadow-sm border <?= $kp['is_active'] ? 'border-green-200 shadow-green-100' : 'border-slate-100' ?> hover:shadow-lg transition-all">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full"><?= htmlspecialchars($kp['tahun_mulai']) ?> - <?= htmlspecialchars($kp['tahun_selesai']) ?></span>
                        <?php if ($kp['is_active']): ?>
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">check_circle</span> Periode Aktif</span>
                        <?php endif; ?>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-2"><?= htmlspecialchars($kp['nama']) ?></h3>
                    <p class="text-sm text-slate-500 leading-relaxed mb-4"><?= htmlspecialchars($kp['deskripsi'] ?? '') ?></p>
                    
                    <a href="index.php?page=kepengurusan_ukm&ukm_id=<?= $ukm['id'] ?>&periode_id=<?= $kp['id'] ?>" class="inline-flex items-center gap-2 text-xs font-bold text-primary hover:underline">
                        <span class="material-symbols-outlined text-sm">groups</span> Lihat Struktur Pengurus
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
