<div class="p-8 max-w-6xl mx-auto w-full">
    <!-- Bento Layout Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Section (Main Column) -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Section: Profil Saya -->
            <section class="bg-white p-8 rounded-3xl shadow-sm border border-surface-container">
                <form action="index.php?action=update_profile" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row gap-8 items-start">
                    <?= csrf_field() ?>
                    <!-- Circular Upload -->
                    <div class="relative group">
                        <div class="w-32 h-32 rounded-3xl ring-4 ring-primary-fixed overflow-hidden bg-surface-container">
                            <img class="w-full h-full object-cover" alt="Profile" src="<?= !empty($admin['foto_path']) ? htmlspecialchars($admin['foto_path']) : 'https://ui-avatars.com/api/?name='.urlencode($admin['nama']).'&background=0D8ABC&color=fff' ?>"/>
                        </div>
                        <label for="foto-upload" class="absolute bottom-0 right-0 p-2 bg-primary text-on-primary rounded-xl shadow-lg hover:scale-105 transition-transform cursor-pointer">
                            <span class="material-symbols-outlined text-sm" data-icon="photo_camera">photo_camera</span>
                        </label>
                        <input id="foto-upload" type="file" name="foto" accept="image/*" class="hidden" onchange="previewImage(this)">
                    </div>

                    <!-- Profile Inputs -->
                    <div class="flex-1 space-y-6 w-full">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Nama Lengkap</label>
                            <input class="w-full bg-surface-container-highest border-none rounded-lg px-4 py-3 text-on-surface focus:ring-0 focus:border-primary border-b-2 border-transparent focus:border-b-primary transition-all" type="text" name="nama" value="<?= htmlspecialchars($admin['nama']) ?>" required/>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Email Address (Username Login)</label>
                            <div class="relative">
                                <input class="w-full bg-surface-container-highest border-none rounded-lg px-4 py-3 text-on-surface focus:ring-0 focus:border-primary border-b-2 border-transparent focus:border-b-primary transition-all" type="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required/>
                            </div>
                        </div>
                        <div class="pt-4">
                            <button type="submit" class="bg-primary text-on-primary px-8 py-3 rounded-xl font-bold hover:bg-primary-container transition-colors shadow-md shadow-primary/20 active:scale-95 duration-200">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </section>
            
            <script>
            function previewImage(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        input.parentElement.querySelector('img').src = e.target.result;
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
            </script>

            <!-- Section: Keamanan - Login Sessions -->
            <section class="bg-white p-8 rounded-3xl shadow-sm border border-surface-container">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-on-surface">Login Sessions</h3>
                        <p class="text-sm text-on-surface-variant">Kelola perangkat yang sedang masuk menggunakan akun Anda.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <?php 
                        $otherSessions = array_filter($sessions ?? [], fn($s) => $s['session_id'] !== $current_session_id);
                        if (!empty($otherSessions)): 
                        ?>
                        <button 
                            onclick="openRevokeAllModal()"
                            class="flex items-center gap-1.5 text-[11px] font-black uppercase tracking-widest text-red-600 bg-red-50 hover:bg-red-600 hover:text-white px-4 py-2 rounded-xl border border-red-200 hover:border-red-600 transition-all duration-200 shadow-sm hover:shadow-md active:scale-95">
                            <span class="material-symbols-outlined text-[15px]">wifi_off</span>
                            Putuskan Semua
                        </button>
                        <?php endif; ?>
                        <span class="material-symbols-outlined text-primary text-3xl">devices</span>
                    </div>
                </div>
                
                <?php if (!empty($sessions)): ?>
                <!-- Session count badge -->
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-xs font-black uppercase tracking-widest text-slate-500">
                        <?= count($sessions) ?> Sesi Aktif
                    </span>
                    <span class="flex-1 h-px bg-slate-100"></span>
                </div>
                <?php endif; ?>

                <div class="space-y-3">
                    <?php if (!empty($sessions)): ?>
                        <?php foreach($sessions as $session): 
                            $isCurrent = ($session['session_id'] === $current_session_id);
                            
                            $ua = $session['user_agent'] ?? 'Unknown Device';
                            $ip = $session['ip_address'] ?? 'Unknown';
                            if (strpos($ip, '.') !== false) {
                                $parts = explode('.', $ip);
                                if(count($parts) == 4) {
                                    $ip = $parts[0] . '.' . $parts[1] . '.*.*';
                                }
                            }
                            $isMobile = str_contains(strtolower($ua), 'mobile');
                            $deviceIcon = $isMobile ? 'smartphone' : 'laptop_mac';
                            $lastActive = date('d M Y, H:i', strtotime($session['last_activity']));
                        ?>
                        <div class="group relative flex flex-col md:flex-row md:items-center justify-between gap-4 p-4 rounded-2xl border transition-all duration-200
                            <?= $isCurrent 
                                ? 'bg-blue-50/60 border-blue-200 ring-2 ring-blue-500/10' 
                                : 'bg-slate-50 border-slate-200 hover:border-red-200 hover:bg-red-50/30' ?>">

                            <!-- Left: Device Info -->
                            <div class="flex items-center gap-4 min-w-0">
                                <div class="shrink-0 w-11 h-11 rounded-xl flex items-center justify-center shadow-sm
                                    <?= $isCurrent ? 'bg-blue-100 text-blue-600' : 'bg-white text-slate-500' ?>">
                                    <span class="material-symbols-outlined text-[22px]"><?= $deviceIcon ?></span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-slate-800 truncate max-w-xs" title="<?= htmlspecialchars($ua) ?>">
                                        <?= htmlspecialchars($ua) ?>
                                    </p>
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold tracking-wider text-slate-500 bg-slate-200/70 px-2 py-0.5 rounded-md border border-slate-200">
                                            <span class="material-symbols-outlined text-[10px]">location_on</span>
                                            IP: <?= htmlspecialchars($ip) ?>
                                        </span>
                                        <span class="inline-flex items-center gap-1 text-[10px] text-slate-400">
                                            <span class="material-symbols-outlined text-[10px]">schedule</span>
                                            <?= $lastActive ?>
                                        </span>
                                    </div>
                                    <?php if ($isCurrent): ?>
                                        <span class="inline-flex items-center gap-1.5 mt-1.5 text-[10px] text-blue-700 font-black uppercase tracking-widest">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                            · Sesi Ini
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Right: Revoke Button -->
                            <?php if (!$isCurrent): ?>
                            <form 
                                action="index.php?action=revoke_session" 
                                method="POST" 
                                class="md:ml-auto shrink-0"
                                onsubmit="return confirmRevoke(this, event)">
                                <?= csrf_field() ?>
                                <input type="hidden" name="session_id" value="<?= htmlspecialchars($session['session_id']) ?>">
                                <button type="submit" 
                                    class="flex items-center gap-2 bg-white text-red-600 hover:bg-red-600 hover:text-white border border-red-200 hover:border-red-600 font-bold text-xs uppercase tracking-wider px-5 py-2.5 rounded-xl shadow-sm hover:shadow-md active:scale-95 transition-all duration-200 group">
                                    <span class="material-symbols-outlined text-[16px] transition-transform group-hover:animate-bounce" style="font-variation-settings: 'FILL' 1;">link_off</span>
                                    Putuskan
                                </button>
                            </form>
                            <?php else: ?>
                            <div class="md:ml-auto shrink-0">
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-black uppercase tracking-widest text-blue-600 bg-blue-100 px-4 py-2.5 rounded-xl border border-blue-200">
                                    <span class="material-symbols-outlined text-[14px]" style="font-variation-settings: 'FILL' 1;">verified</span>
                                    Aktif Sekarang
                                </span>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-10 text-slate-400">
                            <span class="material-symbols-outlined text-5xl mb-3 block opacity-30">devices_off</span>
                            <p class="text-sm font-medium">Tidak ada sesi aktif.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

        </div>

        <!-- Sidebar column -->
        <div class="space-y-8">
            <!-- Change Password -->
            <section class="bg-white p-6 rounded-3xl shadow-sm border border-surface-container">
                <form action="index.php?action=update_password" method="POST">
                    <?= csrf_field() ?>
                    <h3 class="text-md font-bold mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary" data-icon="key">key</span>
                        Ubah Password
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Password Lama</label>
                            <input class="w-full bg-surface-container-highest border-none rounded-lg px-4 py-2 text-sm focus:ring-0 border-b-2 border-transparent focus:border-b-primary transition-all" name="password_lama" placeholder="••••••••" type="password" required/>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Password Baru</label>
                            <input class="w-full bg-surface-container-highest border-none rounded-lg px-4 py-2 text-sm focus:ring-0 border-b-2 border-transparent focus:border-b-primary transition-all" name="password_baru" placeholder="••••••••" type="password" required/>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Konfirmasi Password Baru</label>
                            <input class="w-full bg-surface-container-highest border-none rounded-lg px-4 py-2 text-sm focus:ring-0 border-b-2 border-transparent focus:border-b-primary transition-all" name="konfirmasi_password" placeholder="••••••••" type="password" required/>
                        </div>
                        <button type="submit" class="w-full bg-primary-container text-on-primary py-2 rounded-lg text-sm font-bold hover:bg-primary transition-colors mt-2">
                            Ubah Password
                        </button>
                    </div>
                </form>
            </section>

            <!-- Google Authenticator -->
            <!-- Google Authenticator (2FA) -->
            <div id="keamanan"></div>
            <?php if (!empty($admin['is_2fa_active'])): ?>
            <section class="bg-primary text-on-primary p-6 rounded-3xl shadow-lg border border-primary overflow-hidden relative">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <span class="material-symbols-outlined text-3xl" data-icon="verified_user" style="font-variation-settings: 'FILL' 1;">verified_user</span>
                        <div class="flex items-center gap-2 bg-on-primary/10 px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest">
                            <span class="w-1.5 h-1.5 rounded-full bg-secondary-fixed animate-pulse"></span>
                            Aktif
                        </div>
                    </div>
                    <h3 class="text-md font-bold mb-1">Autentikasi 2-Langkah</h3>
                    <p class="text-xs text-on-primary/70 mb-6">Lapisan keamanan tambahan via OTP 6 digit sedang berjalan. Untuk mematikannya, harap konfirmasi ulang kata sandi Anda.</p>
                    
                    <form action="index.php?action=reset_2fa_dashboard" method="POST" onsubmit="return confirm('Yakin ingin mematikan 2FA? Keamanan akun Anda dapat menurun.');" class="space-y-3">
                        <?= csrf_field() ?>
                        <div>
                            <input class="w-full bg-on-primary/10 border-none rounded-lg px-4 py-2 text-center text-sm focus:ring-0 border-b-2 border-transparent focus:border-b-white transition-all font-mono placeholder:text-on-primary/40 text-white" name="password" placeholder="Masukkan Password Anda" type="password" required autocomplete="off"/>
                        </div>
                        <button type="submit" class="w-full bg-white text-primary py-2 rounded-lg text-sm font-bold hover:bg-red-50 hover:text-red-600 transition-colors">
                            Matikan / Reset 2FA
                        </button>
                    </form>
                </div>
                <div class="absolute top-0 right-0 w-32 h-32 bg-on-primary/5 rounded-full -mr-16 -mt-16 blur-3xl"></div>
            </section>
            <?php else: ?>
            <section class="bg-surface-container-lowest p-6 rounded-3xl shadow-sm border border-surface-container-high overflow-hidden relative">
                <div class="flex items-center justify-between mb-4">
                    <span class="material-symbols-outlined text-3xl text-surface-variant" data-icon="gpp_maybe">gpp_maybe</span>
                    <div class="flex items-center gap-2 bg-error/10 text-error px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest">
                        Tidak Aktif
                    </div>
                </div>
                <h3 class="text-md font-bold mb-1 text-on-surface">Autentikasi 2-Langkah</h3>
                
                <?php if (empty($_SESSION['2fa_unlocked_setup'])): ?>
                <!-- Form Verifikasi Password Sebelum Setup -->
                <p class="text-xs text-on-surface-variant mb-4">Fitur keamanan ganda (2FA) perlindungan dari peretasan jarak jauh. Masukkan password Anda untuk melihat kode aktivasi.</p>
                <form action="index.php?action=init_setup_2fa_dashboard" method="POST" class="space-y-4">
                    <?= csrf_field() ?>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Verifikasi Password Anda</label>
                        <input class="w-full bg-surface-container-highest border-none rounded-lg px-4 py-2 text-center text-sm focus:ring-0 border-b-2 border-transparent focus:border-b-primary transition-all font-mono placeholder:text-outline" name="password" placeholder="••••••••" type="password" required autocomplete="off"/>
                    </div>
                    <button type="submit" class="w-full bg-primary-container text-on-primary py-2 rounded-lg text-sm font-bold hover:bg-primary transition-colors mt-2">
                        Verifikasi & Mulai Setup
                    </button>
                </form>
                <?php else: ?>
                <!-- Form OTP Setup (Password sudah terverifikasi) -->
                <p class="text-xs text-on-surface-variant mb-4 flex items-center gap-1.5 text-success font-semibold">
                    <span class="material-symbols-outlined text-[16px]">check_circle</span> Otorisasi berhasil. Pindai QR ini atau entri Kunci Setup.
                </p>

                <div class="flex flex-col items-center justify-center mb-4 gap-3">
                    <!-- Container for QRCode.js -->
                    <div id="qrcode-container" class="bg-white p-3 rounded-xl border border-outline-variant shadow-sm flex flex-col items-center">
                        <div id="qrcodeCanvas"></div>
                    </div>
                    
                    <div class="flex gap-2 w-full justify-center mt-2">
                        <button type="button" onclick="downloadQRAsPNG()" class="flex items-center gap-1.5 bg-primary text-white hover:bg-primary/90 text-[10px] font-bold uppercase tracking-wider px-4 py-2 rounded-lg shadow-sm transition-colors">
                            <span class="material-symbols-outlined text-[14px]">image</span> Download Image QR
                        </button>
                        <a href="index.php?action=download_2fa_backup" target="_blank" class="flex items-center gap-1.5 bg-white text-on-surface hover:bg-surface-variant text-[10px] border border-outline font-bold uppercase tracking-wider px-4 py-2 rounded-lg shadow-sm transition-colors">
                            <span class="material-symbols-outlined text-[14px]">description</span> Save File Backup
                        </a>
                    </div>
                    <div class="bg-surface-container-high px-4 py-2 rounded-lg border border-outline-variant/50 text-center w-full">
                        <span class="text-[9px] font-bold uppercase tracking-widest text-on-surface-variant block mb-1">Atau Gunakan Kunci Setup (Manual)</span>
                        <code class="text-sm font-mono font-bold tracking-[0.2em] text-primary"><?= htmlspecialchars($_SESSION['setup_totp_secret'] ?? '') ?></code>
                    </div>
                </div>
                
                <form action="index.php?action=setup_2fa_dashboard" method="POST" class="space-y-4">
                    <?= csrf_field() ?>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Masukkan Kode OTP 6-Digit</label>
                        <input class="w-full bg-surface-container-highest border-none rounded-lg px-4 py-2 text-center text-lg tracking-[0.5em] focus:ring-0 border-b-2 border-transparent focus:border-b-primary transition-all font-mono" name="otp" placeholder="000000" type="text" maxlength="6" pattern="\d{6}" required autocomplete="off"/>
                    </div>
                    <button type="submit" class="w-full bg-primary-container text-on-primary py-2 rounded-lg text-sm font-bold hover:bg-primary transition-colors mt-2">
                        Simpan & Aktifkan
                    </button>
                    <!-- Small cancel link if they change their mind -->
                    <p class="text-center mt-2 text-[10px]">
                        <a href="index.php?action=cancel_setup_2fa" class="text-error hover:underline transition-colors font-bold uppercase tracking-wider">Batal & Kembali</a>
                    </p>
                </form>
                <?php endif; ?>
            </section>
            
            <?php if (!empty($_SESSION['2fa_unlocked_setup'])): ?>
            <!-- Load QRCode.js library for client-side QR generation -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const secret = "<?= htmlspecialchars($_SESSION['setup_totp_secret'] ?? '') ?>";
                    const email = "<?= htmlspecialchars($admin['email']) ?>";
                    const otpAuthUrl = `otpauth://totp/AbsensiIoT:${email}?secret=${secret}&issuer=AbsensiIoT`;
                    
                    // Generate QR Code di frontend mem-bypass PHP GD requirement
                    const qrContainer = document.getElementById("qrcodeCanvas");
                    if (qrContainer) {
                        new QRCode(qrContainer, {
                            text: otpAuthUrl,
                            width: 160,
                            height: 160,
                            colorDark : "#000000",
                            colorLight : "#ffffff",
                            correctLevel : QRCode.CorrectLevel.M
                        });
                    }
                });

                function downloadQRAsPNG() {
                    const canvas = document.querySelector("#qrcodeCanvas canvas");
                    if (canvas) {
                        const link = document.createElement("a");
                        link.download = "2FA-QR-Setup.png";
                        link.href = canvas.toDataURL("image/png");
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    } else {
                        alert("QR Code belum siap untuk didownload. Mohon tunggu sejenak.");
                    }
                }
            </script>
            <?php endif; ?>
            
            <?php endif; ?>
            
        </div>
        </div>
    </div>

    <!-- Removed Log Aktivitas Keamanan as per request -->
</div>

<!-- Toast Notification (Floating) -->
<div id="toast-success" class="fixed bottom-6 right-6 flex items-center gap-4 bg-secondary-container text-on-secondary-container px-6 py-4 rounded-full shadow-[0_12px_40px_rgba(25,28,30,0.1)] border border-secondary/10 z-50 transition-all duration-300 transform translate-y-10 opacity-0 pointer-events-none">
    <span class="material-symbols-outlined" data-icon="check_circle" style="font-variation-settings: 'FILL' 1;">check_circle</span>
    <div class="flex flex-col">
        <span class="text-sm font-bold leading-tight">Pengaturan Disimpan</span>
        <span class="text-[10px] opacity-80 uppercase tracking-widest font-black">Success Notification</span>
    </div>
    <button class="ml-4 opacity-50 hover:opacity-100 transition-opacity" onclick="hideToast()">
        <span class="material-symbols-outlined text-sm" data-icon="close">close</span>
    </button>
</div>

<!-- ═══ Modal: Konfirmasi Putuskan Sesi Tunggal ═══ -->
<div id="modal-revoke-single" class="fixed inset-0 z-[100] flex items-center justify-center p-4 opacity-0 pointer-events-none transition-opacity duration-200">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeRevokeSingleModal()"></div>
    <div id="modal-revoke-single-box" class="relative bg-white rounded-3xl shadow-2xl p-8 max-w-sm w-full scale-95 transition-transform duration-200 border border-slate-100">
        <div class="flex flex-col items-center text-center">
            <div class="w-16 h-16 rounded-2xl bg-red-100 flex items-center justify-center mb-5">
                <span class="material-symbols-outlined text-red-600 text-[32px]" style="font-variation-settings: 'FILL' 1;">link_off</span>
            </div>
            <h3 class="text-xl font-black text-slate-900 mb-2">Putuskan Sesi?</h3>
            <p class="text-sm text-slate-500 leading-relaxed mb-8">
                Perangkat ini akan segera terputus dan dipaksa logout. Jika ini Anda sendiri, Anda perlu login ulang.
            </p>
            <div class="flex gap-3 w-full">
                <button onclick="closeRevokeSingleModal()" 
                    class="flex-1 py-3 rounded-xl border border-slate-200 text-slate-600 font-bold text-sm hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button id="btn-confirm-revoke-single"
                    class="flex-1 py-3 rounded-xl bg-red-600 text-white font-bold text-sm hover:bg-red-700 active:scale-95 transition-all shadow-lg shadow-red-200">
                    Ya, Putuskan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ═══ Modal: Konfirmasi Putuskan Semua Sesi ═══ -->
<div id="modal-revoke-all" class="fixed inset-0 z-[100] flex items-center justify-center p-4 opacity-0 pointer-events-none transition-opacity duration-200">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeRevokeAllModal()"></div>
    <div id="modal-revoke-all-box" class="relative bg-white rounded-3xl shadow-2xl p-8 max-w-sm w-full scale-95 transition-transform duration-200 border border-slate-100">
        <div class="flex flex-col items-center text-center">
            <div class="w-16 h-16 rounded-2xl bg-red-100 flex items-center justify-center mb-5">
                <span class="material-symbols-outlined text-red-600 text-[32px]" style="font-variation-settings: 'FILL' 1;">wifi_off</span>
            </div>
            <h3 class="text-xl font-black text-slate-900 mb-2">Putuskan Semua Sesi?</h3>
            <p class="text-sm text-slate-500 leading-relaxed mb-8">
                Semua perangkat <strong>lain</strong> yang sedang login dengan akun Anda akan segera terputus secara paksa. Sesi Anda saat ini tetap aman.
            </p>
            <div class="flex gap-3 w-full">
                <button onclick="closeRevokeAllModal()" 
                    class="flex-1 py-3 rounded-xl border border-slate-200 text-slate-600 font-bold text-sm hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button onclick="submitRevokeAll()"
                    class="flex-1 py-3 rounded-xl bg-red-600 text-white font-bold text-sm hover:bg-red-700 active:scale-95 transition-all shadow-lg shadow-red-200 flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[16px]">wifi_off</span>
                    Putuskan Semua
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form: Revoke All -->
<form id="form-revoke-all" action="index.php?action=revoke_all_sessions" method="POST" class="hidden">
    <?= csrf_field() ?>
</form>

<script src="assets/admin/js/pengaturan.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ─── Fingerprint Management (Superadmin) ──────────────────────────────────────
let isWaitingFingerprint = false;
let fingerprintPollInterval = null;

function mulaiEnrollAdmin(adminId) {
    if (isWaitingFingerprint) return;

    Swal.fire({
        title: 'Daftarkan Sidik Jari',
        html: `
            <div class="text-left space-y-4">
                <p class="text-sm text-slate-600">Proses pendaftaran akan membutuhkan waktu sekitar 15-30 detik. Pastikan:</p>
                <ul class="text-xs space-y-2 text-slate-500 list-disc pl-5">
                    <li>Alat ESP32 dalam posisi Standby atau Verifikasi</li>
                    <li>Jari bersih dan tidak basah</li>
                    <li>Slot sensor tidak terhalang</li>
                </ul>
                <div class="p-3 bg-blue-50 rounded-xl border border-blue-100 flex items-center gap-3">
                    <span class="material-symbols-outlined text-blue-600">info</span>
                    <p class="text-[10px] text-blue-700 leading-tight">Admin akan didaftarkan dengan <b>Offset ID 100+</b> untuk keamanan database.</p>
                </div>
            </div>
        `,
        icon: 'fingerprint',
        showCancelButton: true,
        confirmButtonText: 'Ya, Mulai Daftar',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#0D8ABC',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            let formData = new FormData();
            formData.append('admin_id', adminId);
            formData.append('csrf_token', '<?= csrf_token() ?>');

            return fetch('index.php?action=fingerprint_set_enroll', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status !== 'success') throw new Error(data.message);
                return data;
            })
            .catch(error => {
                Swal.showValidationMessage(`Request failed: ${error}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            isWaitingFingerprint = true;
            
            // Tampilkan Modal Polling yang lebih interaktif
            Swal.fire({
                title: 'Menghubungkan Alat...',
                html: `
                    <div class="py-6 flex flex-col items-center">
                        <div class="relative w-20 h-20 mb-6">
                            <div class="absolute inset-0 rounded-full border-4 border-slate-100"></div>
                            <div class="absolute inset-0 rounded-full border-4 border-primary border-t-transparent animate-spin"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="material-symbols-outlined text-3xl text-primary animate-pulse">fingerprint</span>
                            </div>
                        </div>
                        <p class="text-sm font-bold text-slate-800 mb-1">Menunggu Jari di ESP32</p>
                        <p class="text-xs text-slate-500 animate-pulse">Silakan tempelkan jari Anda 2x saat diminta...</p>
                        
                        <div id="enroll-steps" class="mt-8 grid grid-cols-3 gap-4 w-full px-4">
                            <div class="step-1 flex flex-col items-center opacity-30">
                                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold mb-1">1</div>
                                <span class="text-[9px] uppercase tracking-widest font-bold">Tempel</span>
                            </div>
                            <div class="step-2 flex flex-col items-center opacity-30">
                                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold mb-1">2</div>
                                <span class="text-[9px] uppercase tracking-widest font-bold">Angkat</span>
                            </div>
                            <div class="step-3 flex flex-col items-center opacity-30">
                                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold mb-1">3</div>
                                <span class="text-[9px] uppercase tracking-widest font-bold">Tempel</span>
                            </div>
                        </div>
                    </div>
                `,
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    const startTime = Math.floor(Date.now() / 1000);
                    let retryCount = 0;
                    
                    fingerprintPollInterval = setInterval(() => {
                        fetch(`index.php?action=fingerprint_check_status&id=${adminId}&type=admin&since=${startTime}`)
                        .then(r => r.json())
                        .then(res => {
                            if (res.updated) {
                                clearInterval(fingerprintPollInterval);
                                isWaitingFingerprint = false;
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Sidik jari Superadmin telah terdaftar dengan aman di database dan hardware.',
                                    icon: 'success',
                                    confirmButtonColor: '#0D8ABC'
                                }).then(() => location.reload());
                            }
                            
                            // Visual feedback polling
                            retryCount++;
                            if (retryCount > 60) { // Timeout 2 menit
                                clearInterval(fingerprintPollInterval);
                                isWaitingFingerprint = false;
                                Swal.fire('Timeout', 'Alat tidak merespon dalam waktu yang lama. Silakan coba lagi.', 'error');
                            }
                        });
                    }, 2000);
                }
            });
        }
    });
}

function hapusFingerprintAdmin(adminId) {
    Swal.fire({
        title: 'Hapus Sidik Jari?',
        text: 'Sidik jari akan dihapus dari sensor dan database. Fitur backup akan tertutup sementara.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus'
    }).then((result) => {
        if (result.isConfirmed) {
            let formData = new FormData();
            formData.append('admin_id', adminId);
            formData.append('csrf_token', '<?= csrf_token() ?>');

            fetch('index.php?action=fingerprint_set_delete', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('Terjadwal!', 'Menghapus dari alat...', 'info');
                    setTimeout(() => location.reload(), 2000);
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            });
        }
    });
}

// ─── Modal: Putuskan Sesi Tunggal ─────────────────────────────────────────────
let _pendingRevokeForm = null;

function confirmRevoke(form, e) {
    e.preventDefault();
    _pendingRevokeForm = form;
    const modal = document.getElementById('modal-revoke-single');
    const box   = document.getElementById('modal-revoke-single-box');
    modal.classList.remove('opacity-0', 'pointer-events-none');
    requestAnimationFrame(() => box.classList.remove('scale-95'));
    document.getElementById('btn-confirm-revoke-single').onclick = function() {
        _pendingRevokeForm.submit();
    };
    return false;
}

function closeRevokeSingleModal() {
    const modal = document.getElementById('modal-revoke-single');
    const box   = document.getElementById('modal-revoke-single-box');
    box.classList.add('scale-95');
    setTimeout(() => modal.classList.add('opacity-0', 'pointer-events-none'), 180);
    _pendingRevokeForm = null;
}

// ─── Modal: Putuskan Semua Sesi ───────────────────────────────────────────────
function openRevokeAllModal() {
    const modal = document.getElementById('modal-revoke-all');
    const box   = document.getElementById('modal-revoke-all-box');
    modal.classList.remove('opacity-0', 'pointer-events-none');
    requestAnimationFrame(() => box.classList.remove('scale-95'));
}

function closeRevokeAllModal() {
    const modal = document.getElementById('modal-revoke-all');
    const box   = document.getElementById('modal-revoke-all-box');
    box.classList.add('scale-95');
    setTimeout(() => modal.classList.add('opacity-0', 'pointer-events-none'), 180);
}

function submitRevokeAll() {
    document.getElementById('form-revoke-all').submit();
}

// Close modals on ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRevokeSingleModal();
        closeRevokeAllModal();
    }
});
</script>
