<?php
/**
 * Template: Export Excel (HTML-to-Excel)
 * Digunakan untuk merender tabel yang didownload sebagai .xls
 */

// Mapping Warna Kategori UKM ke Hex
$categoryColors = [
    'Seni' => '#9c27b0',      // Purple
    'Olahraga' => '#2196f3',    // Blue
    'Akademik' => '#4caf50',   // Green
    'Sosial' => '#ff9800',     // Orange
    'Keagamaan' => '#009688',  // Teal
];

// Fallback warna default (Blue-ish)
$primaryColor = '#1e293b'; 

if (isset($ukm['kategori']) && isset($categoryColors[$ukm['kategori']])) {
    $primaryColor = $categoryColors[$ukm['kategori']];
}

// Header untuk Excel
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
    .table-export { border-collapse: collapse; width: 100%; font-family: sans-serif; }
    .table-export th, .table-export td { border: 1px solid #dee2e6; padding: 10px; text-align: left; }
    .header-report { background-color: <?= $primaryColor ?>; color: #ffffff; font-weight: bold; }
    .title-report { font-size: 20px; font-weight: bold; margin-bottom: 5px; color: <?= $primaryColor ?>; }
    .subtitle-report { font-size: 14px; color: #64748b; margin-bottom: 20px; }
    .meta-table { margin-bottom: 20px; }
    .meta-table td { padding: 4px 0; border: none; font-size: 13px; }
    .badge { padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
</style>

<!-- Branding Header -->
<div class="title-report"><?= strtoupper($title) ?></div>
<div class="subtitle-report"><?= isset($ukm['nama']) ? $ukm['nama'] . ' (' . $ukm['singkatan'] . ')' : 'Sistem Absensi Digital' ?></div>

<!-- Metadata Info -->
<?php if (isset($metadata)): ?>
<table class="meta-table">
    <?php foreach ($metadata as $label => $value): ?>
    <tr>
        <td style="width: 150px; font-weight: bold;"><?= $label ?></td>
        <td>: <?= $value ?></td>
    </tr>
    <?php endforeach; ?>
    <tr>
        <td style="font-weight: bold;">Tanggal Unduh</td>
        <td>: <?= date('d M Y, H:i') ?></td>
    </tr>
</table>
<?php endif; ?>

<br>

<!-- Data Table -->
<table class="table-export">
    <thead>
        <tr>
            <?php foreach ($headers as $h): ?>
            <th class="header-report"><?= strtoupper($h) ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
            <?php foreach ($row as $cell): ?>
            <td><?= $cell ?></td>
            <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<tr style="height: 40px;"></tr>
<table>
    <tr>
        <td colspan="<?= count($headers) ?>" style="text-align: right; font-size: 10px; color: #94a3b8; border: none;">
            Dicetak secara otomatis melalui Sistem Absensi Digital IOT.
        </td>
    </tr>
</table>
