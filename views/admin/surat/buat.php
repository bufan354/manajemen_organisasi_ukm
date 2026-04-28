<?php
/**
 * View: Buat Surat Baru (Premium BEM Edition - Light Theme)
 * Sinkronisasi penuh dengan fitur sistem BEM:
 * - Drum Picker (Waktu)
 * - Template Picker (Cari Otomatis)
 * - Mode Paragraf (Template vs RTE)
 * - Lampiran (Pustaka & Upload)
 */
$isEdit = (bool)($edit_data ?? false);
$isClone = (bool)($clone_data ?? false);
$data = $edit_data ?: ($clone_data ?: []);
$konten = isset($data['konten_surat']) ? json_decode($data['konten_surat'], true) : [];

$nomorUrutVal = $next_urut ?? '';
if ($isEdit || $isClone) {
    $parts = explode('/', $data['nomor_surat'] ?? '');
    $nomorUrutVal = $parts[0] ?? '';
    
    // BEM Standard: Robust extraction for group integrity
    $rParts = array_reverse($parts);
    $tahunVal = $rParts[0] ?? date('Y');
    $bulanRomanVal = $rParts[1] ?? '';
    
    // Fallback: extract kode kegiatan from nomor_surat if not in JSON metadata
    if (empty($konten['kode_kegiatan']) && isset($parts[2])) {
        $konten['kode_kegiatan'] = $parts[2];
    }
}

// BEM Standard: Clear recipient for clones
if ($isClone) {
    $data['tujuan'] = '';
    if (isset($konten['sapaan_tujuan'])) $konten['sapaan_tujuan'] = '';
}

$suratModel = new SuratModel();
$list_perihal  = $suratModel->getTemplates($ukm_id, $periode_id, 'perihal');
$list_tujuan   = $suratModel->getTemplates($ukm_id, $periode_id, 'tujuan');
$list_kegiatan = $suratModel->getTemplates($ukm_id, $periode_id, 'kegiatan');
$list_tempat   = $suratModel->getTemplates($ukm_id, $periode_id, 'tempat');
$panitia       = $suratModel->getPanitia($ukm_id, $periode_id);
$lampiran_internal_list = $suratModel->getArsipLampiran($ukm_id, $periode_id);
?>

<div class="p-6 bg-slate-50 min-h-screen">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
        <a href="index.php?page=arsip_surat&ukm_id=<?= $ukm_id ?>" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-200 text-slate-400 hover:text-blue-500 transition-all shadow-sm">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-4xl text-blue-600 font-bold">edit_square</span>
            <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tight">
                <?= $isEdit ? 'Edit Surat' : ($isClone ? 'Duplikat Surat' : 'Buat Surat Baru') ?>
            </h1>
        </div>
    </div>

    <!-- Superadmin UKM Selector -->
    <?php if ($isSuperAdmin): ?>
        <div class="mb-8 max-w-6xl mx-auto bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
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
                <select onchange="window.location.href='index.php?page=buat_surat&ukm_id='+this.value" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-500 font-bold transition-all cursor-pointer text-slate-700 dark:text-white shadow-inner">
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
        <div class="max-w-6xl mx-auto bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-8 rounded-[2.5rem] flex items-center gap-6">
            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-800 rounded-3xl flex items-center justify-center text-blue-600 dark:text-blue-400">
                <span class="material-symbols-outlined text-3xl">info</span>
            </div>
            <div>
                <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Pilih Organisasi Terlebih Dahulu</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Silakan pilih UKM/HMP melalui dropdown di atas untuk mulai membuat surat.</p>
            </div>
        </div>
    <?php else: ?>

    <?php if($isClone): ?>
        <div class="max-w-6xl mx-auto mb-8 p-6 bg-blue-600 rounded-[2rem] text-white shadow-xl shadow-blue-500/20 flex items-center gap-6 animate-in slide-in-from-top duration-500">
            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-3xl">account_tree</span>
            </div>
            <div>
                <h3 class="font-black uppercase tracking-widest text-sm mb-1">Mode Multi-Recipient Terdeteksi</h3>
                <p class="text-xs text-blue-100 font-medium leading-relaxed">Anda sedang membuat salinan surat untuk tujuan berbeda. Nomor surat akan tetap sama (<?= h($clone_data['nomor_surat']) ?>) agar terkelompok dalam satu arsip yang rapi.</p>
            </div>
        </div>
    <?php endif; ?>

    <form id="formBuatSurat" action="index.php?action=arsip_surat_<?= $isEdit ? 'update' : 'store' ?>" method="POST" enctype="multipart/form-data" class="space-y-10 max-w-6xl mx-auto pb-32">
        <?= csrf_field() ?>
        <?php if($isEdit): ?><input type="hidden" name="id" value="<?= $data['id'] ?>"><?php endif; ?>
        <?php if($isClone): ?><input type="hidden" name="parent_id" value="<?= $clone_data['id'] ?>"><?php endif; ?>
        <input type="hidden" name="ukm_id" value="<?= $ukm_id ?>">
        <input type="hidden" name="konten_json" id="konten_json">

        <!-- Card 1: Identitas Surat -->
        <div class="bg-white rounded-[2.5rem] p-10 border border-slate-200 shadow-xl shadow-slate-200/50">
            <div class="flex items-center gap-3 mb-10 text-blue-600">
                <span class="material-symbols-outlined text-2xl">fingerprint</span>
                <h2 class="font-bold uppercase tracking-[0.2em] text-xs">Identitas Surat</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="space-y-3">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Nomor Urut</label>
                    <input type="text" id="next_urut_input" value="<?= $nomorUrutVal ?>" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700" placeholder="001" oninput="updateNomorSurat()">
                    <input type="hidden" name="nomor_surat" id="nomor_surat" value="<?= $data['nomor_surat'] ?? '' ?>">
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Jenis Surat</label>
                    <select name="jenis_surat" id="jenis_surat" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 cursor-pointer" onchange="handleJenisSuratChange()">
                        <option value="L" <?= ($data['jenis_surat'] ?? '') === 'L' ? 'selected' : '' ?>>Surat Keluar (L)</option>
                        <option value="D" <?= ($data['jenis_surat'] ?? '') === 'D' ? 'selected' : '' ?>>Surat Dalam (D)</option>
                    </select>
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Kode Kegiatan</label>
                    <input type="text" name="kode_kegiatan" id="kode_kegiatan" value="<?= h($konten['kode_kegiatan'] ?? '') ?>" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 uppercase" placeholder="Program Kegiatan" oninput="updateNomorSurat()">
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Perihal Surat</label>
                    <div class="tpl-picker" id="picker-perihal">
                        <input type="text" name="perihal" id="input_perihal" placeholder="Cari atau ketik perihal..." value="<?= h($data['perihal'] ?? '') ?>" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700" required onfocus="showTplResults('perihal')" onkeyup="filterTpl('perihal')">
                        <div class="tpl-results" id="results-perihal">
                            <?php foreach($list_perihal as $p): ?>
                                <div class="tpl-item" onclick='selectTpl("input_perihal", "<?= j($p["isi_teks"]) ?>", "perihal")'>
                                    <div class="tpl-item-label"><?= h($p['label']) ?></div>
                                    <div class="tpl-item-text"><?= h($p['isi_teks']) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Titimangsa (Tempat Tanggal)</label>
                    <input type="text" name="tempat_tanggal" placeholder="Majalengka, 12 Januari 2026" value="<?= h($data['tempat_tanggal'] ?? 'Majalengka, ' . date('j F Y')) ?>" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700">
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Kepada Yth (Tujuan)</label>
                    <div class="tpl-picker" id="picker-tujuan">
                        <input type="text" id="tpl_tujuan_search" placeholder="Cari template tujuan..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700" onfocus="showTplResults('tujuan')" onkeyup="filterTpl('tujuan')">
                        <div class="tpl-results" id="results-tujuan">
                            <?php foreach($list_tujuan as $t): ?>
                                <div class="tpl-item" onclick='selectTpl("tujuan_nama", "<?= j($t["isi_teks"]) ?>", "tujuan")'>
                                    <div class="tpl-item-label"><?= h($t['label']) ?></div>
                                    <div class="tpl-item-text"><?= h($t['isi_teks']) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Tanggal Dikirim (Arsip)</label>
                    <input type="date" name="tanggal_dikirim" value="<?= $data['tanggal_dikirim'] ?? '' ?>" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-10">
                <div class="space-y-3">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Sapaan</label>
                    <select name="sapaan_tujuan" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700">
                        <option value="">-- Tanpa Sapaan --</option>
                        <?php foreach(['Bapak', 'Ibu', 'Saudara', 'Saudari'] as $s): ?>
                            <option value="<?= $s ?>" <?= ($konten['sapaan_tujuan'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="md:col-span-3 space-y-3">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Nama Penerima & Alamat</label>
                    <textarea name="tujuan_nama" id="tujuan_nama" rows="2" placeholder="Nama Penerima..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700" required><?= $data['tujuan'] ?? '' ?></textarea>
                </div>
            </div>
        </div>

        <!-- Card 2: Konten Paragraf Pembuka -->
        <div class="bg-white rounded-[2.5rem] p-10 border border-slate-200 shadow-xl shadow-slate-200/50">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3 text-blue-600">
                    <span class="material-symbols-outlined text-2xl">format_quote</span>
                    <h2 class="font-bold uppercase tracking-[0.2em] text-xs">Paragraf Pembuka</h2>
                </div>
                <button type="button" onclick="toggleModeParagraf()" id="toggle-mode-btn" class="px-5 py-2 bg-slate-100 text-[10px] font-black rounded-xl hover:bg-blue-600 hover:text-white transition-all uppercase tracking-widest">
                    <?= !empty($konten['tema_kegiatan_custom']) ? 'Ganti ke Mode Template' : 'Ganti ke Mode Custom' ?>
                </button>
            </div>

            <div id="blok-template" style="<?= !empty($konten['tema_kegiatan_custom']) ? 'display:none' : '' ?>" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Nama Kegiatan</label>
                        <div class="tpl-picker" id="picker-kegiatan">
                            <input type="text" name="nama_kegiatan" id="nama_kegiatan" placeholder="Cth: LDKM 2026" value="<?= h($konten['nama_kegiatan'] ?? '') ?>" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700" onfocus="showTplResults('kegiatan')" onkeyup="filterTpl('kegiatan')">
                            <div class="tpl-results" id="results-kegiatan">
                                <?php foreach($list_kegiatan as $k): ?>
                                    <div class="tpl-item" onclick='selectKegiatan("<?= j($k["label"]) ?>", "<?= j($k["perihal_default"]) ?>")'>
                                        <div class="tpl-item-label"><?= h($k['label']) ?></div>
                                        <div class="tpl-item-text">Kode: <?= h($k['perihal_default']) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Tema Kegiatan</label>
                        <input type="text" name="tema_kegiatan" id="tema_kegiatan" placeholder="Cth: Bersinergi Membangun Bangsa" value="<?= h($konten['tema_kegiatan'] ?? '') ?>" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700">
                    </div>
                </div>
                <div class="p-6 bg-blue-50 border border-blue-100 rounded-2xl text-xs text-blue-600 font-bold leading-relaxed">
                    <i class="material-symbols-outlined text-sm align-middle mr-2">magic_button</i>
                    Sehubungan akan diadakannya kegiatan <span id="prev_nama" class="text-blue-900"><?= !empty($konten['nama_kegiatan']) ? h($konten['nama_kegiatan']) : '[Nama Kegiatan]' ?></span> dengan tema <span id="prev_tema" class="text-blue-900">"<?= !empty($konten['tema_kegiatan']) ? h($konten['tema_kegiatan']) : '[Tema]' ?>"</span> yang akan dilaksanakan pada :
                </div>
            </div>

            <div id="blok-custom" style="<?= !empty($konten['tema_kegiatan_custom']) ? '' : 'display:none' ?>" class="space-y-4">
                <input type="hidden" name="tema_kegiatan_custom" id="input_tema_kegiatan_custom" value="<?= h($konten['tema_kegiatan_custom'] ?? '') ?>">
                <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="flex gap-2 p-3 bg-slate-50 border-b border-slate-200">
                        <button type="button" onclick="execRTE('bold')" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl hover:bg-blue-600 hover:text-white transition-all font-bold">B</button>
                        <button type="button" onclick="execRTE('italic')" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl hover:bg-blue-600 hover:text-white transition-all italic">I</button>
                        <button type="button" onclick="execRTE('underline')" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl hover:bg-blue-600 hover:text-white transition-all underline">U</button>
                    </div>
                    <div id="rte-editor" contenteditable="true" class="p-8 bg-white min-h-[150px] outline-none text-slate-700 text-sm leading-relaxed"><?= $konten['tema_kegiatan_custom'] ?? '' ?></div>
                </div>
            </div>
        </div>

        <!-- Card 3: Waktu & Tempat -->
        <div class="bg-white rounded-[2.5rem] p-10 border border-slate-200 shadow-xl shadow-slate-200/50">
            <div class="flex items-center gap-3 mb-10 text-blue-600">
                <span class="material-symbols-outlined text-2xl">calendar_today</span>
                <h2 class="font-bold uppercase tracking-[0.2em] text-xs">Waktu & Tempat Pelaksanaan</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <!-- Hari & Tanggal -->
                <div class="p-8 bg-slate-50 rounded-[2rem] border border-slate-100">
                    <div class="flex items-center gap-3 mb-6 text-blue-500">
                        <span class="material-symbols-outlined text-xl">event</span>
                        <span class="text-[10px] font-black uppercase tracking-widest">Hari & Tanggal</span>
                    </div>
                    <div class="flex flex-wrap gap-4 items-center">
                        <input type="date" id="tgl-mulai" onchange="formatTanggalRange()" value="<?= $konten['pelaksanaan_tgl_raw'] ?? '' ?>" min="<?= date('Y-m-d') ?>" class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm font-bold text-slate-700">
                        <span class="text-[10px] font-black text-slate-300 uppercase">selama</span>
                        <div class="flex gap-2 items-center">
                            <select id="durasi-hari" onchange="handleDurasiChange()" class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm font-bold text-slate-700">
                                <?php for($i=1; $i<=7; $i++): ?>
                                    <option value="<?= $i ?>" <?= ($konten['pelaksanaan_durasi'] ?? 1) == $i ? 'selected' : '' ?>><?= $i ?> Hari</option>
                                <?php endfor; ?>
                                <option value="custom">Custom...</option>
                            </select>
                            <input type="number" id="custom-hari" min="1" value="<?= $konten['pelaksanaan_durasi'] ?? 1 ?>" oninput="formatTanggalRange()" class="hidden w-20 bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm font-bold text-slate-700">
                        </div>
                    </div>
                    <input type="hidden" name="pelaksanaan_hari_tanggal" id="out-tanggal" value="<?= h($konten['pelaksanaan_hari_tanggal'] ?? '') ?>">
                    <div id="preview-tanggal" class="mt-8 p-5 bg-white rounded-2xl border-l-4 border-l-blue-500 text-[10px] font-black text-blue-600 uppercase tracking-widest shadow-sm">
                        <?= !empty($konten['pelaksanaan_hari_tanggal']) ? h($konten['pelaksanaan_hari_tanggal']) : 'Tentukan Tanggal...' ?>
                    </div>
                </div>

                <!-- Waktu Pelaksanaan -->
                <div class="p-8 bg-slate-50 rounded-[2rem] border border-slate-100">
                    <div class="flex items-center gap-3 mb-6 text-blue-500">
                        <span class="material-symbols-outlined text-xl">schedule</span>
                        <span class="text-[10px] font-black uppercase tracking-widest">Waktu Pelaksanaan</span>
                    </div>
                    <input type="hidden" id="out-waktu" name="pelaksanaan_waktu" value="<?= h($konten['pelaksanaan_waktu'] ?? '') ?>">
                    <div class="flex flex-wrap gap-8 items-start">
                        <div>
                            <div class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Mulai</div>
                            <div class="drum-group">
                                <div>
                                    <button type="button" class="drum-arrow" onclick="drumHS.scrollBy(-1)">▲</button>
                                    <div class="drum-col" id="drum-h-start"></div>
                                    <button type="button" class="drum-arrow" onclick="drumHS.scrollBy(1)">▼</button>
                                </div>
                                <span class="drum-colon">:</span>
                                <div>
                                    <button type="button" class="drum-arrow" onclick="drumMS.scrollBy(-1)">▲</button>
                                    <div class="drum-col" id="drum-m-start"></div>
                                    <button type="button" class="drum-arrow" onclick="drumMS.scrollBy(1)">▼</button>
                                </div>
                            </div>
                        </div>
                        <div class="pt-10 text-[10px] font-black text-slate-300">s.d</div>
                        <div id="drum-end-wrap">
                            <div class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Selesai</div>
                            <div class="drum-group">
                                <div>
                                    <button type="button" class="drum-arrow" onclick="drumHE.scrollBy(-1)">▲</button>
                                    <div class="drum-col" id="drum-h-end"></div>
                                    <button type="button" class="drum-arrow" onclick="drumHE.scrollBy(1)">▼</button>
                                </div>
                                <span class="drum-colon">:</span>
                                <div>
                                    <button type="button" class="drum-arrow" onclick="drumME.scrollBy(-1)">▲</button>
                                    <div class="drum-col" id="drum-m-end"></div>
                                    <button type="button" class="drum-arrow" onclick="drumME.scrollBy(1)">▼</button>
                                </div>
                            </div>
                        </div>
                        <div class="pt-8">
                            <div onclick="doToggleSelesai()" class="toggle-switch-wrap flex flex-col items-center gap-3 p-4 bg-white rounded-2xl border border-slate-200 shadow-sm cursor-pointer">
                                <div id="ts-switch" class="ts-switch"><div id="ts-knob" class="ts-knob"></div></div>
                                <span id="ts-label" class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Tanpa akhir</span>
                            </div>
                        </div>
                    </div>
                    <div id="preview-waktu" class="mt-8 p-5 bg-white rounded-2xl border-l-4 border-l-blue-500 text-[10px] font-black text-blue-600 uppercase tracking-widest shadow-sm">
                        <?= !empty($konten['pelaksanaan_waktu']) ? h($konten['pelaksanaan_waktu']) : 'Tentukan Waktu...' ?>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mt-10">
                <div class="space-y-3">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Tempat Pelaksanaan</label>
                    <div class="tpl-picker" id="picker-tempat">
                        <input type="text" name="pelaksanaan_tempat" id="input_tempat" placeholder="Cari atau ketik tempat..." value="<?= h($konten['pelaksanaan_tempat'] ?? '') ?>" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700" onfocus="showTplResults('tempat')" onkeyup="filterTpl('tempat')">
                        <div class="tpl-results" id="results-tempat">
                            <?php foreach($list_tempat as $t): ?>
                                <div class="tpl-item" onclick='selectTpl("input_tempat", "<?= j($t["label"]) ?>", "tempat")'>
                                    <div class="tpl-item-label"><?= h($t['label']) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Konteks Tambahan (Kalimat Akhir)</label>
                    <input type="text" name="konteks_akhir" placeholder="Cth: sebagai syarat pencairan dana" value="<?= h($konten['konteks_akhir'] ?? '') ?>" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700">
                </div>
            </div>
            <div class="mt-8 space-y-3">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Tembusan (Opsional)</label>
                <textarea name="tembusan" rows="2" placeholder="1. Arsip UKM..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700"><?= h($konten['tembusan'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- Card 4: Penanggung Jawab / Panitia -->
        <div class="bg-white rounded-[2.5rem] p-10 border border-slate-200 shadow-xl shadow-slate-200/50">
            <div class="flex items-center gap-3 mb-10 text-blue-600">
                <span class="material-symbols-outlined text-3xl">draw</span>
                <h2 class="font-bold uppercase tracking-[0.2em] text-xs">Penanggung Jawab / Panitia</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <!-- Ketua -->
                <div class="space-y-6">
                    <div class="space-y-3">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Nama Ketua Pelaksana</label>
                        <input type="text" name="panitia_ketua" id="panitia_ketua" list="panitia_list" placeholder="Cari atau ketik nama..." value="<?= h($konten['panitia_ketua'] ?? '') ?>" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 uppercase" oninput="checkPanitiaMatch()">
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Mode Tanda Tangan</label>
                        <select name="ttd_ketua_mode" id="ttd_ketua_mode" onchange="toggleTtdMode('ketua', this.value)" class="w-full bg-white border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-blue-600 shadow-sm">
                            <option value="digital" <?= ($konten['ttd_ketua_mode'] ?? 'digital') === 'digital' ? 'selected' : '' ?>>✍️ Tanda Tangan Digital (Gambar di Web)</option>
                            <option value="sistem" <?= ($konten['ttd_ketua_mode'] ?? '') === 'sistem' ? 'selected' : '' ?>>📂 Gunakan TTD Sistem (Database)</option>
                            <option value="upload" <?= ($konten['ttd_ketua_mode'] ?? '') === 'upload' ? 'selected' : '' ?>>📁 Unggah Foto Tanda Tangan</option>
                            <option value="basah" <?= ($konten['ttd_ketua_mode'] ?? '') === 'basah' ? 'selected' : '' ?>>🚫 Kosong / Tanda Tangan Basah</option>
                        </select>
                    </div>

                    <!-- Canvas -->
                    <div id="ttd_ketua_digital_wrap" class="hidden space-y-4">
                        <div class="p-6 bg-blue-50/50 rounded-3xl border border-blue-100">
                            <p class="text-[9px] font-black text-blue-500 uppercase tracking-widest mb-4 text-center">Silakan gambar di bawah ini:</p>
                            <div class="relative w-full h-48 bg-white rounded-2xl border-2 border-dashed border-blue-200 overflow-hidden shadow-inner">
                                <canvas id="canvas_ketua" class="w-full h-full cursor-crosshair"></canvas>
                                <button type="button" onclick="clearTtdCanvas('ketua')" class="absolute bottom-3 right-3 px-4 py-2 bg-slate-900 text-white text-[10px] font-black rounded-xl uppercase tracking-widest shadow-lg">Reset</button>
                            </div>
                        </div>
                    </div>

                    <!-- Preview Wrap -->
                    <div id="preview_wrap_ketua" class="hidden flex flex-col items-center gap-3 p-6 bg-slate-50 rounded-3xl border border-slate-100">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Pratinjau Tanda Tangan:</p>
                        <img id="preview_img_ketua" class="h-20 object-contain mix-blend-multiply transition-all">
                        <div id="ketua_match_badge" class="hidden flex items-center gap-2 bg-emerald-500/10 text-emerald-600 px-3 py-1 rounded-full border border-emerald-500/20">
                            <span class="material-symbols-outlined text-sm">verified</span>
                            <span class="text-[9px] font-black uppercase">Database Match</span>
                        </div>
                    </div>

                    <!-- Hidden Input -->
                    <input type="hidden" name="ttd_ketua_base64" id="ttd_ketua_base64">
                    <div id="ttd_ketua_upload_wrap" class="hidden p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <input type="file" name="ttd_ketua_file" class="text-xs file:bg-blue-600 file:text-white file:border-none file:rounded-xl file:px-4 file:py-2">
                    </div>
                </div>

                <!-- Sekre -->
                <div class="space-y-6">
                    <div class="space-y-3">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Nama Sekretaris Pelaksana</label>
                        <input type="text" name="panitia_sekre" id="panitia_sekre" list="panitia_list" placeholder="Cari atau ketik nama..." value="<?= h($konten['panitia_sekre'] ?? '') ?>" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 uppercase" oninput="checkPanitiaMatch()">
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Mode Tanda Tangan</label>
                        <select name="ttd_sekre_mode" id="ttd_sekre_mode" onchange="toggleTtdMode('sekre', this.value)" class="w-full bg-white border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-blue-600 shadow-sm">
                            <option value="digital" <?= ($konten['ttd_sekre_mode'] ?? 'digital') === 'digital' ? 'selected' : '' ?>>✍️ Tanda Tangan Digital (Gambar di Web)</option>
                            <option value="sistem" <?= ($konten['ttd_sekre_mode'] ?? '') === 'sistem' ? 'selected' : '' ?>>📂 Gunakan TTD Sistem (Database)</option>
                            <option value="upload" <?= ($konten['ttd_sekre_mode'] ?? '') === 'upload' ? 'selected' : '' ?>>📁 Unggah Foto Tanda Tangan</option>
                            <option value="basah" <?= ($konten['ttd_sekre_mode'] ?? '') === 'basah' ? 'selected' : '' ?>>🚫 Kosong / Tanda Tangan Basah</option>
                        </select>
                    </div>

                    <!-- Canvas -->
                    <div id="ttd_sekre_digital_wrap" class="hidden space-y-4">
                        <div class="p-6 bg-blue-50/50 rounded-3xl border border-blue-100">
                            <p class="text-[9px] font-black text-blue-500 uppercase tracking-widest mb-4 text-center">Silakan gambar di bawah ini:</p>
                            <div class="relative w-full h-48 bg-white rounded-2xl border-2 border-dashed border-blue-200 overflow-hidden shadow-inner">
                                <canvas id="canvas_sekre" class="w-full h-full cursor-crosshair"></canvas>
                                <button type="button" onclick="clearTtdCanvas('sekre')" class="absolute bottom-3 right-3 px-4 py-2 bg-slate-900 text-white text-[10px] font-black rounded-xl uppercase tracking-widest shadow-lg">Reset</button>
                            </div>
                        </div>
                    </div>

                    <!-- Preview Wrap -->
                    <div id="preview_wrap_sekre" class="hidden flex flex-col items-center gap-3 p-6 bg-slate-50 rounded-3xl border border-slate-100">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Pratinjau Tanda Tangan:</p>
                        <img id="preview_img_sekre" class="h-20 object-contain mix-blend-multiply transition-all">
                        <div id="sekre_match_badge" class="hidden flex items-center gap-2 bg-emerald-500/10 text-emerald-600 px-3 py-1 rounded-full border border-emerald-500/20">
                            <span class="material-symbols-outlined text-sm">verified</span>
                            <span class="text-[9px] font-black uppercase">Database Match</span>
                        </div>
                    </div>

                    <!-- Hidden Input -->
                    <input type="hidden" name="ttd_sekre_base64" id="ttd_sekre_base64">
                    <div id="ttd_sekre_upload_wrap" class="hidden p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <input type="file" name="ttd_sekre_file" class="text-xs file:bg-blue-600 file:text-white file:border-none file:rounded-xl file:px-4 file:py-2">
                    </div>
                </div>
            </div>
        </div>

        <datalist id="panitia_list">
            <?php foreach ($panitia as $p): ?>
                <option value="<?= h($p['nama']) ?>"><?= h($p['jabatan']) ?></option>
            <?php endforeach; ?>
        </datalist>

        <!-- Card 5: Lampiran Berkas -->
        <div class="bg-white rounded-[2.5rem] p-10 border border-slate-200 shadow-xl shadow-slate-200/50">
            <div class="flex items-center gap-3 mb-10 text-blue-600">
                <span class="material-symbols-outlined text-2xl">attachment</span>
                <h2 class="font-bold uppercase tracking-[0.2em] text-xs">Lampiran Berkas (Pustaka & Upload)</h2>
            </div>

            <div class="space-y-10">
                <!-- PILIH DARI DATA INTERNAL -->
                <?php if (!empty($lampiran_internal_list)): ?>
                <div class="space-y-4">
                    <div class="text-[10px] font-black text-blue-500 uppercase tracking-widest ml-1">Pilih Dari Arsip Peminjaman:</div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach($lampiran_internal_list as $li): 
                            $isSelected = in_array($li['id'], ($konten['lampiran_internal_ids'] ?? []));
                        ?>
                        <label class="flex items-center gap-4 bg-slate-50 p-5 rounded-3xl cursor-pointer hover:bg-blue-50 transition-all border border-transparent hover:border-blue-200 group">
                            <input type="checkbox" name="lampiran_internal[]" value="<?= $li['id'] ?>" <?= $isSelected ? 'checked' : '' ?> class="w-6 h-6 rounded-lg accent-blue-600">
                            <div class="flex-grow">
                                <div class="text-xs font-black text-slate-800 uppercase tracking-tight group-hover:text-blue-600"><?= h($li['nama_acara']) ?></div>
                                <div class="text-[9px] font-bold text-slate-400 uppercase mt-1"><?= h($li['tanggal_kegiatan']) ?></div>
                            </div>
                            <span class="material-symbols-outlined text-slate-300 group-hover:text-blue-200">database</span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="space-y-4">
                    <div class="text-[10px] font-black text-blue-500 uppercase tracking-widest ml-1">Upload PDF Baru:</div>
                    <div id="lampiran_drop_zone" onclick="document.getElementById('lampiran_upload').click()" class="relative border-2 border-dashed border-slate-200 rounded-[2.5rem] p-12 text-center hover:bg-blue-50/50 hover:border-blue-300 transition-all cursor-pointer group">
                        <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-5 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-blue-500 text-4xl">cloud_upload</span>
                        </div>
                        <p class="text-sm font-bold text-slate-600">Klik atau seret file PDF ke sini</p>
                        <p class="text-[9px] font-black text-slate-400 uppercase mt-2 tracking-widest">Dapat memilih beberapa file sekaligus</p>
                        <input type="file" name="lampiran_surat[]" id="lampiran_upload" accept=".pdf" multiple class="hidden">
                    </div>
                    
                    <div id="file-list-preview" class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>

                    <?php if(!empty($konten['lampiran_uploaded'])): ?>
                        <div class="mt-8 space-y-4">
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">File PDF Tersimpan:</div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <?php foreach($konten['lampiran_uploaded'] as $path): ?>
                                    <div class="flex items-center justify-between p-5 bg-emerald-50 border border-emerald-100 rounded-3xl">
                                        <div class="flex items-center gap-4">
                                            <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                                            <span class="text-xs font-black text-emerald-700 uppercase tracking-tight"><?= h(basename($path)) ?></span>
                                        </div>
                                        <label class="flex items-center gap-2 cursor-pointer bg-white px-3 py-1.5 rounded-xl border border-emerald-200 hover:bg-red-50 hover:border-red-200 group transition-all">
                                            <input type="checkbox" name="delete_lampiran[]" value="<?= h($path) ?>" class="w-4 h-4 accent-red-500">
                                            <span class="text-[9px] font-black text-emerald-500 group-hover:text-red-500 uppercase tracking-widest">Hapus</span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Card 6A: Tanda Tangan (TTD) -->
        <div class="bg-white rounded-[2.5rem] p-10 border border-slate-200 shadow-xl shadow-slate-200/50">
            <div class="flex items-center gap-3 mb-10 text-blue-600">
                <span class="material-symbols-outlined text-2xl">draw</span>
                <h2 class="font-bold uppercase tracking-[0.2em] text-xs">Tanda Tangan (TTD)</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php 
                $opsi_ttd = [
                    'show_ttd_warek' => ['SERTAKAN TTD WAREK III', 'person', 'Pihak Rektorat'],
                    'show_ttd_presma' => ['SERTAKAN TTD KETUA BEM', 'person', 'Badan Eksekutif Mahasiswa'],
                    'show_ttd_ketum' => ['SERTAKAN TTD KETUA UMUM ' . ($ukm['singkatan'] ?? 'UKM'), 'person', 'Ketua Organisasi'],
                ];

                foreach($opsi_ttd as $key => $data): ?>
                    <label class="relative flex flex-col p-8 bg-slate-50 rounded-[2.5rem] border border-slate-100 cursor-pointer hover:bg-blue-50 transition-all group overflow-hidden ttd-option" data-key="<?= $key ?>">
                        <div class="flex items-center justify-between mb-3 relative z-10">
                            <span class="material-symbols-outlined text-blue-500 group-hover:scale-125 transition-transform duration-500"><?= $data[1] ?></span>
                            <input type="checkbox" name="<?= $key ?>" id="<?= $key ?>" value="1" <?= ($konten[$key] ?? '') == '1' ? 'checked' : '' ?> class="w-7 h-7 rounded-xl accent-blue-600 border-slate-300 transition-all" onchange="handleTTDExclusion(this)">
                        </div>
                        <span class="text-[10px] font-black text-slate-500 tracking-widest uppercase relative z-10"><?= $data[0] ?></span>
                        <span class="text-[9px] text-slate-400 mt-1 relative z-10"><?= $data[2] ?></span>
                        <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-blue-500/5 rounded-full group-hover:scale-[3] transition-transform duration-700"></div>
                        <div class="disabled-overlay hidden absolute inset-0 bg-slate-200/60 rounded-[2.5rem] z-20 flex items-center justify-center">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Nonaktif</span>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Card 6B: Stempel / Cap -->
        <div class="bg-white rounded-[2.5rem] p-10 border border-slate-200 shadow-xl shadow-slate-200/50">
            <div class="flex items-center gap-3 mb-10 text-emerald-600">
                <span class="material-symbols-outlined text-2xl">verified</span>
                <h2 class="font-bold uppercase tracking-[0.2em] text-xs">Stempel / Cap</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php 
                $opsi_cap = [
                    'show_cap_panitia' => ['SERTAKAN CAP PANITIA', 'verified', ''],
                    'show_cap_warek' => ['SERTAKAN CAP LEMBAGA / WAREK', 'verified', 'cap-warek'],
                    'show_cap_bem' => ['SERTAKAN CAP BEM', 'verified', 'cap-bem'],
                    'show_cap_ukm' => ['SERTAKAN CAP UKM / HMP', 'verified', ''],
                ];

                foreach($opsi_cap as $key => $data): ?>
                    <label class="relative flex flex-col p-8 bg-slate-50 rounded-[2.5rem] border border-slate-100 cursor-pointer hover:bg-emerald-50 transition-all group overflow-hidden cap-option" data-key="<?= $key ?>" data-group="<?= $data[2] ?>">
                        <div class="flex items-center justify-between mb-3 relative z-10">
                            <span class="material-symbols-outlined text-emerald-500 group-hover:scale-125 transition-transform duration-500"><?= $data[1] ?></span>
                            <input type="checkbox" name="<?= $key ?>" id="<?= $key ?>" value="1" <?= ($konten[$key] ?? '') == '1' ? 'checked' : '' ?> class="w-7 h-7 rounded-xl accent-emerald-600 border-slate-300 transition-all">
                        </div>
                        <span class="text-[10px] font-black text-slate-500 tracking-widest uppercase relative z-10"><?= $data[0] ?></span>
                        <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-emerald-500/5 rounded-full group-hover:scale-[3] transition-transform duration-700"></div>
                        <div class="disabled-overlay hidden absolute inset-0 bg-slate-200/60 rounded-[2.5rem] z-20 flex items-center justify-center">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Nonaktif</span>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Sticky Footer -->
        <div class="fixed bottom-8 left-1/2 -translate-x-1/2 w-full max-w-lg px-6 z-50">
            <button type="submit" class="w-full py-6 bg-slate-900 hover:bg-blue-600 text-white rounded-[2.5rem] font-black text-lg uppercase tracking-widest shadow-2xl shadow-slate-900/40 transition-all active:scale-95 flex items-center justify-center gap-4 border-4 border-white/10 group">
                <span class="material-symbols-outlined text-3xl group-hover:rotate-12 transition-transform">save_as</span>
                <?= $isEdit ? 'Simpan Perubahan' : 'Arsipkan Surat' ?>
            </button>
        </div>
    </form>
    <?php endif; ?>
</div>

<script>
    // ========== DATA ==========
    const ukmSingkatan = "<?= h($ukm['singkatan'] ?? 'UKM') ?>";
    let currentYear = "<?= date('Y') ?>";
    const romanMonths = ["I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"];
    let currentMonthRoman = romanMonths[new Date().getMonth()];

    // BEM Standard: Maintain group integrity for Clones/Edits
    <?php if ($isEdit || $isClone): 
        $parts = explode('/', $data['nomor_surat'] ?? '');
        $rParts = array_reverse($parts);
    ?>
        // Fallback to current system time if extraction fails
        currentMonthRoman = "<?= $rParts[1] ?? '' ?>" || currentMonthRoman;
        currentYear = "<?= $rParts[0] ?? '' ?>" || currentYear;
        console.log("BEM Debug - Extracted:", {m: "<?= $rParts[1] ?? '' ?>", y: "<?= $rParts[0] ?? '' ?>"});
    <?php endif; ?>
    const isEdit = <?= $isEdit ? 'true' : 'false' ?>;
    const isClone = <?= $isClone ? 'true' : 'false' ?>;
    const nextUrutL = "<?= $next_urut_L ?? '001' ?>";
    const nextUrutD = "<?= $next_urut_D ?? '001' ?>";
    const panitiaList = <?= json_encode($panitia) ?>;

    // ========== Template Picker Logic ==========
    function showTplResults(type) {
        document.querySelectorAll('.tpl-results').forEach(el => el.style.display = 'none');
        const res = document.getElementById('results-' + type);
        if(res) res.style.display = 'block';
    }

    function filterTpl(type) {
        const picker = document.getElementById('picker-' + type);
        const input = picker.querySelector('input');
        const filter = input.value.toLowerCase();
        const results = document.getElementById('results-' + type);
        const items = results.getElementsByClassName('tpl-item');
        
        for(let i=0; i<items.length; i++) {
            const label = items[i].querySelector('.tpl-item-label').innerText.toLowerCase();
            const text = items[i].querySelector('.tpl-item-text') ? items[i].querySelector('.tpl-item-text').innerText.toLowerCase() : '';
            items[i].style.display = (label.includes(filter) || text.includes(filter)) ? "" : "none";
        }
    }

    function selectTpl(targetId, value, type) {
        document.getElementById(targetId).value = value;
        document.getElementById('results-' + type).style.display = 'none';
        if(targetId === 'input_perihal') updateNomorSurat();
    }

    function selectKegiatan(nama, kode) {
        document.getElementById('nama_kegiatan').value = nama;
        document.getElementById('kode_kegiatan').value = kode || 'PROKER';
        document.getElementById('results-kegiatan').style.display = 'none';
        document.getElementById('prev_nama').innerText = nama;
        updateNomorSurat();
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.tpl-picker')) {
            document.querySelectorAll('.tpl-results').forEach(el => el.style.display = 'none');
        }
    });

    // ========== Paragraf Mode ==========
    function toggleModeParagraf() {
        const tpl = document.getElementById('blok-template');
        const cust = document.getElementById('blok-custom');
        const btn = document.getElementById('toggle-mode-btn');
        if(tpl.style.display !== 'none') {
            tpl.style.display = 'none';
            cust.style.display = 'block';
            btn.innerText = 'Ganti ke Mode Template';
        } else {
            tpl.style.display = 'block';
            cust.style.display = 'none';
            btn.innerText = 'Ganti ke Mode Custom';
        }
    }

    function execRTE(cmd) {
        document.getElementById('rte-editor').focus();
        document.execCommand(cmd, false, null);
    }

    // Live Preview
    document.getElementById('nama_kegiatan').addEventListener('input', (e) => document.getElementById('prev_nama').innerText = e.target.value || '[Nama Kegiatan]');
    document.getElementById('tema_kegiatan').addEventListener('input', (e) => document.getElementById('prev_tema').innerText = e.target.value || '[Tema]');

    // ========== Drum Picker Class ==========
    class DrumPicker {
        constructor(elId, values, initVal, onChange) {
            this.el = document.getElementById(elId);
            this.values = values;
            this.idx = Math.max(0, values.indexOf(initVal));
            this.onChange = onChange;
            this.ITEM_HEIGHT = 36;
            this._build();
            this._bind();
            this._render(false);
        }
        _build() {
            const hl = document.createElement('div');
            hl.className = 'drum-highlight';
            this.el.appendChild(hl);
            this.inner = document.createElement('div');
            this.inner.className = 'drum-inner';
            const pad = () => { const d=document.createElement('div'); d.className='drum-item'; return d; };
            [0,1].forEach(() => this.inner.appendChild(pad()));
            this.values.forEach((v, i) => {
                const d = document.createElement('div');
                d.className = 'drum-item'; d.dataset.i = i; d.textContent = v;
                this.inner.appendChild(d);
            });
            [0,1].forEach(() => this.inner.appendChild(pad()));
            this.el.appendChild(this.inner);
        }
        _render(animate = true) {
            const offset = -36 - this.idx * this.ITEM_HEIGHT;
            this.inner.style.transition = animate ? 'transform 0.2s cubic-bezier(0.1, 0.7, 1.0, 0.1)' : 'none';
            this.inner.style.transform = `translateY(${offset}px)`;
            this.inner.querySelectorAll('[data-i]').forEach(el => {
                const diff = Math.abs(parseInt(el.dataset.i) - this.idx);
                el.className = 'drum-item' + (diff===0?' sel':diff===1?' near1':'');
            });
            if (this.onChange) setTimeout(() => this.onChange(this.values[this.idx]), 0);
        }
        scrollBy(delta) {
            const oldIdx = this.idx;
            const len = this.values.length;
            this.idx = (this.idx + delta) % len;
            if (this.idx < 0) this.idx += len;
            this._render(true);
        }
        _bind() {
            this.el.addEventListener('wheel', e => { e.preventDefault(); this.scrollBy(e.deltaY > 0 ? 1 : -1); }, { passive: false });
        }
        val() { return this.values[this.idx]; }
    }

    const hours = Array.from({length:24}, (_,i) => String(i).padStart(2,'0'));
    const mins = Array.from({length:60}, (_,i) => String(i).padStart(2,'0'));
    
    let drumHS, drumMS, drumHE, drumME, _selesaiMode = <?= !empty($konten['waktu_sampai_selesai']) ? 'true' : 'false' ?>;

    document.addEventListener('DOMContentLoaded', () => {
        const existingWaktu = document.getElementById('out-waktu').value || '';
        const parts = existingWaktu.split(' s.d ');
        const startT = (parts[0] || '08.00').split('.');
        const endT = (parts[1] && parts[1] !== 'Selesai') ? parts[1].split('.') : ['17', '00'];

        drumHS = new DrumPicker('drum-h-start', hours, startT[0]||'08', updateWaktu);
        drumMS = new DrumPicker('drum-m-start', mins, startT[1]||'00', updateWaktu);
        drumHE = new DrumPicker('drum-h-end', hours, endT[0]||'17', updateWaktu);
        drumME = new DrumPicker('drum-m-end', mins, endT[1]||'00', updateWaktu);
        
        applyToggleSelesai(_selesaiMode);

        // Initial TTD Mode check
        ['ketua', 'sekre'].forEach(id => {
            const sel = document.getElementById('ttd_' + id + '_mode');
            if(sel) toggleTtdMode(id, sel.value);
        });
        
        if (!isEdit) updateNomorSurat();
        checkPanitiaMatch();
    });

    function updateWaktu() {
        if (!drumHS || !drumMS || !drumHE || !drumME) return;
        const start = drumHS.val() + '.' + drumMS.val();
        const end = _selesaiMode ? 'Selesai' : drumHE.val() + '.' + drumME.val();
        const result = start + ' s.d ' + end;
        document.getElementById('out-waktu').value = result;
        document.getElementById('preview-waktu').innerText = result;
    }

    function doToggleSelesai() {
        _selesaiMode = !_selesaiMode;
        applyToggleSelesai(_selesaiMode);
    }

    function applyToggleSelesai(on) {
        _selesaiMode = on;
        const sw = document.getElementById('ts-switch');
        const knob = document.getElementById('ts-knob');
        const lbl = document.getElementById('ts-label');
        const endWrap = document.getElementById('drum-end-wrap');
        
        sw.style.background = on ? '#2563eb' : '#e2e8f0';
        knob.style.transform = on ? 'translateX(18px)' : 'translateX(0)';
        lbl.textContent = on ? 'Tanpa akhir' : 'Dengan akhir';
        endWrap.style.opacity = on ? '0.3' : '1';
        endWrap.style.pointerEvents = on ? 'none' : '';
        updateWaktu();
    }

    // ========== Tanggal & Nomor Logic ==========
    function handleJenisSuratChange() {
        if (!isClone) {
            const jenis = document.getElementById('jenis_surat').value;
            document.getElementById('next_urut_input').value = (jenis === 'D') ? nextUrutD : nextUrutL;
        }
        updateNomorSurat();
    }

    function updateNomorSurat() {
        if (isEdit && !isClone) return;
        const urut = document.getElementById('next_urut_input').value.padStart(3, '0');
        const jenis = document.getElementById('jenis_surat').value;
        const kode = (document.getElementById('kode_kegiatan').value || '[KODE]').toUpperCase();
        document.getElementById('nomor_surat').value = `${urut}/${jenis}/${kode}/${ukmSingkatan}/${currentMonthRoman}/${currentYear}`;
    }

    function handleDurasiChange() {
        const sel = document.getElementById('durasi-hari');
        const custom = document.getElementById('custom-hari');
        custom.classList.toggle('hidden', sel.value !== 'custom');
        formatTanggalRange();
    }

    function formatTanggalRange() {
        const mulai = document.getElementById('tgl-mulai').value;
        const durSel = document.getElementById('durasi-hari');
        const durCustom = document.getElementById('custom-hari');
        if (!mulai) return;

        let jmlHari = durSel.value === 'custom' ? parseInt(durCustom.value) : parseInt(durSel.value);
        if (isNaN(jmlHari)) jmlHari = 1;

        const d1 = new Date(mulai);
        const hariNames = ['Minggu','Senin','Selasa','Rabu','Kamis',"Jum'at",'Sabtu'];
        const bulanNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

        let result = "";
        if (jmlHari === 1) {
            result = hariNames[d1.getDay()] + ', ' + d1.getDate() + ' ' + bulanNames[d1.getMonth()] + ' ' + d1.getFullYear();
        } else {
            const d2 = new Date(d1);
            d2.setDate(d1.getDate() + (jmlHari - 1));
            const hariText = hariNames[d1.getDay()] === hariNames[d2.getDay()] ? hariNames[d1.getDay()] : hariNames[d1.getDay()] + ' - ' + hariNames[d2.getDay()];
            result = hariText + ', ' + d1.getDate() + ' - ' + d2.getDate() + ' ' + bulanNames[d1.getMonth()] + ' ' + d1.getFullYear();
        }
        document.getElementById('out-tanggal').value = result;
        document.getElementById('preview-tanggal').innerText = result;
    }

    // ========== Lampiran & Signature Logic ==========
    const dropZone = document.getElementById('lampiran_drop_zone');
    const fileInput = document.getElementById('lampiran_upload');
    const previewList = document.getElementById('file-list-preview');

    if (dropZone) {
        fileInput.addEventListener('change', () => updateFilePreview(fileInput.files));
        dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('bg-blue-50'); });
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('bg-blue-50'));
        dropZone.addEventListener('drop', (e) => { e.preventDefault(); fileInput.files = e.dataTransfer.files; updateFilePreview(fileInput.files); });
    }

    function updateFilePreview(files) {
        previewList.innerHTML = '';
        Array.from(files).forEach(file => {
            const item = document.createElement('div');
            item.className = 'flex items-center gap-3 p-4 bg-blue-50 border border-blue-100 rounded-2xl';
            item.innerHTML = `<span class='material-symbols-outlined text-blue-500'>description</span><div class='text-xs font-bold'>${file.name}</div>`;
            previewList.appendChild(item);
        });
    }

    const ttdCanvases = {};
    const ttdCtxs = {};
    const ttdDrawing = {};

    function initTtdCanvas(id) {
        const canvas = document.getElementById('canvas_' + id);
        if (!canvas) return;
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width * 2;
        canvas.height = rect.height * 2;
        const ctx = canvas.getContext('2d');
        ctx.scale(2, 2); ctx.lineWidth = 3; ctx.lineCap = 'round'; ctx.strokeStyle = '#02183b';
        ttdCanvases[id] = canvas; ttdCtxs[id] = ctx; ttdDrawing[id] = false;

        const getPos = (e) => { const r = canvas.getBoundingClientRect(); const cx = e.touches ? e.touches[0].clientX : e.clientX; const cy = e.touches ? e.touches[0].clientY : e.clientY; return { x: cx - r.left, y: cy - r.top }; };
        const start = (e) => { e.preventDefault(); ttdDrawing[id] = true; const p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); };
        const draw = (e) => { if (!ttdDrawing[id]) return; e.preventDefault(); const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); };
        const stop = () => { 
            ttdDrawing[id] = false; ctx.beginPath();
            const dataUrl = canvas.toDataURL();
            document.getElementById('ttd_' + id + '_base64').value = dataUrl;
            const pWrap = document.getElementById('preview_wrap_' + id);
            const pImg = document.getElementById('preview_img_' + id);
            if (pWrap && pImg) { pWrap.classList.remove('hidden'); pImg.src = dataUrl; }
        };
        canvas.addEventListener('mousedown', start); canvas.addEventListener('mousemove', draw); window.addEventListener('mouseup', stop);
        canvas.addEventListener('touchstart', start); canvas.addEventListener('touchmove', draw); window.addEventListener('touchend', stop);
    }

    function clearTtdCanvas(id) {
        if (!ttdCtxs[id]) return;
        ttdCtxs[id].clearRect(0, 0, ttdCanvases[id].width, ttdCanvases[id].height);
        document.getElementById('ttd_' + id + '_base64').value = "";
        document.getElementById('preview_wrap_' + id).classList.add('hidden');
    }

    function toggleTtdMode(id, mode) {
        document.getElementById('ttd_' + id + '_digital_wrap').classList.add('hidden');
        document.getElementById('ttd_' + id + '_upload_wrap').classList.add('hidden');
        const badge = document.getElementById(id + '_match_badge');
        if(badge) badge.classList.add('hidden');

        if (mode === 'digital') {
            document.getElementById('ttd_' + id + '_digital_wrap').classList.remove('hidden');
            setTimeout(() => { 
                if (!ttdCanvases[id]) initTtdCanvas(id); 
                
                // Load existing signature if available
                const existing = id === 'ketua' ? '<?= $konten["ttd_ketua_custom"] ?? "" ?>' : '<?= $konten["ttd_sekre_custom"] ?? "" ?>';
                if (existing && !ttdDrawing[id]) {
                    const img = new Image();
                    img.onload = () => {
                        ttdCtxs[id].clearRect(0, 0, ttdCanvases[id].width, ttdCanvases[id].height);
                        ttdCtxs[id].drawImage(img, 0, 0, ttdCanvases[id].width/2, ttdCanvases[id].height/2);
                        
                        const pWrap = document.getElementById('preview_wrap_' + id);
                        const pImg = document.getElementById('preview_img_' + id);
                        if (pWrap && pImg) { pWrap.classList.remove('hidden'); pImg.src = existing; }
                    };
                    img.src = existing;
                }
            }, 50);
        } else if (mode === 'upload') {
            document.getElementById('ttd_' + id + '_upload_wrap').classList.remove('hidden');
        } else if (mode === 'sistem') {
            checkPanitiaMatch();
        }
    }

    function checkPanitiaMatch() {
        ['ketua', 'sekre'].forEach(id => {
            const input = document.getElementById('panitia_' + id);
            const badge = document.getElementById(id + '_match_badge');
            const preview = document.getElementById('preview_img_' + id);
            const wrap = document.getElementById('preview_wrap_' + id);
            const mode = document.getElementById('ttd_' + id + '_mode').value;
            if (mode !== 'sistem') return;
            const match = panitiaList.find(p => p.nama.toLowerCase() === input.value.toLowerCase());
            if (match && match.ttd_path) {
                if(badge) badge.classList.remove('hidden'); 
                if(wrap) wrap.classList.remove('hidden');
                preview.src = match.ttd_path;
            } else {
                if(badge) badge.classList.add('hidden'); 
                if(wrap) wrap.classList.add('hidden');
            }
        });
    }

    // FORM SUBMIT SYNC
    document.getElementById('formBuatSurat').onsubmit = function() {
        const k = {};
        const fields = ['nama_kegiatan','kode_kegiatan','tema_kegiatan','pelaksanaan_hari_tanggal','pelaksanaan_waktu','pelaksanaan_tempat','konteks_akhir','tembusan','panitia_ketua','ttd_ketua_mode','panitia_sekre','ttd_sekre_mode','show_ttd_warek','show_ttd_presma','show_ttd_ketum','show_cap_panitia','show_cap_warek','show_cap_bem','show_cap_ukm'];
        fields.forEach(f => {
            const el = this.elements[f];
            if (el) k[f] = el.type === 'checkbox' ? (el.checked ? '1' : '0') : el.value;
        });
        
        const internalLampiran = Array.from(this.querySelectorAll('input[name="lampiran_internal[]"]:checked')).map(el => el.value);
        k['lampiran_internal_ids'] = internalLampiran;

        if (document.getElementById('blok-custom').style.display !== 'none') {
            k['tema_kegiatan_custom'] = document.getElementById('rte-editor').innerHTML;
        } else {
            k['tema_kegiatan_custom'] = '';
        }
        
        // Sync sapaan
        k['sapaan_tujuan'] = this.elements['sapaan_tujuan'].value;
        
        // Sync raw date for editing
        k['pelaksanaan_tgl_raw'] = document.getElementById('tgl-mulai').value;
        k['pelaksanaan_durasi'] = document.getElementById('durasi-hari').value === 'custom' ? document.getElementById('custom-hari').value : document.getElementById('durasi-hari').value;
        k['waktu_sampai_selesai'] = _selesaiMode ? '1' : '0';

        document.getElementById('konten_json').value = JSON.stringify(k);
        
        // Sync tujuan to main field
        const sapaan = this.elements['sapaan_tujuan'].value ? this.elements['sapaan_tujuan'].value + ' ' : '';
        const tujuanFull = sapaan + document.getElementById('tujuan_nama').value;
        const hiddenTujuan = document.createElement('input');
        hiddenTujuan.type = 'hidden';
        hiddenTujuan.name = 'tujuan';
        hiddenTujuan.value = tujuanFull;
        this.appendChild(hiddenTujuan);

        return true;
    };

    // ====================================================
    // TTD / CAP Mutual Exclusion Logic
    // ====================================================
    function handleTTDExclusion(changedEl) {
        const warekTTD = document.getElementById('show_ttd_warek');
        const bemTTD = document.getElementById('show_ttd_presma');
        const capWarek = document.getElementById('show_cap_warek');
        const capBEM = document.getElementById('show_cap_bem');

        if (!warekTTD || !bemTTD || !capWarek || !capBEM) return;

        // When Warek is checked → disable BEM TTD + Cap BEM
        if (changedEl === warekTTD && warekTTD.checked) {
            bemTTD.checked = false;
            capBEM.checked = false;
        }
        // When BEM is checked → disable Warek TTD + Cap Warek
        if (changedEl === bemTTD && bemTTD.checked) {
            warekTTD.checked = false;
            capWarek.checked = false;
        }

        // Apply visual disabled states
        updateExclusionVisual(warekTTD, bemTTD, capWarek, capBEM);
    }

    function updateExclusionVisual(warekTTD, bemTTD, capWarek, capBEM) {
        const warekChecked = warekTTD.checked;
        const bemChecked = bemTTD.checked;

        // Disable visual for BEM TTD and Cap BEM when Warek is active
        toggleDisabled(bemTTD, warekChecked);
        toggleDisabled(capBEM, warekChecked);

        // Disable visual for Warek TTD and Cap Warek when BEM is active
        toggleDisabled(warekTTD, bemChecked);
        toggleDisabled(capWarek, bemChecked);
    }

    function toggleDisabled(checkbox, isDisabled) {
        const label = checkbox.closest('label') || checkbox.closest('.ttd-option') || checkbox.closest('.cap-option');
        if (!label) return;
        const overlay = label.querySelector('.disabled-overlay');
        if (isDisabled) {
            checkbox.disabled = true;
            if (overlay) overlay.classList.remove('hidden');
            label.style.opacity = '0.5';
            label.style.pointerEvents = 'none';
        } else {
            checkbox.disabled = false;
            if (overlay) overlay.classList.add('hidden');
            label.style.opacity = '';
            label.style.pointerEvents = '';
        }
    }

    // Run on page load for edit mode
    document.addEventListener('DOMContentLoaded', function() {
        const warekTTD = document.getElementById('show_ttd_warek');
        const bemTTD = document.getElementById('show_ttd_presma');
        const capWarek = document.getElementById('show_cap_warek');
        const capBEM = document.getElementById('show_cap_bem');
        if (warekTTD && bemTTD && capWarek && capBEM) {
            updateExclusionVisual(warekTTD, bemTTD, capWarek, capBEM);
        }
    });
</script>

<style>
    .tpl-picker { position: relative; }
    .tpl-results { position: absolute; top: calc(100% + 8px); left: 0; right: 0; background: #fff; border: 1px solid #e2e8f0; border-radius: 20px; max-height: 250px; overflow-y: auto; z-index: 1000; display: none; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
    .tpl-item { padding: 14px 20px; cursor: pointer; border-bottom: 1px solid #f1f5f9; transition: all 0.2s; }
    .tpl-item:hover { background: #f8fafc; }
    .tpl-item-label { font-weight: 900; color: #1e293b; font-size: 0.9rem; margin-bottom: 2px; text-transform: uppercase; }
    .tpl-item-text { font-size: 0.75rem; color: #64748b; font-weight: 600; }
    .drum-col { width: 54px; height: 160px; background: #fff; border-radius: 16px; overflow: hidden; position: relative; cursor: ns-resize; border: 1px solid #e2e8f0; }
    .drum-inner { position: absolute; top: 0; left: 0; width: 100%; transition: transform 0.2s cubic-bezier(0.1, 0.7, 1.0, 0.1); }
    .drum-item { height: 36px; line-height: 36px; text-align: center; font-size: 1.1rem; color: #cbd5e1; transition: all 0.2s; opacity: 0.3; font-weight: 800; box-sizing: border-box; }
    .drum-item.sel { color: #2563eb; opacity: 1; transform: scale(1.1); }
    .drum-item.near1 { opacity: 0.6; }
    .drum-highlight { position: absolute; top: 72px; left: 4px; right: 4px; height: 36px; background: rgba(37, 99, 235, 0.05); border-radius: 12px; border: 1px solid rgba(37, 99, 235, 0.1); pointer-events: none; z-index: 5; }
    .drum-group { display: flex; align-items: center; gap: 8px; }
    .drum-arrow { background: #f8fafc; border: 1px solid #e2e8f0; color: #64748b; font-size: 0.7rem; cursor: pointer; padding: 4px 10px; border-radius: 10px; width: 100%; margin: 3px 0; font-weight: 900; }
    .drum-arrow:hover { background: #2563eb; color: #fff; border-color: #2563eb; }
    .drum-colon { color: #2563eb; font-weight: 900; font-size: 1.5rem; margin-top: 15px; }
    .ts-switch { position: relative; width: 44px; height: 24px; background: #e2e8f0; border-radius: 12px; transition: 0.4s; }
    .ts-knob { position: absolute; top: 3px; left: 3px; width: 18px; height: 18px; background: #fff; border-radius: 50%; transition: 0.4s; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
</style>
