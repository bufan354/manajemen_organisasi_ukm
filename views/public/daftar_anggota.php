<!-- Navbar Sederhana (Hanya Logo & Kembali) -->
<nav class="bg-white border-b border-slate-200 w-full py-4 px-6 z-50 sticky top-0">
    <div class="max-w-4xl mx-auto flex items-center justify-between">
        <a class="flex items-center gap-1 text-slate-500 hover:text-blue-500 transition-colors shrink-0" href="index.php?page=katalog_ukm">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span class="text-sm font-bold uppercase tracking-wider">Batalkan</span>
        </a>
        <a class="flex items-center gap-1 text-slate-500 hover:text-blue-500 transition-colors shrink-0" href="index.php?page=katalog_ukm">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span class="text-sm font-bold uppercase tracking-wider">Batalkan</span>
        </a>
        <div class="font-black text-xl text-slate-900 tracking-tight"><?= h($APP_NAME) ?></div>
        <div class="w-[84px]"></div> <!-- Spacer for balancing -->
    </div>
</nav>

<main class="min-h-screen bg-slate-50 py-12 px-6">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 text-blue-700 rounded-full mb-4">
                <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">person_add</span>
            </div>
            <h1 class="text-3xl lg:text-4xl font-black text-slate-900 mb-2">Formulir Pendaftaran Anggota</h1>
            <p class="text-slate-600">Bergabunglah dengan organisasi kami dan jadilah bagian dari perubahan besar. Mohon isi data dengan benar dan jujur.</p>
        </div>

        <!-- Stepper Indicator -->
        <div class="flex items-center justify-center mb-12 relative max-w-xl mx-auto">
            <div class="absolute left-0 right-0 top-1/2 h-1 bg-slate-200 -z-10 rounded-full -translate-y-1/2"></div>
            <!-- Dynamic Progress Bar -->
            <div class="absolute left-0 top-1/2 h-1 bg-blue-600 -z-10 rounded-full -translate-y-1/2 transition-all duration-500 ease-in-out w-0" id="progress-bar"></div>
            
            <div class="w-full flex justify-between relative z-10">
                <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold border-4 border-slate-50 shadow-sm transition-colors duration-500 step-indicator" id="step-1-indicator">1</div>
                <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center font-bold border-4 border-slate-50 shadow-sm transition-colors duration-500 step-indicator" id="step-2-indicator">2</div>
                <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center font-bold border-4 border-slate-50 shadow-sm transition-colors duration-500 step-indicator" id="step-3-indicator">3</div>
            </div>
        </div>

        <!-- Forms Container -->
        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200/60 overflow-hidden">
            <?php if (!empty($riwayatPendaftaran)): ?>
                <?php
                    $st = $riwayatPendaftaran['status'];
                    $icon = 'info'; $color = 'blue'; $title = 'Pengajuan Sedang Dalam Proses';
                    $desc = 'Anda telah mengajukan pendaftaran di '.h($ENTITY).' ini. Harap tunggu konfirmasi dari Admin '.h($ENTITY).' sebelum mengajukan permohonan baru.';
                    
                    if ($st === 'diterima') {
                        $icon = 'check_circle'; $color = 'emerald'; $title = 'Selamat! Pendaftaran Diterima';
                        $desc = 'Pendaftaran Anda diterima. Silakan hubungi admin untuk informasi lebih lanjut.';
                    } elseif ($st === 'ditolak') {
                        $icon = 'cancel'; $color = 'red'; $title = 'Pendaftaran Belum Diterima';
                        $desc = 'Maaf, permohonan gabung Anda ditolak.';
                        if (!empty($riwayatPendaftaran['alasan_penolakan'])) {
                            $desc .= '<br><br><strong>Catatan Admin:</strong> ' . htmlspecialchars($riwayatPendaftaran['alasan_penolakan']);
                        }
                    }
                ?>
                <div class="p-8 lg:p-16 text-center flex flex-col items-center justify-center">
                    <div class="w-24 h-24 bg-<?= $color ?>-100 text-<?= $color ?>-600 rounded-full flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 1;"><?= $icon ?></span>
                    </div>
                    <h3 class="text-3xl font-black text-slate-900 mb-4"><?= $title ?></h3>
                    <p class="text-slate-600 max-w-md mx-auto leading-relaxed mb-8"><?= $desc ?></p>
                    
                    <?php if ($st === 'diterima' || $st === 'ditolak'): ?>
                        <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto mt-4">
                            <a href="index.php?page=katalog_ukm" class="px-8 py-4 bg-slate-100 text-slate-700 font-bold rounded-xl shadow-sm hover:bg-slate-200 transition-all text-center">Ke Katalog <?= h($ENTITY) ?></a>
                            <a href="index.php?page=daftar_anggota&ukm_id=<?= $targetUkmId ?>" class="px-8 py-4 bg-<?= $color ?>-600 text-white font-bold rounded-xl shadow-lg hover:bg-<?= $color ?>-700 transition-all active:scale-95 text-center">
                                Daftar Lagi (Reset)
                            </a>
                        </div>
                    <?php else: ?>
                        <a href="index.php?page=katalog_ukm" class="px-8 py-4 bg-slate-100 text-slate-700 font-bold rounded-xl shadow-sm hover:bg-slate-200 transition-all mt-4 inline-block">Kembali ke Katalog <?= h($ENTITY) ?></a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?= renderFlash() ?>
                
                <?php 
                $isClosed = ($settings['form_reg_status'] ?? 'dibuka') === 'ditutup';
                
                $defaultQuestions = [
                    ['text' => 'Mengapa Anda tertarik untuk bergabung dengan '.h($ENTITY).' ini?', 'required' => true],
                    ['text' => 'Apakah Anda memiliki pengalaman sebelumnya di bidang ini? Jika ya, tolong jelaskan.', 'required' => true]
                ];
                $questionsJson = $settings['form_reg_questions'] ?? "[]";
                if (empty(trim($questionsJson)) || $questionsJson === "[]") {
                    $questionsJson = json_encode($defaultQuestions);
                }
                
                $questions = json_decode($questionsJson, true) ?: [];
                
                if ($isClosed && $targetUkmId > 0): 
                ?>
                    <div class="p-8 lg:p-16 text-center flex flex-col items-center justify-center">
                        <div class="w-24 h-24 bg-slate-100 text-slate-500 rounded-full flex items-center justify-center mb-6">
                            <span class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 1;">lock</span>
                        </div>
                        <h3 class="text-3xl font-black text-slate-900 mb-4">Pendaftaran Ditutup</h3>
                        <p class="text-slate-600 max-w-md mx-auto leading-relaxed mb-8">Mohon maaf, <?= h($ENTITY) ?> yang Anda tuju saat ini sedang tidak menerima pendaftar baru. Silakan pantau terus informasi di halaman profil <?= h($ENTITY) ?>.</p>
                        <a href="index.php?page=katalog_ukm" class="px-8 py-4 bg-blue-600 text-white font-bold rounded-xl shadow-lg hover:bg-blue-700 transition-all">Kembali Ke Katalog</a>
                    </div>
                <?php else: ?>
                
                <form id="registration-form" action="index.php?action=pendaftaran_register" method="POST">
    <?= csrf_field() ?>
                    
                    <!-- STEP 1: Identitas Pribadi -->
                    <div id="step-1" class="p-8 lg:p-12 transition-all duration-500">
                        <h3 class="text-2xl font-black text-slate-900 mb-1">Informasi Dasar</h3>
                        <p class="text-sm text-slate-500 mb-8 border-b border-slate-100 pb-4">Pilih <?= h($ENTITY) ?> dan isi data diri Anda dengan benar.</p>
                        
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <?php if (!empty($targetUkmId) && $targetUkmId > 0): 
                                    $targetName = '[' . h($ENTITY) . ' Tidak Ditemukan]';
                                    foreach($ukmList as $u) { if($u['id'] == $targetUkmId) { $targetName = $u['nama'] . ' (' . $u['singkatan'] . ')'; break; } }
                                ?>
                                    <label class="text-xs font-bold uppercase tracking-widest text-slate-500">Mendaftar Sebagai Anggota <?= h($ENTITY) ?></label>
                                    <input type="hidden" name="ukm_id" value="<?= $targetUkmId ?>">
                                    <input type="text" readonly value="<?= htmlspecialchars($targetName) ?>" class="w-full bg-blue-50 border border-blue-100 rounded-xl px-5 py-4 focus:ring-0 transition-all text-sm font-bold text-blue-800 cursor-not-allowed">
                                <?php else: ?>
                                    <label class="text-xs font-bold uppercase tracking-widest text-slate-500">Pilih <?= h($ENTITY) ?> / Organisasi</label>
                                    <select name="ukm_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-5 py-4 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm font-bold text-slate-800 appearance-none cursor-pointer" required>
                                        <option value="" disabled selected>-- Pilih <?= h($ENTITY) ?> yang ingin diikuti --</option>
                                        <?php if (isset($ukmList)): foreach ($ukmList as $u): ?>
                                            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nama']) ?> (<?= htmlspecialchars($u['singkatan']) ?>)</option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                <?php endif; ?>
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-slate-500">Nama Lengkap</label>
                                <input name="nama" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-5 py-4 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm font-bold text-slate-800" placeholder="Contoh: Budi Santoso" type="text" required/>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-xs font-bold uppercase tracking-widest text-slate-500">Nomor WhatsApp Aktif</label>
                                    <input name="no_wa" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-5 py-4 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm font-bold text-slate-800" placeholder="0812xxxxxx" type="tel" required/>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-bold uppercase tracking-widest text-slate-500">Alamat Email</label>
                                    <input name="email" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-5 py-4 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm font-bold text-slate-800" placeholder="contoh@gmail.com" type="email" required/>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-xs font-bold uppercase tracking-widest text-slate-500">Jurusan / Program Studi</label>
                                    <input name="jurusan" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-5 py-4 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm font-bold text-slate-800" placeholder="Teknik Informatika" type="text" required/>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-bold uppercase tracking-widest text-slate-500">Kelas / Semester</label>
                                    <input name="kelas" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-5 py-4 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm font-bold text-slate-800" placeholder="Contoh: TI-4A / Semester 4" type="text" required/>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-10 pt-6 border-t border-slate-100 flex justify-end">
                            <button type="button" onclick="nextStep(2)" class="px-8 py-4 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 active:scale-95 transition-all flex items-center gap-2">
                                Selanjutnya <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 2: Kuisioner Dinamis -->
                    <div id="step-2" class="p-8 lg:p-12 hidden transition-all duration-500">
                        <h3 class="text-2xl font-black text-slate-900 mb-1">Kuisioner Tambahan</h3>
                        <p class="text-sm text-slate-500 mb-8 border-b border-slate-100 pb-4">Pertanyaan berikut dikonfigurasi spesifik oleh <?= h($ENTITY) ?> tujuan Anda.</p>
                        
                        <div class="space-y-8">
                            <?php if (empty($questions)): ?>
                                <div class="bg-blue-50 border border-blue-200 text-blue-700 p-6 rounded-2xl flex items-center gap-4">
                                    <span class="material-symbols-outlined">info</span>
                                    <p class="text-sm font-bold">Tidak ada pertanyaan tambahan dari <?= h($ENTITY) ?> ini. Anda bisa langsung melewati langkah ini.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($questions as $idx => $q): ?>
                                <div class="space-y-3 p-6 bg-slate-50 border border-slate-200 rounded-2xl relative overflow-hidden">
                                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-amber-500"></div>
                                    <label class="block font-bold text-slate-900 flex justify-between items-center">
                                        <?= htmlspecialchars($q['text']) ?>
                                        <?php if (!empty($q['required'])): ?>
                                            <span class="text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded uppercase tracking-widest font-black">Wajib</span>
                                        <?php endif; ?>
                                    </label>
                                    <textarea name="jawaban_kuisioner[<?= $idx ?>]" class="w-full bg-white border border-slate-300 rounded-xl px-5 py-4 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm text-slate-800" rows="3" placeholder="Ketik jawaban Anda..." <?= (!empty($q['required']) ? 'required' : '') ?>></textarea>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mt-10 pt-6 border-t border-slate-100 flex justify-between">
                            <button type="button" onclick="prevStep(1)" class="px-6 py-4 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition-all flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">arrow_back</span> Kembali
                            </button>
                            <button type="button" onclick="nextStep(3)" class="px-8 py-4 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 active:scale-95 transition-all flex items-center gap-2">
                                Selanjutnya <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 3: Motivasi -->
                    <div id="step-3" class="p-8 lg:p-12 hidden transition-all duration-500">
                        <h3 class="text-2xl font-black text-slate-900 mb-1">Motivasi Bergabung</h3>
                        <p class="text-sm text-slate-500 mb-8 border-b border-slate-100 pb-4">Tuliskan alasan dan tujuan Anda bergabung.</p>
                        
                        <div class="space-y-8">
                            <div class="space-y-4">
                                <label class="text-xs font-bold uppercase tracking-widest text-slate-500">Mengapa Anda ingin bergabung? Apa ekspektasi Anda?</label>
                                <textarea name="alasan" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-5 py-4 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm font-bold text-slate-800" rows="6" placeholder="Ketik alasan Anda secara detail..." required></textarea>
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 mt-8 flex gap-4">
                                <div class="shrink-0 text-blue-600">
                                    <span class="material-symbols-outlined text-[32px]" style="font-variation-settings: 'FILL' 1;">info</span>
                                </div>
                                <div class="space-y-2">
                                    <h4 class="font-bold text-slate-900">Verifikasi Biometrik (IoT)</h4>
                                    <p class="text-sm text-slate-600 leading-relaxed">
                                        Data biometrik sidik jari Anda akan direkam via modul <b>ESP32</b> di basecamp untuk keperluan sistem absensi otomatis.
                                    </p>
                                </div>
                            </div>
                            
                            <label id="persetujuan-label" class="flex items-start gap-3 mt-4 cursor-pointer p-4 hover:bg-slate-50 rounded-xl transition-colors">
                                <input type="checkbox" name="persetujuan" id="persetujuan" class="mt-0.5 rounded border-slate-300 text-blue-600 focus:ring-blue-600 w-5 h-5 shadow-sm shrink-0">
                                <span class="text-sm font-bold text-slate-700">Saya setuju untuk memberikan data yang benar dan bersedia melakukan verifikasi biometrik jari. <span class="text-red-500">*</span></span>
                            </label>
                        </div>
                        
                        <div class="mt-10 pt-6 border-t border-slate-100 flex justify-between items-center">
                            <button type="button" onclick="prevStep(2)" class="px-6 py-4 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition-all flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">arrow_back</span> Kembali
                            </button>
                            <button type="submit" class="px-8 py-4 bg-emerald-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-200 hover:bg-emerald-700 active:scale-95 transition-all flex items-center gap-2">
                                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">how_to_reg</span> Ajukan Pendaftaran
                            </button>
                        </div>
                    </div>
                </form>

                <!-- STEP SUCCESS (Shown after redirect back if needed, but handled by controller usually) -->
                <?php if ($flash = getFlash()): ?>
                    <div class="p-8 lg:p-16 text-center flex flex-col items-center justify-center">
                        <div class="w-24 h-24 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mb-6">
                            <span class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 1;">verified</span>
                        </div>
                        <h3 class="text-3xl font-black text-slate-900 mb-4">Berhasil!</h3>
                        <p class="text-slate-600 max-w-md mx-auto leading-relaxed mb-8"><?= htmlspecialchars($flash['message']) ?></p>
                        <a href="index.php?page=katalog_ukm" class="px-8 py-4 bg-blue-600 text-white font-bold rounded-xl shadow-lg hover:bg-blue-700 transition-all">Kembali Ke Katalog <?= h($ENTITY) ?></a>
                    </div>
                <?php endif; ?>

                </form>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<!-- Navigation Script -->
<script src="assets/public/js/daftar-anggota.js"></script>
