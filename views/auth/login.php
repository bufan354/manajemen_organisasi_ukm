<main class="min-h-screen flex items-center justify-center p-6 lg:p-12 bg-gradient-to-br from-[#0058be]/10 to-[#0058be]/05">
    <div class="w-full max-w-6xl grid lg:grid-cols-2 bg-surface-container-lowest rounded-[1.5rem] overflow-hidden shadow-[0_12px_40px_rgba(25,28,30,0.06)] min-h-[720px]">
        <!-- Left Side: Login Form -->
        <div class="flex flex-col justify-center p-12 lg:p-24 relative overflow-hidden">
            <!-- Branding Header -->
            <div class="mb-12">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-primary-container rounded-xl flex items-center justify-center text-white">
                        <span class="material-symbols-outlined" data-icon="sensors">sensors</span>
                    </div>
                    <span class="text-xl font-extrabold tracking-tighter text-on-surface"><?= h($APP_NAME ?? 'The Ledger') ?></span>
                </div>
                <h1 class="text-3xl font-bold tracking-tight text-on-surface mb-2">Selamat Datang</h1>
                <p class="text-on-surface-variant body-md">Silakan masuk ke panel Sistem Absensi IoT Anda.</p>
            </div>
            
            <?php $flash = renderFlash(); if ($flash) echo $flash; ?>
            <form action="index.php?action=login" method="POST" class="space-y-6">
                <?= csrf_field() ?>
                <!-- Email Field -->
                <div class="space-y-2">
                    <label class="block text-xs font-medium uppercase tracking-widest text-on-surface-variant px-1" for="email">Email Address</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-on-surface-variant">
                            <span class="material-symbols-outlined text-[20px]" data-icon="alternate_email">alternate_email</span>
                        </div>
                        <input class="w-full h-14 pl-12 pr-4 bg-surface-container-highest rounded-xl border-none focus:ring-0 focus:border-b-2 focus:border-primary transition-all duration-200 outline-none placeholder:text-outline" id="email" name="email" placeholder="nama@ukm.ac.id" type="email" required/>
                    </div>
                </div>

                <!-- Password Field -->
                <div class="space-y-2">
                    <div class="flex justify-between items-center px-1">
                        <label class="text-xs font-medium uppercase tracking-widest text-on-surface-variant" for="password">Password</label>
                        <a class="text-xs font-semibold text-primary hover:text-primary-container transition-colors" href="#">Forgot Password?</a>
                    </div>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-on-surface-variant">
                            <span class="material-symbols-outlined text-[20px]" data-icon="lock">lock</span>
                        </div>
                        <input class="w-full h-14 pl-12 pr-4 bg-surface-container-highest rounded-xl border-none focus:ring-0 focus:border-b-2 focus:border-primary transition-all duration-200 outline-none placeholder:text-outline" id="password" name="password" placeholder="••••••••" type="password" required/>
                    </div>
                </div>

                <!-- Security Warning -->
                <div class="flex items-center gap-3 p-4 bg-error-container/30 rounded-xl">
                    <span class="material-symbols-outlined text-on-error-container" data-icon="warning">warning</span>
                    <p class="text-xs font-medium text-on-error-container leading-snug">5 failed attempts will lock account for security purposes.</p>
                </div>

                <!-- Action Button -->
                <button class="w-full h-14 bg-gradient-to-r from-primary to-primary-container text-white rounded-xl font-bold tracking-tight shadow-lg shadow-primary/20 active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2" type="submit">
                    <span>Sign In to Dashboard</span>
                    <span class="material-symbols-outlined text-[20px]" data-icon="arrow_forward">arrow_forward</span>
                </button>
            </form>

            <!-- Footer Text -->
            <div class="mt-12 pt-8 border-t border-outline-variant/15">
                <p class="text-xs text-on-surface-variant font-medium uppercase tracking-widest text-center">
                    &copy; <?= date('Y') ?> <?= h($APP_NAME ?? 'The Intelligent Ledger') ?>. All Rights Reserved.
                </p>
            </div>
        </div>

        <!-- Right Side: IoT Illustration & Branding -->
        <div class="hidden lg:flex relative bg-surface-container-low items-center justify-center p-12 overflow-hidden">
            <!-- Background Decorative Elements -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-primary-fixed/30 rounded-full blur-[80px] -mr-48 -mt-48"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-secondary-container/20 rounded-full blur-[60px] -ml-32 -mb-32"></div>
            <div class="relative z-10 w-full max-w-md">
                <!-- IoT Illustration Placeholder -->
                <div class="mb-12 relative">
                    <div class="absolute inset-0 bg-primary/5 rounded-full scale-110 blur-xl"></div>
                    <img alt="Smart City Technology" class="w-full h-80 object-cover rounded-[2rem] shadow-2xl relative z-10 border-8 border-surface-container-lowest" data-alt="Futuristic glowing blue circuit patterns and network nodes connecting smart devices in a dark minimalist tech environment" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDAbf_UBeY7-Lfj4rzFEhcs9jx0B2vux6AOJxEYlwTBqUfcftgIDQ5bvVDuom1alcWK6W5hDmQqswCMdXuLV_pgX5xUbjqXSsdtpyyskK1z2vqxi5Sk5k2gvhhn01eLFmxq28e2s9UTI7XOMyqeC6CRtDcm1YZKGBY_Rw1Lrs9gAeudewYdPtJ-oaVvnrsvjbm3nX362jc6w8881DxFwVs8k0BMj-pfK-DJGbtqzyJjbTDCpCtTEM-ZBbVGkE-QHsTeeRH-YgL_XErH"/>
                    
                    <!-- Floating Data Cards -->
                    <div class="absolute -top-4 -right-8 glass-panel p-4 rounded-xl shadow-xl flex items-center gap-3 z-20">
                        <div class="w-8 h-8 rounded-full bg-secondary-container flex items-center justify-center text-on-secondary-container">
                            <span class="material-symbols-outlined text-sm" data-icon="check_circle">check_circle</span>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-tighter text-on-surface-variant">Live Update</p>
                            <p class="text-xs font-semibold">Node 04: Active</p>
                        </div>
                    </div>

                    <div class="absolute -bottom-6 -left-8 glass-panel p-4 rounded-xl shadow-xl flex items-center gap-3 z-20">
                        <div class="w-8 h-8 rounded-full bg-primary-fixed flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-sm" data-icon="group">group</span>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-tighter text-on-surface-variant">Total Users</p>
                            <p class="text-xs font-semibold">1,248 Verified</p>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <h2 class="text-2xl font-bold tracking-tight text-on-surface mb-4">Precision Management for IoT Ecosystems</h2>
                    <p class="text-on-surface-variant text-sm leading-relaxed">
                        Monitor real-time attendance, track device health, and manage student organization data through our integrated IoT ledger system.
                    </p>
                </div>
            </div>
            <!-- Subtle Grid Texture -->
            <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: radial-gradient(#004191 1px, transparent 0); background-size: 32px 32px;"></div>
        </div>
    </div>
</main>
