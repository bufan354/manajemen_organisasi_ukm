<?php $kp = $kepengurusan ?? []; ?>
<main class="flex-1 p-8 min-h-[calc(100vh-64px-112px)] bg-surface-container-low">
    <div class="mb-8 flex items-center gap-4">
        <a href="index.php?page=kepengurusan" class="w-10 h-10 rounded-full flex items-center justify-center bg-white border border-outline-variant hover:bg-surface transition-colors">
            <span class="material-symbols-outlined text-outline">arrow_back</span>
        </a>
        <div>
            <h2 class="text-3xl font-bold tracking-tight text-on-surface">Edit Arsip Kepengurusan</h2>
            <p class="text-on-surface-variant body-md">Perbarui data detail kabinet lama atau yang sedang berjalan.</p>
        </div>
    </div>

    <?= renderFlash() ?>

    <div class="max-w-4xl bg-surface-container-lowest rounded-2xl shadow-[0_12px_40px_rgba(25,28,30,0.04)] p-8">
        <form action="index.php?action=kepengurusan_update" method="POST" enctype="multipart/form-data" class="space-y-6">
    <?= csrf_field() ?>
            
            <input type="hidden" name="id" value="<?= $kp['id'] ?? '' ?>">
            <div class="grid grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Tahun Mulai</label>
                    <input name="tahun_mulai" class="w-full bg-surface-container-highest/40 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 transition-all font-bold text-sm text-slate-800" value="<?= htmlspecialchars($kp['tahun_mulai'] ?? '') ?>" type="number" required/>
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Tahun Selesai</label>
                    <input name="tahun_selesai" class="w-full bg-surface-container-highest/40 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 transition-all font-bold text-sm text-slate-800" value="<?= htmlspecialchars($kp['tahun_selesai'] ?? '') ?>" type="number" required/>
                </div>
            </div>
            
            <div class="space-y-2">
                <label class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Nama Kabinet</label>
                <input name="nama_kabinet" class="w-full bg-surface-container-highest/40 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 transition-all font-bold text-sm text-slate-800" value="<?= htmlspecialchars($kp['nama_kabinet'] ?? '') ?>" type="text" required/>
            </div>
            
            <div class="space-y-2">
                <label class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Deskripsi Singkat</label>
                <textarea name="deskripsi" class="w-full bg-surface-container-highest/40 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 transition-all text-sm text-slate-800" rows="4" required><?= htmlspecialchars($kp['deskripsi'] ?? '') ?></textarea>
            </div>
            
            <div class="space-y-2">
                <label class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Ganti Dokumentasi (Foto/PDF)</label>
                <div class="border-2 border-dashed border-outline-variant bg-surface-container-lowest hover:bg-surface-container-low rounded-xl p-8 flex flex-col items-center justify-center text-center transition-all cursor-pointer" onclick="document.getElementById('dokumen-input').click()">
                    <input type="file" name="dokumen" id="dokumen-input" accept="image/*,.pdf" class="hidden"/>
                    <span class="material-symbols-outlined text-4xl text-outline mb-2">cloud_upload</span>
                    <p class="text-sm font-bold text-slate-700">Klik untuk upload atau drag and drop</p>
                    <p class="text-xs text-on-surface-variant mt-1">Kosongkan jika tidak ada perubahan. PNG, JPG, PDF (Maks. 10MB)</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-8 mt-8 border-t border-outline-variant/20">
                <a href="index.php?page=kepengurusan" class="px-8 py-3 bg-surface-container-high text-on-surface font-bold rounded-xl hover:bg-surface-container-highest transition-colors">Batal</a>
                <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 active:scale-95 transition-all">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</main>
