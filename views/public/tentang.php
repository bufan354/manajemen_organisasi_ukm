<!-- Hero Abstract -->
<div class="relative overflow-hidden bg-slate-50 pb-20 pt-16">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
        <div>
            <?php
            $appName = htmlspecialchars($settings['app_name'] ?? 'Sistem Absensi');
            $entitas = htmlspecialchars($settings['entitas_nama'] ?? 'IoT');
            $deskripsi = htmlspecialchars($settings['hero_deskripsi'] ?? 'Sebuah ekosistem manajemen kehadiran berbasis IoT yang dirancang untuk memberikan transparansi data secara real-time.');
            ?>
            <span class="text-[10px] font-bold text-primary uppercase tracking-[0.2em] mb-4 block">TENTANG SISTEM</span>
            <h1 class="text-5xl lg:text-6xl font-black text-slate-900 tracking-tighter mb-6"><?= strtoupper($appName) ?></h1>
            <p class="text-slate-600 max-w-md leading-relaxed mb-8">
                <?= $deskripsi ?>
            </p>
            <div class="grid grid-cols-2 gap-4">
                 <div class="bg-blue-50 p-4 rounded-2xl">
                     <div class="text-2xl font-black text-blue-700 mb-1"><?= number_format($stats['total_kehadiran'] ?? 0) ?></div>
                     <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Total Kehadiran</div>
                 </div>
                 <div class="bg-emerald-50 p-4 rounded-2xl">
                     <div class="text-2xl font-black text-emerald-700 mb-1"><?= number_format($stats['total_anggota'] ?? 0) ?></div>
                     <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Anggota Terdaftar</div>
                 </div>
            </div>
        </div>
        <div class="relative">
            <!-- Mockup illustration of the device -->
            <div class="bg-slate-900 absolute inset-0 rounded-[2rem] shadow-2xl mix-blend-multiply opacity-5 transform translate-x-4 translate-y-4"></div>
            <div class="bg-slate-900 rounded-[2rem] shadow-2xl relative overflow-hidden aspect-[4/3] flex flex-col items-center justify-center border border-slate-800">
                 <div class="absolute inset-0 bg-gradient-to-tr from-blue-900/40 to-emerald-900/10"></div>
                 <div class="relative z-10 text-center space-y-4">
                      <div class="text-xs font-mono text-cyan-400 tracking-[0.3em]">SISTEM ABSENSI</div>
                      <div class="text-xl font-light text-slate-300 tracking-[0.4em] mb-8">IoT NODE</div>
                      <div class="w-48 h-2 bg-slate-800 rounded-full mx-auto overflow-hidden">
                          <div class="w-1/2 h-full bg-cyan-400 rounded-full shadow-[0_0_15px_rgba(34,211,238,0.6)] animate-pulse"></div>
                      </div>
                 </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="max-w-7xl mx-auto px-6 py-24">
    <div class="text-center mb-16">
        <h2 class="text-3xl font-bold text-slate-900 mb-4">Fitur Unggulan</h2>
        <div class="h-1 w-16 bg-primary mx-auto rounded-full"></div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Feature 1 (Span 2) -->
        <div class="md:col-span-2 bg-white rounded-3xl p-8 border border-slate-100 shadow-sm">
             <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-6">
                  <span class="material-symbols-outlined">wifi_tethering</span>
             </div>
             <h3 class="text-xl font-bold text-slate-900 mb-4">Sinkronisasi Real-Time</h3>
             <p class="text-sm text-slate-500 leading-relaxed mb-6 max-w-lg">
                 Data absensi langsung terkirim ke dashboard administratif dalam hitungan detik setelah tapping, memastikan validitas data kehadiran.
             </p>
             <div class="grid grid-cols-2 gap-4">
                 <div class="flex items-center gap-2 text-xs font-bold text-slate-700">
                     <span class="material-symbols-outlined text-emerald-500 text-sm">check_circle</span> Webhook Integration
                 </div>
                 <div class="flex items-center gap-2 text-xs font-bold text-slate-700">
                     <span class="material-symbols-outlined text-emerald-500 text-sm">check_circle</span> Instant Notifications
                 </div>
                 <div class="flex items-center gap-2 text-xs font-bold text-slate-700">
                     <span class="material-symbols-outlined text-emerald-500 text-sm">check_circle</span> Cloud Sync
                 </div>
                 <div class="flex items-center gap-2 text-xs font-bold text-slate-700">
                     <span class="material-symbols-outlined text-emerald-500 text-sm">check_circle</span> Zero Latency
                 </div>
             </div>
        </div>
        
        <!-- Feature 2 -->
        <div class="bg-blue-700 rounded-3xl p-8 text-white relative overflow-hidden shadow-xl shadow-blue-900/10">
             <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-6 backdrop-blur-sm">
                  <span class="material-symbols-outlined text-white">security</span>
             </div>
             <h3 class="text-xl font-bold mb-4 relative z-10">Keamanan Enkripsi</h3>
             <p class="text-sm text-blue-100 leading-relaxed relative z-10">
                 Setiap data biometrik sidik jari divalidasi menggunakan protokol khusus untuk mencegah manipulasi dan kebocoran identitas pengguna.
             </p>
             <span class="material-symbols-outlined absolute -right-8 -bottom-8 text-9xl text-white opacity-5">shield</span>
        </div>
        
        <!-- Feature 3 -->
        <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm">
             <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center mb-6">
                  <span class="material-symbols-outlined">bar_chart</span>
             </div>
             <h3 class="text-lg font-bold text-slate-900 mb-3">Visualisasi Data</h3>
             <p class="text-sm text-slate-500 leading-relaxed">
                 Dukungan penuh Chart.js untuk menyajikan statistik kehadiran harian, mingguan, hingga bulanan dalam grafik yang intuitif.
             </p>
        </div>
        
        <!-- Feature 4 (Span 2) -->
        <div class="md:col-span-2 bg-slate-50 rounded-3xl p-8 flex flex-col md:flex-row items-center gap-8 justify-between">
             <div class="max-w-md">
                 <h3 class="text-lg font-bold text-slate-900 mb-3">Manajemen Offline</h3>
                 <p class="text-sm text-slate-500 leading-relaxed">
                     Sistem tetap dapat mencatat absensi meskipun koneksi internet terputus berkat penyimpanan lokal pada mikrokontroler (Local Cache).
                 </p>
             </div>
             <div class="flex flex-col gap-3">
                 <div class="bg-white px-4 py-2 rounded-lg text-xs font-bold text-slate-700 shadow-sm flex items-center gap-2 border border-slate-100">
                     <span class="material-symbols-outlined text-emerald-600 text-sm">wifi_off</span> OFFLINE MODE
                 </div>
                 <div class="bg-white px-4 py-2 rounded-lg text-xs font-bold text-slate-700 shadow-sm flex items-center gap-2 border border-slate-100">
                     <span class="material-symbols-outlined text-blue-600 text-sm">sync</span> AUTO SYNC
                 </div>
             </div>
        </div>
    </div>
</div>

<!-- Technology Stack Base -->
<div class="bg-slate-50 py-24 mb-12">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-slate-900 mb-4">Teknologi yang Digunakan</h2>
            <p class="text-slate-500 text-sm max-w-lg mx-auto">Kami menggunakan kombinasi hardware kelas industri dan framework modern untuk performa terbaik.</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
             <div class="bg-white p-8 rounded-3xl text-center shadow-sm border border-slate-100">
                 <span class="material-symbols-outlined text-4xl text-slate-800 mb-4">memory</span>
                 <h4 class="font-bold text-slate-900 mb-1">ESP32</h4>
                 <p class="text-[10px] text-slate-500 uppercase tracking-wider">Microcontroller w/ Wi-Fi</p>
             </div>
             <div class="bg-white p-8 rounded-3xl text-center shadow-sm border border-slate-100">
                 <span class="material-symbols-outlined text-4xl text-slate-800 mb-4">fingerprint</span>
                 <h4 class="font-bold text-slate-900 mb-1">AS608 Fingerprint</h4>
                 <p class="text-[10px] text-slate-500 uppercase tracking-wider">Biometric Auth</p>
             </div>
             <div class="bg-white p-8 rounded-3xl text-center shadow-sm border border-slate-100">
                 <span class="material-symbols-outlined text-4xl text-cyan-500 mb-4">css</span>
                 <h4 class="font-bold text-slate-900 mb-1">Tailwind CSS</h4>
                 <p class="text-[10px] text-slate-500 uppercase tracking-wider">Utility-first Framework</p>
             </div>
             <div class="bg-white p-8 rounded-3xl text-center shadow-sm border border-slate-100">
                 <span class="material-symbols-outlined text-4xl text-amber-500 mb-4">leaderboard</span>
                 <h4 class="font-bold text-slate-900 mb-1">Chart.js</h4>
                 <p class="text-[10px] text-slate-500 uppercase tracking-wider">Flexible Charting</p>
             </div>
        </div>
    </div>
</div>

<!-- Team Developer -->
<div class="max-w-7xl mx-auto px-6 py-12 mb-20">
    <div class="text-center mb-16">
        <h2 class="text-2xl font-bold text-slate-900 mb-3">Tim Pengembang</h2>
        <p class="text-slate-500 text-sm max-w-lg mx-auto">Visi kami diwujudkan oleh kolaborasi individu yang berdedikasi di bidang IoT, Design, dan Pengembangan Web.</p>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <?php if (!empty($teams)): ?>
            <?php foreach ($teams as $team): ?>
                 <?php
                 $namaParts = explode(' ', trim($team['nama']));
                 $firstName = count($namaParts) > 0 ? $namaParts[0] : 'Admin';
                 $lastName  = count($namaParts) > 1 ? $namaParts[1] : '';
                 
                 if (!empty($team['foto_path']) && file_exists($team['foto_path'])) {
                     $imgSrc = htmlspecialchars($team['foto_path']);
                 } else {
                     $colors = ['0F5132', '052C65', '18181A', '0369A1', '7F1D1D'];
                     $bgColor = $colors[crc32($team['nama']) % count($colors)];
                     $imgSrc = "https://ui-avatars.com/api/?name=" . urlencode($team['nama']) . "&size=200&background={$bgColor}&color=fff";
                 }
                 ?>
                 <div class="text-center group">
                     <div class="w-48 h-48 mx-auto bg-slate-100 rounded-3xl mb-6 overflow-hidden relative shadow-sm">
                         <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($team['nama']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                         <div class="absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                     </div>
                     <h4 class="text-lg font-bold text-slate-900 mb-1 truncate px-2"><?= htmlspecialchars($team['nama']) ?></h4>
                     <p class="text-xs text-primary font-bold uppercase tracking-widest"><?= htmlspecialchars($team['role']) ?></p>
                 </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full text-center text-slate-500 text-sm">Belum ada data tim pengembang.</div>
        <?php endif; ?>
    </div>
</div>

<!-- CTA Block -->
<div class="max-w-5xl mx-auto px-6 mb-24">
    <div class="bg-primary rounded-3xl p-12 text-center text-white relative overflow-hidden shadow-2xl shadow-primary/30">
        <div class="relative z-10">
            <h2 class="text-3xl font-bold mb-4">Siap Memulai Efisiensi?</h2>
            <p class="text-blue-100 max-w-lg mx-auto mb-8">
                Terapkan Sistem Absensi IoT di Institusi atau Perusahaan Anda dengan biaya integrasi minimal.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?= htmlspecialchars($settings['hero_btn1_link'] ?? '#') ?>" class="bg-white text-primary font-bold px-8 py-3 rounded-xl hover:bg-slate-50 transition-colors shadow-lg active:scale-95 block w-full sm:w-auto"><?= htmlspecialchars($settings['hero_btn1_label'] ?? 'Jelajahi') ?></a>
                <a href="<?= htmlspecialchars($settings['hero_btn2_link'] ?? '#') ?>" class="bg-blue-700 text-white font-bold px-8 py-3 rounded-xl hover:bg-blue-800 transition-colors active:scale-95 border border-blue-600 block w-full sm:w-auto"><?= htmlspecialchars($settings['hero_btn2_label'] ?? 'Pelajari') ?></a>
            </div>
        </div>
        <!-- Decorative abstract blobs -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl transform translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-blue-900/40 rounded-full blur-3xl transform -translate-x-1/4 translate-y-1/4"></div>
    </div>
</div>
