<?php
/**
 * Admin View: Konfigurasi Umum
 * Panel superadmin untuk kustomisasi label entitas & hero section.
 */
$s = $settings ?? [];
$currentEntity  = htmlspecialchars($s['entitas_nama'] ?? 'UKM');
$appName        = htmlspecialchars($s['app_name'] ?? 'The Digital Curator');
$appSubtitle    = htmlspecialchars($s['app_subtitle'] ?? 'IoT Admin Panel');
$heroJudul      = htmlspecialchars($s['hero_judul'] ?? '');
$heroDeskripsi  = htmlspecialchars($s['hero_deskripsi'] ?? '');
$heroBtn1Label  = htmlspecialchars($s['hero_btn1_label'] ?? '');
$heroBtn1Link   = htmlspecialchars($s['hero_btn1_link'] ?? '');
$heroBtn2Label  = htmlspecialchars($s['hero_btn2_label'] ?? '');
$heroBtn2Link   = htmlspecialchars($s['hero_btn2_link'] ?? '');
$heroGambar     = $s['hero_gambar'] ?? '';
$heroOverlay    = (int)($s['hero_overlay_opacity'] ?? 20);
?>

<div class="p-8 max-w-5xl mx-auto w-full space-y-8">
    <!-- Header -->
    <div class="flex justify-between items-end">
        <div>
            <div class="flex items-center gap-2 text-primary font-bold text-xs uppercase tracking-widest mb-1">
                <span class="material-symbols-outlined text-sm">tune</span> Konfigurasi Global
            </div>
            <h2 class="text-3xl font-extrabold text-on-surface tracking-tight">Konfigurasi Umum</h2>
            <p class="text-on-surface-variant font-medium mt-1">Atur label entitas, tampilan hero section, dan branding sistem secara global.</p>
        </div>
    </div>

    <?= renderFlash() ?>

    <form action="index.php?action=konfigurasi_umum_save" method="POST" enctype="multipart/form-data" class="space-y-8">
    <?= csrf_field() ?>
        


        <!-- ============================================ -->
        <!-- SECTION A: Label Entitas -->
        <!-- ============================================ -->
        <div class="bg-surface-container-lowest border border-outline-variant/10 rounded-3xl p-8 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-primary/10 text-primary rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">label</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-on-surface">Label Entitas / Organisasi</h3>
                    <p class="text-xs text-on-surface-variant font-medium">Mengubah semua teks "UKM" di seluruh halaman menjadi nama yang Anda tentukan.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Nama Aplikasi -->
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-widest text-slate-500" for="app_name">Nama Aplikasi Utama</label>
                    <input type="text" name="app_name" id="app_name" value="<?= $appName ?>"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-5 py-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-sm font-bold text-slate-800"
                           placeholder="Contoh: The Digital Curator, Absensi IoT"
                           required
                           oninput="updatePreview()">
                    <p class="text-[11px] text-slate-400 font-medium">Nama sistem secara keseluruhan (muncul di title bar & footer).</p>

                </div>

                <div class="space-y-2 pb-2">
                    <label class="text-xs font-bold uppercase tracking-widest text-slate-500" for="app_subtitle">Sub-judul / Tagline Admin</label>
                    <input type="text" name="app_subtitle" id="app_subtitle" value="<?= $appSubtitle ?>"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-5 py-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-sm font-bold text-slate-800"
                           placeholder="Contoh: IoT Admin Panel"
                           required
                           oninput="updatePreview()">
                    <p class="text-[11px] text-slate-400 font-medium">Tampil di bawah nama aplikasi pada sidebar admin.</p>

                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-widest text-slate-500" for="entitas_nama">Nama Entitas</label>
                    <input type="text" name="entitas_nama" id="entitas_nama" value="<?= $currentEntity ?>"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-5 py-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-sm font-bold text-slate-800"
                           placeholder="Contoh: UKM, BEM, Kelas, Komunitas"
                           required
                           oninput="updatePreview()">
                    <p class="text-[11px] text-slate-400 font-medium">Contoh: UKM, BEM, Kelas, Komunitas, Himpunan</p>
                </div>

                <!-- Live Preview -->
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-widest text-slate-500">Preview Branding & Navigasi</label>
                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-inner overflow-hidden relative">
                        <!-- Mock Sidebar -->
                        <div class="flex flex-col gap-4">
                            <div class="flex items-center gap-3 border-b border-white/5 pb-4">
                                <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center text-white shrink-0">
                                    <span class="material-symbols-outlined">fingerprint</span>
                                </div>
                                <div class="overflow-hidden">
                                    <div class="text-white font-bold text-sm truncate" id="preview-app-name"><?= $appName ?></div>
                                    <div class="text-white/40 text-[10px] uppercase tracking-tight truncate" id="preview-app-subtitle"><?= $appSubtitle ?></div>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <div class="flex items-center gap-3 text-white/70 p-2 rounded-lg bg-white/5">
                                    <span class="material-symbols-outlined text-sm">dashboard</span>
                                    <span class="text-xs font-medium" id="preview-sidebar">Daftar <?= $currentEntity ?></span>
                                </div>
                                <div class="flex items-center gap-3 text-white/50 p-2">
                                    <span class="material-symbols-outlined text-sm">groups</span>
                                    <span class="text-xs font-medium" id="preview-pencarian">Anggota <?= $currentEntity ?></span>
                                </div>
                                <div class="flex items-center gap-3 text-white/50 p-2">
                                    <span class="material-symbols-outlined text-sm">settings</span>
                                    <span class="text-xs font-medium" id="preview-profil">Profil <?= $currentEntity ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Floating Browser Bar Mock -->
                        <div class="absolute -top-1 right-4 bg-slate-800 px-3 py-1 rounded-b-lg border-x border-b border-slate-700 flex items-center gap-2">
                            <div class="flex gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-400/50"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400/50"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400/50"></span>
                            </div>
                            <span class="text-[9px] text-white/40 font-medium truncate max-w-[80px]" id="preview-tab-name"><?= $appName ?></span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ============================================ -->
        <!-- SECTION B: Hero Section -->
        <!-- ============================================ -->
        <div class="bg-surface-container-lowest border border-outline-variant/10 rounded-3xl p-8 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-amber-100 text-amber-700 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">web</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-on-surface">Hero Section (Halaman Depan)</h3>
                    <p class="text-xs text-on-surface-variant font-medium">Kustomisasi tampilan utama yang dilihat pengunjung pertama kali.</p>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Judul -->
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-widest text-slate-500" for="hero_judul">Judul Utama</label>
                    <input type="text" name="hero_judul" id="hero_judul" value="<?= $heroJudul ?>"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-5 py-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-sm font-bold text-slate-800"
                           placeholder="SISTEM ABSENSI & MANAJEMEN ORGANISASI">
                    <p class="text-[11px] text-slate-400 font-medium">Default: SISTEM ABSENSI & MANAJEMEN ORGANISASI</p>
                </div>

                <!-- Deskripsi -->
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-widest text-slate-500" for="hero_deskripsi">Deskripsi Singkat</label>
                    <textarea name="hero_deskripsi" id="hero_deskripsi" rows="3"
                              class="w-full bg-slate-50 border border-slate-200 rounded-xl px-5 py-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-sm font-bold text-slate-800"
                              placeholder="Optimalkan efisiensi kehadiran..."><?= $heroDeskripsi ?></textarea>
                </div>

                <!-- Buttons Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tombol Kiri -->
                    <div class="space-y-4 p-5 bg-slate-50 rounded-2xl border border-slate-200">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-3 h-3 bg-primary rounded-full"></span>
                            <span class="text-xs font-bold uppercase tracking-widest text-slate-600">Tombol Utama (Kiri)</span>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Label</label>
                            <input type="text" name="hero_btn1_label" value="<?= $heroBtn1Label ?>"
                                   class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                   placeholder="Jelajahi UKM">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Link URL</label>
                            <input type="text" name="hero_btn1_link" value="<?= $heroBtn1Link ?>"
                                   class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-600 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                   placeholder="index.php?page=katalog_ukm">
                        </div>
                    </div>

                    <!-- Tombol Kanan -->
                    <div class="space-y-4 p-5 bg-slate-50 rounded-2xl border border-slate-200">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-3 h-3 bg-slate-400 rounded-full"></span>
                            <span class="text-xs font-bold uppercase tracking-widest text-slate-600">Tombol Sekunder (Kanan)</span>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Label</label>
                            <input type="text" name="hero_btn2_label" value="<?= $heroBtn2Label ?>"
                                   class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                   placeholder="Dokumentasi API">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Link URL</label>
                            <input type="text" name="hero_btn2_link" value="<?= $heroBtn2Link ?>"
                                   class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-600 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                   placeholder="index.php?page=tentang">
                        </div>
                    </div>
                </div>

                <!-- Gambar Hero -->
                <div class="space-y-4 p-5 bg-slate-50 rounded-2xl border border-slate-200">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-slate-500">image</span>
                        <span class="text-xs font-bold uppercase tracking-widest text-slate-600">Gambar Latar Hero (Parallax)</span>
                    </div>

                    <?php if (!empty($heroGambar)): ?>
                    <div class="relative group aspect-video overflow-hidden rounded-xl border border-slate-200">
                        <img src="<?= htmlspecialchars($heroGambar) ?>" alt="Hero Background" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <span class="text-white font-bold text-xs bg-black/20 px-3 py-1.5 rounded-full backdrop-blur-sm">Gambar Aktif</span>
                        </div>
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-red-500 font-bold hover:text-red-700 transition-colors">
                        <input type="checkbox" name="hapus_hero_gambar" value="1" class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                        Hapus gambar ini (kembali ke default)
                    </label>
                    <?php endif; ?>

                    <div>
                        <input type="file" name="hero_gambar" id="hero_gambar" accept="image/jpeg,image/png,image/webp,image/gif"
                               onchange="previewHeroImage(this)"
                               class="w-full text-sm text-slate-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition-all cursor-pointer">
                        <p class="text-[11px] text-slate-400 font-medium mt-2">
                            <span class="text-primary font-bold">Rekomendasi:</span> Gunakan gambar <span class="font-bold">Landscape (16:9)</span> dengan resolusi <span class="font-bold">1920×1080px</span> untuk hasil terbaik. 
                            Format: JPG, PNG, WEBP. Maks: 2MB.
                        </p>
                        
                        <!-- New Image Preview Container -->
                        <div id="hero-preview-container" class="hidden mt-4 space-y-2">
                            <div class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest flex items-center gap-1">
                                <span class="material-symbols-outlined text-xs">check_circle</span> Preview Gambar Baru
                            </div>
                            <img id="hero-preview-img" src="#" alt="Hero Preview" class="w-full h-40 object-cover rounded-xl border-2 border-emerald-500/30">
                        </div>
                    </div>

                </div>

                <!-- Overlay Opacity -->
                <div class="space-y-3">
                    <label class="text-xs font-bold uppercase tracking-widest text-slate-500" for="hero_overlay_opacity">Overlay Opacity (%)</label>
                    <div class="flex items-center gap-4">
                        <input type="range" name="hero_overlay_opacity" id="hero_overlay_opacity" min="0" max="100" value="<?= $heroOverlay ?>"
                               class="flex-1 h-2 bg-slate-200 rounded-full appearance-none cursor-pointer accent-primary"
                               oninput="document.getElementById('opacity-value').textContent = this.value + '%'">
                        <span id="opacity-value" class="text-sm font-bold text-slate-700 min-w-[48px] text-right"><?= $heroOverlay ?>%</span>
                    </div>
                    <p class="text-[11px] text-slate-400 font-medium">0% = transparan penuh, 100% = overlay gelap penuh. Default: 20%</p>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- ACTION BUTTONS -->
        <!-- ============================================ -->
        <div class="flex items-center justify-between gap-4 bg-surface-container-lowest border border-outline-variant/10 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center gap-3 text-on-surface-variant">
                <span class="material-symbols-outlined text-lg">info</span>
                <p class="text-sm font-medium">Perubahan akan langsung diterapkan di seluruh halaman publik dan admin.</p>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="resetDefaults()" class="px-6 py-3 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition-all flex items-center gap-2 text-sm">
                    <span class="material-symbols-outlined text-base">restart_alt</span> Reset Default
                </button>
                <button type="submit" class="px-8 py-3 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all flex items-center gap-2 text-sm">
                    <span class="material-symbols-outlined text-base">save</span> Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function updatePreview() {
    const entitas = document.getElementById('entitas_nama').value || 'UKM';
    const appName = document.getElementById('app_name').value || 'The Digital Curator';
    const appSubtitle = document.getElementById('app_subtitle').value || 'IoT Admin Panel';

    // Sidebar & Navigation
    document.getElementById('preview-app-name').textContent = appName;
    document.getElementById('preview-app-subtitle').textContent = appSubtitle;
    document.getElementById('preview-tab-name').textContent = appName;
    
    document.getElementById('preview-sidebar').textContent = 'Daftar ' + entitas;
    document.getElementById('preview-pencarian').textContent = 'Anggota ' + entitas;
    document.getElementById('preview-profil').textContent = 'Profil ' + entitas;
}

function previewHeroImage(input) {
    const preview = document.getElementById('hero-preview-img');
    const container = document.getElementById('hero-preview-container');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            container.classList.remove('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        container.classList.add('hidden');
    }
}

function resetDefaults() {
    if (!confirm('Reset semua nilai ke default? Perubahan yang belum disimpan akan hilang.')) return;
    document.getElementById('app_name').value = 'The Digital Curator';
    document.getElementById('app_subtitle').value = 'IoT Admin Panel';
    document.getElementById('entitas_nama').value = 'UKM';
    document.getElementById('hero_judul').value = 'SISTEM ABSENSI & MANAJEMEN ORGANISASI';
    document.getElementById('hero_deskripsi').value = 'Optimalkan efisiensi kehadiran dengan teknologi berbasis Fingerprint + ESP32. Monitoring real-time untuk transparansi organisasi digital.';
    document.querySelector('[name="hero_btn1_label"]').value = 'Jelajahi UKM';
    document.querySelector('[name="hero_btn1_link"]').value = 'index.php?page=katalog_ukm';
    document.querySelector('[name="hero_btn2_label"]').value = 'Dokumentasi API';
    document.querySelector('[name="hero_btn2_link"]').value = 'index.php?page=tentang';
    document.getElementById('hero_overlay_opacity').value = 20;
    document.getElementById('opacity-value').textContent = '20%';
    
    // Hide image preview on reset
    document.getElementById('hero-preview-container').classList.add('hidden');
    document.getElementById('hero_gambar').value = '';
    
    updatePreview();
}
</script>
