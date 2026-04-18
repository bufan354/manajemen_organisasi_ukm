<?php
$currentPage = $_GET['page'] ?? 'detail_ukm';
$currentUkmId = $ukm['id'] ?? ($_GET['id'] ?? ($_GET['ukm_id'] ?? 0));
$navItems = [
    'berita_ukm' => 'Berita',
    'kepengurusan_ukm' => 'Kepengurusan',
    'statistik_ukm' => 'Statistik',
    'kontak_ukm' => 'Kontak',
    'arsip_ukm' => 'Arsip',
];
?>
<!-- Sub-Navbar (UKM Details Context) -->
<nav class="bg-slate-50 border-b border-slate-200/50 w-full top-[72px] sticky z-40">
    <div class="flex items-center gap-8 w-full px-6 py-3 max-w-7xl mx-auto overflow-x-auto no-scrollbar">
        <a class="flex items-center gap-1 text-slate-500 hover:text-blue-500 transition-colors shrink-0" href="index.php?page=katalog_ukm">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span class="text-sm font-medium Inter uppercase tracking-wider">Katalog</span>
        </a>
        <div class="h-4 w-px bg-slate-300"></div>
        <a href="index.php?page=detail_ukm&id=<?= htmlspecialchars($currentUkmId) ?>" class="text-lg font-bold <?= ($currentPage === 'detail_ukm') ? 'text-blue-600' : 'text-slate-800 hover:text-blue-600' ?> transition-colors shrink-0">
            <?= htmlspecialchars($ukm['nama'] ?? 'UKM') ?>
        </a>
        <div class="flex gap-8 ml-auto">
            <?php foreach ($navItems as $page => $label): 
                $isActive = ($currentPage === $page);
                $activeClass = $isActive ? 'text-blue-600 border-b-2 border-blue-600 pb-1 font-bold' : 'text-slate-500 hover:text-blue-500';
            ?>
            <a class="transition-colors text-sm font-medium uppercase tracking-wider shrink-0 <?= $activeClass ?>" href="index.php?page=<?= $page ?>&ukm_id=<?= htmlspecialchars($currentUkmId) ?>">
                <?= htmlspecialchars($label) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</nav>
