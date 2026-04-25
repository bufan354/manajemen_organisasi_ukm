<?php include __DIR__ . '/components/ukm_subnav.php'; ?>
<div class="max-w-7xl mx-auto px-6 py-16">
    <!-- Header -->
    <div class="mb-12 border-b border-slate-200/50 pb-8">
        <span class="text-xs font-bold text-primary uppercase tracking-[0.2em] mb-2 block">PORTAL INFORMASI</span>
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
            <div>
                <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tighter">Berita & Aktivitas</h1>
                <p class="text-slate-600 max-w-lg mt-4 text-sm leading-relaxed">
                    Ikuti perkembangan terbaru, pengumuman resmi, dan prestasi dari seluruh kegiatan UKM.
                </p>
            </div>
            <?php if (!empty($periodeList)): ?>
            <div class="flex-shrink-0">
                <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 block mb-1.5">Filter Periode</label>
                <select onchange="window.location.href='index.php?page=berita_ukm&ukm_id=<?= $ukm['id'] ?>&periode_id=' + this.value"
                    class="bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-700 cursor-pointer focus:ring-2 focus:ring-primary/20 focus:border-primary min-w-[220px]">
                    <?php foreach ($periodeList as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= ($currentPeriodeId == $p['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nama']) ?> (<?= $p['tahun_mulai'] ?>-<?= $p['tahun_selesai'] ?>)<?= $p['is_active'] ? ' ★' : '' ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- News Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php if (empty($beritaList)): ?>
        <div class="col-span-full text-center py-16 text-slate-400">
            <span class="material-symbols-outlined text-5xl mb-4 block">article</span>
            <p class="text-lg font-bold">Belum ada berita</p>
            <p class="text-sm mt-1">Nantikan informasi terbaru dari kami.</p>
        </div>
        <?php else: foreach ($beritaList as $b): ?>
        <a href="index.php?page=detail_berita&id=<?= $b['id'] ?>" class="group bg-white rounded-3xl overflow-hidden shadow-sm border border-slate-100 hover:shadow-xl hover:border-blue-100 transition-all flex flex-col">
            <!-- Image -->
            <div class="aspect-video overflow-hidden bg-slate-100">
                <?php if (!empty($b['gambar_path'])): ?>
                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="<?= htmlspecialchars($b['judul']) ?>" src="<?= htmlspecialchars($b['gambar_path']) ?>"/>
                <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-50 to-slate-100">
                        <span class="material-symbols-outlined text-4xl text-slate-300">image</span>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Content -->
            <div class="p-6 flex flex-col flex-1">
                <div class="flex items-center gap-3 mb-3">
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
                    <span class="<?= $katClass ?> text-[10px] font-bold uppercase px-2.5 py-1 rounded-full tracking-wider"><?= htmlspecialchars($kat) ?></span>
                    <span class="text-[10px] text-slate-400 font-medium"><?= date('d M Y', strtotime($b['created_at'])) ?></span>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2 group-hover:text-primary transition-colors line-clamp-2"><?= htmlspecialchars($b['judul']) ?></h3>
                <p class="text-sm text-slate-500 line-clamp-3 flex-1"><?= htmlspecialchars(mb_substr(strip_tags($b['konten'] ?? ''), 0, 150)) ?>...</p>
                
                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-bold text-primary group-hover:underline">Baca Selengkapnya →</span>
                </div>
            </div>
        </a>
        <?php endforeach; endif; ?>
    </div>
</div>
