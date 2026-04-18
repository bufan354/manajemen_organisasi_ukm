<?php
$u = $ukm ?? [];
$s = $settings ?? [];  // settings key-value map
$fokusOptions = ['Seni', 'Olahraga', 'Teknologi', 'Sosial', 'Akademik', 'Keagamaan', 'Lingkungan', 'Kewirausahaan', 'Umum'];
?>
<div class="p-8 max-w-7xl mx-auto w-full space-y-8 relative pb-32">
    <!-- Page Header -->
    <div class="flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-black text-on-surface tracking-tight">Profil UKM</h2>
            <p class="text-on-surface-variant mt-1">Kelola identitas visual dan informasi publik unit kegiatan mahasiswa Anda.</p>
        </div>
        <?php if (!empty($u['id'])): ?>
        <a href="index.php?page=detail_ukm&id=<?= $u['id'] ?>" target="_blank"
           class="flex items-center gap-2 px-4 py-2 rounded-xl bg-surface-container-low text-on-surface-variant hover:text-primary hover:bg-primary/10 transition-all text-sm font-bold">
            <span class="material-symbols-outlined text-sm">open_in_new</span>
            Lihat Halaman Publik
        </a>
        <?php endif; ?>
    </div>

    <?= renderFlash() ?>
    
    <!-- Preview Section: Banner & Logo -->
    <section class="relative rounded-3xl overflow-hidden ambient-shadow bg-surface-container-lowest">
        <!-- Header Preview -->
        <div id="header-preview-container" class="relative h-72 w-full group overflow-hidden bg-slate-100">
            <?php if (!empty($u['header_path'])): ?>
            <img id="header-preview-img" alt="Header Poster UKM" class="w-full h-full object-cover" src="<?= htmlspecialchars($u['header_path']) ?>"/>
            <?php else: ?>
            <div id="header-preview-placeholder" class="w-full h-full bg-gradient-to-br from-primary/20 to-secondary/10 flex flex-col items-center justify-center gap-2">
                <span class="material-symbols-outlined text-6xl text-primary/30">panorama</span>
                <p class="text-xs font-bold text-primary/40 uppercase tracking-widest">Belum ada header. Unggah gambar di bawah.</p>
            </div>
            <img id="header-preview-img" alt="Header Poster UKM" class="w-full h-full object-cover hidden"/>
            <?php endif; ?>
            <!-- Overlay gradient -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent pointer-events-none"></div>
        </div>

        <!-- Logo + Name -->
        <div class="relative px-8 pb-8 flex items-end justify-between -mt-16">
            <div class="flex items-end gap-6">
                <div class="relative group">
                    <!-- Logo circle preview -->
                    <div id="logo-preview-container" class="w-32 h-32 rounded-full border-[6px] border-white shadow-xl bg-surface-container-lowest overflow-hidden cursor-pointer" title="Klik untuk ganti logo">
                        <?php if (!empty($u['logo_path'])): ?>
                        <img id="logo-preview-img" alt="UKM Logo" class="w-full h-full object-cover" src="<?= htmlspecialchars($u['logo_path']) ?>"/>
                        <?php else: ?>
                        <div id="logo-preview-placeholder" class="w-full h-full bg-primary-container flex items-center justify-center text-white text-4xl font-bold">
                            <?= strtoupper(substr($u['singkatan'] ?? $u['nama'] ?? '?', 0, 2)) ?>
                        </div>
                        <img id="logo-preview-img" alt="UKM Logo" class="w-full h-full object-cover hidden"/>
                        <?php endif; ?>
                    </div>
                    <!-- Edit hint -->
                    <div class="absolute inset-0 rounded-full bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                        <span class="material-symbols-outlined text-white text-2xl">photo_camera</span>
                    </div>
                </div>
                <div class="mb-4">
                    <h3 class="text-2xl font-extrabold text-on-surface"><?= htmlspecialchars($u['nama'] ?? 'Nama UKM') ?></h3>
                    <p class="text-primary font-medium"><?= htmlspecialchars($u['singkatan'] ?? '') ?></p>
                </div>
            </div>
            <div class="flex gap-4 mb-4">
                <div class="px-4 py-2 rounded-xl bg-surface-container-low text-center">
                    <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Anggota Aktif</p>
                    <p class="text-xl font-black text-on-surface"><?= $totalAnggota ?? 0 ?></p>
                </div>
                <div class="px-4 py-2 rounded-xl bg-surface-container-low text-center min-w-[100px]">
                    <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Fokus</p>
                    <p class="text-lg font-black text-secondary break-words"><?= htmlspecialchars($u['kategori'] ?? '-') ?></p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Form Section -->
    <form action="index.php?action=ukm_update" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= $u['id'] ?? '' ?>">
        <div class="grid grid-cols-12 gap-8">
            <!-- Main Form Column -->
            <div class="col-span-12 lg:col-span-8 space-y-8">
                <!-- Informasi Dasar -->
                <div class="p-8 rounded-3xl bg-surface-container-lowest ambient-shadow">
                    <h4 class="text-lg font-bold text-on-surface mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">info</span>
                        Informasi Dasar
                    </h4>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Nama UKM <span class="text-red-500">*</span></label>
                            <input name="nama" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 text-on-surface font-medium" type="text" value="<?= htmlspecialchars($u['nama'] ?? '') ?>" required/>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Singkatan / Kode</label>
                            <input name="singkatan" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 text-on-surface font-medium" type="text" value="<?= htmlspecialchars($u['singkatan'] ?? '') ?>"/>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Slogan</label>
                            <input name="slogan" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 text-on-surface font-medium" type="text" placeholder="Motto atau tagline UKM" value="<?= htmlspecialchars($u['slogan'] ?? '') ?>"/>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Fokus / Bidang <span class="text-red-500">*</span></label>
                            <select name="kategori" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 text-on-surface font-medium" required>
                                <option value="">— Pilih Fokus —</option>
                                <?php foreach ($fokusOptions as $opt): ?>
                                <option value="<?= $opt ?>" <?= ($u['kategori'] ?? '') === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Deskripsi</label>
                            <textarea name="deskripsi" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 text-on-surface font-medium" rows="4"><?= htmlspecialchars($u['deskripsi'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Upload Media -->
                <div class="p-8 rounded-3xl bg-surface-container-lowest ambient-shadow">
                    <h4 class="text-lg font-bold text-on-surface mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary">photo_library</span>
                        Media & Identitas Visual
                    </h4>
                    <div class="space-y-6">
                        <!-- Logo -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Logo UKM</label>
                            <div class="flex items-center gap-6">
                                <div id="logo-upload-preview" class="w-20 h-20 rounded-full overflow-hidden bg-surface-container flex-shrink-0 border-2 border-dashed border-outline-variant flex items-center justify-center">
                                    <?php if (!empty($u['logo_path'])): ?>
                                    <img src="<?= htmlspecialchars($u['logo_path']) ?>" class="w-full h-full object-cover" alt="">
                                    <?php else: ?>
                                    <span class="material-symbols-outlined text-2xl text-outline">image</span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1">
                                    <input id="logo-input" name="logo" type="file" accept="image/*" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 text-on-surface font-medium text-sm file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-primary file:text-white hover:file:bg-primary/90"/>
                                    <p class="text-xs text-on-surface-variant mt-2 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm text-primary">info</span>
                                        Gambar akan otomatis dipotong menjadi <strong>lingkaran sempurna</strong>. Gunakan foto persegi untuk hasil terbaik.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Header -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Gambar Header / Banner</label>
                            <input id="header-input" name="header" type="file" accept="image/*" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 text-on-surface font-medium text-sm file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-secondary file:text-white hover:file:bg-secondary/90"/>
                            <p class="text-xs text-on-surface-variant mt-2 flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm text-amber-500">warning</span>
                                Resolusi minimal <strong>1920×500px</strong>. Preview akan tampil langsung di atas halaman ini.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Kontak & Sosial Media Publik -->
                <div class="p-8 rounded-3xl bg-surface-container-lowest ambient-shadow">
                    <h4 class="text-lg font-bold text-on-surface mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-600">contact_mail</span>
                        Kontak & Sosial Media Publik
                    </h4>
                    <p class="text-sm text-on-surface-variant mb-6">Informasi kontak yang akan ditampilkan pada halaman publik UKM. Kosongkan jika tidak ingin ditampilkan.</p>
                    
                    <div class="space-y-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="col-span-2 md:col-span-1">
                                <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Email Admin (Gmail)</label>
                                <div class="relative">
                                    <input name="email_admin" class="w-full bg-surface-container-low border-none rounded-xl pl-11 pr-4 py-3 focus:ring-2 focus:ring-primary/20 text-on-surface font-medium" type="email" placeholder="contoh: ukm.tech@gmail.com" value="<?= htmlspecialchars($s['email_admin'] ?? '') ?>"/>
                                    <span class="material-symbols-outlined absolute left-4 top-3.5 text-on-surface-variant text-[20px]">mail</span>
                                </div>
                            </div>
                            <div class="col-span-2 md:col-span-1">
                                <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">WhatsApp Admin</label>
                                <div class="relative">
                                    <input name="whatsapp" class="w-full bg-surface-container-low border-none rounded-xl pl-11 pr-4 py-3 focus:ring-2 focus:ring-primary/20 text-on-surface font-medium" type="text" placeholder="contoh: 6281234567890" value="<?= htmlspecialchars($s['whatsapp'] ?? '') ?>"/>
                                    <span class="material-symbols-outlined absolute left-4 top-3.5 text-on-surface-variant text-[20px]">call</span>
                                </div>
                                <p class="text-[10px] text-on-surface-variant mt-1.5 flex gap-1"><span class="material-symbols-outlined text-[12px]">info</span><span>Gunakan awalan kode negara. Misal: 628...</span></p>
                            </div>
                        </div>

                        <hr class="border-outline-variant/30 my-4">
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Instagram URL</label>
                                <div class="relative">
                                    <input name="instagram_url" class="w-full bg-surface-container-low border-none rounded-xl pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-primary/20 text-on-surface" type="url" placeholder="https://instagram.com/..." value="<?= htmlspecialchars($s['instagram_url'] ?? '') ?>"/>
                                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-on-surface-variant text-[18px]">link</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Facebook URL</label>
                                <div class="relative">
                                    <input name="facebook_url" class="w-full bg-surface-container-low border-none rounded-xl pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-primary/20 text-on-surface" type="url" placeholder="https://facebook.com/..." value="<?= htmlspecialchars($s['facebook_url'] ?? '') ?>"/>
                                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-on-surface-variant text-[18px]">link</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Twitter URL</label>
                                <div class="relative">
                                    <input name="twitter_url" class="w-full bg-surface-container-low border-none rounded-xl pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-primary/20 text-on-surface" type="url" placeholder="https://twitter.com/..." value="<?= htmlspecialchars($s['twitter_url'] ?? '') ?>"/>
                                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-on-surface-variant text-[18px]">link</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Secondary Info Column -->
            <div class="col-span-12 lg:col-span-4 space-y-8">
                <div class="p-8 rounded-3xl bg-surface-container-lowest ambient-shadow">
                    <h4 class="text-lg font-bold text-on-surface mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">location_on</span>
                        Lokasi & Info
                    </h4>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Lokasi Markas</label>
                            <div class="relative">
                                <input name="lokasi" class="w-full bg-surface-container-low border-none rounded-xl pl-11 pr-4 py-3 focus:ring-2 focus:ring-primary/20 text-on-surface font-medium" type="text" placeholder="Cth: Lab A, Gedung C" value="<?= htmlspecialchars($u['lokasi'] ?? '') ?>"/>
                                <span class="material-symbols-outlined absolute left-4 top-3 text-primary">map</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Tanggal Berdiri</label>
                            <div class="relative">
                                <input name="tanggal_berdiri" class="w-full bg-surface-container-low border-none rounded-xl pl-11 pr-4 py-3 focus:ring-2 focus:ring-primary/20 text-on-surface font-medium" type="date" value="<?= htmlspecialchars($u['tanggal_berdiri'] ?? '') ?>"/>
                                <span class="material-symbols-outlined absolute left-4 top-3 text-primary">event</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if (($u['status'] ?? 'aktif') === 'aktif'): ?>
                <div class="p-6 rounded-3xl bg-primary shadow-2xl shadow-primary/20 text-white flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest opacity-80">UKM Status</p>
                        <p class="text-xl font-black">Aktif</p>
                    </div>
                    <span class="material-symbols-outlined text-4xl opacity-50">verified_user</span>
                </div>
                <?php else: ?>
                <div class="p-6 rounded-3xl bg-error shadow-2xl shadow-error/20 text-white flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest opacity-80">UKM Status</p>
                        <p class="text-xl font-black">Nonaktif</p>
                    </div>
                    <span class="material-symbols-outlined text-4xl opacity-50">gpp_bad</span>
                </div>
                <div class="bg-error-container border border-error/50 p-4 rounded-xl text-error mt-4">
                    <div class="flex gap-2">
                        <span class="material-symbols-outlined text-xl">warning</span>
                        <div>
                            <p class="font-bold text-sm">UKM Dinonaktifkan</p>
                            <p class="text-xs mt-1">UKM Anda sedang dinonaktifkan oleh Super Admin. UKM tidak akan muncul di halaman publik hingga diaktifkan kembali.</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Quick links untuk superadmin -->
                <?php if (!empty($u['id'])): ?>
                <div class="p-6 rounded-3xl bg-surface-container-lowest ambient-shadow space-y-3">
                    <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Tautan Cepat</p>
                    <a href="index.php?page=anggota" class="flex items-center gap-3 text-sm text-on-surface-variant hover:text-primary transition-colors">
                        <span class="material-symbols-outlined text-sm">person</span> Kelola Anggota
                    </a>
                    <a href="index.php?page=event" class="flex items-center gap-3 text-sm text-on-surface-variant hover:text-primary transition-colors">
                        <span class="material-symbols-outlined text-sm">calendar_month</span> Kelola Event
                    </a>
                    <a href="index.php?page=berita" class="flex items-center gap-3 text-sm text-on-surface-variant hover:text-primary transition-colors">
                        <span class="material-symbols-outlined text-sm">newspaper</span> Kelola Berita
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Submit Bar -->
        <div class="flex justify-end gap-4 pt-8 mt-8">
            <a href="index.php?page=dashboard" class="px-8 py-3 bg-surface-container-high text-on-surface font-bold rounded-xl hover:bg-surface-container-highest transition-colors">Batal</a>
            <button type="submit" class="px-10 py-3 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/20 hover:scale-105 active:scale-95 transition-all">Simpan Perubahan</button>
        </div>
    </form>
</div>

<!-- JS: Live Preview Logo & Header -->
<script>
(function() {
    // ---- Logo Preview ----
    const logoInput  = document.getElementById('logo-input');
    const logoPreviewImg = document.getElementById('logo-preview-img');
    const logoPreviewPlaceholder = document.getElementById('logo-preview-placeholder');
    const logoUploadPreview = document.getElementById('logo-upload-preview');

    if (logoInput) {
        logoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (!file || !file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                // Update hero preview (large circle)
                if (logoPreviewImg) {
                    logoPreviewImg.src = e.target.result;
                    logoPreviewImg.classList.remove('hidden');
                }
                if (logoPreviewPlaceholder) logoPreviewPlaceholder.classList.add('hidden');

                // Update small upload preview
                if (logoUploadPreview) {
                    logoUploadPreview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover" alt="">`;
                }
            };
            reader.readAsDataURL(file);
        });
    }

    // ---- Header Preview ----
    const headerInput = document.getElementById('header-input');
    const headerPreviewImg = document.getElementById('header-preview-img');
    const headerPreviewPlaceholder = document.getElementById('header-preview-placeholder');

    if (headerInput) {
        headerInput.addEventListener('change', function() {
            const file = this.files[0];
            if (!file || !file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                if (headerPreviewImg) {
                    headerPreviewImg.src = e.target.result;
                    headerPreviewImg.classList.remove('hidden');
                }
                if (headerPreviewPlaceholder) headerPreviewPlaceholder.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        });
    }
})();
</script>
