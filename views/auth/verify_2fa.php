<main class="min-h-screen flex items-center justify-center p-6 lg:p-12 bg-gradient-to-br from-[#0058be]/10 to-[#0058be]/05">
    <div class="w-full max-w-lg bg-surface-container-lowest rounded-[1.5rem] overflow-hidden shadow-[0_12px_40px_rgba(25,28,30,0.06)]">
        <div class="flex flex-col justify-center p-10 lg:p-16 relative overflow-hidden">
            <!-- Branding Header -->
            <div class="mb-10 text-center">
                <div class="flex items-center justify-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-primary-container rounded-full flex items-center justify-center text-white shadow-inner">
                        <span class="material-symbols-outlined text-[28px]" data-icon="lock_person">lock_person</span>
                    </div>
                </div>
                <h1 class="text-3xl font-bold tracking-tight text-on-surface mb-2">Verifikasi 2FA</h1>
                <p class="text-on-surface-variant body-md leading-relaxed">Buka aplikasi authenticator Anda dan masukkan kode 6 digit di bawah ini.</p>
            </div>
            
            <?php $flash = renderFlash(); if ($flash) echo $flash; ?>
            
            <form action="index.php?action=verify_2fa_post" method="POST" class="space-y-8">
    <?= csrf_field() ?>
                
                <!-- OTP Field -->
                <div class="space-y-2">
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-on-surface-variant">
                            <span class="material-symbols-outlined text-[24px]" data-icon="password">password</span>
                        </div>
                        <input class="w-full h-16 pl-12 pr-4 bg-surface-container-highest rounded-xl border-none focus:ring-0 focus:border-b-4 focus:border-primary transition-all duration-200 outline-none placeholder:text-outline/50 tracking-[0.5em] text-3xl text-center font-mono text-on-surface font-bold" id="otp" name="otp" placeholder="••••••" type="text" maxlength="6" pattern="\d{6}" required autocomplete="off" autofocus/>
                    </div>
                </div>

                <!-- Action Button -->
                <button class="w-full h-14 bg-gradient-to-r from-primary to-primary-container text-white rounded-xl font-bold tracking-tight shadow-lg shadow-primary/20 active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2" type="submit">
                    <span>Lanjutkan Login</span>
                    <span class="material-symbols-outlined text-[20px]" data-icon="arrow_forward">arrow_forward</span>
                </button>

                <div class="text-center mt-6">
                    <a href="index.php?action=logout" class="text-error font-semibold text-sm hover:underline">Batalkan Login</a>
                </div>
            </form>
        </div>
    </div>
</main>
