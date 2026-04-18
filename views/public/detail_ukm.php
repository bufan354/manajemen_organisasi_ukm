<?php
// Safety: jika UKM tidak ditemukan
if (empty($ukm)) {
    echo '<div class="max-w-2xl mx-auto py-24 text-center px-6">
        <span class="material-symbols-outlined text-6xl text-slate-300 mb-4 block">search_off</span>
        <h1 class="text-2xl font-bold text-slate-700"><?= h($ENTITY) ?> Tidak Ditemukan</h1>
        <p class="text-slate-500 mt-2">Data <?= h($ENTITY) ?> yang Anda cari tidak tersedia.</p>
        <a href="index.php?page=katalog_ukm" class="inline-block mt-6 px-6 py-3 bg-primary text-white rounded-xl font-bold">← Kembali ke Katalog</a>
    </div>';
    return;
}

$ukmId       = $ukm['id'];
$ukmNama     = htmlspecialchars($ukm['nama'] ?? $ENTITY);
$ukmSlogan   = htmlspecialchars($ukm['slogan']   ?? 'Berkarya, Berinovasi, Berprestasi');
$ukmDesk     = htmlspecialchars($ukm['deskripsi'] ?? '');
$ukmFokus    = htmlspecialchars($ukm['kategori'] ?? '-');
$ukmLokasi   = htmlspecialchars($ukm['lokasi']   ?? '-');
$ukmBerdiri  = !empty($ukm['tanggal_berdiri'])
    ? date('d F Y', strtotime($ukm['tanggal_berdiri']))
    : 'Tidak diketahui';
$singkatan   = htmlspecialchars($ukm['singkatan'] ?? '');

$hasHeader   = !empty($ukm['header_path']);
$hasLogo     = !empty($ukm['logo_path']);
?>
<link href="assets/public/css/detail-ukm.css" rel="stylesheet"/>

<style>
/* Parallax hero */
#hero-section {
    position: relative;
    height: 620px;
    overflow: hidden;
    display: flex;
    align-items: center;
}
#hero-bg {
    position: absolute;
    inset: -60px -20px;
    background-size: cover;
    background-position: center;
    will-change: transform;
    <?php if ($hasHeader): ?>
    background-image: url('<?= htmlspecialchars($ukm['header_path']) ?>');
    <?php else: ?>
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #1d4ed8 100%);
    <?php endif; ?>
}
#hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,0.35) 0%, rgba(0,0,0,0.55) 100%);
    backdrop-filter: blur(<?= $hasHeader ? '3px' : '0px' ?>);
}
#hero-logo {
    will-change: transform;
}
</style>

<?php include __DIR__ . '/components/ukm_subnav.php'; ?>

<!-- Section 1: Hero dengan Parallax -->
<section id="hero-section">
    <!-- Background (parallax slow) -->
    <div id="hero-bg"></div>
    <!-- Overlay blur -->
    <div id="hero-overlay"></div>

    <div class="max-w-7xl mx-auto px-6 relative z-10 flex items-center gap-10 w-full">
        <!-- Logo lingkaran (parallax lebih lambat) -->
        <div id="hero-logo" class="hidden md:block flex-shrink-0">
            <div class="w-32 h-32 rounded-full border-4 border-white/80 shadow-2xl overflow-hidden bg-white/10 backdrop-blur-md">
                <?php if ($hasLogo): ?>
                <img src="<?= htmlspecialchars($ukm['logo_path']) ?>" class="w-full h-full object-cover" alt="Logo <?= $ukmNama ?>">
                <?php else: ?>
                <div class="w-full h-full flex items-center justify-center text-white text-3xl font-black">
                    <?= strtoupper(substr($singkatan ?: $ukmNama, 0, 2)) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Info UKM -->
        <div class="space-y-4 flex-1">
            <?php if ($ukmFokus !== '-'): ?>
            <span class="inline-block bg-white/20 backdrop-blur-md px-4 py-1.5 rounded-full text-white text-xs font-bold tracking-widest uppercase border border-white/30 shadow-xl">
                <?= $ukmFokus ?>
            </span>
            <?php endif; ?>
            <h1 class="text-5xl lg:text-6xl font-black text-white tracking-tighter leading-none drop-shadow-lg">
                <?= $ukmNama ?>
            </h1>
            <?php if ($singkatan): ?>
            <p class="text-xl text-white/80 font-bold"><?= $singkatan ?></p>
            <?php endif; ?>
            <p class="text-lg text-blue-100 font-light italic">"<?= $ukmSlogan ?>"</p>

            <!-- Quick stats inline -->
            <div class="flex gap-6 mt-4">
                <div class="text-center">
                    <p class="text-2xl font-black text-white"><?= $totalAnggota ?? 0 ?></p>
                    <p class="text-xs text-blue-200 font-bold uppercase tracking-wider">Anggota</p>
                </div>
                <?php if ($ukmBerdiri !== 'Tidak diketahui'): ?>
                <div class="text-center">
                    <p class="text-2xl font-black text-white"><?= date('Y', strtotime($ukm['tanggal_berdiri'])) ?></p>
                    <p class="text-xs text-blue-200 font-bold uppercase tracking-wider">Berdiri</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Section 2: TENTANG UKM -->
<section class="py-20 bg-surface" id="about">
    <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-12 gap-16">
        <div class="md:col-span-7 space-y-8">
            <div class="space-y-4">
                <h2 class="text-xs font-bold text-primary tracking-[0.2em] uppercase">The Story</h2>
                <h3 class="text-4xl font-semibold text-slate-900 leading-tight">TENTANG <?= strtoupper($ukmNama) ?></h3>
                <?php if ($ukmDesk): ?>
                <p class="text-lg text-slate-600 leading-relaxed"><?= nl2br($ukmDesk) ?></p>
                <?php else: ?>
                <p class="text-lg text-slate-400 leading-relaxed italic">Deskripsi <?= h($ENTITY) ?> belum diisi. Admin dapat menambahkannya melalui panel administrasi.</p>
                <?php endif; ?>
            </div>
            <div class="grid grid-cols-2 gap-8 py-8 border-t border-slate-200/50">
                <div class="space-y-1">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Didirikan</p>
                    <p class="text-xl font-bold text-slate-800"><?= $ukmBerdiri ?></p>
                </div>
                <div class="space-y-1">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Jumlah Anggota</p>
                    <p class="text-xl font-bold text-slate-800"><?= ($totalAnggota ?? 0) ?> Aktif</p>
                </div>
                <div class="space-y-1">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Lokasi</p>
                    <p class="text-xl font-bold text-slate-800"><?= $ukmLokasi ?></p>
                </div>
                <div class="space-y-1">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Fokus</p>
                    <p class="text-xl font-bold text-slate-800"><?= $ukmFokus ?></p>
                </div>
            </div>
        </div>

        <!-- Kartu info kanan -->
        <div class="md:col-span-5 bg-white rounded-[2rem] p-10 space-y-6 border border-slate-100 shadow-sm">
            <?php if ($hasLogo): ?>
            <div class="w-20 h-20 rounded-full overflow-hidden border-2 border-slate-100 shadow">
                <img src="<?= htmlspecialchars($ukm['logo_path']) ?>" class="w-full h-full object-cover" alt="Logo <?= $ukmNama ?>">
            </div>
            <?php endif; ?>
            <h4 class="text-xl font-bold text-slate-900">Informasi <?= h($ENTITY) ?></h4>
            <ul class="space-y-4 divide-y divide-slate-100">
                <li class="flex justify-between pt-4 first:pt-0">
                    <span class="text-sm text-slate-500">Singkatan</span>
                    <span class="text-sm font-bold text-slate-800"><?= $singkatan ?: '-' ?></span>
                </li>
                <li class="flex justify-between pt-4">
                    <span class="text-sm text-slate-500">Bidang</span>
                    <span class="text-sm font-bold text-slate-800"><?= $ukmFokus ?></span>
                </li>
                <li class="flex justify-between pt-4">
                    <span class="text-sm text-slate-500">Berdiri</span>
                    <span class="text-sm font-bold text-slate-800"><?= $ukmBerdiri ?></span>
                </li>
                <li class="flex justify-between pt-4">
                    <span class="text-sm text-slate-500">Lokasi</span>
                    <span class="text-sm font-bold text-slate-800 text-right max-w-[60%]"><?= $ukmLokasi ?></span>
                </li>
                <li class="flex justify-between pt-4">
                    <span class="text-sm text-slate-500">Anggota Aktif</span>
                    <span class="text-sm font-bold text-primary"><?= $totalAnggota ?? 0 ?> orang</span>
                </li>
            </ul>
            <div class="pt-4 border-t border-slate-100">
                <a href="index.php?page=daftar_anggota&ukm_id=<?= $ukm['id'] ?? 0 ?>" class="w-full flex items-center justify-center gap-2 py-3 bg-primary text-white rounded-xl font-bold hover:bg-primary/90 transition-colors">
                    <span class="material-symbols-outlined text-lg">how_to_reg</span>
                    Daftar Sebagai Anggota
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: Statistik ringkas + navigasi ke halaman lain -->
<section class="py-20 bg-slate-50 relative overflow-hidden" id="stats">
    <div class="max-w-7xl mx-auto px-6 relative z-10">
        <div class="mb-12">
            <h2 class="text-xs font-bold text-secondary tracking-[0.2em] uppercase mb-3">Operations Metrics</h2>
            <h3 class="text-4xl font-black text-slate-900">Statistik & Navigasi</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <!-- Anggota -->
            <a href="index.php?page=kepengurusan_ukm&ukm_id=<?= $ukmId ?>" 
               class="bg-white p-8 rounded-3xl border border-slate-100 hover:-translate-y-1 hover:shadow-xl transition-all duration-300 group block">
                <div class="p-3 bg-blue-50 rounded-2xl border border-blue-100 w-fit mb-4">
                    <span class="material-symbols-outlined text-primary text-3xl">groups</span>
                </div>
                <p class="text-slate-500 text-sm font-bold uppercase tracking-widest mb-1">Anggota</p>
                <h4 class="text-5xl font-black text-slate-900 tracking-tight"><?= $totalAnggota ?? 0 ?></h4>
                <p class="text-xs text-primary font-bold mt-3 flex items-center gap-1 group-hover:gap-2 transition-all">
                    Lihat Kepengurusan <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </p>
            </a>

            <!-- Berita -->
            <a href="index.php?page=berita_ukm&ukm_id=<?= $ukmId ?>"
               class="bg-white p-8 rounded-3xl border border-slate-100 hover:-translate-y-1 hover:shadow-xl transition-all duration-300 group block">
                <div class="p-3 bg-emerald-50 rounded-2xl border border-emerald-100 w-fit mb-4">
                    <span class="material-symbols-outlined text-emerald-600 text-3xl">newspaper</span>
                </div>
                <p class="text-slate-500 text-sm font-bold uppercase tracking-widest mb-1">Berita Terbaru</p>
                <h4 class="text-5xl font-black text-slate-900 tracking-tight"><?= count($beritaList ?? []) ?></h4>
                <p class="text-xs text-emerald-600 font-bold mt-3 flex items-center gap-1 group-hover:gap-2 transition-all">
                    Lihat Semua Berita <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </p>
            </a>

            <!-- Statistik -->
            <a href="index.php?page=statistik_ukm&ukm_id=<?= $ukmId ?>"
               class="bg-white p-8 rounded-3xl border border-slate-100 hover:-translate-y-1 hover:shadow-xl transition-all duration-300 group block">
                <div class="p-3 bg-amber-50 rounded-2xl border border-amber-100 w-fit mb-4">
                    <span class="material-symbols-outlined text-amber-600 text-3xl">analytics</span>
                </div>
                <p class="text-slate-500 text-sm font-bold uppercase tracking-widest mb-1">Statistik Kehadiran</p>
                <h4 class="text-3xl font-black text-slate-900 tracking-tight mt-2">Detail →</h4>
                <p class="text-xs text-amber-600 font-bold mt-3 flex items-center gap-1 group-hover:gap-2 transition-all">
                    Lihat Statistik <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </p>
            </a>
        </div>
    </div>
</section>

<!-- Section 4: Berita terbaru (dinamis) -->
<?php if (!empty($beritaList)): ?>
<section class="py-20 bg-white" id="news">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex flex-col md:flex-row items-start md:items-end justify-between mb-12 gap-6">
            <div>
                <h2 class="text-xs font-bold text-primary tracking-[0.2em] uppercase mb-3">The Feed</h2>
                <h3 class="text-4xl font-black text-slate-900">Berita Terbaru</h3>
            </div>
            <a href="index.php?page=berita_ukm&ukm_id=<?= $ukmId ?>" class="flex items-center gap-2 text-primary font-bold hover:gap-4 transition-all">
                Lihat Semua
                <span class="material-symbols-outlined">arrow_forward</span>
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <?php foreach (array_slice($beritaList, 0, 2) as $berita): ?>
            <article class="group cursor-pointer">
                <a href="index.php?page=detail_berita&id=<?= $berita['id'] ?>">
                    <div class="overflow-hidden rounded-[2.5rem] mb-6 relative shadow-sm border border-slate-100 h-[280px] bg-slate-100">
                        <?php if (!empty($berita['gambar_path'])): ?>
                        <img class="w-full h-full object-cover transition duration-700 group-hover:scale-105" 
                             src="<?= htmlspecialchars($berita['gambar_path']) ?>" 
                             alt="<?= htmlspecialchars($berita['judul']) ?>"/>
                        <?php else: ?>
                        <div class="w-full h-full bg-gradient-to-br from-primary/10 to-secondary/5 flex items-center justify-center">
                            <span class="material-symbols-outlined text-6xl text-primary/20">article</span>
                        </div>
                        <?php endif; ?>
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 to-transparent"></div>
                    </div>
                    <div class="space-y-3 px-2">
                        <div class="flex gap-3 items-center text-xs font-bold uppercase tracking-widest text-slate-500">
                            <?php if (!empty($berita['kategori'])): ?>
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full"><?= htmlspecialchars($berita['kategori']) ?></span>
                            <span class="text-slate-300">•</span>
                            <?php endif; ?>
                            <span><?= date('d M Y', strtotime($berita['created_at'])) ?></span>
                        </div>
                        <h4 class="text-xl font-black text-slate-900 leading-tight group-hover:text-primary transition-colors">
                            <?= htmlspecialchars($berita['judul']) ?>
                        </h4>
                        <p class="text-slate-600 leading-relaxed line-clamp-2">
                            <?= htmlspecialchars(strip_tags($berita['konten'] ?? '')) ?>
                        </p>
                    </div>
                </a>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- JS: Parallax Effect -->
<script>
(function() {
    const heroBg   = document.getElementById('hero-bg');
    const heroLogo = document.getElementById('hero-logo');

    if (!heroBg) return;

    let ticking = false;
    window.addEventListener('scroll', function() {
        if (!ticking) {
            requestAnimationFrame(function() {
                const scrollY = window.scrollY;
                // Background: bergerak sangat lambat (0.05 * scroll)
                heroBg.style.transform = `translateY(${scrollY * 0.08}px)`;
                // Logo: bergerak sedikit lebih cepat (0.2 * scroll) tapi masih lebih lambat dari scroll
                if (heroLogo) {
                    heroLogo.style.transform = `translateY(${scrollY * 0.15}px)`;
                }
                ticking = false;
            });
            ticking = true;
        }
    });
})();
</script>
