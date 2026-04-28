<?php
/**
 * View: Print Lampiran Pinjam Barang (Standalone)
 * Mencetak daftar barang yang dipinjam dalam format A4.
 * Diakses via: admin/surat/cetak_lampiran.php?id=XX
 */
require_once '../../../core/Database.php';
require_once '../../../core/Session.php';
require_once '../../../core/models/Surat.php';
require_once '../../../core/models/Ukm.php';
require_once '../../../core/helpers.php';

Session::requireLogin();

try {
    $id = (int)($_GET['id'] ?? 0);
    $role = Session::get('admin_role');

    if ($role === 'superadmin') {
        $ukm_id = (int)($_GET['ukm_id'] ?? Session::get('last_ukm_id'));
    } else {
        $ukm_id = (int)Session::get('ukm_id');
    }

    $suratModel = new SuratModel();
    $ukmModel = new Ukm();

    $lampiran = $suratModel->getLampiranById($id, $ukm_id);
    if (!$lampiran) throw new Exception("Lampiran tidak ditemukan.");

    $ukm = $ukmModel->getById($ukm_id);
    if (!$ukm) throw new Exception("Data UKM tidak ditemukan.");

    $items = json_decode($lampiran['barang_json'], true) ?: [];
} catch (Exception $e) {
    die("Gagal memuat halaman: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>LAMPIRAN PINJAM BARANG - <?= h($lampiran['nama_acara']) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #525659; font-family: 'Times New Roman', Times, serif; font-size: 14px; color: #000; line-height: 1.4; }

        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 10mm auto;
            border: 1px solid #D3D3D3;
            border-radius: 5px;
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .no-print {
            text-align: center;
            padding: 15px;
            background: #222;
            color: #fff;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .btn {
            background: #4A90E2; color: #fff; border: none; padding: 10px 20px; font-size: 16px;
            border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; margin: 0 5px;
        }

        .header-pdf {
            text-align: center;
            margin-bottom: 30px;
        }
        .header-pdf h1 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .header-pdf h2 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .table-items th, .table-items td {
            border: 1px solid #000;
            padding: 8px 12px;
            text-align: left;
        }
        .table-items th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .table-items td.center { text-align: center; }

        .footer-pdf {
            margin-top: 50px;
            text-align: right;
            font-size: 12px;
            color: #555;
            font-style: italic;
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }

        @media print {
            body { background: white; margin: 0; padding: 0; -webkit-print-color-adjust: exact; }
            .page {
                margin: 0 !important;
                padding: 15mm 20mm;
                border: none !important;
                border-radius: 0 !important;
                width: 210mm;
                min-height: 296mm;
                box-shadow: none !important;
                background: white !important;
                page-break-after: always;
                page-break-inside: avoid;
            }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn">Cetak Dokumen</button>
        <button onclick="window.close()" class="btn" style="background: #f39c12;">Tutup</button>
    </div>

    <div class="page">
        <div style="text-align: left; font-size: 12pt; margin-bottom: 20px; font-style: italic;">Lampiran 1</div>

        <div class="header-pdf">
            <h1>Daftar Barang & Tempat Yang Akan Dipinjam</h1>
            <h2>Pada Tanggal <?= h($lampiran['tanggal_kegiatan']) ?> Untuk Acara <?= h($lampiran['nama_acara']) ?> <?= h($lampiran['tahun']) ?></h2>
        </div>

        <table class="table-items">
            <thead>
                <tr>
                    <th style="width: 50px;">No.</th>
                    <th>Nama Barang</th>
                    <th style="width: 150px;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $idx => $item): ?>
                    <tr>
                        <td class="center"><?= $idx + 1 ?>.</td>
                        <td><?= h($item['nama']) ?></td>
                        <td class="center"><?= h($item['jumlah']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($items)): ?>
                    <tr><td colspan="3" align="center">Tidak ada barang terpilih.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="footer-pdf">
            Dicetak pada: <?= date('d F Y') ?> pukul <?= date('H:i') ?> WIB
        </div>
    </div>

</body>
</html>
