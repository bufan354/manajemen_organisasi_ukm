<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= isset($title) ? $title : 'Sistem Absensi IoT'; ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="assets/admin/js/tailwind-config.js"></script>
    <link href="assets/common/css/global.css" rel="stylesheet"/>
    <style>
        body { background-color: #f8f9fc; color: #191c1e; }
        .material-symbols-outlined { font-variation-settings: 'wght' 300, 'FILL' 0; }
    </style>
</head>
<body class="bg-surface selection:bg-primary-fixed text-on-surface antialiased">
