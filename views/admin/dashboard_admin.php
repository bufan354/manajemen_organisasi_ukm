<div class="p-8 max-w-7xl mx-auto w-full space-y-8">
    <!-- Welcome Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <div class="flex items-center gap-2 text-primary font-bold text-xs uppercase tracking-widest mb-1">
                <span class="material-symbols-outlined text-sm"><?= $isPeeking ? 'visibility' : 'dashboard' ?></span> 
                <?= $isPeeking ? 'Mode Peninjauan UKM' : 'Dashboard Panel' ?>
            </div>
            <h2 class="text-3xl font-extrabold text-on-surface tracking-tight">
                <?= $isPeeking ? 'Analisis ' . h($ukm['singkatan'] ?? $ukm['nama']) : 'Selamat Datang, ' . h(Session::get('admin_nama') ?? 'Admin') ?>
            </h2>
            <p class="text-on-surface-variant font-medium mt-1">
                <?= $isPeeking ? 'Melihat detail aktivitas dan statistik organisasi dari sudut pandang admin.' : 'Ringkasan aktivitas terkini organisasi Anda.' ?>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <?php if ($isPeeking): ?>
            <a href="index.php?page=dashboard" class="flex items-center gap-2 px-5 py-2.5 bg-surface-container-highest text-on-surface border border-outline-variant/30 font-bold rounded-xl hover:bg-surface-dim transition-all shadow-sm">
                <span class="material-symbols-outlined text-xl">arrow_back</span>
                Kembali ke Pusat Komando
            </a>
            <?php else: ?>
            <span class="text-xs font-bold text-on-surface-variant uppercase tracking-widest"><?= date('l, d M Y') ?></span>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($ukm) && ($ukm['status'] ?? 'aktif') === 'nonaktif'): ?>
    <div class="bg-error-container border border-error/50 p-5 rounded-2xl text-error mb-4">
        <div class="flex gap-3 items-center">
            <span class="material-symbols-outlined text-3xl">warning</span>
            <div>
                <p class="font-bold text-base">UKM Saat Ini Berstatus Nonaktif</p>
                <p class="text-sm mt-1">Super Admin telah menonaktifkan UKM ini. Profil UKM dan segala kegiatannya disembunyikan dari halaman publik dan pendaftaran anggota dihentikan sementara.</p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Top Stats (Bento) -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Main Highlight -->
        <div class="bg-primary-container text-on-primary-container p-6 rounded-3xl relative overflow-hidden md:col-span-2">
            <div class="relative z-10 flex flex-col h-full justify-between">
                <div>
                    <h3 class="text-xs font-black uppercase tracking-[0.2em] opacity-80">Total Anggota</h3>
                    <p class="text-5xl font-black mt-2 tracking-tighter"><?= number_format($totalAnggota ?? 0) ?></p>
                    <p class="text-sm mt-1 opacity-90 font-medium">Mahasiswa Terdaftar di Database</p>
                </div>
                <div class="mt-6 inline-flex items-center gap-2 bg-on-primary-container/10 w-max px-3 py-1.5 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-secondary-container animate-pulse"></span>
                    <span class="text-[10px] font-bold uppercase tracking-widest">Live Database</span>
                </div>
            </div>
            <span class="material-symbols-outlined absolute -right-6 text-[150px] opacity-10 rotate-12 top-1/2 -translate-y-1/2" style="font-variation-settings: 'FILL' 1;">groups</span>
        </div>
        
        <!-- Kehadiran Hari Ini -->
        <div class="bg-surface-container-lowest border border-surface-container p-6 rounded-3xl flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">how_to_reg</span>
                </div>
                <h3 class="text-3xl font-black text-on-surface"><?= $todayKehadiran ?? 0 ?></h3>
            </div>
            <p class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mt-2">Kehadiran Hari Ini</p>
        </div>

        <!-- Event Aktif -->
        <div class="bg-surface-container-lowest border border-surface-container p-6 rounded-3xl flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 rounded-xl <?= !empty($activeEvent) ? 'bg-blue-50 text-blue-600' : 'bg-slate-50 text-slate-400' ?> flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">event</span>
                </div>
                <?php if (!empty($activeEvent)): ?>
                <h3 class="text-sm font-black text-on-surface leading-tight"><?= htmlspecialchars($activeEvent['nama']) ?></h3>
                <p class="text-[10px] text-slate-500 mt-1"><?= date('H:i', strtotime($activeEvent['waktu_mulai'])) ?> – <?= date('H:i', strtotime($activeEvent['waktu_selesai'])) ?></p>
                <?php else: ?>
                <h3 class="text-sm font-black text-slate-400">Tidak ada kegiatan</h3>
                <?php endif; ?>
            </div>
            <p class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mt-2">
                <?= !empty($activeEvent) ? '● Kegiatan Aktif' : 'Kegiatan Sekarang' ?>
            </p>
        </div>
    </div>

    <!-- Analytics Section -->
    <div class="grid grid-cols-1 gap-6">
        <div class="bg-surface-container-lowest border border-surface-container rounded-3xl p-6 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="font-bold text-lg text-slate-800">Tren Kehadiran Anggota</h3>
                    <p class="text-xs text-slate-500 font-medium">Analisis tingkat kehadiran pada 7 kegiatan terakhir organisasi Anda.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                    <span class="text-[10px] font-bold text-primary uppercase tracking-widest">Live Insight</span>
                </div>
            </div>
            
            <div class="h-[300px] w-full relative">
                <?php if (empty($attendanceStats)): ?>
                <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 gap-2">
                    <span class="material-symbols-outlined text-4xl">analytics</span>
                    <p class="text-sm font-medium">Data kegiatan belum terkumpul untuk grafik.</p>
                </div>
                <?php endif; ?>
                <canvas id="attendanceTrendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Recent Events -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-surface-container-lowest border border-surface-container rounded-3xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="font-bold text-lg text-slate-800">Kegiatan Terbaru</h3>
                        <p class="text-xs text-slate-500 font-medium">Daftar kegiatan yang mendatang atau sedang berjalan.</p>
                    </div>
                    <a href="index.php?page=event" class="text-xs font-bold text-primary hover:underline">Lihat Semua →</a>
                </div>
                
                <div class="space-y-4">
                    <?php if (empty($eventList)): ?>
                    <p class="text-sm text-on-surface-variant text-center py-6">Belum ada kegiatan.</p>
                    <?php else: foreach (array_slice($eventList, 0, 5) as $ev): ?>
                    <div class="flex items-center gap-4 p-3 hover:bg-slate-50 rounded-xl transition-colors">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 font-bold flex-shrink-0">
                            <?= strtoupper(substr($ev['nama'], 0, 1)) ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-sm text-slate-800 truncate"><?= htmlspecialchars($ev['nama']) ?></p>
                            <p class="text-xs text-slate-400"><?= date('d M Y', strtotime($ev['waktu_mulai'])) ?> · <?= htmlspecialchars($ev['lokasi'] ?? '-') ?></p>
                        </div>
                        <?php if (!empty($ev['status_absensi'])): ?>
                        <span class="px-2 py-0.5 bg-secondary-container text-on-secondary-container text-[10px] font-bold rounded-full uppercase">Aktif</span>
                        <?php else: ?>
                        <span class="px-2 py-0.5 bg-slate-100 text-slate-500 text-[10px] font-bold rounded-full uppercase">Selesai</span>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column: Quick Stats -->
        <div class="space-y-6">
            <div class="bg-surface-container-lowest border border-surface-container rounded-3xl p-6 shadow-sm">
                <h3 class="font-bold text-lg text-slate-800 mb-6">Anggota Terbaru</h3>
                <div class="space-y-4">
                    <?php if (empty($anggotaList)): ?>
                    <p class="text-sm text-on-surface-variant text-center py-6">Belum ada anggota.</p>
                    <?php else: foreach (array_slice($anggotaList, 0, 5) as $ang): ?>
                    <div class="flex items-center gap-3 p-2">
                        <div class="w-9 h-9 rounded-full bg-primary-container flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                            <?= strtoupper(substr($ang['nama'], 0, 1)) ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-700 truncate"><?= htmlspecialchars($ang['nama']) ?></p>
                            <p class="text-[10px] text-slate-400"><?= htmlspecialchars($ang['jabatan'] ?? 'Anggota') ?></p>
                        </div>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
                <a href="index.php?page=anggota" class="block mt-4 text-center text-xs font-bold text-primary hover:underline">Lihat Semua Anggota →</a>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js and Data Integration -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('attendanceTrendChart');
    if (!ctx) return;

    // Data from PHP
    const statsData = <?= json_encode($attendanceStats ?? []) ?>;
    
    if (statsData.length === 0) {
        ctx.style.display = 'none';
        return;
    }

    const labels = statsData.map(s => s.nama.length > 15 ? s.nama.substring(0, 12) + '...' : s.nama);
    const dataPoints = statsData.map(s => s.persentase);
    
    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.4)'); // blue-500
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0.05)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Tingkat Kehadiran',
                data: dataPoints,
                borderColor: '#2563eb', // blue-600
                backgroundColor: gradient,
                borderWidth: 4,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#2563eb',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    titleFont: { family: 'Inter', size: 12, weight: 'bold' },
                    bodyFont: { family: 'Inter', size: 14 },
                    callbacks: {
                        label: function(context) {
                            return ` ${context.parsed.y}% Anggota Hadir`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: { color: 'rgba(0, 0, 0, 0.03)', drawBorder: false },
                    ticks: {
                        color: '#64748b',
                        font: { size: 10, weight: '600' },
                        callback: val => val + '%'
                    }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: {
                        color: '#64748b',
                        font: { size: 10, weight: '600' }
                    }
                }
            }
        }
    });
});
</script>
