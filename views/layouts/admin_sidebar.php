<?php
$currentPage = $_GET['page'] ?? 'dashboard';
$isSuperAdmin = (Session::get('admin_role') === 'superadmin');
$_entityLabel = getEntityLabel();

$navItems = [
    'dashboard' => ['icon' => 'dashboard',   'label' => 'Dashboard'],
    'profil'    => ['icon' => 'storefront',  'label' => 'Profil ' . htmlspecialchars($_entityLabel)],
];

// Menu "Semua UKM" hanya untuk super admin
if ($isSuperAdmin) {
    $navItems['ukm'] = ['icon' => 'groups', 'label' => 'Semua ' . htmlspecialchars($_entityLabel)];
}

$navItems['anggota']      = ['icon' => 'person',          'label' => 'Anggota',     'fill' => true];
$navItems['jabatan']      = ['icon' => 'badge',             'label' => 'Jabatan'];
$navItems['berita']       = ['icon' => 'newspaper',        'label' => 'Berita'];
$navItems['event']        = ['icon' => 'calendar_month',   'label' => 'Kegiatan'];
$navItems['pendaftaran']  = ['icon' => 'app_registration', 'label' => 'Pendaftaran'];

$systemItems = [
    'pengaturan' => ['icon' => 'settings', 'label' => 'Pengaturan'],
];

// "Kelola Admin", "Log Keamanan", dan "Konfigurasi Umum" hanya untuk super admin
if ($isSuperAdmin) {
    $systemItems['backup_database'] = ['icon' => 'database', 'label' => 'Backup Database'];
    $systemItems['konfigurasi_umum'] = ['icon' => 'tune', 'label' => 'Konfigurasi Umum'];
    $systemItems['kelola_admin'] = ['icon' => 'admin_panel_settings', 'label' => 'Kelola Admin'];
    $systemItems['log_keamanan'] = ['icon' => 'security', 'label' => 'Log Keamanan'];
}

function renderNav($id, $item, $currentPage) {
    if ($id == $currentPage) {
        $fillRule = (isset($item['fill']) && $item['fill']) ? "style=\"font-variation-settings: 'FILL' 1;\"" : "";
        return "<a class=\"flex items-center gap-3 px-4 py-3 rounded-full border-l-4 border-blue-700 bg-blue-50/50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 transition-all duration-200 ease-in-out active:scale-95 font-sans text-sm font-medium tracking-tight\" href=\"index.php?page={$id}\">
            <span class=\"material-symbols-outlined\" {$fillRule}>{$item['icon']}</span>
            <span>{$item['label']}</span>
        </a>";
    } else {
        return "<a class=\"flex items-center gap-3 px-4 py-3 rounded-full text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all duration-200 ease-in-out active:scale-95 font-sans text-sm font-medium tracking-tight\" href=\"index.php?page={$id}\">
            <span class=\"material-symbols-outlined\">{$item['icon']}</span>
            <span>{$item['label']}</span>
        </a>";
    }
}
?>

<aside class="w-[260px] h-screen fixed left-0 top-0 bg-white dark:bg-slate-900 shadow-[12px_0_40px_rgba(25,28,30,0.06)] flex flex-col py-6 z-50">
    <div class="px-6 mb-8 flex items-center gap-3">
        <div class="w-10 h-10 bg-primary-container rounded-xl flex items-center justify-center overflow-hidden">
            <img class="w-full h-full object-cover" alt="Logo" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAbPdqJoliQZsqDJr245pD77Jsr7C1hEvBmXiEfGsLT9raIjkyXBPINjucsZPKePWIPWUXTqmz55rMJFWz6WJQLyhyXyIMamVB7390vmZB98zUSEUeEWLgUvf7nb9QUBmkiQLD3_UZ6iB_2Ztr53Rh3GalAhe1-OpegqfuNx1HZFp7AYQxlExv9uqhWOCqrMl52DiJYQcqSyHbPUOpMjUk5_dWS-LfVtB1hEvKg273Qzb1WvGI8yIDP2_u3rKQ5flrFrqXm6-hM2Lds"/>
        </div>
        <div>
            <h1 class="text-xl font-bold tracking-tighter text-slate-900 dark:text-white"><?= h($APP_NAME ?? 'The Ledger') ?></h1>
            <p class="text-[10px] uppercase tracking-widest text-on-surface-variant font-bold"><?= h(getSetting('app_subtitle', 'IoT Admin Panel')) ?></p>
        </div>
    </div>
    
    <nav class="flex-1 overflow-y-auto custom-scrollbar px-3 space-y-1">
        <div class="px-3 mb-2 text-[10px] font-bold uppercase tracking-[0.1em] text-outline">Utama</div>
        
        <?php foreach ($navItems as $id => $item): ?>
            <?= renderNav($id, $item, $currentPage) ?>
        <?php endforeach; ?>
        
        <div class="px-3 mt-6 mb-2 text-[10px] font-bold uppercase tracking-[0.1em] text-outline">Sistem</div>
        
        <?php foreach ($systemItems as $id => $item): ?>
            <?= renderNav($id, $item, $currentPage) ?>
        <?php endforeach; ?>
        
        <div class="px-3 mt-8 mb-4">
            <a href="index.php?action=logout" class="flex items-center justify-center gap-2 px-4 py-3 w-full bg-red-50 text-red-600 hover:bg-red-100 rounded-xl font-bold text-sm transition-colors dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40 tracking-wide">
                <span class="material-symbols-outlined text-sm">logout</span>
                Logout
            </a>
        </div>
    </nav>
</aside>
