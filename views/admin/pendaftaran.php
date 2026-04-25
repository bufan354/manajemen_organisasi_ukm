<?php
$status = $settings['form_reg_status'] ?? 'dibuka';

// Default Instructions
$defaultInstruction = "Pendaftaran Anda telah berhasil disubmit. \nSilakan tunggu persetujuan dari pengurus admin. \nKami akan segera menghubungi Anda jika ada informasi lanjutan.";
$instruction = $settings['form_reg_instruction'] ?? $defaultInstruction;
if (empty(trim($instruction))) {
    $instruction = $defaultInstruction;
}

// Default Questionnaire
$defaultQuestions = [
    ['text' => 'Mengapa Anda tertarik untuk bergabung dengan <?= h($ENTITY) ?> ini?', 'required' => true],
    ['text' => 'Apakah Anda memiliki pengalaman sebelumnya di bidang ini? Jika ya, tolong jelaskan.', 'required' => true]
];
$questionsJson = $settings['form_reg_questions'] ?? "[]";
if (empty(trim($questionsJson)) || $questionsJson === "[]") {
    $questionsJson = json_encode($defaultQuestions);
}

$questions = json_decode($questionsJson, true) ?: [];
?>
<main class="flex-1 p-8 min-h-[calc(100vh-64px)] bg-surface-container-low">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-on-surface tracking-tight">Kelola Pendaftaran</h2>
            <p class="text-on-surface-variant mt-1">Konfigurasi formulir pendaftaran, kuisioner, dan syarat absensi untuk calon anggota baru <?= h($ENTITY) ?>.</p>
        </div>
        
        <?php if ($role === 'superadmin'): ?>
        <div class="bg-white p-2 rounded-xl shadow-sm border border-outline-variant/30 flex items-center gap-2">
            <span class="material-symbols-outlined text-slate-400 pl-2">domain</span>
            <select onchange="window.location.href='index.php?page=pendaftaran&ukm_id='+this.value" class="bg-transparent border-none text-sm font-bold text-slate-700 focus:ring-0 cursor-pointer pr-8 py-2">
                <option value="0" disabled <?= $ukmId === 0 ? 'selected' : '' ?>>-- Pilih UKM Dulu --</option>
                <?php foreach ($ukmList as $u): ?>
                    <option value="<?= $u['id'] ?>" <?= $ukmId === $u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['nama']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($role === 'superadmin' && $ukmId === 0): ?>
    <div class="bg-blue-50 text-blue-800 p-6 rounded-2xl border border-blue-200 shadow-sm flex items-start gap-4">
        <span class="material-symbols-outlined text-blue-600">info</span>
        <div>
            <h3 class="font-bold text-lg">Pilih <?= h($ENTITY) ?> Terlebih Dahulu</h3>
            <p class="text-sm mt-1">Sebagai Super Admin, Anda diwajibkan untuk memilih <?= h($ENTITY) ?> lewat *dropdown* di ujung kanan atas layar untuk bisa mengonfigurasi form pendaftarannya secara spesifik per-<?= h($ENTITY) ?>.</p>
        </div>
    </div>
    <?php else: ?>

    <form action="index.php?action=pendaftaran_config_save" method="POST">
    <?= csrf_field() ?>
        
        <input type="hidden" name="ukm_id" value="<?= $ukmId ?>">
        
        <div class="flex justify-end mb-6">
            <button type="submit" class="flex items-center gap-2 px-5 py-2.5 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/20 hover:bg-primary-container active:scale-95 transition-all">
                <span class="material-symbols-outlined text-xl">save</span>
                Simpan Konfigurasi
            </button>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
            <!-- Kuisioner Section -->
            <div class="xl:col-span-8 space-y-6">
                <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-outline-variant/20 p-8">
                    <div class="flex items-center justify-between mb-8 pb-4 border-b border-surface-container">
                        <div>
                            <h3 class="text-xl font-bold text-on-surface flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">dynamic_form</span>
                                Pertanyaan Kuisioner Tambahan
                            </h3>
                            <p class="text-sm text-on-surface-variant font-medium mt-1">Saring calon anggota dengan memberikan pertanyaan tambahan setelah pendaftaran identitas dasar.</p>
                        </div>
                        <button type="button" onclick="addQuestion()" class="px-4 py-2 bg-blue-50 text-blue-700 font-bold text-sm border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">add</span> Tambah Baris Baru
                        </button>
                    </div>

                    <div class="space-y-4" id="questionnaire-list">
                        <?php if (empty($questions)): ?>
                            <p id="empty-state" class="text-sm text-slate-500 text-center py-4 bg-slate-50 rounded-xl border border-dashed border-slate-300">Belum ada blok pertanyaan tambahan. Anda dapat menambahkannya menggunakan tombol di atas.</p>
                        <?php else: ?>
                            <!-- JS will populate or SSR -->
                            <?php foreach ($questions as $idx => $q): ?>
                            <div class="p-6 bg-surface-container-low rounded-xl border border-surface-container flex gap-4 group question-row">
                                <div class="flex flex-col gap-2 opacity-30 group-hover:opacity-100 transition-opacity mt-2">
                                    <span class="material-symbols-outlined text-[20px]">drag_indicator</span>
                                </div>
                                <div class="flex-1 space-y-4">
                                    <input type="text" name="questions[<?= $idx ?>][text]" class="w-full bg-white border border-outline-variant/30 rounded-lg px-4 py-3 font-bold text-slate-800 focus:ring-2 focus:ring-primary/20 transition-all text-sm mb-2" value="<?= htmlspecialchars($q['text']) ?>" required placeholder="Ketik Pertanyaan Di Sini...">
                                    <div class="flex items-center justify-between">
                                        <label class="flex items-center gap-2 text-sm text-slate-600 font-bold">
                                            <input type="checkbox" name="questions[<?= $idx ?>][required]" class="rounded border-slate-300 text-primary w-4 h-4 focus:ring-primary" <?= ($q['required'] ?? false) ? 'checked' : '' ?>> Wajib Diisi
                                        </label>
                                        <button type="button" onclick="this.closest('.question-row').remove(); checkEmpty();" class="text-red-500 hover:bg-red-50 p-2 rounded-lg transition-colors flex items-center" title="Hapus Pertanyaan">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Info & Config Pasca-Daftar Section -->
            <div class="xl:col-span-4 space-y-6">
                <!-- Dashboard Registration Toggle -->
                <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-outline-variant/20 p-8">
                     <h3 class="font-bold text-lg text-slate-900 mb-4">Status Pendaftaran</h3>
                     <div class="space-y-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="regStatus" value="dibuka" class="rounded-full w-4 h-4 text-primary focus:ring-primary" <?= $status === 'dibuka' ? 'checked' : '' ?>>
                            <span class="text-sm font-bold text-slate-700">Dibuka (Menerima Pendaftar Baru)</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="regStatus" value="ditutup" class="rounded-full w-4 h-4 text-slate-400 focus:ring-slate-400" <?= $status === 'ditutup' ? 'checked' : '' ?>>
                            <span class="text-sm font-bold text-slate-500">Ditutup Sementara</span>
                        </label>
                     </div>
                </div>

                <!-- Custom Instruction -->
                <div class="bg-primary/5 rounded-2xl border border-primary/20 p-6 relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 text-primary opacity-10">
                        <span class="material-symbols-outlined text-[100px]" style="font-variation-settings: 'FILL' 1;">fingerprint</span>
                    </div>
                    <div class="relative z-10">
                        <h3 class="font-bold text-md text-primary mb-2 flex items-center gap-2">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">info</span> Catatan Pasca Pendaftaran
                        </h3>
                        <p class="text-[12px] text-slate-700 leading-relaxed mb-4 font-medium">Instruksi final yang akan dibaca calon anggota sesaat setelah berhasil mendaftar (berpindah ke halaman sukses).</p>
                        
                        <div class="space-y-2">
                            <textarea name="regInstruction" class="w-full bg-white border border-primary/30 rounded-xl px-3 py-2 focus:ring-2 focus:ring-primary/20 transition-all text-sm text-slate-800" rows="8"><?= htmlspecialchars($instruction) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php endif; ?>
</main>

<link href="assets/admin/css/pendaftaran.css" rel="stylesheet"/>

<!-- Basic JS for Dynamic Fields -->
<script>
    let qIndex = 999;
    function addQuestion() {
        const list = document.getElementById('questionnaire-list');
        const emptyState = document.getElementById('empty-state');
        if (emptyState) emptyState.remove();
        
        qIndex++;
        const html = `
        <div class="p-6 bg-surface-container-low rounded-xl border border-surface-container flex gap-4 group question-row">
            <div class="flex flex-col gap-2 opacity-30 mt-2">
                <span class="material-symbols-outlined text-[20px]">drag_indicator</span>
            </div>
            <div class="flex-1 space-y-4">
                <input type="text" name="questions[${qIndex}][text]" class="w-full bg-white border border-outline-variant/30 rounded-lg px-4 py-3 font-bold text-slate-800 focus:ring-primary" required placeholder="Tuliskan Pertanyaan Anda...">
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-slate-600 font-bold">
                        <input type="checkbox" name="questions[${qIndex}][required]" checked class="rounded border-slate-300 text-primary w-4 h-4 focus:ring-primary"> Wajib Diisi
                    </label>
                    <button type="button" onclick="this.closest('.question-row').remove(); checkEmpty();" class="text-red-500 hover:bg-red-50 p-2 rounded-lg transition-colors flex items-center">
                        <span class="material-symbols-outlined text-[20px]">delete</span>
                    </button>
                </div>
            </div>
        </div>`;
        list.insertAdjacentHTML('beforeend', html);
    }

    function checkEmpty() {
        const list = document.getElementById('questionnaire-list');
        if (list.querySelectorAll('.question-row').length === 0) {
            list.innerHTML = '<p id="empty-state" class="text-sm text-slate-500 text-center py-4 bg-slate-50 rounded-xl border border-dashed border-slate-300">Belum ada blok pertanyaan tambahan. Anda dapat menambahkannya menggunakan tombol di atas.</p>';
        }
    }
</script>
