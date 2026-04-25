<?php include __DIR__ . '/components/ukm_subnav.php'; ?>

<style>
    .org-line-vertical { width: 3px; background-color: #60a5fa; } /* solid blue-400 */
    .org-line-horizontal { height: 3px; background-color: #60a5fa; } /* solid blue-400 */
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<div class="max-w-7xl mx-auto px-6 pt-16 pb-24">
    <!-- Header Section -->
    <header class="mb-20 text-center">
        <?php if (!empty($periode)): ?>
        <div class="inline-flex items-center gap-2 px-3 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full mb-4">
            <?= htmlspecialchars($periode['tahun_mulai']) ?> - <?= htmlspecialchars($periode['tahun_selesai']) ?>
        </div>
        <?php endif; ?>
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 mb-2">Struktur Organisasi</h1>
        <?php if (!empty($periode)): ?>
        <h2 class="text-2xl font-bold text-slate-600 mb-6"><?= htmlspecialchars($periode['nama']) ?> <?= $periode['is_active'] ? '<span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-bold ml-2 align-middle">Aktif</span>' : '' ?></h2>
        <?php endif; ?>
        <p class="text-slate-600 text-lg max-w-2xl mx-auto leading-relaxed">Membangun masa depan teknologi melalui kolaborasi, inovasi, dan kepemimpinan yang berdedikasi di <?= htmlspecialchars($ukm['nama'] ?? $ENTITY) ?>.</p>
    </header>

    <?php if (empty($anggotaList)): ?>
    <div class="text-center py-16 text-slate-400">
        <span class="material-symbols-outlined text-5xl mb-4 block">groups</span>
        <p class="text-lg font-bold">Belum ada data anggota</p>
        <p class="text-sm mt-1">Data kepengurusan belum tersedia.</p>
    </div>
    <?php else: ?>

    <?php
        // Jabatan standar BPH (nilai hierarki yang bukan koordinator / anggota biasa)
        $jabatanStandarBph = ['Ketua', 'Wakil', 'Sekretaris', 'Bendahara'];

        $ketua      = array_filter($anggotaList, fn($a) => ($a['hierarki'] ?? '') === 'Ketua');
        $wakil      = array_filter($anggotaList, fn($a) => ($a['hierarki'] ?? '') === 'Wakil');
        $sekretaris = array_filter($anggotaList, fn($a) => ($a['hierarki'] ?? '') === 'Sekretaris');
        $bendahara  = array_filter($anggotaList, fn($a) => ($a['hierarki'] ?? '') === 'Bendahara');

        // Koordinator = 'Koordinator' (lama) ATAU jabatan kustom (bukan standar BPH & bukan 'Anggota')
        $koordinator = array_filter($anggotaList, function($a) use ($jabatanStandarBph) {
            $h = $a['hierarki'] ?? '';
            return $h === 'Koordinator'
                || (!in_array($h, [...$jabatanStandarBph, 'Anggota', '']) && $h !== '');
        });

        // Get IDs of core + koordinator
        $coreAndKoorIds = array_merge(
            array_column($ketua, 'id'), 
            array_column($wakil, 'id'), 
            array_column($sekretaris, 'id'), 
            array_column($bendahara, 'id'), 
            array_column($koordinator, 'id')
        );

        $anggota = array_filter($anggotaList, fn($a) => !in_array($a['id'], $coreAndKoorIds));

        // Helper to format default placeholder
        function getAvatarUrl($url, $name) {
            return !empty($url) ? htmlspecialchars($url) : 'https://ui-avatars.com/api/?name=' . urlencode(substr($name, 0, 2)) . '&background=random&color=fff';
        }
    ?>

    <!-- Hierarchy Visualization -->
    <div class="flex flex-col items-center">
        
        <?php if (!empty($ketua)): $k = array_values($ketua)[0]; ?>
        <!-- Second Level: Ketua -->
        <div class="relative flex flex-col items-center">
            <div class="bg-white border border-slate-200/60 border-t-4 border-t-blue-600 p-6 rounded-2xl shadow-lg text-center w-64 group hover:-translate-y-1 hover:shadow-xl hover:border-blue-400 transition-all duration-300 z-10">
                <div class="w-20 h-20 mx-auto mb-4 rounded-xl overflow-hidden shadow-inner group-hover:scale-105 transition-transform duration-300">
                    <img class="w-full h-full object-cover" src="<?= getAvatarUrl($k['foto_path'] ?? '', $k['nama']) ?>"/>
                </div>
                <h3 class="font-bold text-base text-slate-900 mb-1"><?= htmlspecialchars($k['nama']) ?></h3>
                <span class="inline-block mt-1 text-blue-700 text-[9px] font-extrabold uppercase tracking-[0.2em] border border-blue-200 bg-blue-50 px-4 py-1.5 rounded-full"><?= htmlspecialchars($k['jabatan'] ?? 'Ketua Umum') ?></span>
            </div>
            <?php if (!empty($wakil) || !empty($sekretaris) || !empty($bendahara) || !empty($koordinator)): ?>
            <div class="org-line-vertical h-12 relative z-0"></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php 
        $coreTeam = [];
        if (!empty($wakil)) $coreTeam[] = ['item' => array_values($wakil)[0], 'type' => 'wakil'];
        if (!empty($sekretaris)) $coreTeam[] = ['item' => array_values($sekretaris)[0], 'type' => 'sekretaris'];
        if (!empty($bendahara)) $coreTeam[] = ['item' => array_values($bendahara)[0], 'type' => 'bendahara'];
        $numCore = count($coreTeam);
        ?>

        <?php if ($numCore > 0): ?>
        <!-- Third Level: Core Team -->
        <div class="grid grid-cols-1 md:grid-cols-<?= $numCore ?> w-full max-w-5xl mt-0 relative">
            <?php foreach ($coreTeam as $index => $core): $person = $core['item']; ?>
            <div class="flex flex-col items-center relative pt-8 md:pt-10">
                <?php if ($numCore > 1): ?>
                    <?php if ($index === 0): ?>
                        <!-- First item: span half right -->
                        <div class="hidden md:block absolute top-0 left-1/2 w-1/2 org-line-horizontal z-0"></div>
                    <?php elseif ($index === $numCore - 1): ?>
                        <!-- Last item: span half left -->
                        <div class="hidden md:block absolute top-0 right-1/2 w-1/2 org-line-horizontal z-0"></div>
                    <?php else: ?>
                        <!-- Middle items: full span -->
                        <div class="hidden md:block absolute top-0 left-0 w-full org-line-horizontal z-0"></div>
                    <?php endif; ?>
                <?php endif; ?>
                
                <div class="hidden md:block absolute top-0 left-1/2 -translate-x-1/2 org-line-vertical h-10 z-0"></div>

                <div class="bg-white border border-slate-200/60 border-t-4 border-t-blue-400 p-5 rounded-2xl shadow-md text-center w-[90%] max-w-[240px] group hover:-translate-y-1 hover:shadow-lg hover:border-blue-400 transition-all duration-300 relative z-10">
                    <div class="w-16 h-16 mx-auto mb-3 rounded-xl overflow-hidden shadow-inner group-hover:scale-105 transition-transform duration-300">
                        <img class="w-full h-full object-cover" src="<?= getAvatarUrl($person['foto_path'] ?? '', $person['nama']) ?>"/>
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm mb-1"><?= htmlspecialchars($person['nama']) ?></h4>
                    <p class="text-slate-500 text-[10px] font-bold uppercase tracking-wider"><?= htmlspecialchars($person['jabatan']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($koordinator)): ?>
        <!-- Fourth Level: Division Coordinators / Jabatan Kustom -->
        <div class="w-full mt-24">
            <div class="flex items-center gap-6 mb-12">
                <div class="h-[1px] flex-1 bg-slate-200"></div>
                <h2 class="text-xs font-black uppercase tracking-[0.3em] text-slate-400">Pengurus Divisi &amp; Bidang</h2>
                <div class="h-[1px] flex-1 bg-slate-200"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($koordinator as $koor): ?>
                <div class="bg-slate-50 backdrop-blur-sm p-6 rounded-2xl border border-slate-200 hover:border-blue-400 hover:shadow-md transition-all group">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="bg-blue-100 p-2.5 rounded-xl text-blue-700 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined block text-[20px]">manage_accounts</span>
                        </div>
                        <h3 class="font-bold text-slate-900 tracking-tight"><?= htmlspecialchars($koor['jabatan']) ?></h3>
                    </div>
                    <div class="flex items-center gap-4">
                        <img class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm" src="<?= getAvatarUrl($koor['foto_path'] ?? '', $koor['nama']) ?>"/>
                        <div>
                            <p class="font-bold text-sm text-slate-900"><?= htmlspecialchars($koor['nama']) ?></p>
                            <p class="text-[9px] font-black uppercase tracking-widest text-blue-700">Koordinator</p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Bottom Level: Anggota Aktif -->
    <?php if (!empty($anggota)): ?>
    <section class="mt-32">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-12">
            <div>
                <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 mb-2">Anggota Aktif</h2>
                <p class="text-slate-500 font-medium">Total <?= count($anggota) ?> mahasiswa berdedikasi tinggi</p>
            </div>
            <?php if (!empty($periode)): ?>
            <div class="flex p-1 bg-slate-100 rounded-full">
                <span class="bg-blue-600 text-white px-5 py-2 rounded-full text-xs font-bold shadow-sm flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[14px]">calendar_month</span>
                    <?= htmlspecialchars($periode['nama']) ?> (<?= $periode['tahun_mulai'] ?>–<?= $periode['tahun_selesai'] ?>)
                </span>
            </div>
            <?php endif; ?>
        </div>
        
        <div id="anggotaGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6 md:gap-8">
            <?php foreach ($anggota as $idx => $ang): ?>
            <div class="group anggota-item <?= $idx >= 6 ? 'hidden' : '' ?>">
                <div class="relative mb-4 aspect-square overflow-hidden rounded-2xl bg-slate-100 shadow-sm transform transition-all duration-300 group-hover:-translate-y-1 group-hover:shadow-lg">
                    <img class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300" src="<?= getAvatarUrl($ang['foto_path'] ?? '', $ang['nama']) ?>"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
                <div class="px-1">
                    <h5 class="font-bold text-sm text-slate-900 truncate mb-0.5"><?= htmlspecialchars($ang['nama']) ?></h5>
                    <p class="text-[10px] text-blue-700 font-black uppercase tracking-widest truncate"><?= htmlspecialchars($ang['jabatan'] ?: 'Anggota') ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (count($anggota) > 6): ?>
        <div class="mt-16 text-center" id="toggleAnggotaWrap">
            <button onclick="toggleAllAnggota()" id="btnToggleAnggota" class="inline-block bg-slate-50 text-blue-700 px-10 py-3.5 rounded-full font-bold text-xs uppercase tracking-widest border border-slate-200 hover:bg-white hover:border-blue-400 transition-all active:scale-95 shadow-sm">
                Lihat Semua Anggota (<?= count($anggota) ?>)
            </button>
        </div>
        <script>
        function toggleAllAnggota() {
            const items = document.querySelectorAll('.anggota-item.hidden');
            const btn = document.getElementById('btnToggleAnggota');
            if (items.length > 0) {
                document.querySelectorAll('.anggota-item').forEach(el => el.classList.remove('hidden'));
                btn.textContent = 'Tampilkan Lebih Sedikit';
            } else {
                document.querySelectorAll('.anggota-item').forEach((el, i) => { if (i >= 6) el.classList.add('hidden'); });
                btn.textContent = 'Lihat Semua Anggota (<?= count($anggota) ?>)';
            }
        }
        </script>
        <?php endif; ?>
    </section>
    <?php endif; ?>

    <?php endif; ?>
</div>
