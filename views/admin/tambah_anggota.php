<main class="flex-1 p-8 min-h-[calc(100vh-64px-112px)] bg-surface-container-low">
    <!-- Header -->
    <div class="mb-8 flex items-center gap-4">
        <a href="index.php?page=anggota" class="w-10 h-10 rounded-full flex items-center justify-center bg-white border border-outline-variant hover:bg-surface transition-colors">
            <span class="material-symbols-outlined text-outline">arrow_back</span>
        </a>
        <div>
            <h2 class="text-3xl font-bold tracking-tight text-on-surface">Tambah Anggota Baru</h2>
            <p class="text-on-surface-variant body-md">Masukkan data anggota, kredensial IoT, dan pengaturan jabatan kepengurusan.</p>
        </div>
    </div>

    <!-- Form Section -->
    <div class="max-w-4xl bg-surface-container-lowest rounded-2xl shadow-[0_12px_40px_rgba(25,28,30,0.04)] p-8">
        <form action="index.php?action=anggota_store" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Data Pribadi -->
                <div class="space-y-6">
                    <h3 class="text-lg font-bold border-b border-outline-variant/20 pb-2">Informasi Pribadi</h3>
                    
                    <div class="flex items-center gap-6 mb-2">
                        <div class="w-24 h-24 rounded-full bg-surface-container flex items-center justify-center border-2 border-dashed border-outline/50 overflow-hidden relative group">
                            <span class="material-symbols-outlined text-outline text-3xl">person</span>
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                                <span class="material-symbols-outlined text-white">upload</span>
                            </div>
                        </div>
                        <div>
                            <p class="font-bold text-sm text-on-surface mb-1">Foto Profil</p>
                            <p class="text-xs text-on-surface-variant mb-3">Format JPG/PNG, maks. 2MB</p>
                            <button type="button" class="px-4 py-2 bg-surface text-primary text-xs font-bold rounded-lg border border-primary/20 hover:bg-primary-fixed transition-colors">Pilih File</button>
                        </div>
                    </div>

                    <?php if (!empty($ukmList)): ?>
                    <div class="mb-4">
                        <label class="block text-sm font-bold tracking-wide text-on-surface mb-2">UKM Tujuan</label>
                        <div class="relative">
                            <select name="ukm_id" required class="w-full px-4 py-3 bg-surface-container rounded-xl border-none focus:ring-2 focus:ring-primary/20 text-sm font-medium appearance-none cursor-pointer">
                                <option disabled selected value="">-- Pilih UKM --</option>
                                <?php $defUkm = $_GET['ukm_id'] ?? ''; ?>
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
                        <input name="nama" type="text" class="w-full px-4 py-3 bg-surface-container rounded-xl border-none focus:ring-2 focus:ring-primary/20 text-sm" placeholder="Contoh: Budi Santoso" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold tracking-wide text-on-surface mb-2">NIM / Nomor Induk</label>
                        <input name="nim" type="text" class="w-full px-4 py-3 bg-surface-container rounded-xl border-none focus:ring-2 focus:ring-primary/20 text-sm" placeholder="Contoh: 123456789">
                    </div>

                    <div>
                        <label class="block text-sm font-bold tracking-wide text-on-surface mb-2">Email Universitas</label>
                        <input name="email" type="email" class="w-full px-4 py-3 bg-surface-container rounded-xl border-none focus:ring-2 focus:ring-primary/20 text-sm" placeholder="nama@student.kampus.ac.id">
                    </div>
                </div>

                <!-- Hak Akses & Kredensial -->
                <div class="space-y-6">
                    <h3 class="text-lg font-bold border-b border-outline-variant/20 pb-2">Kredensial & Kepengurusan</h3>
                    
                    <div>
                        <div class="p-4 bg-tertiary-container/30 border border-tertiary/20 rounded-xl flex items-start gap-3">
                            <span class="material-symbols-outlined text-tertiary mt-0.5">fingerprint</span>
                            <div>
                                <h4 class="text-sm font-bold text-on-surface mb-1">Registrasi Sidik Jari</h4>
                                <p class="text-xs text-on-surface-variant">Pendaftaran sidik jari anggota hanya dapat dilakukan setelah Anda menyimpan data dasar anggota ini. Fitur scan akan tersedia di halaman Edit Anggota.</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-bold tracking-wide text-on-surface mb-2">Jabatan <span class="text-error">*</span></label>
                            <div class="relative">
                                <select name="hierarki" id="hierarki-select-tambah" required
                                        class="w-full px-4 py-3 bg-surface-container rounded-xl border-none focus:ring-2 focus:ring-primary/20 text-sm font-medium cursor-pointer appearance-none">
                                    <option value="" disabled selected>— Pilih Jabatan —</option>
                                    <?php
                                    // Jabatan standar
                                    foreach (JabatanKustom::JABATAN_STANDAR as $std):
                                    ?>
                                    <option value="<?= htmlspecialchars($std['hierarki']) ?>"
                                            data-label="<?= htmlspecialchars($std['label']) ?>">
                                        <?= htmlspecialchars($std['label']) ?> (Standar)
                                    </option>
                                    <?php endforeach; ?>

                                    <?php if (!empty($jabatanKustom)): ?>
                                    <option disabled>── Jabatan Kustom UKM ──</option>
                                    <?php foreach ($jabatanKustom as $jk): ?>
                                    <option value="<?= htmlspecialchars($jk['nama_jabatan']) ?>"
                                            data-label="<?= htmlspecialchars($jk['nama_jabatan']) ?>">
                                        <?= htmlspecialchars($jk['nama_jabatan']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant">expand_more</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold tracking-wide text-on-surface mb-2">Nama Jabatan Spesifik <span class="text-xs text-on-surface-variant font-normal">(label yang tampil di profil)</span></label>
                            <input type="text" name="jabatan" id="jabatan-input-tambah"
                                   placeholder="Misal: Koordinator Hardware, Kabid SDM..."
                                   value="Anggota"
                                   class="w-full px-4 py-3 bg-surface-container rounded-xl border border-outline-variant/30 focus:ring-2 focus:ring-primary/20 focus:outline-none text-sm font-bold text-slate-800"
                                   required>
                            <p class="text-[10px] text-on-surface-variant mt-1.5">Pilih jabatan dari dropdown untuk mengisi otomatis, atau ketik nama spesifik secara manual.</p>
                        </div>
                        <div class="flex items-start gap-2 p-3 bg-primary/5 rounded-xl border border-primary/10">
                            <span class="material-symbols-outlined text-primary text-sm mt-0.5">info</span>
                            <p class="text-[10px] text-on-surface-variant leading-relaxed">Belum ada jabatan? <a href="index.php?page=jabatan" class="text-primary font-bold hover:underline">Kelola jabatan kustom UKM →</a></p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold tracking-wide text-on-surface mb-2">Status Anggota</label>
                        <div class="flex gap-4">
                            <label class="flex-1 flex items-center gap-3 p-3 bg-surface-container rounded-xl border border-transparent has-[:checked]:border-primary has-[:checked]:bg-primary-fixed/20 cursor-pointer transition-all">
                                <input type="radio" name="status" value="aktif" checked class="text-primary focus:ring-primary">
                                <span class="text-sm font-bold text-on-surface">Aktif</span>
                            </label>
                            <label class="flex-1 flex items-center gap-3 p-3 bg-surface-container rounded-xl border border-transparent has-[:checked]:border-error has-[:checked]:bg-error-container/30 cursor-pointer transition-all">
                                <input type="radio" name="status" value="nonaktif" class="text-error focus:ring-error">
                                <span class="text-sm font-bold text-on-surface">Non-Aktif</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-4 pt-6 border-t border-outline-variant/20">
                <a href="index.php?page=anggota" class="px-8 py-3 bg-surface-container-high text-on-surface font-bold rounded-xl hover:bg-surface-container-highest transition-colors">Batal</a>
                <button type="submit" class="px-8 py-3 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/20 hover:bg-primary-container transition-transform active:scale-95">Simpan Data Anggota</button>
            </div>
        </form>
    </div>
</main>

<script>
(function() {
    const sel = document.getElementById('hierarki-select-tambah');
    const inp = document.getElementById('jabatan-input-tambah');
    if (!sel || !inp) return;
    sel.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        const label = opt.getAttribute('data-label');
        if (label) inp.value = label;
    });
})();
</script>
