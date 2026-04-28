<?php
?>

<div class="p-6 max-w-6xl mx-auto space-y-8">
    <!-- Superadmin UKM Selector -->
    <?php if ($isSuperAdmin): ?>
        <div class="mb-8 bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 rounded-2xl flex items-center justify-center text-emerald-600">
                    <span class="material-symbols-outlined text-3xl">corporate_fare</span>
                </div>
                <div>
                    <h2 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight">Cetak Lampiran Organisasi</h2>
                    <p class="text-xs text-slate-500 font-medium">Pilih UKM/HMP untuk membuat lampiran peminjaman mereka.</p>
                </div>
            </div>
            <div class="w-full md:w-72">
                <select onchange="window.location.href='index.php?page=cetak_lampiran&ukm_id='+this.value" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-emerald-500 font-bold transition-all cursor-pointer text-slate-700 dark:text-white shadow-inner">
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
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Silakan pilih UKM/HMP melalui dropdown di atas untuk membuat lampiran peminjaman.</p>
            </div>
        </div>
    <?php else: ?>
    <div class="flex items-center gap-3">
        <div class="p-3 bg-emerald-500 rounded-2xl shadow-lg shadow-emerald-500/20">
            <span class="material-symbols-outlined text-white text-3xl">inventory_2</span>
        </div>
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Form Cetak & Simpan Lampiran</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Siapkan daftar barang inventaris untuk lampiran surat resmi.</p>
        </div>
    </div>

    <?= renderFlash() ?>

    <form id="formLampiran" action="index.php?action=lampiran_pinjam_store" method="POST" class="space-y-8">
        <?= csrf_field() ?>
        <input type="hidden" name="barang_json" id="barang_json">
        
        <!-- SECTION: META DATA (HEADER FORM) - Light Mode -->
        <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-10 border border-slate-200 dark:border-slate-800 shadow-xl shadow-slate-200/50 dark:shadow-none relative overflow-hidden group">
            <!-- Decoration -->
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-emerald-500/5 blur-[100px] rounded-full group-hover:bg-emerald-500/10 transition-all duration-700"></div>
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-end relative">
                <!-- Date Range -->
                <div class="lg:col-span-7 space-y-4">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.25em] ml-1">Tanggal Pelaksanaan</label>
                    <div class="flex flex-col sm:flex-row items-center gap-4">
                        <div class="relative flex-1 w-full">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-xl">calendar_today</span>
                            <input type="date" id="tgl_mulai" required onchange="updateDateString()"
                                   class="w-full bg-slate-50 dark:bg-slate-800/50 border-2 border-slate-100 dark:border-slate-700/50 hover:border-emerald-500/50 focus:border-emerald-500 rounded-2xl pl-12 pr-4 py-4 text-sm text-slate-900 dark:text-white transition-all outline-none">
                        </div>
                        <span class="text-slate-300 font-bold uppercase text-[10px] tracking-widest">Durasi</span>
                        <div class="relative flex-1 w-full">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-xl">timer</span>
                            <select id="durasi" required onchange="updateDateString()"
                                    class="w-full bg-slate-50 dark:bg-slate-800/50 border-2 border-slate-100 dark:border-slate-700/50 hover:border-emerald-500/50 focus:border-emerald-500 rounded-2xl pl-12 pr-4 py-4 text-sm text-slate-900 dark:text-white transition-all outline-none">
                                <?php for($i=1; $i<=7; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?> Hari</option>
                                <?php endfor; ?>
                                <option value="14">2 Minggu (14 Hari)</option>
                                <option value="30">1 Bulan (30 Hari)</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Date Preview Box -->
                    <div class="p-5 rounded-2xl bg-emerald-50 dark:bg-emerald-500/5 border border-emerald-100 dark:border-emerald-500/20 flex items-center gap-3">
                        <div class="w-1.5 h-8 bg-emerald-500 rounded-full"></div>
                        <input type="text" name="tanggal_kegiatan" id="tanggal_kegiatan_input" readonly 
                               placeholder="Pilih tanggal di atas..."
                               class="bg-transparent border-none p-0 text-sm font-bold text-emerald-600 dark:text-emerald-400 w-full focus:ring-0">
                    </div>
                </div>

                <!-- Event Name -->
                <div class="lg:col-span-5 space-y-4">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.25em] ml-1">Nama Acara / Kegiatan</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-xl">search</span>
                        <input type="text" name="nama_acara" required placeholder="Cari atau ketik nama acara..."
                               class="w-full bg-slate-50 dark:bg-slate-800/50 border-2 border-slate-100 dark:border-slate-700/50 hover:border-emerald-500/50 focus:border-emerald-500 rounded-2xl pl-12 pr-4 py-4 text-sm text-slate-900 dark:text-white transition-all outline-none placeholder:text-slate-400">
                    </div>
                    <button type="submit" class="w-full py-4 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] transition-all shadow-xl shadow-emerald-600/20 active:scale-[0.98]">
                        Generate & Arsipkan
                    </button>
                </div>
            </div>
        </div>

        <!-- SECTION: ITEMS SELECTION -->
        <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-10 border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-10">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-xl text-blue-600">
                        <span class="material-symbols-outlined">checklist</span>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 uppercase tracking-tight">Pilih Barang & Tentukan Jumlah</h2>
                </div>
                <div class="flex items-center gap-2 px-4 py-2 bg-slate-50 dark:bg-slate-800 rounded-full border border-slate-100 dark:border-slate-700">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total:</span>
                    <span id="total_items_count" class="text-sm font-black text-blue-600">0</span>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Barang</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <?php if (empty($items)): ?>
                    <div class="col-span-full text-center py-20 bg-slate-50 dark:bg-slate-800/40 rounded-[2rem] border border-dashed border-slate-200 dark:border-slate-700">
                        <span class="material-symbols-outlined text-5xl text-slate-200 dark:text-slate-300 mb-4">inventory_2</span>
                        <p class="text-sm text-slate-400 mb-4 italic">Belum ada barang di Master Inventaris.</p>
                        <a href="index.php?page=master_barang&ukm_id=<?= $ukm_id ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-xl font-bold text-xs hover:bg-blue-700 transition-all">
                            Tambah Barang Sekarang
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <div class="flex items-center justify-between p-6 bg-slate-50 dark:bg-slate-800/40 rounded-[2rem] border border-transparent hover:border-emerald-500/30 dark:hover:border-emerald-500/20 transition-all group hover:shadow-lg hover:shadow-emerald-500/5">
                            <div class="flex-1 min-w-0 mr-4">
                                <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200 truncate group-hover:text-emerald-500 transition-colors"><?= h($item['nama_barang']) ?></h4>
                                <p class="text-[10px] text-slate-400 uppercase tracking-widest font-black mt-1"><?= h($item['satuan'] ?? 'Pcs') ?></p>
                            </div>
                            
                            <div class="flex items-center gap-3 bg-white dark:bg-slate-900 rounded-2xl p-1.5 shadow-sm border border-slate-100 dark:border-slate-800">
                                <button type="button" onclick="adjustCount('item_<?= $item['id'] ?>', -1)" 
                                        class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 text-slate-300 hover:text-red-600 transition-all">
                                    <span class="material-symbols-outlined text-lg">remove</span>
                                </button>
                                <input type="number" 
                                       id="item_<?= $item['id'] ?>" 
                                       name="counts[<?= h($item['nama_barang']) ?>]" 
                                       value="0" min="0" 
                                       class="w-10 text-center bg-transparent border-none text-sm font-black text-slate-900 dark:text-white p-0 focus:ring-0 item-count-input"
                                       onchange="updateTotal()">
                                <button type="button" onclick="adjustCount('item_<?= $item['id'] ?>', 1)" 
                                        class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-900/20 text-slate-300 hover:text-emerald-600 transition-all">
                                    <span class="material-symbols-outlined text-lg">add</span>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </form>
    <?php endif; ?>
</div>

<script>
    function updateDateString() {
        const start = document.getElementById('tgl_mulai').value;
        const duration = parseInt(document.getElementById('durasi').value) || 1;
        const input = document.getElementById('tanggal_kegiatan_input');
        
        if (!start) {
            input.value = "";
            return;
        }
    
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        const d1 = new Date(start);
        
        // Calculate end date: Start Date + (Duration - 1)
        const d2 = new Date(d1);
        d2.setDate(d1.getDate() + (duration - 1));
        
        const f1 = d1.toLocaleDateString('id-ID', options);
        const f2 = d2.toLocaleDateString('id-ID', options);
        
        if (duration === 1) {
            input.value = f1;
        } else {
            input.value = `${f1} s.d ${f2}`;
        }
    }

    function adjustCount(id, val) {
        const input = document.getElementById(id);
        let current = parseInt(input.value) || 0;
        current += val;
        if (current < 0) current = 0;
        input.value = current;
        updateTotal();
    }

    function updateTotal() {
        const inputs = document.querySelectorAll('.item-count-input');
        let totalCount = 0;
        const selectedItems = [];

        inputs.forEach(input => {
            const count = parseInt(input.value) || 0;
            if (count > 0) {
                totalCount++;
                const name = input.getAttribute('name').match(/\[(.*?)\]/)[1];
                const parent = input.closest('.group');
                const satuan = parent.querySelector('p').innerText;
                selectedItems.push({ nama: name, jumlah: count, satuan: satuan });
            }
        });

        document.getElementById('total_items_count').innerText = totalCount;
        document.getElementById('barang_json').value = JSON.stringify(selectedItems);
    }

    document.getElementById('formLampiran').onsubmit = function() {
        updateTotal();
        const json = document.getElementById('barang_json').value;
        const tgl = document.getElementById('tanggal_kegiatan_input').value;

        if (!tgl) {
            alert('Silakan tentukan tanggal pelaksanaan.');
            return false;
        }
        if (json === '[]' || !json) {
            alert('Silakan pilih minimal 1 barang dengan jumlah > 0');
            return false;
        }
        return true;
    }
</script>

<style>
    input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(0);
        cursor: pointer;
    }
</style>
