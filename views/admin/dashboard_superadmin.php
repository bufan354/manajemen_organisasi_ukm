<div class="p-8 max-w-7xl mx-auto w-full space-y-8">
    <!-- Welcome Header -->
    <div class="flex justify-between items-end">
        <div>
            <div class="flex items-center gap-2 text-primary font-bold text-xs uppercase tracking-widest mb-1">
                <span class="material-symbols-outlined text-sm">public</span> Pusat Komando
            </div>
            <h2 class="text-3xl font-extrabold text-on-surface tracking-tight">Dashboard Sistem</h2>
            <p class="text-on-surface-variant font-medium mt-1">Ringkasan aktivitas seluruh <?= h($ENTITY) ?>, perangkat, dan infrastruktur IoT.</p>
        </div>
        <div class="hidden md:flex items-center gap-3">
            <button class="flex items-center gap-2 px-5 py-2.5 bg-surface-container-lowest text-on-surface border border-outline-variant/15 font-medium rounded-xl hover:bg-surface-container-low transition-all">
                <span class="material-symbols-outlined text-xl">ios_share</span> Export Laporan
            </button>
            <a href="index.php?page=ukm" class="flex items-center gap-2 px-5 py-2.5 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/20 hover:bg-primary-container active:scale-95 transition-all outline-none">
                <span class="material-symbols-outlined text-xl">hub</span> Kelola <?= h($ENTITY) ?>
            </a>
        </div>
    </div>

    <!-- Top Stats (Bento Macro) -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Main Highlight -->
        <div class="bg-primary-container text-on-primary-container p-6 rounded-3xl relative overflow-hidden md:col-span-2">
            <div class="relative z-10 flex flex-col h-full justify-between">
                <div>
                    <h3 class="text-xs font-black uppercase tracking-[0.2em] opacity-80">Populasi Global</h3>
                    <p class="text-5xl font-black mt-2 tracking-tighter"><?= number_format($totalAnggota ?? 0) ?></p>
                    <p class="text-sm mt-1 opacity-90 font-medium">Total Mahasiswa Aktif se-Universitas</p>
                </div>
                <div class="mt-6 inline-flex items-center gap-2 bg-on-primary-container/10 w-max px-3 py-1.5 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-secondary-container animate-pulse"></span>
                    <span class="text-[10px] font-bold uppercase tracking-widest">Live Syncing</span>
                </div>
            </div>
            <span class="material-symbols-outlined absolute -right-6 text-[150px] opacity-10 rotate-12 top-1/2 -translate-y-1/2" style="font-variation-settings: 'FILL' 1;">groups</span>
        </div>
        
        <!-- Minor Highlight 1 -->
        <div class="bg-surface-container-lowest border border-surface-container p-6 rounded-3xl flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">corporate_fare</span>
                </div>
                <h3 class="text-3xl font-black text-on-surface"><?= $totalUkm ?? 0 ?></h3>
            </div>
            <p class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mt-2"><?= h($ENTITY) ?> Aktif</p>
        </div>

        <!-- Minor Highlight 2 -->
        <div class="bg-surface-container-lowest border border-surface-container p-6 rounded-3xl flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">event</span>
                </div>
                <h3 class="text-3xl font-black text-on-surface"><?= $totalEvent ?? 0 ?></h3>
            </div>
            <p class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mt-2">Total Event</p>
        </div>
    </div>

    <!-- Analytics Section -->
    <div class="grid grid-cols-1 gap-6">
        <div class="bg-surface-container-lowest border border-surface-container rounded-3xl p-6 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="font-bold text-lg text-slate-800">Performa Kehadiran Global</h3>
                    <p class="text-xs text-slate-500 font-medium">Perbandingan tingkat kehadiran rata-rata antar seluruh <?= h($ENTITY) ?> aktif.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">Global Analytics</span>
                </div>
            </div>
            
            <div class="h-[350px] w-full relative">
                <?php if (empty($globalUkmStats)): ?>
                <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 gap-2">
                    <span class="material-symbols-outlined text-4xl">monitoring</span>
                    <p class="text-sm font-medium">Belum ada data UKM yang terkumpul.</p>
                </div>
                <?php endif; ?>
                <canvas id="globalUkmChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: UKM Overview -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-surface-container-lowest border border-surface-container rounded-3xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="font-bold text-lg text-slate-800">Daftar <?= h($ENTITY) ?> Terdaftar</h3>
                        <p class="text-xs text-slate-500 font-medium">Monitoring seluruh unit kegiatan yang terintegrasi sistem IoT.</p>
                    </div>
                    <a href="index.php?page=ukm" class="text-xs font-bold text-primary hover:underline">Kelola →</a>
                </div>
                
                <div class="space-y-4">
                    <?php if (empty($ukmList)): ?>
                    <p class="text-sm text-on-surface-variant text-center py-6">Belum ada <?= h($ENTITY) ?> terdaftar.</p>
                    <?php else: foreach (array_slice($ukmList, 0, 5) as $ukm): ?>
                    <a href="index.php?page=dashboard&ukm_id=<?= $ukm['id'] ?>" class="flex items-center gap-4 group cursor-pointer p-2 hover:bg-slate-50 rounded-xl transition-all duration-300">
                        <div class="w-12 h-12 bg-slate-100 rounded-xl overflow-hidden flex-shrink-0">
                            <?php if (!empty($ukm['logo_path'])): ?>
                                <img src="<?= htmlspecialchars($ukm['logo_path']) ?>" alt="<?= htmlspecialchars($ukm['singkatan'] ?? '') ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($ukm['singkatan'] ?? $ukm['nama']) ?>&background=0D8ABC&color=fff" alt="" class="w-full h-full object-cover">
                            <?php endif; ?>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-bold text-sm text-slate-800 group-hover:text-primary transition-colors"><?= htmlspecialchars($ukm['singkatan'] ?? $ukm['nama']) ?></span>
                                <span class="text-xs font-bold text-primary"><?= htmlspecialchars($ukm['kategori'] ?? '-') ?></span>
                            </div>
                            <p class="text-xs text-slate-400 truncate"><?= htmlspecialchars(mb_substr($ukm['deskripsi'] ?? '', 0, 60)) ?></p>
                        </div>
                    </a>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column: System Info -->
        <div class="space-y-6">
            <div class="bg-surface-container-lowest border border-surface-container rounded-3xl p-6 shadow-sm">
                <h3 class="font-bold text-lg text-slate-800 mb-6">Statistik Sistem</h3>
                <div class="space-y-4">
                    <div class="p-4 bg-slate-50 rounded-2xl flex items-center justify-between border border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-green-500"></div>
                            <span class="text-sm font-bold text-slate-700">Total Berita</span>
                        </div>
                        <span class="font-mono font-bold text-slate-900"><?= $totalBerita ?? 0 ?></span>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-2xl flex items-center justify-between border border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                            <span class="text-sm font-bold text-slate-700">Total Event</span>
                        </div>
                        <span class="font-mono font-bold text-slate-900"><?= $totalEvent ?? 0 ?></span>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-2xl flex items-center justify-between border border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-purple-500"></div>
                            <span class="text-sm font-bold text-slate-700">Total <?= h($ENTITY) ?></span>
                        </div>
                        <span class="font-mono font-bold text-slate-900"><?= $totalUkm ?? 0 ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js and Data Integration -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('globalUkmChart');
    if (!ctx) return;

    // Data from PHP
    const ukmData = <?= json_encode($globalUkmStats ?? []) ?>;
    
    if (ukmData.length === 0) {
        ctx.style.display = 'none';
        return;
    }

    // Sort by rate and slice top 10 for readability if many
    const topUkms = ukmData.slice(0, 10);
    const labels = topUkms.map(u => u.singkatan || u.nama);
    const dataPoints = topUkms.map(u => u.attendance_rate);
    
    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(16, 185, 129, 0.8)'); // emerald-500
    gradient.addColorStop(1, 'rgba(16, 185, 129, 0.1)');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Rata-rata Kehadiran (%)',
                data: dataPoints,
                backgroundColor: gradient,
                borderColor: '#10b981',
                borderWidth: 2,
                borderRadius: 12,
                borderSkipped: false,
                barPercentage: 0.5,
                categoryPercentage: 0.8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y', // Horizontal bars for better readability
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    cornerRadius: 12,
                    titleFont: { family: 'Inter', size: 12, weight: 'bold' },
                    bodyFont: { family: 'Inter', size: 14 },
                    callbacks: {
                        label: function(context) {
                            return ` ${context.parsed.x}% Kehadiran`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    max: 100,
                    grid: { color: 'rgba(0, 0, 0, 0.03)', drawBorder: false },
                    ticks: {
                        color: '#64748b',
                        font: { size: 10, weight: '600' },
                        callback: val => val + '%'
                    }
                },
                y: {
                    grid: { display: false, drawBorder: false },
                    ticks: {
                        color: '#475569',
                        font: { size: 12, weight: '700' }
                    }
                }
            }
        }
    });
});
</script>
