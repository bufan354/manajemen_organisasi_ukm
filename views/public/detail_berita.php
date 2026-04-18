<?php $b = $berita ?? null; ?>
<?php if (!$b): ?>
<div class="max-w-4xl mx-auto px-6 py-24 text-center">
    <span class="material-symbols-outlined text-6xl text-slate-300 mb-4 block">article</span>
    <h2 class="text-2xl font-bold text-slate-800 mb-2">Berita Tidak Ditemukan</h2>
    <p class="text-slate-500 mb-6">Artikel yang Anda cari tidak tersedia atau telah dihapus.</p>
    <a href="index.php?page=berita_ukm" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primary/90 transition-colors">
        <span class="material-symbols-outlined text-sm">arrow_back</span> Kembali ke Berita
    </a>
</div>
<?php else: ?>
<article class="max-w-4xl mx-auto px-6 py-16">
    <!-- Breadcrumb & Meta -->
    <div class="mb-8">
        <a href="index.php?page=berita_ukm" class="inline-flex items-center gap-2 text-sm text-primary font-bold hover:underline mb-6">
            <span class="material-symbols-outlined text-sm">arrow_back</span> Kembali ke Berita
        </a>
        
        <div class="flex items-center gap-3 mb-4">
            <?php
                $kat = $b['kategori'] ?? 'Informasi';
                $katColors = [
                    'Prestasi' => 'bg-emerald-100 text-emerald-700',
                    'Pengumuman' => 'bg-amber-100 text-amber-700',
                    'Kegiatan' => 'bg-blue-100 text-blue-700',
                    'Informasi' => 'bg-purple-100 text-purple-700',
                ];
                $katClass = $katColors[$kat] ?? 'bg-slate-100 text-slate-600';
            ?>
            <span class="<?= $katClass ?> text-[10px] font-bold uppercase px-3 py-1 rounded-full tracking-wider"><?= htmlspecialchars($kat) ?></span>
            <span class="text-sm text-slate-400"><?= date('d M Y', strtotime($b['created_at'])) ?></span>
        </div>
        
        <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight leading-tight"><?= htmlspecialchars($b['judul']) ?></h1>
    </div>
    
    <!-- Hero Image -->
    <?php if (!empty($b['gambar_path'])): ?>
    <div class="mb-10 rounded-3xl overflow-hidden aspect-video bg-slate-100">
        <img class="w-full h-full object-cover" alt="<?= htmlspecialchars($b['judul']) ?>" src="<?= htmlspecialchars($b['gambar_path']) ?>"/>
    </div>
    <?php endif; ?>
    
    <!-- Article Content -->
    <div class="prose prose-slate prose-lg max-w-none">
        <?= $b['konten'] ?? '' ?>
    </div>
    
    <!-- Footer -->
    <div class="mt-12 pt-8 border-t border-slate-200 flex items-center justify-between">
        <div class="text-sm text-slate-500">
            Dipublikasikan pada <?= date('d F Y, H:i', strtotime($b['created_at'])) ?>
        </div>
        <a href="index.php?page=berita_ukm" class="text-sm font-bold text-primary hover:underline">← Semua Berita</a>
    </div>
</article>
<?php endif; ?>
