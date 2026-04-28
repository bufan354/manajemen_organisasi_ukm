<?php
?>

<div class="p-6 max-w-4xl mx-auto space-y-8">
    <!-- Superadmin UKM Selector -->
    <?php if ($isSuperAdmin): ?>
        <div class="mb-8 bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 rounded-2xl flex items-center justify-center text-emerald-600">
                    <span class="material-symbols-outlined text-3xl">corporate_fare</span>
                </div>
                <div>
                    <h2 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">Kelola Inventaris</h2>
                    <p class="text-xs text-slate-500 font-medium">Pilih UKM/HMP untuk mengelola inventaris mereka.</p>
                </div>
            </div>
            <div class="w-full md:w-72">
                <select onchange="window.location.href='index.php?page=master_barang&ukm_id='+this.value" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-emerald-500 font-bold transition-all cursor-pointer text-slate-700 dark:text-white shadow-inner">
                    <option value="0" disabled <?= $ukm_id === 0 ? 'selected' : '' ?>>-- Pilih UKM/Organisasi --</option>
                    <?php 
                    $ukmList = (new Ukm())->getAll();
                    foreach ($ukmList as $u): 
                    ?>
                        <option value="<?= $u['id'] ?>" <?= $ukm_id == $u['id'] ? 'selected' : '' ?>><?= h($u['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($isSuperAdmin && $ukm_id === 0): ?>
        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 p-8 rounded-[2.5rem] flex items-center gap-6">
            <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-800 rounded-3xl flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                <span class="material-symbols-outlined text-3xl">info</span>
            </div>
            <div>
                <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Pilih <?= h($ENTITY) ?> Terlebih Dahulu</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Silakan pilih UKM/HMP melalui dropdown di atas untuk mengelola inventaris barang.</p>
            </div>
        </div>
    <?php else: ?>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-3xl text-emerald-600">inventory_2</span>
                Master Barang
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Daftar inventaris barang milik organisasi untuk peminjaman.</p>
        </div>
        
        <button onclick="openAddModal()" class="flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm transition-all shadow-lg shadow-emerald-500/20">
            <span class="material-symbols-outlined text-sm">add</span>
            Tambah Barang
        </button>
    </div>

    <!-- Inventory Table -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/50 border-bottom border-slate-200 dark:border-slate-800">
                    <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-400 w-20">No</th>
                    <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-400">Nama Barang</th>
                    <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-400">Satuan</th>
                    <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-400 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-slate-400 italic">Belum ada data barang.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $idx => $item): ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4 text-sm text-slate-400"><?= $idx + 1 ?></td>
                            <td class="px-6 py-4 font-bold text-slate-900 dark:text-white"><?= h($item['nama_barang']) ?></td>
                            <td class="px-6 py-4 text-sm text-slate-500 uppercase tracking-widest font-black"><?= h($item['satuan'] ?? 'Pcs') ?></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="openEditModal(<?= h(json_encode($item)) ?>)" class="p-2 text-slate-400 hover:text-emerald-600 transition-all">
                                        <span class="material-symbols-outlined text-xl">edit</span>
                                    </button>
                                    <form action="index.php?action=barang_delete" method="POST" onsubmit="return confirm('Hapus barang ini?')" class="inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                        <button type="submit" class="p-2 text-slate-300 hover:text-red-600 transition-all">
                                            <span class="material-symbols-outlined text-xl">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Form -->
<div id="barangModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] w-full max-w-md p-8 shadow-2xl border border-slate-200 dark:border-slate-800 scale-95 transition-all duration-300" id="modalContent">
        <div class="flex items-center justify-between mb-8">
            <h2 id="modalTitle" class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Tambah Barang</h2>
            <button onclick="closeModal()" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <span class="material-symbols-outlined text-slate-400">close</span>
            </button>
        </div>

        <form id="barangForm" action="index.php?action=barang_store" method="POST" class="space-y-6">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="item_id">
            
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Nama Barang</label>
                <input type="text" name="nama_barang" id="nama_barang" required placeholder="Contoh: Proyektor EPSON EB-X100" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-emerald-500 transition-all font-bold">
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Satuan</label>
                <input type="text" name="satuan" id="satuan" required placeholder="Cth: Pcs, Unit, Set, Lembar" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-emerald-500 transition-all font-bold">
            </div>

            <button type="submit" class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-black text-sm uppercase tracking-widest transition-all shadow-xl shadow-emerald-500/20 active:scale-95">
                Simpan Barang
            </button>
        </form>
    </div>
    <?php endif; ?>
</div>

<script>
    const modal = document.getElementById('barangModal');
    const modalContent = document.getElementById('modalContent');
    
    function openAddModal() {
        document.getElementById('modalTitle').innerText = "Tambah Barang";
        document.getElementById('barangForm').action = "index.php?action=barang_store";
        document.getElementById('item_id').value = "";
        document.getElementById('nama_barang').value = "";
        document.getElementById('satuan').value = "Pcs";
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => modalContent.classList.remove('scale-95', 'opacity-0'), 10);
    }

    function openEditModal(item) {
        document.getElementById('modalTitle').innerText = "Edit Barang";
        document.getElementById('barangForm').action = "index.php?action=barang_update";
        document.getElementById('item_id').value = item.id;
        document.getElementById('nama_barang').value = item.nama_barang;
        document.getElementById('satuan').value = item.satuan || "Pcs";
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => modalContent.classList.remove('scale-95', 'opacity-0'), 10);
    }

    function closeModal() {
        modalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }, 200);
    }
</script>
