<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= $title ?? h($APP_NAME) . ' - Sistem Absensi IoT' ?></title>
    
    <!-- Open Graph Meta Tags -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <script src="assets/public/js/tailwind-config.js"></script>
    <link href="assets/common/css/global.css" rel="stylesheet"/>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    </style>
</head>
<body class="bg-surface text-on-surface font-body selection:bg-primary-fixed-dim selection:text-on-primary">

<!-- Top Navigation Bar -->
<header class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md shadow-sm dark:shadow-none w-full top-0 sticky z-50">
    <nav class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between tracking-tight">
        <!-- Logo -->
        <a href="index.php?page=home" class="text-xl font-bold text-blue-700 dark:text-blue-400"><?= h($APP_NAME) ?></a>

        <!-- Desktop Navigation -->
        
        <div class="hidden md:flex items-center gap-8">
            <!-- Active State Handled via PHP if we were to make it dynamic, but for now we'll do simple highlights based on page -->
            <?php $reqPage = $_GET['page'] ?? 'home'; ?>
            
            <a class="<?= $reqPage === 'home' ? 'text-blue-700 dark:text-blue-400 font-semibold border-b-2 border-blue-700' : 'text-slate-600 dark:text-slate-400 hover:text-blue-600' ?> transition-all py-1" href="index.php?page=home">Beranda</a>
            <a class="<?= $reqPage === 'katalog_ukm' ? 'text-blue-700 dark:text-blue-400 font-semibold border-b-2 border-blue-700' : 'text-slate-600 dark:text-slate-400 hover:text-blue-600' ?> transition-all py-1" href="index.php?page=katalog_ukm"><?= h(getEntityLabel()) ?></a>
            <a class="<?= $reqPage === 'tentang' ? 'text-blue-700 dark:text-blue-400 font-semibold border-b-2 border-blue-700' : 'text-slate-600 dark:text-slate-400 hover:text-blue-600' ?> transition-all py-1" href="index.php?page=tentang">Tentang Kami</a>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="hidden md:flex relative items-center">
                <span class="material-symbols-outlined absolute left-3 text-slate-400 text-sm">search</span>
                <input type="text" placeholder="Cari <?= h(getEntityLabel()) ?>..." class="pl-9 pr-4 py-2 bg-slate-100 rounded-full text-sm outline-none w-48 focus:w-64 transition-all focus:ring-2 ring-primary/20">
            </div>
            <a href="index.php?page=login" class="bg-primary text-white px-6 py-2 rounded-xl font-medium transform hover:scale-[1.02] active:scale-95 duration-200 inline-block text-center whitespace-nowrap shadow-sm">Sign In</a>
        </div>
    </nav>
</header>
<main>

