<main class="flex-1 p-8 bg-surface-container-low min-h-screen">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-10 text-center">
            <div class="w-20 h-20 bg-primary/10 rounded-3xl flex items-center justify-center mx-auto mb-6">
                <span class="material-symbols-outlined text-primary text-4xl" style="font-variation-settings: 'FILL' 1;">shield_lock</span>
            </div>
            <h1 class="text-3xl font-black text-on-surface tracking-tight mb-2">Otorisasi Backup Database</h1>
            <p class="text-on-surface-variant text-sm">Demi keamanan data sensitif, Anda perlu melewati verifikasi multi-faktor sebelum dapat mengunduh salinan database.</p>
        </div>

        <?= renderFlash() ?>

        <div class="bg-white rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.05)] border border-surface-container overflow-hidden">
            <!-- Progress Stepper -->
            <div class="flex border-b border-surface-container">
                <?php 
                $stages = [
                    'password' => ['icon' => 'lock', 'label' => 'Password'],
                    '2fa' => ['icon' => 'verified_user', 'label' => '2FA OTP'],
                    'ready' => ['icon' => 'download', 'label' => 'Siap']
                ];
                
                $currentStep = 'password';
                if ($stage === 'password_verified') $currentStep = $admin['is_2fa_active'] ? '2fa' : 'ready';
                if ($stage === 'ready') $currentStep = 'ready';

                $i = 1;
                foreach ($stages as $key => $s): 
                    $isActive = ($currentStep === $key);
                    $isDone = false;
                    
                    // Logic to determine if a step is done
                    if ($key === 'password' && ($stage === 'password_verified' || $stage === 'ready')) $isDone = true;
                    if ($key === '2fa' && ($stage === 'ready')) $isDone = true;
                    
                    // Skip 2FA step if not active
                    if ($key === '2fa' && empty($admin['is_2fa_active'])) continue;
                ?>
                <div class="flex-1 flex flex-col items-center py-4 relative <?= $isActive ? 'bg-primary/5' : '' ?>">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center mb-1 <?= $isDone ? 'bg-success text-white' : ($isActive ? 'bg-primary text-white' : 'bg-surface-container-high text-outline') ?>">
                        <span class="material-symbols-outlined text-sm"><?= $isDone ? 'check' : $s['icon'] ?></span>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-widest <?= $isActive ? 'text-primary' : 'text-outline' ?>"><?= $s['label'] ?></span>
                    <?php if ($isActive): ?>
                    <div class="absolute bottom-0 left-0 w-full h-1 bg-primary"></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="p-10">
                <?php if ($stage === 'none'): ?>
                    <!-- STAGE 1: PASSWORD -->
                    <form action="index.php?action=backup_verify_password" method="POST" class="space-y-6">
    <?= csrf_field() ?>
                        
                        <div class="text-center mb-6">
                            <h2 class="text-xl font-bold text-on-surface mb-1">Konfirmasi Identitas</h2>
                            <p class="text-xs text-on-surface-variant">Masukkan kata sandi akun superadmin Anda.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] font-black uppercase tracking-widest text-outline ml-1">Password Superadmin</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline group-focus-within:text-primary transition-colors">key</span>
                                <input type="password" name="password" required autofocus
                                    class="w-full pl-12 pr-4 py-4 bg-surface-container-lowest border-2 border-surface-container rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all outline-none text-lg" 
                                    placeholder="••••••••">
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-primary text-white py-4 rounded-2xl font-bold shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all active:scale-95 flex items-center justify-center gap-2">
                            Lanjut Verifikasi
                            <span class="material-symbols-outlined">arrow_forward</span>
                        </button>
                    </form>

                <?php elseif ($stage === 'password_verified' && !empty($admin['is_2fa_active'])): ?>
                    <!-- STAGE 2: 2FA OTP -->
                    <form action="index.php?action=backup_verify_2fa" method="POST" class="space-y-6 text-center">
    <?= csrf_field() ?>
                        
                        <div class="mb-6">
                            <h2 class="text-xl font-bold text-on-surface mb-1">Masukan Kode OTP</h2>
                            <p class="text-xs text-on-surface-variant">Buka aplikasi Authenticator Anda dan masukkan 6 digit kode.</p>
                        </div>
                        <div class="flex justify-center">
                            <input type="text" name="otp" maxlength="6" pattern="\d{6}" required autofocus
                                class="w-48 text-center text-4xl font-black tracking-[0.5em] bg-surface-container-lowest border-b-4 border-primary p-4 outline-none focus:bg-primary/5 transition-all"
                                placeholder="000000" autocomplete="off">
                        </div>
                        <button type="submit" class="w-full bg-primary text-white py-4 rounded-2xl font-bold shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all active:scale-95 mt-8">
                            Verifikasi OTP
                        </button>
                    </form>

                <?php elseif ($stage === 'ready'): ?>
                    <!-- STAGE 4: READY TO DOWNLOAD -->
                    <div class="text-center py-6 space-y-8">
                        <div class="w-24 h-24 bg-success/10 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-success/5">
                            <span class="material-symbols-outlined text-success text-5xl" style="font-variation-settings: 'FILL' 1;">cloud_done</span>
                        </div>
                        <div class="space-y-2">
                            <h2 class="text-3xl font-black text-on-surface tracking-tight">Verifikasi Selesai</h2>
                            <p class="text-sm text-on-surface-variant">Semua protokol keamanan telah dilewati. Anda dapat mengunduh backup sekarang.</p>
                        </div>
                        
                        <div class="bg-amber-50 border border-amber-100 p-6 rounded-3xl text-left space-y-3">
                            <div class="flex items-center gap-2 text-amber-700">
                                <span class="material-symbols-outlined text-sm">info</span>
                                <span class="text-[10px] font-black uppercase tracking-widest">Informasi File</span>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm font-bold text-amber-900">Format: SQL Dump (.sql)</p>
                                <p class="text-xs text-amber-700/80">Berisi struktur tabel dan seluruh data transaksi UKM, kehadiran, dan log sistem.</p>
                            </div>
                        </div>

                        <form action="index.php?action=backup_download" method="POST">
    <?= csrf_field() ?>
                            
                            <button type="submit" class="w-full bg-emerald-600 text-white py-5 rounded-3xl font-black shadow-[0_15px_30px_rgba(16,185,129,0.3)] hover:bg-emerald-500 hover:-translate-y-1 transition-all active:scale-95 flex items-center justify-center gap-4 text-xl group border-2 border-emerald-400/20">
                                <span class="material-symbols-outlined text-2xl group-hover:bounce transition-transform">download</span>
                                Unduh Database Sekarang
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-8 text-center">
            <a href="index.php?page=dashboard" class="text-outline hover:text-primary transition-colors text-xs font-bold uppercase tracking-widest flex items-center justify-center gap-1">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</main>
