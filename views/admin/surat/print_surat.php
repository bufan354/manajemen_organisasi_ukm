<?php
/**
 * View: Print Surat (Standalone)
 * Replikasi visual BEM: Kiri = Warek/BEM, Kanan = Ketua UKM.
 * Diakses langsung via: admin/surat/cetak_surat.php?id=XX
 */
require_once '../../../core/helpers.php';
require_once '../../../core/models/Surat.php';
require_once '../../../core/models/Ukm.php';
require_once '../../../core/models/Pengaturan.php';
require_once '../../../core/Session.php';

Session::requireLogin();

$id = (int)($_GET['id'] ?? 0);
$suratModel = new SuratModel();
$pengaturanModel = new Pengaturan();
$ukmModel = new Ukm();

// Resolve ukm_id (support superadmin)
$target_ukm_id = (int)($_GET['ukm_id'] ?? 0);
if ($target_ukm_id === 0) $target_ukm_id = (int)Session::get('ukm_id');

if ($target_ukm_id === 0 && Session::get('admin_role') === 'superadmin') {
    $stmt = Database::getConnection()->prepare("SELECT * FROM arsip_surat WHERE id = ?");
    $stmt->execute([$id]);
    $surat = $stmt->fetch();
} else {
    $surat = $suratModel->getArsipById($id, $target_ukm_id);
}

if (!$surat) die("Surat tidak ditemukan.");

$ukm = $ukmModel->getById($surat['ukm_id']);
$konten = json_decode($surat['konten_surat'], true) ?: [];
$global_settings = $suratModel->getAllGlobalSettings();

// Kop Surat
$kop_surat = $pengaturanModel->get($surat['ukm_id'], 'kop_surat');

// Panitia dari database
$panitia_list = $suratModel->getPanitia($surat['ukm_id'], $surat['periode_id']);
$ketua_pelaksana = null;
$sekretaris_pelaksana = null;
foreach ($panitia_list as $p) {
    if (stripos($p['jabatan'], 'ketua') !== false && stripos($p['jabatan'], 'umum') === false) $ketua_pelaksana = $p;
    if (stripos($p['jabatan'], 'sekretaris') !== false) $sekretaris_pelaksana = $p;
}

// ============================================================
// LOGIKA TEKS OTOMATIS (Replikasi dari BEM)
// ============================================================
$nama_keg = trim($konten['nama_kegiatan'] ?? '');
$tema_keg = trim($konten['tema_kegiatan'] ?? ($konten['tema'] ?? ''));
$parts_nomor = explode('/', $surat['nomor_surat']);
$tahun_surat = end($parts_nomor) ?: date('Y');
$perihal_lower = mb_strtolower($surat['perihal']);

// Paragraf Pembuka
if (!empty($nama_keg)) {
    $pembuka = 'Puji syukur kita panjatkan kehadirat Allah SWT karena atas rahmat hidayah-Nya kita masih diberikan kesehatan dan selalu mendapatkan perlindungannya. Sehubungan akan diadakannya kegiatan <b>'
        . htmlspecialchars($nama_keg) . '</b> Tahun ' . htmlspecialchars($tahun_surat)
        . (!empty($tema_keg) ? ' dengan tema "<b>' . htmlspecialchars($tema_keg) . '</b>"' : '')
        . ' yang akan dilaksanakan pada :';
} else {
    $pembuka = 'Puji syukur kita panjatkan kehadirat Allah SWT karena atas rahmat hidayah-Nya kita masih diberikan kesehatan dan selalu mendapatkan perlindungannya. Sehubungan dengan agenda kegiatan organisasi kami yang akan dilaksanakan pada :';
}

// Paragraf Permohonan
$tujuan_baris_1 = trim(explode("\n", $surat['tujuan'])[0]);
$sapaan = !empty($konten['sapaan_tujuan']) ? htmlspecialchars($konten['sapaan_tujuan']) . ' ' : '';

if (strpos($perihal_lower, 'undangan') !== false) {
    $suffix = ' agar dapat menghadiri kegiatan tersebut.';
} elseif (strpos($perihal_lower, 'peminjaman') !== false || strpos($perihal_lower, 'permohonan tempat') !== false) {
    $suffix = ' untuk dapat menggunakan fasilitas tersebut.';
} elseif (strpos($perihal_lower, 'delegasi') !== false || strpos($perihal_lower, 'utusan') !== false) {
    $suffix = ' untuk mendelegasikan perwakilannya pada kegiatan tersebut.';
} else {
    $suffix = ' demi mendukung terselenggaranya acara tersebut.';
}
$paragraf_permohonan = 'Dengan ini kami menyampaikan ' . $perihal_lower . ' kepada ' . $sapaan . htmlspecialchars($tujuan_baris_1) . $suffix;

// Paragraf Penutup
$paragraf_penutup = 'Demikian surat ' . $perihal_lower . ' ini kami sampaikan, atas perhatian dan kerjasamanya kami ucapkan terimakasih.';

// ============================================================
// LOGIKA TANDA TANGAN
// ============================================================

// KIRI = Warek ATAU Ketua BEM (mengetahui)
$left_line1 = '';
$left_line2 = '';
$left_name  = '';
$left_ttd   = '';
$left_cap   = '';

if ($konten['show_ttd_warek'] ?? false) {
    $left_line1 = 'a.n Rektor INSTBUNAS Majalengka';
    $left_line2 = $global_settings['warek_jabatan'] ?? 'WAREK III Bid. Kemahasiswaan';
    $left_name  = $global_settings['warek_nama'] ?? '..........................';
    $left_ttd   = ($konten['use_ttd_warek'] ?? true) ? ($global_settings['warek_ttd'] ?? '') : '';
    $left_cap   = ($konten['show_cap_warek'] ?? false) ? ($global_settings['cap_warek'] ?? '') : '';
} elseif ($konten['show_ttd_presma'] ?? false) {
    $left_line1 = 'Ketua BEM';
    $left_line2 = trim(str_ireplace('Ketua BEM', '', $global_settings['presma_jabatan'] ?? 'INSTBUNAS Majalengka'));
    $left_name  = $global_settings['presma_nama'] ?? '..........................';
    $left_ttd   = ($konten['use_ttd_presma'] ?? true) ? ($global_settings['presma_ttd'] ?? '') : '';
    $left_cap   = ($konten['show_cap_bem'] ?? false) ? ($global_settings['cap_bem'] ?? '') : '';
}

// KANAN = Ketua Umum UKM
$right_line1 = 'Ketua Umum ' . htmlspecialchars($ukm['nama'] ?? 'Organisasi');
$right_line2 = '';
$right_name  = $pengaturanModel->get($surat['ukm_id'], 'ketum_nama') ?: '..........................';
$right_ttd   = ($konten['show_ttd_ketum'] ?? false) ? ($pengaturanModel->get($surat['ukm_id'], 'ketum_ttd') ?? '') : '';
$right_cap   = ($konten['show_cap_ukm'] ?? false) ? ($pengaturanModel->get($surat['ukm_id'], 'cap_ukm') ?? '') : '';

// Nama panitia untuk header TTD
$kode_keg = "";
if (isset($parts_nomor[2])) $kode_keg = $parts_nomor[2];
$nama_panitia_header = !empty($nama_keg) ? $nama_keg : $kode_keg;

// Helper render TTD path
function renderTTDPath($val) {
    if (empty($val)) return '';
    return htmlspecialchars($val);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Surat - <?php echo htmlspecialchars($surat['nomor_surat']); ?></title>
    <style>
        /* Reset & Setup Kertas A4 */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #525659; font-family: 'Times New Roman', Times, serif; font-size: 16px; color: #000; line-height: 1.5; }

        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 15mm 20mm;
            margin: 10mm auto;
            border: 1px solid #D3D3D3;
            border-radius: 5px;
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        /* Non-Printable Elements */
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
        .btn-warning { background: #f39c12; }

        /* Meta Surat */
        .meta-surat { width: 100%; margin-bottom: 5px; line-height: 1.3; }
        .meta-surat td { vertical-align: top; }
        .col-label { width: 75px; }
        .col-titik { width: 15px; text-align: center; }

        /* Isi Surat */
        .isi-surat { text-align: justify; margin-bottom: 10px; }
        .indent { text-indent: 40px; margin-top: 5px; }

        /* Tabel Waktu Pelaksanaan */
        .waktu-pelaksanaan { width: calc(100% - 40px); margin-left: 40px; margin-top: 10px; margin-bottom: 10px; border-collapse: collapse; }
        .waktu-pelaksanaan td { vertical-align: top; padding: 4px 10px; border: none; }

        /* TTD Area */
        .ttd-area { width: 100%; margin-top: 15px; text-align: center; }
        .ttd-area .ttd-title { font-weight: bold; margin-bottom: 5px; }
        .ttd-table { width: 100%; margin-bottom: 5px; border-collapse: collapse; border: none !important; }
        .ttd-table td { width: 50%; vertical-align: top; padding-bottom: 5px; border: none !important; }
        .ttd-name { font-weight: bold; text-decoration: underline; margin-top: 55px; }
        .ttd-jabatan { font-size: 14px; }

        @page { size: A4 portrait; margin: 0; }

        @media print {
            body { background: white; margin: 0; padding: 0; -webkit-print-color-adjust: exact; }
            .page {
                margin: 0 !important;
                padding: 10mm 15mm;
                border: none !important;
                border-radius: 0 !important;
                width: 210mm;
                min-height: 295mm;
                box-shadow: none !important;
                outline: none !important;
                background: white !important;
                page-break-after: always;
                overflow: hidden;
            }
            * { border: none !important; box-shadow: none !important; outline: none !important; }
            img { border-style: none !important; border: 0 !important; outline: none !important; }
            table, tr, td { border: none !important; border-collapse: collapse !important; }
            .page:last-of-type { page-break-after: avoid !important; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn"><i class="fas fa-print"></i> Cetak Dokumen</button>
        <a href="index.php?page=arsip_surat&ukm_id=<?php echo $surat['ukm_id']; ?>" class="btn btn-warning">← Kembali ke Arsip</a>
    </div>

    <div class="page">
        <!-- 1. KOP SURAT -->
        <?php if ($kop_surat): ?>
            <div style="margin: -10mm -15mm -5px -15mm; text-align: center;">
                <img src="<?php echo htmlspecialchars($kop_surat); ?>" style="width:100%; height:auto; display:block;" alt="Kop Surat">
            </div>
        <?php else: ?>
            <div style="height: 100px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; color: #999; margin-bottom: 20px;">
                Kop Surat Belum Diatur di Pengaturan
            </div>
        <?php endif; ?>

        <!-- 2. META SURAT -->
        <table class="meta-surat" style="width: 100%; border-collapse: collapse;">
            <tr>
                <td class="col-label" style="width: 75px;">Nomor</td>
                <td class="col-titik" style="width: 15px;">:</td>
                <td style="vertical-align: top;"><?php echo htmlspecialchars($surat['nomor_surat']); ?></td>
                <td style="width: 1%; white-space: nowrap; text-align: left; vertical-align: top;">
                    <?php echo htmlspecialchars($surat['tempat_tanggal'] ?: 'Majalengka, ' . date('d F Y', strtotime($surat['tanggal_dikirim']))); ?>
                </td>
            </tr>
            <tr>
                <td class="col-label">Lampiran</td>
                <td class="col-titik">:</td>
                <td style="vertical-align: top;"><?php echo htmlspecialchars($konten['lampiran_jumlah'] ?? '-'); ?></td>
                <td></td>
            </tr>
            <tr>
                <td class="col-label" style="vertical-align: top;">Perihal</td>
                <td class="col-titik" style="vertical-align: top;">:</td>
                <td style="vertical-align: top; padding-right: 30px;">
                    <div style="font-weight: bold; text-decoration: underline; line-height: 1.4;">
                        <?php echo htmlspecialchars($surat['perihal']); ?>
                    </div>
                </td>
                <td></td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td style="vertical-align: top; padding-top: 15px; white-space: nowrap;">
                    Yth,<br>
                    <b><?php echo nl2br(htmlspecialchars($surat['tujuan'])); ?></b><br>
                    Di Tempat
                </td>
            </tr>
        </table>

        <!-- 3. ISI SURAT -->
        <div class="isi-surat">
            <p><b><i>Assalamu'alaikum Wr. Wb.</i></b></p>

            <p class="indent"><?php echo $pembuka; ?></p>

            <table class="waktu-pelaksanaan">
                <tr>
                    <td style="width: 120px;">Hari, tanggal</td>
                    <td style="width: 15px;">:</td>
                    <td><?php echo htmlspecialchars($konten['pelaksanaan_hari_tanggal'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td>Waktu</td>
                    <td>:</td>
                    <td><?php echo htmlspecialchars($konten['pelaksanaan_waktu'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td>Tempat</td>
                    <td>:</td>
                    <td><?php echo htmlspecialchars($konten['pelaksanaan_tempat'] ?? '-'); ?></td>
                </tr>
            </table>

            <p class="indent"><?php echo $paragraf_permohonan; ?></p>
            <p class="indent"><?php echo $paragraf_penutup; ?></p>

            <p style="margin-top: 15px;"><b><i>Wassalamu'alaikum Wr. Wb.</i></b></p>
        </div>

        <!-- 4. TANDA TANGAN -->
        <div class="ttd-area">
            <div class="ttd-title">PANITIA PELAKSANA <?php echo strtoupper(htmlspecialchars($nama_panitia_header)); ?> <?php echo $tahun_surat; ?></div>

            <table class="ttd-table" style="margin-bottom: 5px;">
                <tr>
                    <td style="position:relative;">
                        Ketua Pelaksana
                        <?php if(!empty($global_settings['cap_panitia']) && ($konten['show_cap_panitia'] ?? false)): ?>
                            <img src="<?php echo renderTTDPath($global_settings['cap_panitia']); ?>" style="position:absolute; top:20px; left:100%; transform:translateX(-50%); max-width:190px; max-height:95px; mix-blend-mode:multiply; pointer-events:none; opacity:0.85; z-index:2;">
                        <?php endif; ?>
                        <?php
                        $ttd_ketua_val = $konten['ttd_ketua_custom'] ?? ($konten['panitia_ketua_ttd'] ?? ($ketua_pelaksana['ttd_path'] ?? ''));
                        if(!empty($ttd_ketua_val)): ?>
                            <img src="<?php echo renderTTDPath($ttd_ketua_val); ?>" style="position:absolute; bottom:15px; left:50%; transform:translateX(-50%); max-height:85px; mix-blend-mode:multiply; pointer-events:none;">
                        <?php endif; ?>
                        <div class="ttd-name"><?php echo htmlspecialchars($konten['panitia_ketua'] ?? ($konten['nama_ketua'] ?? ($ketua_pelaksana['nama'] ?? '...........................'))); ?></div>
                    </td>
                    <td style="position:relative;">
                        Sekretaris
                        <?php
                        $ttd_sekre_val = $konten['ttd_sekre_custom'] ?? ($konten['panitia_sekre_ttd'] ?? ($sekretaris_pelaksana['ttd_path'] ?? ''));
                        if(!empty($ttd_sekre_val)): ?>
                            <img src="<?php echo renderTTDPath($ttd_sekre_val); ?>" style="position:absolute; bottom:15px; left:50%; transform:translateX(-50%); max-height:85px; mix-blend-mode:multiply; pointer-events:none;">
                        <?php endif; ?>
                        <div class="ttd-name"><?php echo htmlspecialchars($konten['panitia_sekre'] ?? ($konten['nama_sekretaris'] ?? ($sekretaris_pelaksana['nama'] ?? '...........................'))); ?></div>
                    </td>
                </tr>
            </table>

            <div style="margin-top: -10px; margin-bottom: 10px;">Mengetahui,</div>

            <table class="ttd-table">
                <tr>
                    <!-- KIRI: Warek atau Ketua BEM -->
                    <td style="position:relative;">
                        <?php echo htmlspecialchars($left_line1); ?><br>
                        <span class="ttd-jabatan"><?php echo htmlspecialchars($left_line2); ?></span>
                        <?php if(!empty($left_cap)): ?>
                            <img src="<?php echo renderTTDPath($left_cap); ?>" style="position:absolute; bottom:0px; left:0; max-width:180px; max-height:130px; mix-blend-mode:multiply; pointer-events:none; opacity:0.85; z-index:2;">
                        <?php endif; ?>
                        <?php if(!empty($left_ttd)): ?>
                            <img src="<?php echo renderTTDPath($left_ttd); ?>" style="position:absolute; bottom:20px; left:50%; transform:translateX(-50%); max-height:85px; mix-blend-mode:multiply; pointer-events:none;">
                        <?php endif; ?>
                        <div class="ttd-name"><?php echo htmlspecialchars($left_name); ?></div>
                    </td>
                    <!-- KANAN: Ketua Umum UKM -->
                    <td style="position:relative;">
                        <?php echo $right_line1; ?><br>
                        <span class="ttd-jabatan"><?php echo htmlspecialchars($right_line2); ?></span>
                        <?php if(!empty($right_cap)): ?>
                            <img src="<?php echo renderTTDPath($right_cap); ?>" style="position:absolute; bottom:0px; left:10%; max-width:180px; max-height:130px; mix-blend-mode:multiply; pointer-events:none; opacity:0.85; z-index:2;">
                        <?php endif; ?>
                        <?php if(!empty($right_ttd)): ?>
                            <img src="<?php echo renderTTDPath($right_ttd); ?>" style="position:absolute; bottom:20px; left:50%; transform:translateX(-50%); max-height:85px; mix-blend-mode:multiply; pointer-events:none;">
                        <?php endif; ?>
                        <div class="ttd-name"><?php echo htmlspecialchars($right_name); ?></div>
                    </td>
                </tr>
            </table>
        </div>

    </div>

</body>
</html>
