<div class="p-8 max-w-7xl mx-auto w-full">
    <!-- Header Section -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="text-3xl font-extrabold text-on-surface tracking-tight">Kelola Admin</h2>
            <p class="text-on-surface-variant mt-1">Manajemen akses dan hak istimewa administrator sistem</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="flex items-center gap-2 px-5 py-2.5 bg-surface-container-lowest text-on-surface border border-outline-variant/15 font-medium rounded-xl hover:bg-surface-container-low transition-all">
                <span class="material-symbols-outlined text-xl">ios_share</span>
                Export
            </button>
            <a href="index.php?page=tambah_admin" class="flex items-center gap-2 px-5 py-2.5 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/20 hover:bg-primary-container active:scale-95 transition-all">
                <span class="material-symbols-outlined text-xl">add</span>
                Tambah Admin
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="grid grid-cols-12 gap-4 mb-6">
        <div class="col-span-12 lg:col-span-8 flex gap-4">
            <div class="relative group">
                <select class="appearance-none bg-white border-none px-6 py-3 pr-12 rounded-xl text-sm font-medium text-on-surface shadow-sm focus:ring-2 focus:ring-primary transition-all">
                    <option>Semua Role</option>
                    <option>Super Admin</option>
                    <option>Admin <?= h($ENTITY) ?></option>
                </select>
                <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">expand_more</span>
            </div>
            <div class="relative group">
                <select class="appearance-none bg-white border-none px-6 py-3 pr-12 rounded-xl text-sm font-medium text-on-surface shadow-sm focus:ring-2 focus:ring-primary transition-all">
                    <option>Semua Status</option>
                    <option>Aktif</option>
                    <option>Nonaktif</option>
                </select>
                <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">expand_more</span>
            </div>
        </div>
        <div class="col-span-12 lg:col-span-4 flex justify-end">
            <p class="text-xs font-bold text-on-surface-variant uppercase tracking-widest self-center">Menampilkan 1-8 dari 24 admin</p>
        </div>
    </div>

    <?= renderFlash() ?>
    <!-- Table Card -->
    <div class="bg-surface-container-lowest rounded-2xl shadow-xl shadow-slate-200/50 overflow-hidden border border-outline-variant/10">
        <table class="w-full text-left border-collapse">
            <thead class="bg-surface-container-high">
                <tr>
                    <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-widest">Nama</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-widest">Email</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-widest">Role</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-widest"><?= h($ENTITY) ?> yang Dikelola</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-widest text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-container">
                <?php if (empty($adminList)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-on-surface-variant">Belum ada admin terdaftar.</td>
                </tr>
                <?php else: foreach ($adminList as $admin): ?>
                <tr class="hover:bg-surface-container-low transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <?php if ($admin['foto_path']): ?>
                                <img class="w-10 h-10 rounded-full object-cover shadow-sm" alt="Admin Prfl" src="<?= $admin['foto_path'] ?>"/>
                            <?php else: ?>
                                <div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center text-white font-bold text-sm">
                                    <?= strtoupper(substr($admin['nama'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            <span class="font-semibold text-on-surface"><?= htmlspecialchars($admin['nama']) ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-on-surface-variant"><?= htmlspecialchars($admin['email']) ?></td>
                    <td class="px-6 py-4">
                        <?php if ($admin['role'] === 'superadmin'): ?>
                            <span class="px-3 py-1 bg-primary/10 text-primary text-[11px] font-bold uppercase rounded-full">Super Admin</span>
                        <?php else: ?>
                            <span class="px-3 py-1 bg-secondary-container/30 text-on-secondary-container text-[11px] font-bold uppercase rounded-full">Admin <?= h($ENTITY) ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-sm text-on-surface-variant">
                        <?= $admin['ukm_nama'] ? htmlspecialchars($admin['ukm_nama']) : '<span class="italic text-outline">Seluruh '.h($ENTITY).'</span>' ?>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-1">
                            <a href="index.php?page=edit_admin&id=<?= $admin['id'] ?>" class="p-2 text-slate-400 hover:text-primary hover:bg-primary/5 rounded-lg transition-all flex items-center justify-center" title="Edit Data"><span class="material-symbols-outlined text-[20px]">edit</span></a>
                            <?php if ($admin['id'] != Session::get('admin_id')): ?>
                            <button onclick="openResetModal('<?= addslashes($admin['nama']) ?>', <?= $admin['id'] ?>)" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="Reset Akses"><span class="material-symbols-outlined text-[20px]">key</span></button>
                            <button onclick="openDeleteModal('<?= addslashes($admin['nama']) ?>', <?= $admin['id'] ?>)" class="p-2 text-slate-400 hover:text-error hover:bg-error/5 rounded-lg transition-all" title="Hapus Admin"><span class="material-symbols-outlined text-[20px]">delete</span></button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
        
        <!-- Pagination Shell -->
        <div class="px-8 py-5 bg-surface-container-low flex justify-between items-center text-xs">
            <p class="font-medium text-on-surface-variant italic">Menampilkan seluruh admin sistem.</p>
        </div>
    </div>

    <!-- System Stats Preview -->
    <div class="mt-8 grid grid-cols-12 gap-6">
        <div class="col-span-12 lg:col-span-4 bg-primary-container text-white p-8 rounded-3xl relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="text-[11px] font-black uppercase tracking-[0.2em] opacity-80">Database Connected</h3>
                <p class="text-4xl font-black mt-4 tracking-tighter">Live</p>
                <p class="text-sm mt-1 opacity-90 font-medium">MySQL/MariaDB via PDO</p>
                
                <div class="mt-8 flex items-center gap-2">
                    <span class="material-symbols-outlined text-green-400" style="font-variation-settings: 'FILL' 1;">verified</span>
                    <span class="text-xs font-bold">Secure Environment (.env)</span>
                </div>
            </div>
            <span class="material-symbols-outlined absolute -right-8 -bottom-8 text-[180px] opacity-10 rotate-12">database</span>
        </div>
        
        <div class="col-span-12 lg:col-span-8 bg-surface-container-highest/30 backdrop-blur-md p-8 rounded-3xl flex flex-col justify-center border border-surface-container">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-on-surface">Backend Architecture</h3>
                <span class="text-xs font-black uppercase tracking-widest text-primary">OOP Pattern</span>
            </div>
            <div class="space-y-4">
                <div class="flex items-center gap-4 text-sm">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    <span class="text-on-surface font-medium">Automatic Image Cleanup (unlink() on Delete)</span>
                </div>
                <div class="flex items-center gap-4 text-sm">
                    <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                    <span class="text-on-surface font-medium">Singleton Database Driver with Credentials in .env</span>
                </div>
                <div class="flex items-center gap-4 text-sm">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    <span class="text-on-surface font-medium">Separation of Concerns: Models, Controllers, Helpers</span>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

<!-- Modal: Delete Confirmation -->
<div id="deleteModal" class="hidden fixed inset-0 z-[110] flex items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="relative bg-white rounded-2xl p-8 max-w-sm w-full shadow-2xl overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="deleteModalContent">
        <div class="w-16 h-16 rounded-full bg-red-100 text-red-600 flex items-center justify-center mb-6 mx-auto">
            <span class="material-symbols-outlined text-[32px]">no_accounts</span>
        </div>
        <h3 class="text-2xl font-black text-slate-900 text-center mb-2">Hapus Akses Admin?</h3>
        <p class="text-slate-500 text-center text-sm leading-relaxed mb-8">
            Apakah Anda yakin ingin menghapus akses admin <strong id="deleteTargetName" class="text-slate-800"></strong>? File foto akan otomatis dihapus dari server.
        </p>
        <form action="index.php?action=admin_delete" method="POST" class="flex gap-4">
    <?= csrf_field() ?>
            <input type="hidden" name="id" id="deleteTargetId">
            <button type="button" onclick="closeDeleteModal()" class="flex-1 py-3 px-4 bg-slate-100 text-slate-600 font-bold text-sm rounded-xl hover:bg-slate-200 transition-colors">Batal</button>
            <button type="submit" class="flex-1 py-3 px-4 bg-red-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-red-200 hover:bg-red-700 transition-colors">Ya, Hapus</button>
        </form>
    </div>
</div>

<script>
    function openDeleteModal(name, id) {
        document.getElementById('deleteTargetName').innerText = name;
        document.getElementById('deleteTargetId').value = id;
        const modal = document.getElementById('deleteModal');
        const content = document.getElementById('deleteModalContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
        }, 10);
    }

    function closeDeleteModal() {
        const content = document.getElementById('deleteModalContent');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            document.getElementById('deleteModal').classList.add('hidden');
        }, 300);
    }

    function openResetModal(name, id) {
        document.getElementById('resetTargetName').innerText = name;
        document.getElementById('resetTargetId').value = id;
        const modal = document.getElementById('resetModal');
        const content = document.getElementById('resetModalContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
        }, 10);
    }

    function closeResetModal() {
        const content = document.getElementById('resetModalContent');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            document.getElementById('resetModal').classList.add('hidden');
        }, 300);
    }
</script>

<!-- Modal: Reset Access Confirmation -->
<div id="resetModal" class="hidden fixed inset-0 z-[110] flex items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeResetModal()"></div>
    <div class="relative bg-white rounded-2xl p-8 max-w-md w-full shadow-2xl overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="resetModalContent">
        <div class="w-16 h-16 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mb-6 mx-auto">
            <span class="material-symbols-outlined text-[32px]">lock_reset</span>
        </div>
        <h3 class="text-2xl font-black text-slate-900 text-center mb-2">Reset Akses Admin</h3>
        <p class="text-slate-500 text-center text-sm leading-relaxed mb-6">
            Anda akan mengatur ulang kredensial untuk <strong id="resetTargetName" class="text-slate-800"></strong>. Tindakan ini akan dicatat dalam log keamanan.
        </p>
        
        <form action="index.php?action=admin_reset_access" method="POST" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="target_id" id="resetTargetId">
            
            <div class="space-y-2">
                <label class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Password Baru Admin</label>
                <input type="password" name="new_password" placeholder="Kosongkan jika hanya reset 2FA" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 text-sm">
            </div>

            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                <input type="checkbox" name="reset_2fa" value="1" id="reset_2fa_chk" class="w-4 h-4 text-primary rounded border-slate-300">
                <label for="reset_2fa_chk" class="text-sm font-medium text-slate-700 cursor-pointer">Nonaktifkan 2FA (TOTP)</label>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <label class="text-[11px] font-bold uppercase tracking-widest text-primary mb-2 block">Konfirmasi Password Anda (Superadmin)</label>
                <input type="password" name="super_password" required placeholder="Masukkan password Anda untuk konfirmasi" class="w-full bg-primary/5 border-primary/20 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 text-sm font-bold">
                <p class="text-[10px] text-slate-400 mt-2 italic">* Wajib diisi untuk memvalidasi identitas Superadmin.</p>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="button" onclick="closeResetModal()" class="flex-1 py-3 px-4 bg-slate-100 text-slate-600 font-bold text-sm rounded-xl hover:bg-slate-200 transition-colors">Batal</button>
                <button type="submit" class="flex-1 py-3 px-4 bg-amber-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-amber-200 hover:bg-amber-700 transition-colors">Proses Reset</button>
            </div>
        </form>
    </div>
</div>
