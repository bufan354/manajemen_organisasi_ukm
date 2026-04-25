<main class="flex-1 p-8 min-h-[calc(100vh-64px-112px)] bg-surface-container-low">
    <div class="mb-8 flex items-center gap-4">
        <a href="index.php?page=kelola_admin" class="w-10 h-10 rounded-full flex items-center justify-center bg-white border border-outline-variant hover:bg-surface transition-colors">
            <span class="material-symbols-outlined text-outline">arrow_back</span>
        </a>
        <div>
            <h2 class="text-3xl font-bold tracking-tight text-on-surface">Tambah Admin Baru</h2>
            <p class="text-on-surface-variant body-md">Beri hak akses khusus ke sistem pengelolaan bagi staf lain.</p>
        </div>
    </div>

    <?= renderFlash() ?>

    <div class="max-w-4xl bg-surface-container-lowest rounded-2xl shadow-[0_12px_40px_rgba(25,28,30,0.04)] p-8">
        <form action="index.php?action=admin_store" method="POST" enctype="multipart/form-data" class="space-y-6">
    <?= csrf_field() ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Personal Info -->
                <div class="col-span-2 space-y-2">
                    <label class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Nama Lengkap</label>
                    <input name="nama" class="w-full bg-surface-container-highest/40 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 transition-all font-bold text-sm text-slate-800" placeholder="Masukkan nama..." type="text" required/>
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Alamat Email</label>
                    <input name="email" class="w-full bg-surface-container-highest/40 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 transition-all text-sm text-slate-800" placeholder="contoh@iotabsensi.id" type="email" required/>
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Password Sementara</label>
                    <input name="password" class="w-full bg-surface-container-highest/40 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 transition-all text-sm text-slate-800" placeholder="Setidaknya 8 karakter" type="password" required/>
                </div>

                <!-- Access Level -->
                <div class="space-y-2">
                    <label class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Role Akun</label>
                    <select name="role" class="w-full bg-surface-container-highest/40 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 transition-all text-sm text-slate-800 cursor-pointer">
                        <option value="admin">Admin <?= h($ENTITY) ?></option>
                        <option value="superadmin">Super Admin</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant"><?= h($ENTITY) ?> yang Dikelola</label>
                    <select name="ukm_id" id="select_ukm" onchange="updatePeriodeOptions()" class="w-full bg-surface-container-highest/40 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 transition-all text-sm text-slate-800 cursor-pointer">
                        <option value="">-- Pilih <?= h($ENTITY) ?> (Jika Admin <?= h($ENTITY) ?>) --</option>
                        <?php if (isset($ukmList)): foreach ($ukmList as $u): ?>
                        <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['singkatan']) ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                    <p class="text-[10px] text-slate-500 mt-1">Hanya berlaku untuk posisi Admin <?= h($ENTITY) ?>.</p>
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Periode Kepengurusan</label>
                    <select name="periode_id" id="select_periode" class="w-full bg-surface-container-highest/40 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 transition-all text-sm text-slate-800 cursor-pointer">
                        <option value="">-- Pilih UKM terlebih dahulu --</option>
                    </select>
                    <p class="text-[10px] text-slate-500 mt-1">Admin UKM akan terikat pada periode ini. Data yang bisa dikelola sesuai periode yang dipilih.</p>
                </div>

                <div class="col-span-1 pt-4">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary" type="checkbox" checked/>
                        <span class="text-sm font-bold text-slate-700">Kirim instruksi login ke email</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-8 mt-8 border-t border-outline-variant/20">
                <a href="index.php?page=kelola_admin" class="px-8 py-3 bg-surface-container-high text-on-surface font-bold rounded-xl hover:bg-surface-container-highest transition-colors">Batal</a>
                <button type="submit" class="px-8 py-3 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/20 hover:bg-primary-container active:scale-95 transition-all">Simpan Akun</button>
            </div>
        </form>
    </div>
</main>

<script>
// Pre-loaded periode data per UKM (rendered server-side)
const periodeMap = <?= json_encode($periodeMap ?? []) ?>;

function updatePeriodeOptions() {
    const ukmId = document.getElementById('select_ukm').value;
    const periodeSelect = document.getElementById('select_periode');
    periodeSelect.innerHTML = '<option value="">-- Pilih Periode --</option>';
    
    if (ukmId && periodeMap[ukmId]) {
        periodeMap[ukmId].forEach(function(p) {
            const opt = document.createElement('option');
            opt.value = p.id;
            opt.textContent = p.nama + ' (' + p.tahun_mulai + '-' + p.tahun_selesai + ')' + (p.is_active == 1 ? ' ★ Aktif' : '');
            periodeSelect.appendChild(opt);
        });
    } else {
        periodeSelect.innerHTML = '<option value="">-- Pilih UKM terlebih dahulu --</option>';
    }
}
</script>
