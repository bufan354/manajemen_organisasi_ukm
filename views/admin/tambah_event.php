<main class="flex-1 p-8 min-h-[calc(100vh-64px-112px)] bg-surface-container-low">
    <!-- Header -->
    <div class="mb-8 flex items-center gap-4">
        <a href="index.php?page=event" class="w-10 h-10 rounded-full flex items-center justify-center bg-white border border-outline-variant hover:bg-surface transition-colors">
            <span class="material-symbols-outlined text-outline">arrow_back</span>
        </a>
        <div>
            <h2 class="text-3xl font-bold tracking-tight text-on-surface">Tambah Kegiatan Baru</h2>
            <p class="text-on-surface-variant body-md">Jadwalkan kegiatan baru untuk absensi IoT digital peserta.</p>
        </div>
    </div>

    <!-- Form Section -->
    <div class="max-w-4xl bg-surface-container-lowest rounded-2xl shadow-[0_12px_40px_rgba(25,28,30,0.04)] p-8">
        <form action="index.php?action=event_store" method="POST">
    <?= csrf_field() ?>
            
            <div class="space-y-6">
                <?php if (!empty($ukmList)): ?>
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">UKM Tujuan</label>
                    <select name="ukm_id" required class="w-full bg-surface-container-high/40 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary/20 text-sm font-medium cursor-pointer">
                        <option disabled selected value="">-- Pilih UKM --</option>
                        <?php foreach ($ukmList as $u): ?>
                            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['singkatan']) ?> - <?= htmlspecialchars($u['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                <!-- Deskripsi -->
                <div class="grid grid-cols-2 gap-6">
                    <div class="col-span-2 space-y-2">
                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Nama Kegiatan</label>
                        <input name="nama" class="w-full bg-surface-container-high/40 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary/20 transition-all font-bold text-sm text-slate-800" placeholder="Contoh: Webinar Cyber Security" type="text" required/>
                    </div>
                    <div class="col-span-2 space-y-2">
                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Deskripsi Singkat</label>
                        <textarea name="deskripsi" class="w-full bg-surface-container-high/40 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary/20 transition-all h-24 text-sm text-slate-800" placeholder="Jelaskan agenda dan tujuan kegiatan..." required></textarea>
                    </div>
                </div>

                <!-- Tipe Kegiatan -->
                <div class="space-y-4 pt-4 border-t border-outline-variant/10">
                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Pola Kegiatan</label>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <label class="flex items-center gap-3 p-4 bg-surface-container-high/20 border border-transparent rounded-xl cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 transition-all flex-1">
                            <input type="radio" name="is_routine" value="0" class="w-5 h-5 text-primary focus:ring-primary border-slate-300" onchange="toggleRoutine(false)" checked>
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm">Kegiatan Sekali Jalan</h4>
                                <p class="text-xs text-slate-500">Acara spesifik dengan tanggal & jam tetap.</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-4 bg-surface-container-high/20 border border-transparent rounded-xl cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 transition-all flex-1">
                            <input type="radio" name="is_routine" value="1" class="w-5 h-5 text-primary focus:ring-primary border-slate-300" onchange="toggleRoutine(true)">
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm">Rutinitas Mingguan</h4>
                                <p class="text-xs text-slate-500">Berulang setiap minggu di hari tertentu.</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Waktu Single Event (Default) -->
                <div id="single-event-panel" class="grid grid-cols-1 md:grid-cols-2 gap-6 transition-all duration-300 origin-top">
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Waktu Mulai</label>
                        <input name="waktu_mulai" class="w-full bg-surface-container-high/40 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary/20 text-sm text-slate-800 font-medium" type="datetime-local" required/>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Durasi Kegiatan</label>
                        <select name="durasi" class="w-full bg-surface-container-high/40 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary/20 text-sm text-slate-800 font-medium" required>
                            <option value="30">30 Menit</option>
                            <option value="60">1 Jam</option>
                            <option value="90">1.5 Jam</option>
                            <option value="120" selected>2 Jam</option>
                            <option value="150">2.5 Jam</option>
                            <option value="180">3 Jam</option>
                            <option value="240">4 Jam</option>
                            <option value="300">5 Jam</option>
                            <option value="360">6 Jam</option>
                            <option value="480">8 Jam</option>
                        </select>
                    </div>
                </div>

                <!-- Waktu Routine Event (Hidden Default) -->
                <div id="routine-event-panel" class="hidden space-y-6 transition-all duration-300 origin-top">
                    <!-- Hari Pilihan -->
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Pilih Hari Rutinitas (Bisa lebih dari satu)</label>
                        <div class="flex flex-wrap gap-3">
                            <?php 
                            $days = ['1'=>'Senin', '2'=>'Selasa', '3'=>'Rabu', '4'=>'Kamis', '5'=>'Jumat', '6'=>'Sabtu', '0'=>'Minggu'];
                            foreach($days as $val => $label): 
                            ?>
                            <label class="flex items-center gap-2 px-4 py-2 bg-surface-container-high/20 border border-slate-200 rounded-lg cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/10 transition-all">
                                <input type="checkbox" name="hari[]" value="<?= $val ?>" class="text-primary focus:ring-primary rounded border-slate-300 routine-input">
                                <span class="font-bold text-sm text-slate-700"><?= $label ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- Waktu Mulai & Berakhir -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Jam Mulai</label>
                            <input name="jam_mulai" class="w-full bg-surface-container-high/40 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary/20 text-sm text-slate-800 font-medium routine-input" type="time"/>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Durasi Pertemuan (Mingguan)</label>
                            <select name="durasi_rutin" class="w-full bg-surface-container-high/40 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary/20 text-sm text-slate-800 font-medium routine-input">
                                <option value="30">30 Menit</option>
                                <option value="60">1 Jam</option>
                                <option value="90">1.5 Jam</option>
                                <option value="120" selected>2 Jam</option>
                                <option value="150">2.5 Jam</option>
                                <option value="180">3 Jam</option>
                                <option value="240">4 Jam</option>
                                <option value="300">5 Jam</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Shared Lokasi & Status -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-outline-variant/10">
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Lokasi</label>
                        <input name="lokasi" class="w-full bg-surface-container-high/40 border-none rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary/20 text-sm text-slate-800 font-medium" placeholder="Ruangan / Online Link" type="text" required/>
                    </div>
                    <!-- Status -->
                    <div class="flex flex-col justify-end pb-2">
                        <label class="flex items-center gap-4 p-3 bg-surface-container-high/20 rounded-xl cursor-pointer">
                            <div class="relative inline-block w-10 h-6 align-middle select-none transition duration-200 ease-in">
                                <input class="toggle-checkbox absolute block w-4 h-4 rounded-full bg-white border-2 border-slate-200 appearance-none cursor-pointer left-1 top-1 transition-all checked:left-5 checked:bg-primary checked:border-primary" id="toggle" name="status_absensi" type="checkbox" value="1" checked/>
                                <label class="toggle-label block overflow-hidden h-6 rounded-full bg-slate-200 cursor-pointer" for="toggle"></label>
                            </div>
                            <span class="text-sm font-bold text-slate-700">Aktifkan Absensi Otomatis</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-4 pt-8 mt-8 border-t border-outline-variant/20">
                <a href="index.php?page=event" class="px-8 py-3 bg-surface-container-high text-on-surface font-bold rounded-xl hover:bg-surface-container-highest transition-colors">Batal</a>
                <button type="submit" class="px-8 py-3 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/20 hover:bg-primary-container transition-transform active:scale-95 border-none cursor-pointer">Simpan Kegiatan Baru</button>
            </div>
        </form>
    </div>
</main>

<script>
function toggleRoutine(isRoutine) {
    const singlePanel = document.getElementById('single-event-panel');
    const routinePanel = document.getElementById('routine-event-panel');
    const singleInputs = singlePanel.querySelectorAll('input[type="datetime-local"]');
    const routineInputs = document.querySelectorAll('.routine-input');

    if (isRoutine) {
        singlePanel.classList.add('hidden');
        routinePanel.classList.remove('hidden');
        singleInputs.forEach(i => i.removeAttribute('required'));
        routineInputs.forEach(i => i.setAttribute('required', 'required'));
        document.querySelectorAll('input[type="checkbox"][name="hari[]"]').forEach(i => i.removeAttribute('required'));
    } else {
        singlePanel.classList.remove('hidden');
        routinePanel.classList.add('hidden');
        singleInputs.forEach(i => i.setAttribute('required', 'required'));
        routineInputs.forEach(i => i.removeAttribute('required'));
    }
}

// Set min date to now for Once Events
function setMinDate() {
    const now = new Date();
    const tzOffset = now.getTimezoneOffset() * 60000;
    const localISOTime = (new Date(now - tzOffset)).toISOString().slice(0, 16);
    const dateInput = document.getElementsByName('waktu_mulai')[0];
    if (dateInput) {
        dateInput.setAttribute('min', localISOTime);
        dateInput.value = localISOTime;
    }
}

// init
toggleRoutine(false);
setMinDate();
</script>
