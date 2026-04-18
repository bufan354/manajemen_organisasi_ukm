<?php
// Hero Section: Load dynamic settings
$_heroJudul     = getSetting('hero_judul', 'SISTEM ABSENSI & MANAJEMEN ORGANISASI');
$_heroDeskripsi = getSetting('hero_deskripsi', 'Optimalkan efisiensi kehadiran dengan teknologi berbasis Fingerprint + ESP32. Monitoring real-time untuk transparansi organisasi digital.');
$_heroBtn1Label = getSetting('hero_btn1_label', 'Jelajahi ' . $ENTITY);
$_heroBtn1Link  = getSetting('hero_btn1_link', 'index.php?page=katalog_ukm');
$_heroBtn2Label = getSetting('hero_btn2_label', 'Dokumentasi API');
$_heroBtn2Link  = getSetting('hero_btn2_link', 'index.php?page=tentang');
$_heroGambar    = getSetting('hero_gambar', '');
$_heroOverlay   = (int)getSetting('hero_overlay_opacity', '20');

// Split judul untuk styling baris kedua dengan warna primary
$_judulParts = explode('&', $_heroJudul, 2);
$_judulBaris1 = trim($_judulParts[0] ?? '');
$_judulBaris2 = isset($_judulParts[1]) ? trim($_judulParts[1]) : '';
?>
<!-- Hero Section -->
<section class="relative min-h-[870px] flex items-center overflow-hidden bg-surface-container">
    <div class="absolute inset-0 z-0 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-primary/10 via-surface to-surface-container opacity-60"></div>
        <!-- Decorative IoT Pattern -->
        <div class="absolute right-0 top-0 w-1/2 h-full opacity-10 pointer-events-none">
            <svg height="100%" viewbox="0 0 400 400" width="100%">
                <path d="M100 0 L100 400 M200 0 L200 400 M300 0 L300 400 M0 100 L400 100 M0 200 L400 200 M0 300 L400 300" fill="none" stroke="currentColor" stroke-width="0.5"></path>
            </svg>
        </div>
        <!-- Parallax Image -->
        <?php if (!empty($_heroGambar)): ?>
        <img class="parallax-bg absolute right-[-10%] top-1/2 -translate-y-1/2 w-3/5 h-4/5 object-cover rounded-3xl blur-sm mix-blend-overlay scale-110" style="opacity: <?= (100 - $_heroOverlay) / 100 ?>" alt="Hero Background" src="<?= htmlspecialchars($_heroGambar) ?>"/>
        <?php else: ?>
        <img class="parallax-bg absolute right-[-10%] top-1/2 -translate-y-1/2 w-3/5 h-4/5 object-cover rounded-3xl opacity-20 blur-sm mix-blend-overlay scale-110" alt="Hero IoT Setup" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBGnB-fagjOzlzpcQ62UKO0u6eWjKcR4btmdqMc3Owx4OkX82HoClFF2LuIV39vsnISu0tZP8W8tMNw0YOVnAVwRJ54dLmSwejEP-53fUhZ3_lktANI9B2wj3HfIobsw5qNu4Gju8KykAicua31xZXswiDTZZh_5boNCexkoFjes9qfbA7v141toNIePDpnqTpfUX_xDzXiZ208uQb-OXF8OLnDUO51UBg3PkbJZ1KsJtasPxeKf_buBeJok_bbIva7NmDjyPU8ags"/>
        <?php endif; ?>
    </div>
    
    <div class="relative z-10 max-w-7xl mx-auto px-6 w-full py-20">
        <div class="max-w-3xl">
            <span class="inline-block px-4 py-1.5 mb-6 text-xs font-semibold tracking-widest text-primary bg-primary-fixed rounded-full">INTERNET OF THINGS SOLUTIONS</span>
            <h1 class="text-5xl md:text-7xl font-bold tracking-tighter text-on-surface mb-8 leading-[1.1]">
                <?= htmlspecialchars($_judulBaris1) ?> <?php if ($_judulBaris2): ?>&<br/>
                <span class="text-primary"><?= htmlspecialchars($_judulBaris2) ?></span>
                <?php endif; ?>
            </h1>
            <p class="text-xl text-on-surface-variant mb-12 max-w-xl font-light leading-relaxed">
                <?= htmlspecialchars($_heroDeskripsi) ?>
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="<?= htmlspecialchars($_heroBtn1Link) ?>" class="bg-primary text-white px-8 py-4 rounded-xl font-semibold text-lg shadow-lg shadow-primary/20 transform hover:scale-[1.02] active:scale-95 transition-all text-center inline-block">
                    <?= htmlspecialchars($_heroBtn1Label) ?>
                </a>
                <a href="<?= htmlspecialchars($_heroBtn2Link) ?>" class="bg-surface-container-high text-on-surface-variant px-8 py-4 rounded-xl font-semibold text-lg hover:bg-surface-dim transition-all">
                    <?= htmlspecialchars($_heroBtn2Label) ?>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- <?= h($ENTITY) ?> Grid Section -->
<section class="py-24 bg-surface px-6">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
            <div>
                <h2 class="text-3xl font-bold tracking-tight text-on-surface mb-4">DAFTAR <?= h(strtoupper($ENTITY)) ?></h2>
                <p class="text-on-surface-variant max-w-md">Pemantauan aktivitas real-time berdasarkan data absensi IoT harian.</p>
            </div>
            <div class="flex flex-col items-start md:items-end gap-3">
                <div class="flex items-center gap-2">
                    <span class="h-1 w-12 bg-primary-container"></span>
                    <span class="text-xs font-bold text-primary uppercase tracking-widest">Live Activity Monitor</span>
                </div>
                <select id="chartPeriodFilter" onchange="updateChartData(this.value)" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-primary/20 outline-none transition-all cursor-pointer shadow-sm min-w-[200px]">
                    <option value="week">1 Minggu Terakhir</option>
                    <option value="month" selected>Bulan Ini</option>
                    <option value="year">1 Tahun Terakhir</option>
                    <option value="all">Sepanjang Waktu</option>
                </select>
            </div>
        </div>
        
        <!-- Live Activity Monitor Chart -->
        <!-- <div class="mb-16 bg-surface-container-lowest p-6 md:p-8 rounded-[2rem] shadow-sm border border-outline-variant/10">
            <div class="h-[300px] w-full">
                <canvas id="ukmChart"></canvas>
            </div>
        </div> -->
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (empty($ukmList)): ?>
            <div class="col-span-full text-center py-16 text-on-surface-variant">
                <span class="material-symbols-outlined text-4xl text-outline mb-2 block">corporate_fare</span>
                <p class="text-lg font-bold">Belum ada <?= h($ENTITY) ?> terdaftar</p>
            </div>
            <?php else: foreach (array_slice($ukmList, 0, 6) as $ukm): ?>
            <div class="group bg-surface-container-lowest p-8 rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl hover:shadow-on-surface/5 border border-outline-variant/10">
                <div class="flex justify-between items-start mb-8">
                    <div class="p-3 bg-primary/5 rounded-xl text-primary group-hover:bg-primary group-hover:text-white transition-colors overflow-hidden w-12 h-12 flex items-center justify-center">
                        <?php if (!empty($ukm['logo_path'])): ?>
                            <img src="<?= htmlspecialchars($ukm['logo_path']) ?>" class="w-full h-full object-cover" alt="">
                        <?php else: ?>
                            <span class="material-symbols-outlined text-3xl">groups</span>
                        <?php endif; ?>
                    </div>
                    <span class="px-3 py-1 bg-secondary-container/50 text-secondary-fixed-variant text-[10px] font-bold uppercase rounded-md">Aktif</span>
                </div>
                <h3 class="text-xl font-bold mb-2 text-slate-800"><?= htmlspecialchars($ukm['singkatan'] ?? $ukm['nama']) ?></h3>
                <p class="text-sm text-on-surface-variant mb-6"><?= htmlspecialchars(mb_substr($ukm['deskripsi'] ?? '', 0, 80)) ?></p>
                <a href="index.php?page=detail_ukm&id=<?= $ukm['id'] ?>" class="w-full py-3 bg-surface-container-low text-on-surface font-semibold rounded-lg group-hover:bg-primary group-hover:text-white transition-all block text-center">Lihat Detail</a>
            </div>
            <?php endforeach; endif; ?>
        </div>

        <?php if (count($ukmList ?? []) > 6): ?>
        <div class="text-center mt-12">
            <a href="index.php?page=katalog_ukm" class="inline-flex items-center gap-2 px-8 py-3 bg-primary/10 text-primary font-bold rounded-xl hover:bg-primary/20 transition-colors">
                Lihat Semua <?= h($ENTITY) ?> <span class="material-symbols-outlined">arrow_forward</span>
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- News Section -->
<section class="py-24 bg-surface-container-low px-6">
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-16">
        <!-- Headline/Title Column -->
        <div class="lg:col-span-4">
            <h2 class="text-4xl font-bold tracking-tighter text-on-surface mb-6">BERITA TERKINI</h2>
            <p class="text-on-surface-variant mb-10 leading-relaxed">Informasi terbaru seputar kegiatan organisasi, pencapaian <?= h($ENTITY) ?>, dan pembaruan sistem IoT di lingkungan institusi.</p>
            <a href="index.php?page=berita_ukm" class="flex items-center gap-2 text-primary font-bold hover:gap-4 transition-all">
                Lihat Semua Berita
                <span class="material-symbols-outlined">arrow_forward</span>
            </a>
        </div>
        
        <!-- Vertical News List -->
        <div class="lg:col-span-8 flex flex-col gap-6">
            <?php if (empty($beritaList)): ?>
            <div class="text-center py-12 text-on-surface-variant">
                <p class="text-lg font-bold">Belum ada berita</p>
            </div>
            <?php else: 
                foreach (array_slice($beritaList, 0, 3) as $b): ?>
            <a href="index.php?page=detail_berita&id=<?= $b['id'] ?>" class="group flex flex-col md:flex-row gap-6 p-6 bg-surface-container-lowest rounded-xl transition-all duration-300 hover:shadow-xl hover:shadow-on-surface/5 border border-outline-variant/10">
                <div class="relative w-full md:w-48 h-32 flex-shrink-0 rounded-lg overflow-hidden bg-slate-100">
                    <?php if (!empty($b['gambar_path'])): ?>
                    <img class="w-full h-full object-cover" alt="<?= htmlspecialchars($b['judul']) ?>" src="<?= htmlspecialchars($b['gambar_path']) ?>"/>
                    <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-50 to-slate-100">
                        <span class="material-symbols-outlined text-3xl text-slate-300">image</span>
                    </div>
                    <?php endif; ?>
                    <div class="absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
                <div class="flex flex-col justify-center">
                    <div class="flex items-center gap-3 mb-2">
                        <?php
                            $kat = $b['kategori'] ?? 'Informasi';
                            $katColors = [
                                'Prestasi' => 'bg-secondary-container/50 text-secondary-fixed-variant',
                                'Pengumuman' => 'bg-amber-100 text-amber-700',
                                'Kegiatan' => 'bg-primary-fixed text-primary',
                                'Informasi' => 'bg-purple-100 text-purple-700',
                            ];
                            $katClass = $katColors[$kat] ?? 'bg-slate-100 text-slate-600';
                        ?>
                        <span class="px-2 py-0.5 <?= $katClass ?> text-[10px] font-bold uppercase rounded"><?= htmlspecialchars($kat) ?></span>
                        <span class="text-[11px] font-medium text-on-surface-variant"><?= date('d M Y', strtotime($b['created_at'])) ?></span>
                    </div>
                    <h4 class="text-lg font-bold text-on-surface group-hover:text-primary transition-colors mb-2"><?= htmlspecialchars($b['judul']) ?></h4>
                    <p class="text-sm text-on-surface-variant line-clamp-2"><?= htmlspecialchars(mb_substr(strip_tags($b['konten'] ?? ''), 0, 120)) ?>...</p>
                </div>
            </a>
            <?php endforeach; endif; ?>
        </div>
    </div>
</section>

<!-- Chart.js Plugin -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let ukmActivityChart = null;

document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('ukmChart').getContext('2d');
    
    // Inject data from PHP
    const labels = <?= $chartLabels ?? '[]' ?>;
    const dataRates = <?= $chartData ?? '[]' ?>;
    
    // Create gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.6)'); // blue-500
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');
    
    ukmActivityChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Tingkat Kehadiran (%)',
                data: dataRates,
                backgroundColor: gradient,
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
                barPercentage: 0.6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { family: 'Inter', size: 13 },
                    bodyFont: { family: 'Inter', size: 14, weight: 'bold' },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + '% Aktif';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false,
                    },
                    ticks: {
                        color: '#64748b',
                        font: { family: 'Inter', size: 11 },
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false,
                    },
                    ticks: {
                        color: '#475569',
                        font: { family: 'Inter', size: 12, weight: '500' }
                    }
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeOutQuart'
            }
        }
    });
});

function updateChartData(period) {
    const filterEl = document.getElementById('chartPeriodFilter');
    if (!filterEl || !ukmActivityChart) return;
    
    filterEl.disabled = true;
    filterEl.classList.add('opacity-50');

    fetch(`index.php?action=get_chart_activity&period=${period}`)
        .then(response => response.json())
        .then(data => {
            ukmActivityChart.data.labels = data.labels;
            ukmActivityChart.data.datasets[0].data = data.dataRates;
            ukmActivityChart.update();
        })
        .catch(error => console.error('Error fetching chart data:', error))
        .finally(() => {
            filterEl.disabled = false;
            filterEl.classList.remove('opacity-50');
        });
}
</script>
