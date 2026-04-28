<?php
$isEdit = (bool)($edit_data ?? false);
$data = $edit_data ?: [];
$type = $_GET['type'] ?? ($data['jenis_surat'] ?? 'M');
?>

<div class="p-6 max-w-2xl mx-auto space-y-8">
    <!-- Superadmin UKM Selector -->
    <?php if ($isSuperAdmin): ?>
        <div class="mb-8 bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center text-blue-600">
                    <span class="material-symbols-outlined text-3xl">corporate_fare</span>
                </div>
                <div>
                    <h2 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">Catat Surat Organisasi</h2>
                    <p class="text-xs text-slate-500 font-medium">Pilih UKM/HMP untuk mencatat surat manual mereka.</p>
                </div>
            </div>
            <div class="w-full md:w-48">
                <select onchange="window.location.href='index.php?page=arsip_manual&type=<?= $type ?>&ukm_id='+this.value" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-500 font-bold transition-all cursor-pointer text-slate-700 dark:text-white shadow-inner">
                    <option value="0" disabled <?= $ukm_id === 0 ? 'selected' : '' ?>>-- Pilih UKM --</option>
                    <?php 
                    $ukmList = (new Ukm())->getAll();
                    foreach ($ukmList as $u): 
                    ?>
                        <option value="<?= $u['id'] ?>" <?= $ukm_id == $u['id'] ? 'selected' : '' ?>><?= h($u['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($isSuperAdmin && $ukm_id === 0): ?>
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-8 rounded-[2.5rem] flex items-center gap-6">
            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-800 rounded-3xl flex items-center justify-center text-blue-600 dark:text-blue-400">
                <span class="material-symbols-outlined text-3xl">info</span>
            </div>
            <div>
                <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Pilih <?= h($ENTITY) ?> Terlebih Dahulu</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Silakan pilih UKM/HMP melalui dropdown di atas untuk mencatat surat manual.</p>
            </div>
        </div>
    <?php else: ?>
    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
        <a href="index.php?page=arsip_surat&jenis=<?= $type ?>&ukm_id=<?= $ukm_id ?>" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-blue-600 transition-all">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                <?= $isEdit ? 'Edit Catatan' : 'Catat Surat Manual' ?>
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Unggah file scan atau catat surat fisik yang diterima/dikirim.</p>
        </div>
    </div>

    <form action="index.php?action=arsip_surat_<?= $isEdit ? 'update' : 'store' ?>" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-8 border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
        <?= csrf_field() ?>
        <?php if($isEdit): ?><input type="hidden" name="id" value="<?= $data['id'] ?>"><?php endif; ?>
        
        <div class="space-y-4">
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Jenis Surat</label>
                <select name="jenis_surat" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-500 font-bold" onchange="updateLabels(this.value)">
                    <option value="L" <?= $type === 'L' ? 'selected' : '' ?>>Surat Keluar (L)</option>
                    <option value="D" <?= $type === 'D' ? 'selected' : '' ?>>Surat Keluar Dalam (D)</option>
                    <option value="M" <?= $type === 'M' ? 'selected' : '' ?>>Surat Masuk (M)</option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Nomor Surat</label>
                    <input type="text" name="nomor_surat" required value="<?= $data['nomor_surat'] ?? '' ?>" placeholder="001/ABC/..." class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="space-y-2">
                    <label id="label_tanggal" class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Tanggal Surat</label>
                    <input type="date" name="tanggal_dikirim" required value="<?= $data['tanggal_dikirim'] ?? date('Y-m-d') ?>" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Perihal</label>
                <input type="text" name="perihal" required value="<?= $data['perihal'] ?? '' ?>" placeholder="Contoh: Undangan Partisipasi" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-500 font-bold">
            </div>

            <div class="space-y-2">
                <label id="label_tujuan" class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1"><?= $type === 'M' ? 'Asal Surat / Pengirim' : 'Tujuan Surat' ?></label>
                <textarea name="tujuan" id="input_tujuan" required rows="2" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-500"><?= $data['tujuan'] ?? '' ?></textarea>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Unggah File (Scan PDF/Gambar)</label>
                <div class="relative group">
                    <input type="file" name="file_surat" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <div class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl px-5 py-8 text-center transition-all group-hover:border-blue-400">
                        <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">cloud_upload</span>
                        <p class="text-sm text-slate-500">Klik atau seret file ke sini</p>
                        <p class="text-[10px] text-slate-400 mt-1 uppercase">PDF, JPG, PNG (Maks 5MB)</p>
                    </div>
                </div>
                <?php if(!empty($data['file_surat'])): ?>
                    <p class="text-xs text-blue-600 mt-2 font-bold flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">attachment</span>
                        File saat ini: <a href="<?= h($data['file_surat']) ?>" target="_blank" class="underline"><?= basename($data['file_surat']) ?></a>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black text-sm uppercase tracking-widest transition-all shadow-xl shadow-blue-500/20 active:scale-95">
            Simpan Catatan Surat
        </button>
    </form>
    <?php endif; ?>
</div>

<script>
function updateLabels(val) {
    const labelTgl = document.getElementById('label_tanggal');
    const labelTujuan = document.getElementById('label_tujuan');
    const inputTujuan = document.getElementById('input_tujuan');

    if (val === 'M') {
        labelTgl.innerText = 'Tanggal Diterima';
        labelTujuan.innerText = 'Asal Surat / Pengirim';
        inputTujuan.placeholder = 'Contoh: Universitas Majalengka / BEM...';
    } else {
        labelTgl.innerText = 'Tanggal Dikirim';
        labelTujuan.innerText = 'Tujuan Surat (Kepada Yth)';
        inputTujuan.placeholder = 'Contoh: Nama Instansi / Jabatan Penerima...';
    }
}
// Run once on load
updateLabels(document.getElementsByName('jenis_surat')[0].value);
</script>
