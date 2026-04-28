<?php
/**
 * View: Arsip Surat (Premium Version - Light Theme)
 * Sinkronisasi penuh dengan standar BEM: Grouping, Multi-Recipient, & Integritas Nomor.
 */

// Grouping Logic (BEM Standard)
$groupedSurat = [];
foreach ($suratList as $s) {
    // Gunakan nomor_surat sebagai kunci grup utama
    $groupedSurat[$s['nomor_surat']][] = $s;
}
?>

<style>
    .hidden { display: none !important; }
    .cursor-pointer { cursor: pointer !important; }
</style>

<script>
    // Force toggleGroup into global scope immediately
    window.toggleGroup = function(groupId, btn) {
        const rows = document.querySelectorAll(`tr[data-group="${groupId}"]`);
        const icon = btn.querySelector('.material-symbols-outlined:last-child');
        rows.forEach(row => {
            row.classList.toggle('hidden');
        });
        if (icon) {
            const isRotated = icon.style.transform === 'rotate(180deg)';
            icon.style.transform = isRotated ? 'rotate(0deg)' : 'rotate(180deg)';
        }
    };

    window.copyRedaksi = async function(data, btn) {
        let perihal = data.perihal || "Surat";
        let kegiatan = data.kegiatan || "Kegiatan Organisasi";
        let tujuan = data.tujuan;
        let tujuanFirst = tujuan.split('\n')[0].trim();
        let sapaan = data.sapaan ? data.sapaan + " " : "";
        
        // Tentukan kata kerja aksi
        let actionWord = "menyampaikan " + perihal.toLowerCase() + " kepada " + sapaan + tujuanFirst;
        if(perihal.toLowerCase().includes("undangan")) {
            actionWord = "mengundang " + sapaan + tujuanFirst;
        }

        let text = `Assalamu'alaikum Wr. Wb.
Yth. 
${tujuan}

Sehubungan dengan diadakanya ${kegiatan}. Dengan ini kami ${actionWord} pada kegiatan tersebut, yang akan dilaksanakan pada :

🗓️ | ${data.hari || '-'}
🕘 | ${data.waktu || '-'}
🏢 | ${data.tempat || '-'}

${data.konteks ? data.konteks + '\n\n' : ''}Demikian informasi ini kami sampaikan, atas perhatian dan kerjasamanya kami ucapkan terima kasih.

Wassalamu’alaikum Wr. Wb.`;

        try {
            await navigator.clipboard.writeText(text);
            const original = btn.innerHTML;
            btn.innerHTML = '<span class="material-symbols-outlined text-sm">check</span> Tersalin';
            btn.classList.add('bg-emerald-600', 'text-white');
            setTimeout(() => {
                btn.innerHTML = original;
                btn.classList.remove('bg-emerald-600', 'text-white');
            }, 2000);
        } catch (err) {
            console.error('Gagal menyalin:', err);
        }
    };

    window.confirmDelete = function(id, nomor) {
        if (confirm(`Apakah Anda yakin ingin menghapus arsip surat nomor:\n${nomor}\n\nTindakan ini permanen.`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'index.php?action=arsip_surat_delete';
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = 'csrf_token';
            csrf.value = '<?= Session::csrfToken() ?>';
            
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'id';
            idInput.value = id;

            const ukmInput = document.createElement('input');
            ukmInput.type = 'hidden';
            ukmInput.name = 'ukm_id';
            ukmInput.value = '<?= $ukm_id ?>';
            
            form.appendChild(csrf);
            form.appendChild(idInput);
            form.appendChild(ukmInput);
            document.body.appendChild(form);
            form.submit();
        }
    };
</script>

<div class="p-6 bg-slate-50 dark:bg-[#0a0a0a] min-h-screen">
    <!-- Superadmin UKM Selector -->
    <?php if ($isSuperAdmin): ?>
        <div class="mb-8 bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
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
                <select onchange="window.location.href='index.php?page=arsip_surat&ukm_id='+this.value" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-500 font-bold transition-all cursor-pointer text-slate-700 dark:text-white shadow-inner">
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

    <?php if ($ukm_id > 0): ?>
        <div class="mb-8 bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-2xl flex items-center justify-center text-indigo-600">
                    <span class="material-symbols-outlined text-3xl">calendar_month</span>
                </div>
                <div>
                    <h2 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">Periode Kepengurusan</h2>
                    <p class="text-xs text-slate-500 font-medium">Pilih periode aktif atau lihat arsip dari kepengurusan sebelumnya.</p>
                </div>
            </div>
            <div class="w-full md:w-72">
                <select onchange="window.location.href='index.php?page=arsip_surat&ukm_id=<?= $ukm_id ?>&periode_id='+this.value" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-indigo-500 font-bold transition-all cursor-pointer text-slate-700 dark:text-white shadow-inner">
                    <option value="0" disabled <?= $periode_id === 0 ? 'selected' : '' ?>>-- Pilih Periode --</option>
                    <?php foreach ($semua_periode as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= $periode_id == $p['id'] ? 'selected' : '' ?>>
                            <?= h($p['nama']) ?> <?= $p['is_active'] ? '(Aktif)' : '(Riwayat)' ?>
                        </option>
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
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Silakan pilih UKM/HMP melalui dropdown di atas untuk mengelola arsip surat mereka.</p>
            </div>
        </div>
    <?php else: ?>
        <!-- Kop Surat Management -->
        <div class="mb-8 bg-white dark:bg-slate-900 rounded-[2.5rem] p-8 border border-slate-200 dark:border-slate-800 shadow-xl shadow-slate-200/50 dark:shadow-none relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity">
                <span class="material-symbols-outlined text-9xl text-slate-900 dark:text-white">description</span>
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-6 text-slate-900 dark:text-white">
                    <div class="p-2 bg-slate-100 dark:bg-slate-800 rounded-xl">
                        <span class="material-symbols-outlined text-2xl text-blue-600">image</span>
                    </div>
                    <h2 class="font-bold uppercase tracking-[0.2em] text-xs">Template Kop Surat Output</h2>
                </div>
                
                <?php if ($can_edit): ?>
                <form action="index.php?action=arsip_surat_save_kop" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="ukm_id" value="<?= $ukm_id ?>">
                    <div class="flex flex-col items-center justify-center border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-3xl p-8 mb-6 hover:border-blue-500 transition-colors cursor-pointer bg-slate-50 dark:bg-slate-800/30 relative group/upload">
                        <input type="file" name="kop_file" onchange="this.form.submit()" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <?php if ($kop_surat): ?>
                            <img src="<?= h($kop_surat) ?>" class="max-w-full h-auto rounded-lg shadow-lg mb-4" alt="Kop Surat">
                        <?php else: ?>
                            <div class="w-full h-32 flex items-center justify-center text-slate-400 italic text-sm group-hover/upload:text-blue-500 transition-colors">Seret & Lepas file di sini atau klik untuk mengganti.</div>
                        <?php endif; ?>
                        <p class="text-[10px] text-slate-400 uppercase font-bold tracking-widest">Format: JPG/PNG. Rekomendasi lebar: 2480px.</p>
                    </div>

                    <div class="flex justify-end gap-3">
                        <span class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-500 rounded-full text-[10px] font-bold uppercase tracking-widest border border-emerald-100 dark:border-emerald-500/20">
                            <span class="material-symbols-outlined text-sm">check_circle</span> TERSEDIA (AKTIF)
                        </span>
                        <button type="submit" class="flex items-center gap-2 px-6 py-3 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white rounded-2xl font-bold text-xs uppercase tracking-widest transition-all shadow-lg">
                            <span class="material-symbols-outlined text-sm">upload</span> Simpan Kop Surat
                        </button>
                    </div>
                </form>
                <?php else: ?>
                    <?php if ($kop_surat): ?>
                        <img src="<?= h($kop_surat) ?>" class="max-w-full h-auto rounded-lg shadow-lg mb-4" alt="Kop Surat">
                    <?php else: ?>
                        <div class="w-full h-32 flex items-center justify-center text-slate-400 italic text-sm">Belum ada kop surat untuk periode ini.</div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Header Actions -->
        <div class="flex flex-wrap gap-3 mb-8">
            <?php 
            $tabs = ['L' => 'Keluar (L)', 'D' => 'Dalam (D)', 'M' => 'Masuk (M)'];
            $colors = ['L' => 'blue', 'D' => 'slate', 'M' => 'purple'];
            foreach ($tabs as $k => $v): 
                $active = ($jenis === $k);
                $color = $colors[$k];
            ?>
                <a href="index.php?page=arsip_surat&jenis=<?= $k ?>&ukm_id=<?= $ukm_id ?>" class="px-8 py-4 rounded-2xl text-xs font-black uppercase tracking-widest transition-all border-b-4 <?= $active ? "bg-{$color}-600 text-white border-{$color}-800 shadow-xl shadow-{$color}-500/20" : "bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 border-slate-100 dark:border-transparent hover:bg-slate-50 dark:hover:bg-slate-800" ?>">
                    <?= $v ?>
                </a>
            <?php endforeach; ?>

            <div class="flex-1"></div>

            <div class="flex gap-2">
                <a href="index.php?action=arsip_surat_export&jenis=<?= $jenis ?>&ukm_id=<?= $ukm_id ?>" class="flex items-center gap-2 px-6 py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-xl shadow-emerald-500/20">
                    <span class="material-symbols-outlined text-sm">description</span> Excel
                </a>
                <?php if ($can_edit): ?>
                <a href="index.php?page=buat_surat&ukm_id=<?= $ukm_id ?>&jenis=<?= $jenis ?>" class="flex items-center gap-2 px-6 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-xl shadow-blue-500/20">
                    <span class="material-symbols-outlined text-sm">add</span> Buat Otomatis
                </a>
                <a href="index.php?page=arsip_manual&type=<?= $jenis ?>&ukm_id=<?= $ukm_id ?>" class="flex items-center gap-2 px-6 py-4 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-xl shadow-amber-500/20">
                    <span class="material-symbols-outlined text-sm">upload_file</span> Catat Manual
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-xl shadow-slate-200/50 dark:shadow-none overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800">
                            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 text-center w-20">No</th>
                            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 text-center">Tanggal <?= $jenis === 'M' ? 'Diterima' : 'Dikirim' ?></th>
                            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Nomor Surat</th>
                            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Perihal</th>
                            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400"><?= $jenis === 'M' ? 'Asal Instansi' : 'Dituju Kepada' ?></th>
                            <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                        <?php if (empty($groupedSurat)): ?>
                            <tr>
                                <td colspan="6" class="px-8 py-20 text-center">
                                    <div class="w-20 h-20 bg-slate-50 dark:bg-slate-800 rounded-3xl flex items-center justify-center mx-auto mb-4 text-slate-300 dark:text-slate-600">
                                        <span class="material-symbols-outlined text-4xl">inventory_2</span>
                                    </div>
                                    <p class="text-slate-400 font-bold uppercase tracking-widest text-xs italic">Belum ada arsip surat.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            $no_urut = 1;
                            foreach ($groupedSurat as $nomorSurat => $items): 
                                $parent = $items[0];
                                $hasChildren = count($items) > 1;
                                $isManual = !empty($parent['file_surat']);
                                $groupId = "group_" . md5($nomorSurat);
                            ?>
                                <!-- Parent Row -->
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group <?= $hasChildren ? 'bg-slate-50/30 dark:bg-slate-800/10' : '' ?>">
                                    <td class="px-8 py-6 text-xs font-black text-slate-300 dark:text-slate-500 text-center"><?= str_pad($no_urut++, 2, '0', STR_PAD_LEFT) ?></td>
                                    <td class="px-8 py-6 text-center">
                                        <?php if ($parent['tanggal_dikirim']): ?>
                                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-xl text-[10px] font-black uppercase tracking-widest border border-blue-100 dark:border-blue-500/20">
                                                <span class="material-symbols-outlined text-xs">calendar_month</span>
                                                <?= date('d/m/Y', strtotime($parent['tanggal_dikirim'])) ?>
                                                <button onclick="openDateModal(<?= $parent['id'] ?>)" class="ml-1 text-blue-400 hover:text-blue-600 transition-all"><span class="material-symbols-outlined text-[14px]">edit</span></button>
                                            </div>
                                        <?php else: ?>
                                            <button onclick="openDateModal(<?= $parent['id'] ?>)" class="px-4 py-2 bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-500 hover:bg-amber-500 hover:text-white border border-amber-100 dark:border-amber-500/20 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm">
                                                <span class="material-symbols-outlined text-xs align-middle mr-1">event_note</span> Set Tanggal
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="text-sm font-black text-slate-800 dark:text-slate-100 mb-2 tracking-tight group-hover:text-blue-600 transition-colors"><?= h($parent['nomor_surat']) ?></div>
                                        <div class="flex flex-wrap gap-2">
                                            <?php if ($isManual): ?>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-[9px] font-black text-slate-500 dark:text-slate-400 uppercase border border-slate-200 dark:border-slate-700 shadow-sm">
                                                    <span class="material-symbols-outlined text-[12px]">hand_paper</span> Manual
                                                </span>
                                                <a href="<?= h($parent['file_surat']) ?>" target="_blank" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-500/10 text-[9px] font-black text-blue-600 dark:text-blue-400 uppercase border border-blue-100 dark:border-blue-500/20 hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                                    <span class="material-symbols-outlined text-[12px]">visibility</span> Lihat File
                                                </a>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-900/40 text-[9px] font-black text-blue-600 dark:text-blue-400 uppercase border border-blue-100 dark:border-blue-500/30 shadow-sm">
                                                    <span class="material-symbols-outlined text-[12px]">robot</span> Sistem
                                                </span>
                                                <a href="views/admin/surat/print_surat.php?id=<?= $parent['id'] ?>" target="_blank" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-600 text-[9px] font-black text-white uppercase shadow-md hover:bg-blue-700 transition-all">
                                                    <span class="material-symbols-outlined text-[12px]">print</span> Cetak PDF
                                                </a>
                                            <?php endif; ?>

                                            <?php if ($hasChildren): ?>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-900/40 text-[9px] font-black text-emerald-600 dark:text-emerald-400 uppercase border border-emerald-100 dark:border-emerald-500/30 shadow-sm">
                                                    <span class="material-symbols-outlined text-[12px]">group</span> <?= count($items) ?> Recipient
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="text-xs text-slate-800 dark:text-slate-100 font-black line-clamp-2 max-w-[250px] mb-2 uppercase tracking-tight"><?= h($parent['perihal']) ?></div>
                                        <div class="flex flex-wrap gap-2">
                                            <?php 
                                            $konten_s = json_decode($parent['konten_surat'], true);
                                            if(!empty($konten_s['lampiran_ids'])): ?>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-purple-50 dark:bg-purple-900/30 text-[8px] font-black text-purple-600 dark:text-purple-400 uppercase border border-purple-100 dark:border-purple-800/50 shadow-sm">
                                                    <span class="material-symbols-outlined text-[12px]">inventory_2</span> Lampiran Sistem
                                                </span>
                                            <?php endif; ?>

                                            <?php if(!empty($konten_s['lampiran_uploaded'])): ?>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-900/30 text-[8px] font-black text-blue-600 dark:text-blue-400 uppercase border border-blue-100 dark:border-blue-800/50 shadow-sm">
                                                    <span class="material-symbols-outlined text-[12px]">picture_as_pdf</span> PDF Upload
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="text-xs text-slate-500 dark:text-slate-400 font-medium line-clamp-2 max-w-[250px]"><?= nl2br(h($parent['tujuan'])) ?></div>
                                        <?php if ($hasChildren): ?>
                                            <div class="mt-2 flex items-center gap-1 text-[9px] font-black text-emerald-500 uppercase tracking-widest">
                                                <span class="material-symbols-outlined text-[12px]">layers</span> Multi-Recipient Group
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col gap-1.5 w-32 mx-auto">
                                            <div class="grid grid-cols-2 gap-1.5">
                                                <?php if ($can_edit): ?>
                                                <a href="index.php?page=<?= $isManual ? 'arsip_manual' : 'buat_surat' ?>&edit=<?= $parent['id'] ?>&ukm_id=<?= $ukm_id ?>" class="flex items-center justify-center gap-1.5 py-2 bg-slate-50 dark:bg-slate-800 hover:bg-blue-600 hover:text-white text-slate-500 dark:text-slate-300 rounded-lg text-[9px] font-black uppercase transition-all shadow-sm border border-slate-100 dark:border-slate-700">
                                                    <span class="material-symbols-outlined text-sm">edit</span> Edit
                                                </a>
                                                <?php endif; ?>
                                                <button type="button" onclick="copyRedaksi(<?= htmlspecialchars(json_encode([
                                                    'tujuan' => $parent['tujuan'],
                                                    'perihal' => $parent['perihal'],
                                                    'nomor' => $parent['nomor_surat'],
                                                    'kegiatan' => $konten_s['nama_kegiatan'] ?? '',
                                                    'hari' => $konten_s['pelaksanaan_hari_tanggal'] ?? '',
                                                    'waktu' => $konten_s['pelaksanaan_waktu'] ?? '',
                                                    'tempat' => $konten_s['pelaksanaan_tempat'] ?? '',
                                                    'konteks' => $konten_s['konteks_akhir'] ?? '',
                                                    'sapaan' => $konten_s['sapaan_tujuan'] ?? ''
                                                ])) ?>, this)" class="<?= $can_edit ? '' : 'col-span-2 ' ?>flex items-center justify-center gap-1.5 py-2 bg-slate-50 dark:bg-slate-800 hover:bg-indigo-600 hover:text-white text-slate-500 dark:text-slate-300 rounded-lg text-[9px] font-black uppercase transition-all shadow-sm border border-slate-100 dark:border-slate-700">
                                                    <span class="material-symbols-outlined text-sm">content_paste</span> Salin
                                                </button>
                                            </div>
                                            <?php if ($can_edit): ?>
                                            <div class="grid grid-cols-2 gap-1.5">
                                                <a href="index.php?page=buat_surat&clone=<?= $parent['id'] ?>&ukm_id=<?= $ukm_id ?>" class="flex items-center justify-center gap-1.5 py-2 w-full bg-emerald-50 dark:bg-emerald-900/30 hover:bg-emerald-600 hover:text-white text-emerald-600 dark:text-emerald-400 rounded-lg text-[9px] font-black uppercase transition-all border border-emerald-100 dark:border-emerald-900/50">
                                                    <span class="material-symbols-outlined text-sm">content_copy</span> Dup
                                                </a>
                                                
                                                <?php if ($jenis === 'M' || $parent['id'] == $latest_id): ?>
                                                    <button onclick="confirmDelete(<?= $parent['id'] ?>, '<?= addslashes($parent['nomor_surat']) ?>')" class="flex items-center justify-center gap-1.5 py-2 bg-red-50 dark:bg-red-900/30 hover:bg-red-600 hover:text-white text-red-600 dark:text-red-400 rounded-lg text-[9px] font-black uppercase transition-all border border-red-100 dark:border-red-900/50">
                                                        <span class="material-symbols-outlined text-sm">delete</span> Hapus
                                                    </button>
                                                <?php else: ?>
                                                    <button onclick="alert('❌ Akses Dibatalkan!\n\nHanya surat urutan PALING AKHIR yang boleh dihapus untuk menjaga integritas nomor urut.\n\nSilakan hapus surat terbaru terlebih dahulu jika ingin menghapus urutan ini.')" class="flex items-center justify-center gap-1.5 py-2 bg-slate-50 dark:bg-slate-800 text-slate-300 dark:text-slate-600 rounded-lg text-[9px] font-black uppercase transition-all border border-slate-100 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all shadow-sm" title="Terkunci: Bukan urutan terakhir">
                                                        <span class="material-symbols-outlined text-sm">lock</span> Lock
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                            <?php endif; ?>

                                            <?php if ($hasChildren): ?>
                                                <button onclick="toggleGroup('<?= $groupId ?>', this)" class="w-full flex items-center justify-between px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg shadow-blue-500/20 group/toggle cursor-pointer">
                                                    <span class="flex items-center gap-2">
                                                        <span class="material-symbols-outlined text-sm">group</span>
                                                        Lihat Anggota Grup (<?= count($items) ?>)
                                                    </span>
                                                    <span class="material-symbols-outlined text-sm transition-transform duration-300">expand_more</span>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Child Rows (Accordion) -->
                                <?php if ($hasChildren): ?>
                                    <?php foreach (array_slice($items, 1) as $child): 
                                        $isChildManual = !empty($child['file_surat']);
                                    ?>
                                        <tr data-group="<?= $groupId ?>" class="hidden bg-slate-50/20 dark:bg-slate-900/40 border-l-4 border-blue-600 transition-all">
                                            <td class="px-8 py-4 text-center">
                                                <div class="text-[14px] font-black text-slate-300 dark:text-slate-600">└</div>
                                            </td>
                                            <td class="px-8 py-4 text-center">
                                                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-slate-800 text-slate-500 rounded-lg text-[9px] font-black uppercase border border-slate-100 dark:border-slate-700">
                                                    <?= $child['tanggal_dikirim'] ? date('d/m/Y', strtotime($child['tanggal_dikirim'])) : '-' ?>
                                                </div>
                                            </td>
                                            <td class="px-8 py-4">
                                                <div class="text-xs font-bold text-slate-400"><?= h($child['nomor_surat']) ?></div>
                                                <div class="flex gap-2 mt-1">
                                                    <a href="<?= $isChildManual ? h($child['file_surat']) : "views/admin/surat/print_surat.php?id={$child['id']}" ?>" target="_blank" class="text-[8px] font-black text-blue-500 uppercase hover:underline flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-[10px]">picture_as_pdf</span> Lihat PDF
                                                    </a>
                                                </div>
                                            </td>
                                            <td class="px-8 py-4">
                                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-1 opacity-50">
                                                    <span class="material-symbols-outlined text-sm">link</span> Identik Group
                                                </div>
                                            </td>
                                            <td class="px-8 py-4">
                                                <div class="text-xs text-slate-800 dark:text-slate-100 font-bold"><?= nl2br(h($child['tujuan'])) ?></div>
                                            </td>
                                            <td class="px-8 py-4 text-center">
                                                <div class="flex items-center justify-center gap-3">
                                                    <?php $konten_c = json_decode($child['konten_surat'], true); ?>
                                                    <button type="button" onclick="copyRedaksi(<?= htmlspecialchars(json_encode([
                                                        'tujuan' => $child['tujuan'],
                                                        'perihal' => $child['perihal'],
                                                        'nomor' => $child['nomor_surat'],
                                                        'kegiatan' => $konten_c['nama_kegiatan'] ?? '',
                                                        'hari' => $konten_c['pelaksanaan_hari_tanggal'] ?? '',
                                                        'waktu' => $konten_c['pelaksanaan_waktu'] ?? '',
                                                        'tempat' => $konten_c['pelaksanaan_tempat'] ?? '',
                                                        'konteks' => $konten_c['konteks_akhir'] ?? '',
                                                        'sapaan' => $konten_c['sapaan_tujuan'] ?? ''
                                                    ])) ?>, this)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white dark:bg-slate-800 text-slate-400 hover:text-indigo-500 hover:shadow-md transition-all border border-slate-100 dark:border-slate-700" title="Salin Redaksi">
                                                        <span class="material-symbols-outlined text-[18px]">content_paste</span>
                                                    </button>
                                                    <?php if ($can_edit): ?>
                                                    <a href="index.php?page=<?= $isChildManual ? 'arsip_manual' : 'buat_surat' ?>&edit=<?= $child['id'] ?>&ukm_id=<?= $ukm_id ?>" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white dark:bg-slate-800 text-slate-400 hover:text-blue-500 hover:shadow-md transition-all border border-slate-100 dark:border-slate-700">
                                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                                    </a>
                                                    <?php if ($jenis === 'M' || $child['id'] == $latest_id): ?>
                                                        <button onclick="confirmDelete(<?= $child['id'] ?>, '<?= addslashes($child['nomor_surat']) ?>')" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white dark:bg-slate-800 text-slate-400 hover:text-red-500 hover:shadow-md transition-all border border-slate-100 dark:border-slate-700">
                                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                                        </button>
                                                    <?php else: ?>
                                                        <button onclick="alert('Hanya surat urutan terakhir yang bisa dihapus.')" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-300 dark:text-slate-600 border border-slate-100 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all" title="Terkunci">
                                                            <span class="material-symbols-outlined text-[18px]">lock</span>
                                                        </button>
                                                    <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal: Set Tanggal -->
<div id="dateModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] w-full max-w-sm p-10 shadow-2xl border border-slate-100 dark:border-slate-800 transition-all duration-300">
        <div class="w-16 h-16 bg-blue-50 dark:bg-blue-900/30 rounded-3xl flex items-center justify-center text-blue-600 mx-auto mb-6">
            <span class="material-symbols-outlined text-3xl">calendar_month</span>
        </div>
        <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight mb-2 text-center">Set Tanggal <?= $jenis === 'M' ? 'Diterima' : 'Dikirim' ?></h2>
        <p class="text-xs text-slate-400 text-center mb-8">Tentukan tanggal surat ini untuk catatan arsip.</p>
        
        <form action="index.php?action=arsip_surat_update_tanggal" method="POST" class="space-y-6">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="modal_surat_id">
            <input type="hidden" name="ukm_id" value="<?= $ukm_id ?>">
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Pilih Tanggal</label>
                <input type="date" name="tanggal_dikirim" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-transparent rounded-2xl px-5 py-4 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all font-bold">
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeDateModal()" class="flex-1 py-4 bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded-2xl font-black text-xs uppercase tracking-widest transition-all hover:bg-slate-100">Batal</button>
                <button type="submit" class="flex-1 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-xl shadow-blue-500/20">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openDateModal(id) {
        document.getElementById('modal_surat_id').value = id;
        const modal = document.getElementById('dateModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function closeDateModal() {
        const modal = document.getElementById('dateModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

</script>
