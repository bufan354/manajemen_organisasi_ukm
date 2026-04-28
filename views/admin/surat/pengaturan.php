<?php
/**
 * View: Pengaturan Surat Premium
 * Implementasi lengkap sesuai standar BEM.
 */
$edit_id = (int)($_GET['edit_id'] ?? 0);
$edit_template = null;
if ($edit_id > 0) {
    $edit_template = $suratModel->getTemplateById($edit_id, $ukm_id);
}

// Load UKM Specific Settings for Ketua Umum
$pModel = new Pengaturan();
$ukm_settings = [
    'ketum_nama' => $pModel->get($ukm_id, 'ketum_nama'),
    'ketum_ttd' => $pModel->get($ukm_id, 'ketum_ttd'),
];
?>

<div class="p-6 max-w-7xl mx-auto space-y-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                <span class="material-symbols-outlined text-4xl text-blue-600">settings_applications</span>
                Konfigurasi Surat & Atribut
            </h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1">Kelola pimpinan, template, tanda tangan, dan stempel resmi organisasi.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 rounded-full text-xs font-bold uppercase tracking-wider">Surat System v2.0</span>
        </div>
    </div>

    <!-- Superadmin UKM Selector -->
    <?php if ($isSuperAdmin): ?>
        <div class="mb-8 max-w-7xl mx-auto bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center text-blue-600">
                    <span class="material-symbols-outlined text-3xl">corporate_fare</span>
                </div>
                <div>
                    <h2 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">Kelola Arsip Organisasi</h2>
                    <p class="text-xs text-slate-500 font-medium">Pilih UKM/HMP untuk mengelola pengarsipan surat mereka.</p>
                </div>
            </div>
            <div class="w-full md:w-72">
                <select onchange="window.location.href='index.php?page=pengaturan_surat&ukm_id='+this.value" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-500 font-bold transition-all cursor-pointer text-slate-700 dark:text-white shadow-inner">
                    <option value="0" disabled <?= $ukm_id === 0 ? 'selected' : '' ?>>-- Pilih UKM/Organisasi --</option>
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
        <div class="max-w-7xl mx-auto bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-8 rounded-[2.5rem] flex items-center gap-6">
            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-800 rounded-3xl flex items-center justify-center text-blue-600 dark:text-blue-400">
                <span class="material-symbols-outlined text-3xl">info</span>
            </div>
            <div>
                <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Pilih Organisasi Terlebih Dahulu</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Silakan pilih UKM/HMP melalui dropdown di atas untuk mengelola pengaturan surat.</p>
            </div>
        </div>
    <?php else: ?>

    <?= renderFlash() ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- COLUMN LEFT: Pimpinan & Global Settings -->
        <div class="lg:col-span-5 space-y-8">
            
            <!-- SECTION: PIMPINAN ORGANISASI (Pelaksana) -->
            <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-8 border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex items-center gap-3 mb-8">
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                        <span class="material-symbols-outlined text-blue-600">groups</span>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 uppercase tracking-tight">Panitia Pelaksana</h2>
                </div>

                <div class="space-y-8">
                    <?php 
                    $jabatans = [
                        'ketua' => 'Ketua Pelaksana',
                        'sekretaris' => 'Sekretaris Pelaksana'
                    ];
                    foreach ($jabatans as $key => $label): 
                        $p = array_filter($panitia_inti, fn($x) => $x['jabatan'] === $key);
                        $p = reset($p);
                    ?>
                    <form action="index.php?action=panitia_tetap_save" method="POST" enctype="multipart/form-data" class="group relative">
                        <?= csrf_field() ?>
                        <input type="hidden" name="jabatan" value="<?= $key ?>">
                        <input type="hidden" name="type" value="inti">
                        
                        <div class="p-6 rounded-[2rem] bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-700/50 transition-all hover:shadow-md">
                            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-4"><?= $label ?></label>
                            
                            <div class="space-y-5">
                                <div class="relative">
                                    <input type="text" name="nama" value="<?= h($p['nama'] ?? '') ?>" placeholder="Nama Lengkap" 
                                           class="w-full bg-white dark:bg-slate-900 border-none rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-blue-500 shadow-sm placeholder:text-slate-300">
                                </div>
                                
                                <div class="space-y-4">
                                    <div class="flex items-center gap-2 p-1 bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800">
                                        <button type="button" onclick="setSignMode('<?= $key ?>', 'upload')" id="btn_upload_<?= $key ?>" class="flex-1 py-2 text-[9px] font-bold uppercase rounded-lg bg-blue-600 text-white transition-all">Upload File</button>
                                        <button type="button" onclick="setSignMode('<?= $key ?>', 'draw')" id="btn_draw_<?= $key ?>" class="flex-1 py-2 text-[9px] font-bold uppercase rounded-lg text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">Gambar TTD</button>
                                    </div>

                                    <!-- Upload Mode -->
                                    <div id="mode_upload_<?= $key ?>" class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-end">
                                        <div class="space-y-2">
                                            <p class="text-[10px] font-bold text-slate-400">Pilih PNG Transparan</p>
                                            <label class="flex flex-col items-center justify-center w-full h-20 bg-white dark:bg-slate-900 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                                <div class="flex items-center gap-2">
                                                    <span class="material-symbols-outlined text-slate-400 text-sm">cloud_upload</span>
                                                    <span class="text-[10px] font-bold text-slate-500">Pilih File</span>
                                                </div>
                                                <input type="file" name="ttd_file" class="hidden">
                                            </label>
                                        </div>
                                        <div class="flex items-center justify-center h-20 bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-700/50">
                                            <?php if (!empty($p['ttd_path'])): ?>
                                                <img src="<?= h($p['ttd_path']) ?>" class="max-h-14 object-contain mix-blend-multiply dark:mix-blend-normal" alt="TTD">
                                            <?php else: ?>
                                                <span class="text-[10px] text-slate-300 italic uppercase font-bold tracking-widest">No TTD</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Draw Mode -->
                                    <div id="mode_draw_<?= $key ?>" class="hidden space-y-3">
                                        <div class="relative w-full h-32 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                                            <canvas id="canvas_<?= $key ?>" class="w-full h-full cursor-crosshair"></canvas>
                                            <button type="button" onclick="clearCanvas('<?= $key ?>')" class="absolute top-2 right-2 p-1.5 bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-red-500 rounded-lg transition-colors">
                                                <span class="material-symbols-outlined text-sm">delete</span>
                                            </button>
                                        </div>
                                        <input type="hidden" name="ttd_base64" id="base64_<?= $key ?>">
                                        <p class="text-[9px] text-slate-400 italic font-medium">Gunakan mouse/touchpad untuk menggambar tanda tangan.</p>
                                    </div>
                                </div>
                                
                                <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-xs font-bold shadow-lg shadow-blue-500/20 transition-all flex items-center justify-center gap-2 group-hover:scale-[1.02]">
                                    <span class="material-symbols-outlined text-sm">save</span>
                                    Simpan <?= $label ?>
                                </button>
                            </div>
                        </div>
                    </form>
                    <?php endforeach; ?>
                </div>

                <!-- KOP SURAT QUICK LINK -->
                <div class="mt-10 p-6 rounded-[2rem] bg-emerald-50/30 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-900/20">
                    <div class="flex items-center gap-3 mb-3 text-emerald-600 dark:text-emerald-400">
                        <span class="material-symbols-outlined">image</span>
                        <h3 class="font-bold text-sm tracking-tight">Kop Surat (Header)</h3>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed mb-4">Pastikan logo UKM sudah diunggah di menu <strong>Profil Organisasi</strong> agar otomatis muncul pada header surat resmi.</p>
                    
                    <form action="index.php?action=surat_kop_save" method="POST" enctype="multipart/form-data" class="space-y-4">
                        <?= csrf_field() ?>
                        <div class="flex items-center gap-3">
                            <input type="file" name="kop_file" class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-full text-[10px] font-bold hover:bg-emerald-700 transition-colors">Unggah Kop</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- SECTION: ATRIBUT LEMBAGA & UKM (REKTORAT, BEM, KETUM) -->
            <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-8 border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden relative group">
                <!-- Decoration -->
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-blue-600/5 blur-[80px] rounded-full group-hover:bg-blue-600/10 transition-all"></div>
                
                <div class="flex items-center gap-3 mb-8 relative">
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                        <span class="material-symbols-outlined text-blue-600">workspace_premium</span>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 uppercase tracking-tight">Atribut Lembaga & Stempel</h2>
                </div>

                <form action="index.php?action=surat_global_save" method="POST" enctype="multipart/form-data" class="space-y-10 relative">
                    <?= csrf_field() ?>
                    <input type="hidden" name="ukm_id" value="<?= $ukm_id ?>">
                    
                    <!-- Rectorate / Warek -->
                    <div class="space-y-4">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] flex items-center gap-2">
                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            PIHAK REKTORAT / WAREK III
                        </h3>
                        <div class="grid grid-cols-1 gap-4">
                            <input type="text" name="warek_nama" value="<?= h($global_settings['warek_nama'] ?? '') ?>" placeholder="Nama Pejabat (BEM / WAREK)" class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-none rounded-2xl px-5 py-4 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 shadow-sm">
                            <input type="text" name="warek_jabatan" value="<?= h($global_settings['warek_jabatan'] ?? '') ?>" placeholder="Jabatan (cth: WAREK III Bid. Kemahasiswaan)" class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-none rounded-2xl px-5 py-4 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 shadow-sm">
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="flex-1">
                                <label class="text-[10px] text-slate-400 mb-2 block">Upload TTD Warek</label>
                                <input type="file" name="warek_ttd" class="block w-full text-[10px] text-slate-500 file:mr-4 file:py-2 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-600 hover:file:bg-slate-200">
                            </div>
                            <?php if(!empty($global_settings['warek_ttd'])): ?>
                                <img src="<?= h($global_settings['warek_ttd']) ?>" class="h-12 w-20 object-contain bg-white rounded-lg p-1 shadow-sm">
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Presma / BEM -->
                    <div class="space-y-4">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] flex items-center gap-2">
                            <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                            BADAN EKSEKUTIF MAHASISWA (BEM)
                        </h3>
                        <div class="grid grid-cols-1 gap-4">
                            <input type="text" name="presma_nama" value="<?= h($global_settings['presma_nama'] ?? '') ?>" placeholder="Nama Ketua BEM" class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-none rounded-2xl px-5 py-4 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500 shadow-sm">
                            <input type="text" name="presma_jabatan" value="<?= h($global_settings['presma_jabatan'] ?? '') ?>" placeholder="Jabatan (cth: Ketua BEM INSTBUNAS)" class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-none rounded-2xl px-5 py-4 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500 shadow-sm">
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="flex-1">
                                <label class="text-[10px] text-slate-400 mb-2 block">Upload TTD Presma</label>
                                <input type="file" name="presma_ttd" class="block w-full text-[10px] text-slate-500 file:mr-4 file:py-2 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-600 hover:file:bg-slate-200">
                            </div>
                            <?php if(!empty($global_settings['presma_ttd'])): ?>
                                <img src="<?= h($global_settings['presma_ttd']) ?>" class="h-12 w-20 object-contain bg-white rounded-lg p-1 shadow-sm">
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Ketua Umum UKM -->
                    <div class="space-y-4 pt-6 border-t border-slate-100 dark:border-slate-800">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] flex items-center gap-2">
                            <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                            KETUA UMUM <?= h($ukm['singkatan'] ?? 'UKM') ?>
                        </h3>
                        <div class="grid grid-cols-1 gap-4">
                            <input type="text" name="ketum_nama" value="<?= h($ukm_settings['ketum_nama'] ?? '') ?>" placeholder="Nama Ketua Umum <?= h($ukm['singkatan'] ?? 'UKM') ?>" class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-none rounded-2xl px-5 py-4 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 shadow-sm">
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="flex-1">
                                <label class="text-[10px] text-slate-400 mb-2 block">Upload TTD Ketua Umum</label>
                                <input type="file" name="ketum_ttd" class="block w-full text-[10px] text-slate-500 file:mr-4 file:py-2 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-600 hover:file:bg-slate-200">
                            </div>
                            <?php if(!empty($ukm_settings['ketum_ttd'])): ?>
                                <img src="<?= h($ukm_settings['ketum_ttd']) ?>" class="h-12 w-20 object-contain bg-white rounded-lg p-1 shadow-sm">
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Stempel / Caps -->
                    <div class="pt-6 border-t border-slate-100 dark:border-slate-800 space-y-6">
                        <h3 class="text-xs font-bold text-slate-400 dark:text-slate-300 uppercase tracking-[0.15em]">Pengaturan Stempel / Cap</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <?php 
                            $stamps = [
                                'cap_panitia' => 'Cap Panitia',
                                'cap_warek'   => 'Cap Lembaga / WAREK',
                                'cap_bem'     => 'Cap Ketua BEM',
                                'cap_ukm'     => 'Cap UKM / HMP'
                            ];
                            foreach($stamps as $skey => $slabel):
                                $stamp_src = ($skey === 'cap_ukm') 
                                    ? (new Pengaturan())->get($ukm_id, 'cap_ukm') 
                                    : ($global_settings[$skey] ?? '');
                            ?>
                            <div class="space-y-3">
                                <label class="text-[10px] font-medium text-slate-400 block"><?= $slabel ?></label>
                                <div class="flex items-center gap-3 bg-slate-50 dark:bg-slate-800/30 p-3 rounded-2xl border border-slate-100 dark:border-slate-700/50 shadow-sm">
                                    <input type="file" name="<?= $skey ?>" class="hidden" id="<?= $skey ?>_input">
                                    <label for="<?= $skey ?>_input" class="cursor-pointer p-2 bg-white dark:bg-slate-700 border border-slate-100 dark:border-slate-600 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors shadow-sm">
                                        <span class="material-symbols-outlined text-slate-400 dark:text-white text-sm">add_photo_alternate</span>
                                    </label>
                                    <div class="flex-1 flex justify-center">
                                        <?php if(!empty($stamp_src)): ?>
                                            <img src="<?= h($stamp_src) ?>" class="h-10 object-contain">
                                        <?php else: ?>
                                            <div class="w-10 h-10 bg-white dark:bg-slate-800 rounded-lg flex items-center justify-center border border-slate-100 dark:border-slate-700 border-dashed shadow-sm">
                                                <span class="text-[8px] text-slate-300 dark:text-slate-600 font-bold uppercase">No Cap</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-2xl text-xs font-bold shadow-xl transition-all hover:scale-[1.01]">
                        Simpan Semua Pengaturan Lembaga
                    </button>
                </form>
            </div>
        </div>

        <!-- COLUMN RIGHT: Template & Panitia Database -->
        <div class="lg:col-span-7 space-y-8">
            
            <!-- SECTION: MANAJEMEN TEMPLATE -->
            <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-8 border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-purple-50 dark:bg-purple-900/20 rounded-xl">
                            <span class="material-symbols-outlined text-purple-600">auto_fix_high</span>
                        </div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 uppercase tracking-tight">Manajemen Template</h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-5 gap-8">
                    <!-- Form Tambah/Edit -->
                    <div class="md:col-span-2">
                        <form action="index.php?action=surat_template_store" method="POST" id="templateForm" class="bg-slate-50 dark:bg-slate-800/40 p-6 rounded-[2rem] border border-slate-100 dark:border-slate-700/50 space-y-5">
                            <?= csrf_field() ?>
                            <?php if ($edit_template): ?>
                                <input type="hidden" name="id" value="<?= $edit_template['id'] ?>">
                            <?php endif; ?>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-2 ml-1">Jenis Template</label>
                                    <select name="jenis" id="jenisSelect" class="w-full bg-white dark:bg-slate-900 border-none rounded-2xl px-4 py-3 text-sm shadow-sm focus:ring-2 focus:ring-purple-500">
                                        <option value="perihal" <?= ($edit_template['jenis'] ?? '') === 'perihal' ? 'selected' : '' ?>>Perihal (Subjek Surat)</option>
                                        <option value="tujuan" <?= ($edit_template['jenis'] ?? '') === 'tujuan' ? 'selected' : '' ?>>Tujuan (Kepada Yth.)</option>
                                        <option value="kegiatan" <?= ($edit_template['jenis'] ?? '') === 'kegiatan' ? 'selected' : '' ?>>Kegiatan (Nama & Kode)</option>
                                        <option value="tempat" <?= ($edit_template['jenis'] ?? '') === 'tempat' ? 'selected' : '' ?>>Tempat Pelaksanaan</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-2 ml-1">Nama Label</label>
                                    <input type="text" name="label" value="<?= h($edit_template['label'] ?? '') ?>" placeholder="Cth: Undangan Rapat" required class="w-full bg-white dark:bg-slate-900 border-none rounded-2xl px-4 py-3 text-sm shadow-sm text-left">
                                </div>
                                
                                <div id="kodeKegiatanWrap" class="<?= ($edit_template['jenis'] ?? '') === 'kegiatan' ? '' : 'hidden' ?>">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-2 ml-1">Kode Kegiatan</label>
                                    <input type="text" name="perihal_default" value="<?= h($edit_template['perihal_default'] ?? '') ?>" placeholder="Cth: BEMCUP" class="w-full bg-white dark:bg-slate-900 border-none rounded-2xl px-4 py-3 text-sm shadow-sm uppercase text-left">
                                    <small class="text-[9px] text-slate-400 mt-1 block ml-1">Kode ini akan digunakan pada nomor surat.</small>
                                </div>

                                <div id="isiTeksWrap" class="<?= ($edit_template['jenis'] ?? '') === 'kegiatan' ? 'hidden' : '' ?>">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-2 ml-1">Isi Teks / Redaksi</label>
                                    <textarea name="isi_teks" rows="4" placeholder="Ketik perihal surat di sini..." class="w-full bg-white dark:bg-slate-900 border-none rounded-2xl px-4 py-3 text-sm shadow-sm text-left"><?= h($edit_template['isi_teks'] ?? '') ?></textarea>
                                </div>
                            </div>

                            <div class="flex flex-col gap-3">
                                <button type="submit" class="w-full py-4 bg-purple-600 hover:bg-purple-700 text-white rounded-2xl text-xs font-bold shadow-lg shadow-purple-500/20 transition-all flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-sm"><?= $edit_template ? 'save' : 'add' ?></span>
                                    <?= $edit_template ? 'Update Template' : 'Simpan Template' ?>
                                </button>
                                <?php if ($edit_template): ?>
                                    <a href="index.php?page=pengaturan_surat" class="w-full py-3 bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-2xl text-[10px] font-bold text-center uppercase tracking-widest hover:bg-slate-300 transition-all">Batal Edit</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>

                    <!-- List Templates Grouped -->
                    <div class="md:col-span-3 space-y-4">
                        <?php 
                        $types = [
                            'tujuan' => ['icon' => 'person_pin', 'label' => 'Tujuan (Kepada Yth)'],
                            'perihal' => ['icon' => 'subject', 'label' => 'Perihal'],
                            'kegiatan' => ['icon' => 'calendar_month', 'label' => 'Kegiatan (Nama & Kode)'],
                            'tempat' => ['icon' => 'location_on', 'label' => 'Tempat Pelaksanaan']
                        ];
                        foreach($types as $tkey => $tinfo):
                            $filtered = array_filter($templates, fn($x) => $x['jenis'] === $tkey);
                        ?>
                        <div class="accordion-item bg-slate-50 dark:bg-slate-800/20 rounded-2xl border border-slate-100 dark:border-slate-800/50 overflow-hidden">
                            <button onclick="toggleAccordion(this)" class="w-full px-6 py-4 flex items-center justify-between group text-left">
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined text-sm text-slate-400"><?= $tinfo['icon'] ?></span>
                                    <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wide text-left">Template "<?= h($tinfo['label']) ?>"</h4>
                                    <span class="px-2 py-0.5 bg-slate-200 dark:bg-slate-700 text-slate-500 rounded-full text-[9px] font-black"><?= count($filtered) ?></span>
                                </div>
                                <span class="material-symbols-outlined text-slate-300 group-hover:text-blue-500 transition-all transform rotate-0 accordion-icon">expand_more</span>
                            </button>
                            <div class="accordion-content hidden px-6 pb-4 border-t border-slate-100 dark:border-slate-800/50">
                                <div class="divide-y divide-slate-100 dark:divide-slate-800 pt-2">
                                    <?php if(empty($filtered)): ?>
                                        <p class="py-4 text-[10px] text-slate-400 italic text-left">Belum ada template untuk kategori ini.</p>
                                    <?php else: ?>
                                        <?php foreach($filtered as $tmp): ?>
                                        <div class="py-4 flex items-center justify-between gap-4 group/item">
                                            <div class="flex-1">
                                                <div class="text-xs font-bold text-slate-800 dark:text-slate-200"><?= h($tmp['label']) ?></div>
                                                <div class="text-[10px] text-slate-400 line-clamp-2 mt-1"><?= nl2br(h($tmp['isi_teks'])) ?></div>
                                            </div>
                                            <div class="flex gap-2 opacity-0 group-hover/item:opacity-100 transition-opacity">
                                                <a href="index.php?page=pengaturan_surat&edit_id=<?= $tmp['id'] ?>" class="p-2 text-slate-300 hover:text-blue-500 transition-colors">
                                                    <span class="material-symbols-outlined text-sm">edit</span>
                                                </a>
                                                <form action="index.php?action=surat_template_delete" method="POST" onsubmit="return confirm('Hapus template ini?')">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="id" value="<?= $tmp['id'] ?>">
                                                    <button type="submit" class="p-2 text-slate-300 hover:text-red-500 transition-colors">
                                                        <span class="material-symbols-outlined text-sm">delete</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- SECTION: DATABASE TANDA TANGAN KEPANITIAAN -->
            <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-8 border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex items-center gap-3 mb-8">
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                        <span class="material-symbols-outlined text-blue-600">signature</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 uppercase tracking-tight">Database Tanda Tangan Kepanitiaan</h2>
                        <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-medium">Database TTD Panitia Lainnya</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                    <!-- Form Tambah Panitia -->
                    <div class="md:col-span-4">
                        <form action="index.php?action=panitia_tetap_save" method="POST" enctype="multipart/form-data" class="bg-slate-50 dark:bg-slate-800/40 p-6 rounded-[2rem] border border-slate-100 dark:border-slate-700/50 space-y-5">
                            <?= csrf_field() ?>
                            <input type="hidden" name="type" value="panitia">
                            <div class="space-y-4">
                                <div>
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-2 ml-1">Nama Lengkap (UPPERCASE)</label>
                                    <input type="text" name="nama" placeholder="Cth: ADI NUGRAHA" required class="w-full bg-white dark:bg-slate-900 border-none rounded-2xl px-4 py-3 text-sm shadow-sm uppercase">
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-2 ml-1">Jabatan</label>
                                    <input type="text" name="jabatan" placeholder="Cth: Divisi Humas" required class="w-full bg-white dark:bg-slate-900 border-none rounded-2xl px-4 py-3 text-sm shadow-sm uppercase">
                                </div>
                                <div class="space-y-4">
                                    <div class="flex items-center gap-2 p-1 bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800">
                                        <button type="button" onclick="setSignMode('new_panitia', 'upload')" id="btn_upload_new_panitia" class="flex-1 py-2 text-[9px] font-bold uppercase rounded-lg bg-blue-600 text-white transition-all">Upload</button>
                                        <button type="button" onclick="setSignMode('new_panitia', 'draw')" id="btn_draw_new_panitia" class="flex-1 py-2 text-[9px] font-bold uppercase rounded-lg text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">Gambar</button>
                                    </div>

                                    <!-- Upload Mode -->
                                    <div id="mode_upload_new_panitia" class="space-y-2">
                                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-2 ml-1">Unggah Tanda Tangan (PNG)</label>
                                        <input type="file" name="ttd_file" class="block w-full text-[10px] text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-bold file:bg-blue-50 file:text-blue-700">
                                    </div>

                                    <!-- Draw Mode -->
                                    <div id="mode_draw_new_panitia" class="hidden space-y-3">
                                        <div class="relative w-full h-32 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                                            <canvas id="canvas_new_panitia" class="w-full h-full cursor-crosshair"></canvas>
                                            <button type="button" onclick="clearCanvas('new_panitia')" class="absolute top-2 right-2 p-1.5 bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-red-500 rounded-lg transition-colors">
                                                <span class="material-symbols-outlined text-sm">delete</span>
                                            </button>
                                        </div>
                                        <input type="hidden" name="ttd_base64" id="base64_new_panitia">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="w-full py-4 bg-slate-900 text-white rounded-2xl text-xs font-bold shadow-lg transition-all flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-sm">add</span>
                                Simpan Data
                            </button>
                        </form>
                    </div>

                    <!-- List Panitia List -->
                    <div class="md:col-span-8">
                        <div class="bg-slate-50 dark:bg-slate-800/20 rounded-[2rem] border border-slate-100 dark:border-slate-800/50 overflow-hidden">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-100 dark:border-slate-800/50">
                                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama & Jabatan</th>
                                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Pratinjau TTD</th>
                                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                                    <?php if(empty($panitia_list)): ?>
                                        <tr>
                                            <td colspan="3" class="px-6 py-10 text-center text-xs text-slate-400 italic">Belum ada data tanda tangan kepanitiaan.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach($panitia_list as $pl): ?>
                                        <tr class="hover:bg-white dark:hover:bg-slate-800/50 transition-colors group">
                                            <td class="px-6 py-4">
                                                <div class="text-xs font-bold text-slate-800 dark:text-slate-200"><?= h($pl['nama']) ?></div>
                                                <div class="text-[10px] text-blue-500 font-medium mt-0.5"><?= h($pl['jabatan']) ?></div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex justify-center">
                                                    <div class="w-20 h-10 bg-white rounded-lg border border-slate-100 flex items-center justify-center p-1 overflow-hidden shadow-sm">
                                                        <img src="<?= h($pl['ttd_path']) ?>" class="max-h-full object-contain mix-blend-multiply">
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <form action="index.php?action=panitia_tetap_delete" method="POST" onsubmit="return confirm('Hapus data ini?')">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="id" value="<?= $pl['id'] ?>">
                                                    <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition-all">
                                                        <span class="material-symbols-outlined text-sm">delete</span>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
    function toggleAccordion(btn) {
        const content = btn.nextElementSibling;
        const icon = btn.querySelector('.accordion-icon');
        content.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    }

    // --- SIGNATURE PAD LOGIC ---
    const canvases = {};
    const ctxs = {};
    const drawing = {};

    function initCanvas(id) {
        const canvas = document.getElementById('canvas_' + id);
        if (!canvas) return;
        
        // Resize canvas to internal resolution
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width * 2;
        canvas.height = rect.height * 2;
        
        const ctx = canvas.getContext('2d');
        ctx.scale(2, 2);
        ctx.lineWidth = 3;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#000000';
        
        canvases[id] = canvas;
        ctxs[id] = ctx;
        drawing[id] = false;

        const startDrawing = (e) => {
            drawing[id] = true;
            draw(e);
        };
        const stopDrawing = () => {
            drawing[id] = false;
            ctx.beginPath();
            // Update hidden input
            const input = document.getElementById('base64_' + id);
            if (input) input.value = canvas.toDataURL();
        };
        const draw = (e) => {
            if (!drawing[id]) return;
            const r = canvas.getBoundingClientRect();
            const x = (e.clientX || (e.touches && e.touches[0].clientX)) - r.left;
            const y = (e.clientY || (e.touches && e.touches[0].clientY)) - r.top;
            
            ctx.lineTo(x, y);
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(x, y);
        };

        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('touchstart', (e) => { e.preventDefault(); startDrawing(e); });
        canvas.addEventListener('touchend', (e) => { e.preventDefault(); stopDrawing(); });
        canvas.addEventListener('touchmove', (e) => { e.preventDefault(); draw(e); });
    }

    function setSignMode(id, mode) {
        const upEl = document.getElementById('mode_upload_' + id);
        const drEl = document.getElementById('mode_draw_' + id);
        const btnUp = document.getElementById('btn_upload_' + id);
        const btnDr = document.getElementById('btn_draw_' + id);

        if (mode === 'upload') {
            upEl.classList.remove('hidden');
            drEl.classList.add('hidden');
            btnUp.className = "flex-1 py-2 text-[9px] font-bold uppercase rounded-lg bg-blue-600 text-white transition-all";
            btnDr.className = "flex-1 py-2 text-[9px] font-bold uppercase rounded-lg text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all";
            const input = document.getElementById('base64_' + id);
            if (input) input.value = "";
        } else {
            upEl.classList.add('hidden');
            drEl.classList.remove('hidden');
            btnUp.className = "flex-1 py-2 text-[9px] font-bold uppercase rounded-lg text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all";
            btnDr.className = "flex-1 py-2 text-[9px] font-bold uppercase rounded-lg bg-blue-600 text-white transition-all";
            if (!canvases[id]) initCanvas(id);
        }
    }

    function clearCanvas(id) {
        const canvas = canvases[id];
        const ctx = ctxs[id];
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        const input = document.getElementById('base64_' + id);
        if (input) input.value = "";
    }

    // Flash message timeout
    setTimeout(() => {
        const flash = document.getElementById('flash-msg');
        if (flash) {
            flash.classList.add('opacity-0', 'scale-95');
            setTimeout(() => flash.remove(), 500);
        }
    }, 4000);
    // --- TEMPLATE FORM LOGIC ---
    const jenisSelect = document.getElementById('jenisSelect');
    const kodeKegiatanWrap = document.getElementById('kodeKegiatanWrap');
    const isiTeksWrap = document.getElementById('isiTeksWrap');

    if (jenisSelect) {
        const updateForm = () => {
            if (jenisSelect.value === 'kegiatan') {
                kodeKegiatanWrap.classList.remove('hidden');
                isiTeksWrap.classList.add('hidden');
            } else {
                kodeKegiatanWrap.classList.add('hidden');
                isiTeksWrap.classList.remove('hidden');
            }
        };
        jenisSelect.addEventListener('change', updateForm);
        updateForm();
    }
</script>

<style>
    .accordion-content {
        transition: all 0.3s ease-in-out;
    }
    .rotate-180 {
        transform: rotate(180deg);
    }
</style>
