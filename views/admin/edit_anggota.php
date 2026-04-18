<?php $a = $anggota ?? []; ?>
<main class="flex-1 p-8 min-h-[calc(100vh-64px-112px)] bg-surface-container-low">
    <!-- Header -->
    <div class="mb-8 flex items-center gap-4">
        <a href="index.php?page=anggota" class="w-10 h-10 rounded-full flex items-center justify-center bg-white border border-outline-variant hover:bg-surface transition-colors">
            <span class="material-symbols-outlined text-outline">arrow_back</span>
        </a>
        <div>
            <h2 class="text-3xl font-bold tracking-tight text-on-surface">Edit Data Anggota</h2>
            <p class="text-on-surface-variant body-md">Perbarui informasi anggota, status IoT, dan riwayat posisi kepengurusan.</p>
        </div>
    </div>

    <?= renderFlash() ?>

    <!-- Form Section -->
    <div class="max-w-4xl bg-surface-container-lowest rounded-2xl shadow-[0_12px_40px_rgba(25,28,30,0.04)] p-8">
        <form action="index.php?action=anggota_update" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>
            
            <input type="hidden" name="id" value="<?= $a['id'] ?? '' ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Data Pribadi -->
                <div class="space-y-6">
                    <h3 class="text-lg font-bold border-b border-outline-variant/20 pb-2">Informasi Pribadi</h3>
                    
                    <div class="flex items-center gap-6 mb-2">
                        <div class="w-24 h-24 rounded-full bg-surface-container flex items-center justify-center border-2 border-dashed border-outline/50 overflow-hidden relative group p-1">
                            <?php if (!empty($a['foto_path'])): ?>
                                <img src="<?= htmlspecialchars($a['foto_path']) ?>" class="w-full h-full object-cover rounded-full" alt="Avatar">
                            <?php else: ?>
                                <span class="material-symbols-outlined text-outline text-3xl">person</span>
                            <?php endif; ?>
                            <div class="absolute inset-0 bg-black/40 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer" onclick="document.getElementById('foto-input').click()">
                                <span class="material-symbols-outlined text-white">edit</span>
                            </div>
                        </div>
                        <div>
                            <p class="font-bold text-sm text-on-surface mb-1">Foto Profil</p>
                            <p class="text-xs text-on-surface-variant mb-3">Pilih file baru bila ingin menimpa</p>
                            <input type="file" name="foto" id="foto-input" accept="image/*" class="hidden">
                            <button type="button" onclick="document.getElementById('foto-input').click()" class="px-4 py-2 bg-surface text-primary text-xs font-bold rounded-lg border border-primary/20 hover:bg-primary-fixed transition-colors">Pilih File Pengganti</button>
                        </div>
                    </div>

                    <?php if (!empty($ukmList)): ?>
                    <div class="mb-4">
                        <label class="block text-sm font-bold tracking-wide text-on-surface mb-2">UKM Tujuan</label>
                        <div class="relative">
                            <select name="ukm_id" required class="w-full px-4 py-3 bg-surface-container rounded-xl border-none focus:ring-2 focus:ring-primary/20 text-sm font-medium appearance-none cursor-pointer">
                                <option disabled selected value="">-- Pilih UKM --</option>
                                <?php $defUkm = $a['ukm_id'] ?? ''; ?>
                                <?php foreach ($ukmList as $u): ?>
                                    <option value="<?= $u['id'] ?>" <?= ($defUkm == $u['id']) ? 'selected' : '' ?>><?= htmlspecialchars($u['nama']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant">expand_more</span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div>
                        <label class="block text-sm font-bold tracking-wide text-on-surface mb-2">Nama Lengkap</label>
                        <input name="nama" type="text" class="w-full px-4 py-3 bg-surface-container rounded-xl border-none focus:ring-2 focus:ring-primary/20 text-sm font-bold text-slate-800" value="<?= htmlspecialchars($a['nama'] ?? '') ?>" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold tracking-wide text-on-surface mb-2">NIM / Nomor Induk</label>
                        <input name="nim" type="text" class="w-full px-4 py-3 bg-surface-container rounded-xl border-none focus:ring-2 focus:ring-primary/20 text-sm font-bold text-slate-800" value="<?= htmlspecialchars($a['nim'] ?? '') ?>">
                    </div>

                    <div>
                        <label class="block text-sm font-bold tracking-wide text-on-surface mb-2">Email Universitas</label>
                        <input name="email" type="email" class="w-full px-4 py-3 bg-surface-container rounded-xl border-none focus:ring-2 focus:ring-primary/20 text-sm font-bold text-slate-800" value="<?= htmlspecialchars($a['email'] ?? '') ?>">
                    </div>
                </div>

                <!-- Hak Akses & Kredensial -->
                <div class="space-y-6">
                    <h3 class="text-lg font-bold border-b border-outline-variant/20 pb-2">Kredensial & Kepengurusan</h3>
                    
                    <div>
                        <label class="block text-sm font-bold tracking-wide text-on-surface mb-2">Status Sidik Jari</label>
                        <div class="p-4 bg-surface-container rounded-xl border border-outline-variant/50 relative overflow-hidden">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <?php if (!empty($a['fingerprint_id'])): ?>
                                        <div class="w-10 h-10 rounded-full bg-success/20 flex items-center justify-center text-success">
                                            <span class="material-symbols-outlined">fingerprint</span>
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-on-surface">✅ Terdaftar</p>
                                            <p class="text-[10px] text-on-surface-variant">ID Template: <?= htmlspecialchars($a['fingerprint_id']) ?></p>
                                        </div>
                                    <?php else: ?>
                                        <div class="w-10 h-10 rounded-full bg-error/20 flex items-center justify-center text-error">
                                            <span class="material-symbols-outlined">fingerprint</span>
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-on-surface">❌ Belum terdaftar</p>
                                            <p class="text-[10px] text-on-surface-variant">Data sidik jari belum tersedia</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Fingerprint Action Button -->
                            <button type="button" id="btn-scan-fingerprint" class="w-full py-2 bg-primary text-white text-sm font-bold rounded-lg shadow-md hover:bg-primary/90 flex items-center justify-center gap-2 transition-all">
                                <span class="material-symbols-outlined text-sm">settings_remote</span>
                                <?= !empty($a['fingerprint_id']) ? 'Ganti Sidik Jari' : 'Tambah Sidik Jari' ?>
                            </button>

                            <!-- Loading State -->
                            <div id="scan-loading" class="hidden absolute inset-0 bg-surface/90 backdrop-blur-sm flex flex-col items-center justify-center pt-2">
                                <div class="animate-spin rounded-full h-6 w-6 border-2 border-primary border-t-transparent mb-2"></div>
                                <p class="text-xs font-bold text-primary text-center">Menunggu Scan di Perangkat...</p>
                                <p class="text-[9px] text-on-surface-variant text-center px-4 mt-1">Tempelkan jari ke sensor AS608.</p>
                                <button type="button" id="btn-cancel-scan" class="mt-2 text-[10px] font-bold text-error bg-error/10 px-3 py-1 rounded-full">Batal</button>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-bold tracking-wide text-on-surface mb-2">Jabatan <span class="text-error">*</span></label>
                            <?php $curHierarki = $a['hierarki'] ?? 'Anggota'; ?>
                            <div class="relative">
                                <select name="hierarki" id="hierarki-select-edit" required
                                        class="w-full px-4 py-3 bg-surface-container rounded-xl border-none focus:ring-2 focus:ring-primary/20 text-sm font-medium cursor-pointer appearance-none">
                                    <?php foreach (JabatanKustom::JABATAN_STANDAR as $std): ?>
                                    <option value="<?= htmlspecialchars($std['hierarki']) ?>"
                                            data-label="<?= htmlspecialchars($std['label']) ?>"
                                            <?= $curHierarki === $std['hierarki'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($std['label']) ?> (Standar)
                                    </option>
                                    <?php endforeach; ?>

                                    <?php if (!empty($jabatanKustom)): ?>
                                    <option disabled>── Jabatan Kustom UKM ──</option>
                                    <?php foreach ($jabatanKustom as $jk): ?>
                                    <option value="<?= htmlspecialchars($jk['nama_jabatan']) ?>"
                                            data-label="<?= htmlspecialchars($jk['nama_jabatan']) ?>"
                                            <?= $curHierarki === $jk['nama_jabatan'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($jk['nama_jabatan']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                    <?php endif; ?>

                                    <?php
                                    // Jika hierarki yang tersimpan tidak ada di standar maupun kustom
                                    // (jabatan lama yang sudah dihapus), tampilkan sebagai opsi orphan
                                    $isOrphan = !in_array($curHierarki, array_column(JabatanKustom::JABATAN_STANDAR, 'hierarki'))
                                              && (empty($jabatanKustom) || !in_array($curHierarki, array_column($jabatanKustom, 'nama_jabatan')));
                                    if ($isOrphan && $curHierarki): ?>
                                    <option disabled>───</option>
                                    <option value="<?= htmlspecialchars($curHierarki) ?>" selected>
                                        <?= htmlspecialchars($curHierarki) ?> (Saat ini)
                                    </option>
                                    <?php endif; ?>
                                </select>
                                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant">expand_more</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold tracking-wide text-on-surface mb-2">Nama Jabatan Spesifik <span class="text-xs text-on-surface-variant font-normal">(label yang tampil di profil)</span></label>
                            <input type="text" name="jabatan" id="jabatan-input-edit"
                                   placeholder="Misal: Koordinator Hardware, Kabid SDM..."
                                   value="<?= htmlspecialchars($a['jabatan'] ?? 'Anggota') ?>"
                                   class="w-full px-4 py-3 bg-surface-container rounded-xl border border-outline-variant/30 focus:ring-2 focus:ring-primary/20 focus:outline-none text-sm font-bold text-slate-800"
                                   required>
                            <p class="text-[10px] text-on-surface-variant mt-1.5">Pilih jabatan dari dropdown untuk mengisi otomatis, atau ketik nama spesifik secara manual.</p>
                        </div>
                        <div class="flex items-start gap-2 p-3 bg-primary/5 rounded-xl border border-primary/10">
                            <span class="material-symbols-outlined text-primary text-sm mt-0.5">info</span>
                            <p class="text-[10px] text-on-surface-variant leading-relaxed">Kelola jabatan kustom: <a href="index.php?page=jabatan" class="text-primary font-bold hover:underline">Halaman Jabatan →</a></p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold tracking-wide text-on-surface mb-2">Status Anggota</label>
                        <?php $status = $a['status'] ?? 'aktif'; ?>
                        <div class="flex gap-4">
                            <label class="flex-1 flex items-center gap-3 p-3 bg-surface-container rounded-xl border border-transparent has-[:checked]:border-primary has-[:checked]:bg-primary-fixed/20 cursor-pointer transition-all">
                                <input type="radio" name="status" value="aktif" <?= $status === 'aktif' ? 'checked' : '' ?> class="text-primary focus:ring-primary">
                                <span class="text-sm font-bold text-on-surface">Aktif</span>
                            </label>
                            <label class="flex-1 flex items-center gap-3 p-3 bg-surface-container rounded-xl border border-transparent has-[:checked]:border-error has-[:checked]:bg-error-container/30 cursor-pointer transition-all">
                                <input type="radio" name="status" value="nonaktif" <?= $status === 'nonaktif' ? 'checked' : '' ?> class="text-error focus:ring-error">
                                <span class="text-sm font-bold text-on-surface">Non-Aktif</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-4 pt-6 border-t border-outline-variant/20">
                <a href="index.php?page=anggota" class="px-8 py-3 bg-surface-container-high text-on-surface font-bold rounded-xl hover:bg-surface-container-highest transition-colors">Batal</a>
                <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 transition-transform active:scale-95">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</main>

<script>
// Auto-fill jabatan input when hierarki dropdown changes
(function() {
    const sel = document.getElementById('hierarki-select-edit');
    const inp = document.getElementById('jabatan-input-edit');
    if (!sel || !inp) return;
    sel.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        const label = opt.getAttribute('data-label');
        if (label) inp.value = label;
    });
})();
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnScan = document.getElementById('btn-scan-fingerprint');
    const loadingUI = document.getElementById('scan-loading');
    const btnCancel = document.getElementById('btn-cancel-scan');
    const statusText = loadingUI ? loadingUI.querySelector('p.text-xs') : null;
    let pollInterval = null;
    let pollTimeout = null;
    let countdownInterval = null;
    let scanStartTime = 0;
    const anggotaId = <?= (int)($a['id'] ?? 0) ?>;
    const SCAN_TIMEOUT_MS = 120000; // 2 menit (sesuai server-side expire)

    function cancelEnrollOnServer() {
        const formData = new FormData();
        formData.append('anggota_id', anggotaId);
        formData.append('csrf_token', '<?= csrf_token() ?>');
        fetch('index.php?action=fingerprint_cancel_enroll', { method: 'POST', body: formData })
            .catch(err => console.error('Cancel enroll error:', err));
    }

    function cleanupScanUI() {
        loadingUI.classList.add('hidden');
        if (pollInterval) { clearInterval(pollInterval); pollInterval = null; }
        if (pollTimeout) { clearTimeout(pollTimeout); pollTimeout = null; }
        if (countdownInterval) { clearInterval(countdownInterval); countdownInterval = null; }
    }

    function startCountdown() {
        let remaining = Math.floor(SCAN_TIMEOUT_MS / 1000);
        countdownInterval = setInterval(() => {
            remaining--;
            if (remaining > 0 && statusText) {
                statusText.textContent = `Menunggu Scan di Perangkat... (${remaining}s)`;
            }
            if (remaining <= 0) {
                clearInterval(countdownInterval);
            }
        }, 1000);
    }

    if (btnScan) {
        btnScan.addEventListener('click', function() {
            loadingUI.classList.remove('hidden');
            scanStartTime = Math.floor(Date.now() / 1000); // integer seconds
            if (statusText) statusText.textContent = 'Mengirim perintah ke perangkat...';
            
            // Set server mode to ENROLL for this anggotaId
            const formData = new FormData();
            formData.append('anggota_id', anggotaId);
            formData.append('csrf_token', '<?= csrf_token() ?>');
            
            fetch('index.php?action=fingerprint_set_enroll', {
                method: 'POST',
                body: formData
            }).then(response => response.json())
              .then(data => {
                  if (data.status === 'success') {
                      if (statusText) statusText.textContent = 'Menunggu Scan di Perangkat...';
                      // Poll the server every 2 seconds to check if fingerprint was updated
                      pollInterval = setInterval(checkFingerprintStatus, 2000);
                      // Auto-cancel setelah timeout
                      pollTimeout = setTimeout(() => {
                          cleanupScanUI();
                          cancelEnrollOnServer();
                          alert('Waktu habis (2 menit). Pendaftaran sidik jari dibatalkan.\nSilakan coba lagi.');
                      }, SCAN_TIMEOUT_MS);
                      startCountdown();
                  } else {
                      alert('Gagal menginisiasi mode scan. Periksa koneksi.');
                      loadingUI.classList.add('hidden');
                  }
              }).catch(err => {
                  console.error(err);
                  alert('Kesalahan sistem saat menginisiasi.');
                  loadingUI.classList.add('hidden');
              });
        });
    }

    if (btnCancel) {
        btnCancel.addEventListener('click', function() {
            cleanupScanUI();
            cancelEnrollOnServer();
        });
    }

    function checkFingerprintStatus() {
        fetch(`index.php?action=fingerprint_check_status&id=${anggotaId}&since=${scanStartTime}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' && data.updated) {
                    cleanupScanUI();
                    alert('Berhasil! Sidik Jari telah didaftarkan.');
                    window.location.reload();
                }
            })
            .catch(err => console.error('Error polling:', err));
    }
});
</script>
