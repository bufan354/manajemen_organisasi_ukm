<?php $adm = $admin ?? []; ?>
<main class="flex-1 p-8 min-h-[calc(100vh-64px-112px)] bg-surface-container-low">
    <div class="mb-8 flex items-center gap-4">
        <a href="index.php?page=kelola_admin" class="w-10 h-10 rounded-full flex items-center justify-center bg-white border border-outline-variant hover:bg-surface transition-colors">
            <span class="material-symbols-outlined text-outline">arrow_back</span>
        </a>
        <div>
            <h2 class="text-3xl font-bold tracking-tight text-on-surface">Edit Data Admin</h2>
            <p class="text-on-surface-variant body-md">Perbarui hak akses, ruang lingkup, atau identitas staff administrator.</p>
        </div>
    </div>

    <?= renderFlash() ?>

    <div class="max-w-4xl bg-surface-container-lowest rounded-2xl shadow-[0_12px_40px_rgba(25,28,30,0.04)] p-8">
        <form action="index.php?action=admin_update" method="POST" enctype="multipart/form-data" class="space-y-6">
    <?= csrf_field() ?>
            
            <input type="hidden" name="id" value="<?= $adm['id'] ?? '' ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Personal Info -->
                <div class="col-span-2 space-y-2">
                    <label class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Nama Lengkap</label>
                    <input name="nama" class="w-full bg-surface-container-highest/40 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 transition-all font-bold text-sm text-slate-800" value="<?= htmlspecialchars($adm['nama'] ?? '') ?>" type="text" required/>
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Alamat Email</label>
                    <input name="email" class="w-full bg-surface-container-highest/40 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 transition-all text-sm text-slate-800" value="<?= htmlspecialchars($adm['email'] ?? '') ?>" type="email" required/>
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Set Ulang Password (Opsional)</label>
                    <input name="password" class="w-full bg-surface-container-highest/40 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 transition-all text-sm text-slate-800 placeholder:text-slate-400" placeholder="Kosongkan jika tidak diganti" type="password"/>
                </div>

                <!-- Access Level -->
                <div class="space-y-2">
                    <label class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Role Akun</label>
                    <?php $role = $adm['role'] ?? 'admin'; ?>
                    <select name="role" class="w-full bg-surface-container-highest/40 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 transition-all text-sm text-slate-800 cursor-pointer">
                        <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin <?= h($ENTITY) ?></option>
                        <option value="superadmin" <?= $role === 'superadmin' ? 'selected' : '' ?>>Super Admin</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant"><?= h($ENTITY) ?> yang Dikelola</label>
                    <select name="ukm_id" id="select_ukm" onchange="updatePeriodeOptions()" class="w-full bg-surface-container-highest/40 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 transition-all text-sm text-slate-800 cursor-pointer">
                        <option value="">-- Pilih <?= h($ENTITY) ?> (Jika Admin <?= h($ENTITY) ?>) --</option>
                        <?php if (isset($ukmList)): foreach ($ukmList as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= (isset($adm['ukm_id']) && $adm['ukm_id'] == $u['id']) ? 'selected' : '' ?>><?= htmlspecialchars($u['singkatan'] ?? $u['nama']) ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                    <p class="text-[10px] text-slate-500 mt-1">Hanya berlaku untuk posisi Admin <?= h($ENTITY) ?>.</p>
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Periode Kepengurusan</label>
                    <select name="periode_id" id="select_periode" class="w-full bg-surface-container-highest/40 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 transition-all text-sm text-slate-800 cursor-pointer">
                        <option value="">-- Pilih <?= h($ENTITY) ?> terlebih dahulu --</option>
                    </select>
                    <p class="text-[10px] text-slate-500 mt-1">Admin <?= h($ENTITY) ?> akan terikat pada periode ini.</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-8 mt-8 border-t border-outline-variant/20">
                <a href="index.php?page=kelola_admin" class="px-8 py-3 bg-surface-container-high text-on-surface font-bold rounded-xl hover:bg-surface-container-highest transition-colors">Batal</a>
                <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 active:scale-95 transition-all">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</main>

<script>
const periodeMap = <?= json_encode($periodeMap ?? []) ?>;
const currentPeriodeId = <?= json_encode($adm['periode_id'] ?? null) ?>;

function updatePeriodeOptions() {
    const ukmId = document.getElementById('select_ukm').value;
    const periodeSelect = document.getElementById('select_periode');
    periodeSelect.innerHTML = '<option value="">-- Pilih Periode --</option>';
    
    if (ukmId && periodeMap[ukmId]) {
        periodeMap[ukmId].forEach(function(p) {
            const opt = document.createElement('option');
            opt.value = p.id;
            opt.textContent = p.nama + ' (' + p.tahun_mulai + '-' + p.tahun_selesai + ')' + (p.is_active == 1 ? ' ★ Aktif' : '');
            if (currentPeriodeId && p.id == currentPeriodeId) opt.selected = true;
            periodeSelect.appendChild(opt);
        });
    } else {
        periodeSelect.innerHTML = '<option value="">-- Pilih UKM terlebih dahulu --</option>';
    }
}

// Auto-trigger on page load to populate periode for the pre-selected UKM
document.addEventListener('DOMContentLoaded', updatePeriodeOptions);
</script>
