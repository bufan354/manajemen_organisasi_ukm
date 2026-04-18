<main class="flex-1 p-8 min-h-[calc(100vh-64px-112px)] bg-surface-container-low">
    <?php 
        $isSA = (Session::get('admin_role') === 'superadmin');
        $selUkm = $selectedFilterUkm ?? '';
        $ukmList = $ukmList ?? [];
        $needUkmSelect = $isSA && !is_numeric($selUkm);
    ?>
    <!-- Content Header & Actions -->
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-1">
            <h2 class="text-3xl font-bold tracking-tight text-on-surface">Manajemen Anggota</h2>
            <p class="text-on-surface-variant body-md">Kelola seluruh data anggota, sidik jari biometrik, dan hak akses sistem.</p>
        </div>
        <?php if (!isset($_SESSION['is_active_periode']) || $_SESSION['is_active_periode']): ?>
        <div class="flex flex-wrap items-center gap-3">
            <button class="flex items-center gap-2 px-5 py-2.5 bg-white text-on-surface-variant font-semibold text-sm rounded-xl border border-outline-variant hover:bg-surface transition-all active:scale-95">
                <span class="material-symbols-outlined text-[20px]" data-icon="upload_file">upload_file</span>
                Import CSV
            </button>
            <button class="flex items-center gap-2 px-5 py-2.5 bg-white text-on-surface-variant font-semibold text-sm rounded-xl border border-outline-variant hover:bg-surface transition-all active:scale-95">
                <span class="material-symbols-outlined text-[20px]" data-icon="download">download</span>
                Export
            </button>
            <a href="index.php?page=verifikasi_pendaftar" class="flex items-center gap-2 px-5 py-2.5 bg-orange-50 text-orange-700 font-semibold text-sm rounded-xl border border-orange-200 hover:bg-orange-100 transition-all active:scale-95">
                <span class="material-symbols-outlined text-[20px]" data-icon="how_to_reg">how_to_reg</span>
                Periksa Pendaftar
            </a>
            <?php if (!$needUkmSelect): ?>
            <a href="index.php?page=tambah_anggota<?= $isSA && $selUkm ? '&ukm_id=' . $selUkm : '' ?>" class="flex items-center gap-2 px-6 py-2.5 bg-primary text-white font-semibold text-sm rounded-xl shadow-lg shadow-primary/20 hover:bg-primary-container transition-all active:scale-95">
                <span class="material-symbols-outlined text-[20px]" data-icon="person_add">person_add</span>
                Tambah Anggota
            </a>
            <?php else: ?>
            <button disabled class="cursor-not-allowed opacity-50 flex items-center gap-2 px-6 py-2.5 bg-primary text-white font-semibold text-sm rounded-xl shadow-lg shadow-primary/20">
                <span class="material-symbols-outlined text-[20px]" data-icon="person_add">person_add</span>
                Tambah Anggota
            </button>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <?= renderFlash() ?>

    <!-- Filter & Search Bento Card -->
    <div class="bg-surface-container-lowest rounded-2xl p-6 mb-6 shadow-[0_12px_40px_rgba(25,28,30,0.04)] flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-[300px] relative">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline text-[20px]" data-icon="search">search</span>
            <input class="w-full pl-12 pr-4 py-3 bg-surface-container rounded-xl border-none focus:ring-2 focus:ring-primary/20 text-sm placeholder:text-outline" placeholder="Cari berdasarkan nama, email, atau UID Card..." type="text"/>
        </div>
        <div class="flex items-center gap-4">
            <?php if ($isSA && !empty($ukmList)): ?>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-outline uppercase tracking-wider">UKM:</span>
                <select onchange="window.location.href='index.php?page=anggota&ukm_id='+this.value" 
                    class="bg-surface-container border-none rounded-xl text-sm font-medium py-3 px-4 focus:ring-2 focus:ring-primary/20 cursor-pointer">
                    <option value="">— Pilih UKM —</option>
                    <?php foreach ($ukmList as $u): ?>
                    <option value="<?= $u['id'] ?>" <?= (isset($selUkm) && $selUkm == $u['id']) ? 'selected' : '' ?>><?= htmlspecialchars($u['singkatan'] ?? $u['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-outline uppercase tracking-wider">Status:</span>
                <select class="bg-surface-container border-none rounded-xl text-sm font-medium py-3 px-4 focus:ring-2 focus:ring-primary/20 cursor-pointer">
                    <option>Semua Status</option>
                    <option>Aktif</option>
                    <option>Non-Aktif</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Table -->
    <?php if ($needUkmSelect): ?>
    <!-- Superadmin: no UKM selected yet -->
    <div class="bg-surface-container-lowest rounded-2xl shadow-[0_12px_40px_rgba(25,28,30,0.04)] flex flex-col items-center justify-center py-24 gap-4">
        <div class="w-20 h-20 rounded-2xl bg-primary/10 flex items-center justify-center mb-2">
            <span class="material-symbols-outlined text-primary text-4xl" style="font-variation-settings: 'FILL' 1;">hub</span>
        </div>
        <h3 class="text-xl font-bold text-on-surface">Pilih UKM Terlebih Dahulu</h3>
        <p class="text-sm text-on-surface-variant text-center max-w-sm">Sebagai Super Admin, Anda perlu memilih UKM dari dropdown di atas untuk melihat dan mengelola daftar anggota UKM tersebut.</p>
        <?php if (!empty($ukmList)): ?>
        <div class="flex flex-wrap gap-2 mt-4 justify-center">
            <?php foreach (array_slice($ukmList, 0, 6) as $u): ?>
            <a href="index.php?page=anggota&ukm_id=<?= $u['id'] ?>" 
               class="px-4 py-2 bg-primary/10 text-primary font-bold text-xs rounded-xl hover:bg-primary hover:text-white transition-all border border-primary/20">
                <?= htmlspecialchars($u['singkatan'] ?? $u['nama']) ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="bg-surface-container-lowest rounded-2xl shadow-[0_12px_40px_rgba(25,28,30,0.04)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-high">
                    <tr>
                        <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-[0.1em]">ID</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-[0.1em]">Anggota</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-[0.1em]">UID Card</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-[0.1em]">Jabatan</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-[0.1em]">Status</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-[0.1em] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/15">
                    <?php if (empty($anggotaList)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-4xl text-outline mb-2 block">person_off</span>
                            Belum ada anggota terdaftar.
                        </td>
                    </tr>
                    <?php else: foreach ($anggotaList as $i => $a): ?>
                    <tr class="hover:bg-surface-container-low transition-colors group">
                        <td class="px-6 py-5 text-sm font-medium text-outline">#<?= str_pad($a['id'], 3, '0', STR_PAD_LEFT) ?></td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full overflow-hidden bg-slate-100 ring-2 ring-transparent group-hover:ring-primary/20 transition-all">
                                    <?php if (!empty($a['foto_path'])): ?>
                                        <img class="w-full h-full object-cover" alt="Member Avatar" src="<?= htmlspecialchars($a['foto_path']) ?>"/>
                                    <?php else: ?>
                                        <div class="w-full h-full bg-primary-container flex items-center justify-center text-white font-bold text-sm">
                                            <?= strtoupper(substr($a['nama'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-on-surface"><?= htmlspecialchars($a['nama']) ?></p>
                                    <p class="text-xs text-on-surface-variant"><?= htmlspecialchars($a['email'] ?? '-') ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <?php if (!empty($a['fingerprint_id'])): ?>
                                <code class="px-2 py-1 bg-surface-container rounded text-xs font-mono font-bold text-success/80 flex items-center gap-1 w-max">
                                    <span class="material-symbols-outlined text-[14px]">fingerprint</span>
                                    Terdaftar
                                </code>
                            <?php else: ?>
                                <span class="text-xs text-outline italic">Belum assign</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-5">
                            <?php
                                $jabatan = $a['jabatan'] ?? 'Anggota';
                                $jabatanClass = ($jabatan === 'Ketua') 
                                    ? 'bg-primary-fixed text-on-primary-fixed-variant' 
                                    : 'bg-surface-container-highest text-on-surface-variant';
                            ?>
                            <span class="px-3 py-1 <?= $jabatanClass ?> rounded-full text-[11px] font-bold uppercase tracking-wider"><?= htmlspecialchars($jabatan) ?></span>
                        </td>
                        <td class="px-6 py-5">
                            <?php $isAktif = ($a['status'] ?? 'aktif') === 'aktif'; ?>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full <?= $isAktif ? 'bg-secondary' : 'bg-error' ?>"></span>
                                <span class="text-sm font-semibold <?= $isAktif ? 'text-secondary' : 'text-error' ?>"><?= $isAktif ? 'Aktif' : 'Non-Aktif' ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <?php if (!isset($_SESSION['is_active_periode']) || $_SESSION['is_active_periode']): ?>
                            <div class="flex items-center justify-end gap-2">
                                <?php if (!empty($a['fingerprint_id'])): ?>
                                <button onclick="hapusSidikJari(<?= $a['id'] ?>)" class="p-2 text-outline hover:text-red-500 hover:bg-red-50 rounded-lg transition-all flex items-center justify-center" title="Hapus Sidik Jari dari Sensor">
                                    <span class="material-symbols-outlined text-[20px]">fingerprint_off</span>
                                </button>
                                <?php endif; ?>
                                <a href="index.php?page=edit_anggota&id=<?= $a['id'] ?>" class="p-2 text-outline hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all flex items-center justify-center" title="Edit Member">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </a>
                                <button onclick="openDeleteModal('<?= addslashes($a['nama']) ?>', <?= $a['id'] ?>)" class="p-2 text-outline hover:text-error hover:bg-error-container rounded-lg transition-all" title="Delete">
                                    <span class="material-symbols-outlined text-[20px]" data-icon="delete">delete</span>
                                </button>
                            </div>
                            <?php else: ?>
                            <span class="text-xs text-outline italic">Read-Only</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="px-6 py-4 flex items-center justify-between border-t border-outline-variant/15">
            <p class="text-xs font-bold text-outline uppercase tracking-widest">Menampilkan <?= count($anggotaList ?? []) ?> Anggota</p>
        </div>
    </div>
    <?php endif; ?>
</main>

<!-- Delete Confirmation Modal (Native JS) -->
<div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="relative bg-white rounded-2xl p-8 max-w-sm w-full shadow-2xl overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="deleteModalContent">
        <div class="w-16 h-16 rounded-full bg-red-100 text-red-600 flex items-center justify-center mb-6 mx-auto">
            <span class="material-symbols-outlined text-[32px]">warning</span>
        </div>
        <h3 class="text-2xl font-black text-slate-900 text-center mb-2">Hapus Anggota?</h3>
        <p class="text-slate-500 text-center text-sm leading-relaxed mb-8">
            Apakah Anda yakin ingin mencabut hak akses IoT dan menghapus data <strong id="deleteMemberName" class="text-slate-800"></strong>? Tindakan ini <strong>permanen</strong>.
        </p>
        <form action="index.php?action=anggota_delete" method="POST" class="flex gap-4">
    <?= csrf_field() ?>
            
            <input type="hidden" name="id" id="deleteTargetId">
            <button type="button" onclick="closeDeleteModal()" class="flex-1 py-3 px-4 bg-slate-100 text-slate-600 font-bold text-sm rounded-xl hover:bg-slate-200 transition-colors">Batal</button>
            <button type="submit" class="flex-1 py-3 px-4 bg-red-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-red-200 hover:bg-red-700 transition-colors">Ya, Hapus</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function openDeleteModal(name, id) {
        document.getElementById('deleteMemberName').innerText = name;
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

    // FINGERPRINT DELETION LOGIC (GLOBAL DEVICE)
    let isWaitingESP = false;
    let pollInterval = null;

    function hapusSidikJari(anggotaId) {
        if (isWaitingESP) {
            Swal.fire('Tunggu!', 'Sedang ada proses sinkronisasi fingerprint yang berjalan.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Hapus Sidik Jari?',
            text: "Data sidik jari akan dihapus secara permanen dari ESP32 serta database. Pastikan ESP32 dalam keadaan AKTIF.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                mulaiPenghapusanESP32(anggotaId);
            }
        });
    }

    function mulaiPenghapusanESP32(anggotaId) {
        isWaitingESP = true;
        
        let formData = new FormData();
        formData.append('anggota_id', anggotaId);
        formData.append('csrf_token', '<?= csrf_token() ?>');

        fetch('index.php?action=fingerprint_set_delete', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                Swal.fire({
                    title: 'Memproses ke Alat!',
                    html: `Menunggu ESP32 menghapus dari sensor...<br><span class="text-xs text-slate-500">Jangan matikan alat. Waktu maksimal 2 menit.</span>`,
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                        
                        const startTime = Math.floor(Date.now() / 1000);
                        
                        pollInterval = setInterval(() => {
                            fetch(`index.php?action=fingerprint_check_status&id=${anggotaId}&since=${startTime}`)
                            .then(r => r.json())
                            .then(res => {
                                if(res.updated) {
                                    clearInterval(pollInterval);
                                    isWaitingESP = false;
                                    
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: 'Sidik jari sukses dicabut dari database & sensor.',
                                        confirmButtonText: 'OK'
                                    }).then(() => location.reload());
                                }
                            });
                        }, 2000); // Tembak poll ESP tiap 2 detik
                    }
                });
            } else {
                isWaitingESP = false;
                Swal.fire('Gagal', data.message || 'Terjadi kesalahan sistem.', 'error');
            }
        })
        .catch(err => {
            isWaitingESP = false;
            Swal.fire('Error', 'Gagal menghubungi server.', 'error');
        });
    }
</script>
