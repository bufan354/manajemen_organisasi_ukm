<main class="flex-1 p-8 min-h-[calc(100vh-64px-112px)] bg-surface-container-low">
    <div class="mb-8 flex items-center gap-4">
        <a href="index.php?page=ukm" class="w-10 h-10 rounded-full flex items-center justify-center bg-white border border-outline-variant hover:bg-surface transition-colors">
            <span class="material-symbols-outlined text-outline">arrow_back</span>
        </a>
        <div>
            <h2 class="text-3xl font-bold tracking-tight text-on-surface">Daftarkan <?= h($ENTITY) ?> Baru</h2>
            <p class="text-on-surface-variant body-md">Buat entitas organisasi baru yang akan berdiri sendiri di portal IoT.</p>
        </div>
    </div>

    <div class="max-w-4xl bg-surface-container-lowest rounded-2xl shadow-[0_12px_40px_rgba(25,28,30,0.04)] p-8">
        <form action="index.php?action=ukm_store" method="POST" enctype="multipart/form-data" class="space-y-6">
            <?= csrf_field() ?>
            <!-- Branding -->
            <div class="flex flex-col md:flex-row gap-8 items-start mb-8 pb-8 border-b border-outline-variant/20">
                <div class="space-y-3">
                    <label class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant block">Logo Resmi <?= h($ENTITY) ?></label>
                    <div class="w-32 h-32 rounded-3xl border-2 border-dashed border-outline-variant bg-surface-container-lowest hover:bg-surface-container-low flex flex-col items-center justify-center cursor-pointer transition-all overflow-hidden relative group" onclick="document.getElementById('logo-input').click()">
                        <input type="file" name="logo" id="logo-input" accept="image/*" class="hidden"/>
                        <span class="material-symbols-outlined text-3xl text-outline mb-1 group-hover:scale-110 transition-transform">add_photo_alternate</span>
                        <span class="text-[10px] font-bold text-slate-500">Upload Logo</span>
                    </div>
                </div>
                <div class="flex-1 space-y-6 w-full">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Nama Lengkap <?= h($ENTITY) ?></label>
                            <input name="nama" class="w-full bg-surface-container-highest/40 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 transition-all font-bold text-sm text-slate-800" placeholder="Contoh: Unit Kegiatan Mahasiswa Robotika" type="text" required/>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Singkatan / Akronim</label>
                            <input name="singkatan" class="w-full bg-surface-container-highest/40 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 transition-all font-bold text-sm text-slate-800" placeholder="Contoh: UKM Robotika" type="text" required/>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Kategori Organisasi</label>
                        <select name="kategori" class="w-full bg-surface-container-highest/40 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 transition-all text-sm text-slate-800 cursor-pointer">
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Seni & Olahraga">Seni & Olahraga</option>
                            <option value="Teknologi & Sains">Teknologi & Sains</option>
                            <option value="Keagamaan">Keagamaan</option>
                            <option value="Sosial Kreatif">Sosial Kreatif</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Detail Info -->
            <div class="space-y-6">
                <div class="space-y-2">
                    <label class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Slogan / Visi Singkat</label>
                    <input name="slogan" class="w-full bg-surface-container-highest/40 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 transition-all text-sm text-slate-800" placeholder="Maju Terus Pantang Mundur" type="text"/>
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Deskripsi Profil <?= h($ENTITY) ?></label>
                    <textarea name="deskripsi" class="w-full bg-surface-container-highest/40 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 transition-all text-sm text-slate-800" rows="5" placeholder="Tuliskan latar belakang dan kegiatan utama UKM disini..." required></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-8 mt-8 border-t border-outline-variant/20">
                <a href="index.php?page=ukm" class="px-8 py-3 bg-surface-container-high text-on-surface font-bold rounded-xl hover:bg-surface-container-highest transition-colors">Batal</a>
                <button type="submit" class="px-8 py-3 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/20 hover:bg-primary-container active:scale-95 transition-all">Daftarkan <?= h($ENTITY) ?></button>
            </div>
        </form>
    </div>
</main>
