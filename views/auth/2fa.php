

<div class="min-h-screen w-full flex flex-col items-center justify-center relative overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,_rgba(0,88,190,0.08)_0%,_transparent_50%)] pointer-events-none"></div>
    <div class="w-full max-w-lg mx-auto p-6 relative z-10">
        <div class="bg-surface-container-lowest rounded-xl shadow-[0_12px_40px_rgba(25,28,30,0.06)] overflow-hidden transition-all duration-300">
            <div class="p-8 md:p-12">
                <div class="flex flex-col items-center text-center mb-8">
                    <div class="w-16 h-16 bg-primary-fixed rounded-full flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-primary text-4xl" data-icon="shield_lock" data-weight="fill" style="font-variation-settings: 'FILL' 1;">shield_lock</span>
                    </div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-on-surface mb-2">Verifikasi Keamanan</h1>
                    <p class="text-on-surface-variant text-sm leading-relaxed max-w-xs">
                        Masukkan 6 digit kode dari aplikasi Google Authenticator Anda.
                    </p>
                </div>
                
                <form action="index.php?page=dashboard" method="POST" class="space-y-8">
    <?= csrf_field() ?>
                    <div class="flex justify-between gap-2 md:gap-4 px-2">
                        <input class="otp-input w-full aspect-square text-center text-2xl font-bold bg-surface-container-highest rounded-lg border-none transition-all duration-200" maxlength="1" placeholder="0" type="text" name="otp_1"/>
                        <input class="otp-input w-full aspect-square text-center text-2xl font-bold bg-surface-container-highest rounded-lg border-none transition-all duration-200" maxlength="1" placeholder="0" type="text" name="otp_2"/>
                        <input class="otp-input w-full aspect-square text-center text-2xl font-bold bg-surface-container-highest rounded-lg border-none transition-all duration-200" maxlength="1" placeholder="0" type="text" name="otp_3"/>
                        <input class="otp-input w-full aspect-square text-center text-2xl font-bold bg-surface-container-highest rounded-lg border-none transition-all duration-200" maxlength="1" placeholder="0" type="text" name="otp_4"/>
                        <input class="otp-input w-full aspect-square text-center text-2xl font-bold bg-surface-container-highest rounded-lg border-none transition-all duration-200" maxlength="1" placeholder="0" type="text" name="otp_5"/>
                        <input class="otp-input w-full aspect-square text-center text-2xl font-bold bg-surface-container-highest rounded-lg border-none transition-all duration-200" maxlength="1" placeholder="0" type="text" name="otp_6"/>
                    </div>
                    <div class="flex items-center space-x-3">
                        <input class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary/20 cursor-pointer" id="remember" type="checkbox" name="remember"/>
                        <label class="text-sm text-on-surface-variant font-medium cursor-pointer" for="remember">Ingat perangkat ini selama 30 hari</label>
                    </div>
                    <button class="w-full bg-primary hover:bg-primary-container text-white py-4 px-6 rounded-xl font-bold tracking-tight transition-all duration-200 active:scale-[0.98] shadow-lg shadow-primary/20" type="submit">
                        Verifikasi
                    </button>
                </form>

                <div class="mt-10 pt-8 border-t border-outline-variant/15 space-y-6">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-on-surface-variant tracking-[+0.05em] uppercase">Belum Setup?</span>
                        <button class="text-primary text-xs font-bold flex items-center gap-1 hover:underline transition-all">
                            <span class="material-symbols-outlined text-sm" data-icon="qr_code_2">qr_code_2</span>
                            Lihat QR Code
                        </button>
                    </div>
                    <div class="bg-surface-container-low rounded-xl p-4 flex items-center gap-4">
                        <div class="bg-white p-2 rounded-lg shrink-0">
                            <img class="w-16 h-16 opacity-80" alt="qr code" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAbzVCfLJq7XYHlrW5ABCQWJqLaLHKbSAyPOAJNulyfHZtie3HuGv3AySqY6N6EQhVR9G3mnShuLDlXEhHxi27-UxtNL0Mc6hAfmPa3G6Xr8l_Ki1EAgfx9_5UJsO0pc6xE4wKPrrAYX8jdlYBjiX-2fFM7Qk-5Bz7GCncHnzk7BJFXxEmMZ4V-bub-PQDFIW6vniSHf261NFezm-LMbjWr5C0Z1Gg1BpeNJBHiykqEnogvf65jmar30WdWmjjYjGPIxCyzqGZPumaJ"/>
                        </div>
                        <div class="text-xs text-on-surface-variant leading-relaxed">
                            Pindai kode ini dengan Google Authenticator jika ini adalah pertama kali Anda mengatur panel admin.
                        </div>
                    </div>
                </div>

                <div class="mt-8 text-center">
                    <button class="group inline-flex items-center gap-2 text-sm text-on-surface-variant hover:text-primary transition-colors">
                        <span class="material-symbols-outlined text-lg" data-icon="key">key</span>
                        <span class="font-medium underline decoration-primary/30 group-hover:decoration-primary transition-all underline-offset-4">Gunakan Kode Cadangan</span>
                    </button>
                    <div class="hidden mt-6 grid grid-cols-2 gap-2 text-left">
                        <div class="text-[10px] font-mono bg-surface-container-high px-3 py-2 rounded text-on-surface-variant">XXXX-XXXX-01</div>
                        <div class="text-[10px] font-mono bg-surface-container-high px-3 py-2 rounded text-on-surface-variant">XXXX-XXXX-02</div>
                        <div class="text-[10px] font-mono bg-surface-container-high px-3 py-2 rounded text-on-surface-variant">XXXX-XXXX-03</div>
                        <div class="text-[10px] font-mono bg-surface-container-high px-3 py-2 rounded text-on-surface-variant">XXXX-XXXX-04</div>
                        <div class="text-[10px] font-mono bg-surface-container-high px-3 py-2 rounded text-on-surface-variant">XXXX-XXXX-05</div>
                        <div class="text-[10px] font-mono bg-surface-container-high px-3 py-2 rounded text-on-surface-variant">XXXX-XXXX-06</div>
                        <div class="text-[10px] font-mono bg-surface-container-high px-3 py-2 rounded text-on-surface-variant">XXXX-XXXX-07</div>
                        <div class="text-[10px] font-mono bg-surface-container-high px-3 py-2 rounded text-on-surface-variant">XXXX-XXXX-08</div>
                        <div class="text-[10px] font-mono bg-surface-container-high px-3 py-2 rounded text-on-surface-variant">XXXX-XXXX-09</div>
                        <div class="text-[10px] font-mono bg-surface-container-high px-3 py-2 rounded text-on-surface-variant">XXXX-XXXX-10</div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="mt-12 flex flex-col items-center gap-4">
            <div class="flex gap-8 items-center">
                <span class="text-xs font-medium uppercase tracking-[+0.05em] text-slate-400 font-['Inter'] hover:text-blue-700 transition-all cursor-pointer">Privacy Policy</span>
                <span class="text-xs font-medium uppercase tracking-[+0.05em] text-slate-400 font-['Inter'] hover:text-blue-700 transition-all cursor-pointer">Terms of Service</span>
                <span class="text-xs font-medium uppercase tracking-[+0.05em] text-slate-400 font-['Inter'] hover:text-blue-700 transition-all cursor-pointer">Support</span>
            </div>
            <p class="text-xs font-medium uppercase tracking-[+0.05em] text-slate-400 font-['Inter']">© 2024 Intelligent Ledger IoT. All rights reserved.</p>
        </footer>
    </div>

    <!-- Floating Top Right Element -->
    <div class="fixed top-6 right-6 flex items-center gap-2 px-4 py-2 bg-white/80 backdrop-blur-xl rounded-full shadow-[0_12px_40px_rgba(25,28,30,0.06)] border-none">
        <span class="text-xl font-bold tracking-tighter text-slate-900">Absensi IoT</span>
        <div class="h-4 w-px bg-outline-variant/30 mx-1"></div>
        <span class="material-symbols-outlined text-primary text-xl" data-icon="help_outline">help_outline</span>
    </div>
</div>

<script src="assets/common/js/2fa.js"></script>
