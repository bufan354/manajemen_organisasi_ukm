<main class="flex-1 p-8 min-h-[calc(100vh-64px)] bg-surface-container-low">
    <!-- Content Header & Actions -->
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-1">
            <h2 class="text-3xl font-bold tracking-tight text-on-surface">Verifikasi Pendaftar</h2>
            <p class="text-on-surface-variant body-md">Tinjau dan proses pendaftaran calon anggota <?= h($ENTITY) ?> yang baru masuk.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="index.php?page=anggota" class="flex items-center gap-2 px-5 py-2.5 bg-white text-on-surface-variant font-semibold text-sm rounded-xl border border-outline-variant hover:bg-surface transition-all active:scale-95">
                <span class="material-symbols-outlined text-[20px]" data-icon="arrow_back">arrow_back</span>
                Kembali ke Manajemen Anggota
            </a>
        </div>
    </div>
    
    <?= renderFlash() ?>

    <!-- Table -->
    <div class="bg-surface-container-lowest rounded-2xl shadow-[0_12px_40px_rgba(25,28,30,0.04)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-high">
                    <tr>
                        <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-[0.1em]">Calon Anggota</th>
                        <?php if (Session::get('admin_role') === 'superadmin'): ?>
                        <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-[0.1em]">Asal <?= h($ENTITY) ?></th>
                        <?php endif; ?>
                        <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-[0.1em]">Tanggal Daftar</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-[0.1em]">Status</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-[0.1em] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/15">
                    <?php if (empty($pendaftaranList)): ?>
                    <tr>
                        <td colspan="<?= Session::get('admin_role') === 'superadmin' ? '7' : '6' ?>" class="px-6 py-12 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-4xl text-outline mb-2 block">inbox</span>
                            Tidak ada data pendaftaran yang perlu diverifikasi.
                        </td>
                    </tr>
                    <?php else: foreach ($pendaftaranList as $p): ?>
                    <tr class="hover:bg-surface-container-low transition-colors group">
                        <td class="px-6 py-5">
                            <p class="text-sm font-bold text-on-surface"><?= htmlspecialchars($p['nama']) ?></p>
                            <?php if(!empty($p['alasan'])): ?>
                            <p class="text-[10px] text-slate-500 mt-1 line-clamp-1 max-w-[200px]" title="<?= htmlspecialchars($p['alasan']) ?>">
                                "<?= htmlspecialchars($p['alasan']) ?>"
                            </p>
                            <?php endif; ?>
                        </td>
                        <?php if (Session::get('admin_role') === 'superadmin'): ?>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-primary/10 text-primary text-[10px] font-bold uppercase rounded-full tracking-wider border border-primary/20">
                                <span class="material-symbols-outlined text-[12px]">domain</span>
                                <?= htmlspecialchars($p['ukm_nama'] ?? '-') ?>
                            </span>
                        </td>
                        <?php endif; ?>
                        <td class="px-6 py-5">
                            <p class="text-sm text-on-surface-variant"><?= date('d M Y', strtotime($p['created_at'])) ?></p>
                        </td>
                        <td class="px-6 py-5">
                            <?php
                                $st = $p['status'] ?? 'pending';
                                $badgeClass = 'bg-amber-100 text-amber-800 border-amber-200';
                                if ($st === 'diterima') $badgeClass = 'bg-emerald-100 text-emerald-800 border-emerald-200';
                                if ($st === 'ditolak') $badgeClass = 'bg-red-100 text-red-800 border-red-200';
                            ?>
                            <span class="px-2 py-1 <?= $badgeClass ?> rounded-md text-[10px] font-black uppercase tracking-widest border">
                                <?= htmlspecialchars($st) ?>
                            </span>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <?php if (!isset($_SESSION['is_active_periode']) || $_SESSION['is_active_periode']): ?>
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" 
                                    onclick='openDetailModal(<?= json_encode([
                                        "nama" => $p["nama"],
                                        "email" => $p["email"],
                                        "no_wa" => $p["no_wa"],
                                        "jurusan" => $p["jurusan"],
                                        "kelas" => $p["kelas"],
                                        "alasan" => $p["alasan"],
                                        "status" => $p["status"],
                                        "created_at" => date("d M Y, H:i", strtotime($p["created_at"])),
                                        "answers" => $p["answers"] ?? []
                                    ], JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'
                                    class="p-2 text-primary hover:bg-primary/5 rounded-lg transition-all flex items-center justify-center font-bold text-xs" title="Lihat Detail Jawaban">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                                <?php if ($st === 'pending'): ?>
                                <form action="index.php?action=pendaftaran_status" method="POST" class="m-0 inline-block">
    <?= csrf_field() ?>
                                    
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <input type="hidden" name="status" value="diterima">
                                    <button type="submit" onclick="return confirm('Terima pendaftar ini? Data akan dipindahkan ke buku tabel Anggota Utama.')" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all flex items-center justify-center font-bold text-xs" title="Setujui/Terima">
                                        <span class="material-symbols-outlined text-[20px]">check_circle</span>
                                    </button>
                                </form>
                                <form action="index.php?action=pendaftaran_status" method="POST" class="m-0 inline-block" onsubmit="return handleReject(this)">
    <?= csrf_field() ?>
                                    
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <input type="hidden" name="status" value="ditolak">
                                    <input type="hidden" name="alasan_tolak" class="reject-reason" value="">
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all flex items-center justify-center font-bold text-xs" title="Tolak">
                                        <span class="material-symbols-outlined text-[20px]">cancel</span>
                                    </button>
                                </form>
                                <?php else: ?>
                                <form action="index.php?action=pendaftaran_delete" method="POST" class="m-0 inline-block">
    <?= csrf_field() ?>
                                    
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <button type="submit" onclick="return confirm('Hapus permanen arsip pendaftaran ini?')" class="p-2 text-outline hover:text-error hover:bg-error-container rounded-lg transition-all flex items-center justify-center font-bold text-xs" title="Hapus Arsip">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </form>
                                <?php endif; ?>
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
        <!-- Pagination border base -->
        <div class="px-6 py-4 flex items-center justify-between border-t border-outline-variant/15 bg-surface-container-high/20">
            <p class="text-xs font-bold text-outline uppercase tracking-widest">Menampilkan <?= count($pendaftaranList ?? []) ?> Pendaftar</p>
        </div>
    </div>
</main>

<script>
let pendingRejectForm = null;

function handleReject(form) {
    pendingRejectForm = form;
    const modal = document.getElementById('rejectModal');
    const content = document.getElementById('rejectModalContent');
    
    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('translate-y-4', 'opacity-0');
    }, 10);
    return false; // Prevent form submit initially
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    const content = document.getElementById('rejectModalContent');
    content.classList.add('translate-y-4', 'opacity-0');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

function setRejectTemplate(text) {
    document.getElementById('rejectReasonInput').value = text;
}

function submitReject() {
    if (!pendingRejectForm) return;
    const reason = document.getElementById('rejectReasonInput').value;
    pendingRejectForm.querySelector('.reject-reason').value = reason;
    pendingRejectForm.submit();
}

function openDetailModal(data) {
    const modal = document.getElementById('detailModal');
    const content = document.getElementById('detailModalContent');
    
    // Set Header Info
    document.getElementById('det-nama').innerText = data.nama;
    document.getElementById('det-status').innerText = data.status.toUpperCase();
    document.getElementById('det-tgl').innerText = data.created_at;
    
    // Set Profile Info
    document.getElementById('det-email').innerText = data.email;
    document.getElementById('det-wa').innerText = data.no_wa;
    document.getElementById('det-wa-link').href = `https://wa.me/${data.no_wa.replace(/[^0-9]/g, '')}`;
    document.getElementById('det-jurusan').innerText = data.jurusan || '-';
    document.getElementById('det-kelas').innerText = data.kelas || '-';
    
    // Set Motivasi
    document.getElementById('det-motivasi').innerText = data.alasan || 'Tidak ada motivasi yang dituliskan.';
    
    // Set Kuisioner
    const qContainer = document.getElementById('det-kuisioner');
    qContainer.innerHTML = '';
    
    if (data.answers && data.answers.length > 0) {
        data.answers.forEach((item, index) => {
            const qDiv = document.createElement('div');
            qDiv.className = 'p-4 bg-slate-50 rounded-xl border border-slate-100';
            qDiv.innerHTML = `
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Pertanyaan ${index + 1}</p>
                <p class="text-xs font-bold text-slate-800 mb-2">${item.pertanyaan_teks}</p>
                <p class="text-sm text-slate-600 bg-white p-3 rounded-lg border border-slate-200/50 italic">"${item.jawaban_teks}"</p>
            `;
            qContainer.appendChild(qDiv);
        });
    } else {
        qContainer.innerHTML = '<p class="text-xs text-slate-400 italic py-4">Tidak ada pertanyaan kuisioner tambahan untuk UKM ini.</p>';
    }

    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('translate-y-4', 'opacity-0');
    }, 10);
}

function closeDetailModal() {
    const modal = document.getElementById('detailModal');
    const content = document.getElementById('detailModalContent');
    content.classList.add('translate-y-4', 'opacity-0');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}
</script>

<!-- Modal Detail Pendaftar -->
<div id="detailModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeDetailModal()"></div>
    <div id="detailModalContent" class="relative bg-white w-full max-w-2xl max-h-[90vh] overflow-hidden rounded-3xl shadow-2xl transition-all duration-300 opacity-0 translate-y-4 flex flex-col">
        <!-- Header -->
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-primary/10 text-primary rounded-2xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">person_search</span>
                </div>
                <div>
                    <h3 id="det-nama" class="text-xl font-black text-slate-900">Nama Pendaftar</h3>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span id="det-status" class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-black rounded-md border border-amber-200">PENDING</span>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">• Terdaftar pada <span id="det-tgl">26 Apr 2024</span></span>
                    </div>
                </div>
            </div>
            <button onclick="closeDetailModal()" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-200 text-slate-400 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto p-8 space-y-8 custom-scrollbar">
            <!-- Profil Singkat -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h4 class="text-xs font-bold text-primary uppercase tracking-[0.2em]">Informasi Kontak</h4>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center text-slate-500">
                                <span class="material-symbols-outlined text-sm">mail</span>
                            </div>
                            <span id="det-email" class="text-sm font-medium text-slate-700">email@example.com</span>
                        </div>
                        <div class="flex items-center gap-3 group">
                            <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center text-emerald-600">
                                <span class="material-symbols-outlined text-sm">call</span>
                            </div>
                            <a id="det-wa-link" href="#" target="_blank" class="text-sm font-bold text-emerald-600 hover:underline flex items-center gap-1">
                                <span id="det-wa">0812345678</span>
                                <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="space-y-4">
                    <h4 class="text-xs font-bold text-primary uppercase tracking-[0.2em]">Akademik</h4>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center text-slate-500">
                                <span class="material-symbols-outlined text-sm">school</span>
                            </div>
                            <span id="det-jurusan" class="text-sm font-medium text-slate-700">Jurusan</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center text-slate-500">
                                <span class="material-symbols-outlined text-sm">layers</span>
                            </div>
                            <span id="det-kelas" class="text-sm font-medium text-slate-700">Kelas / Semester</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Motivasi -->
            <div class="space-y-3">
                <h4 class="text-xs font-bold text-primary uppercase tracking-[0.2em]">Motivasi Bergabung</h4>
                <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100 italic text-slate-600 text-sm leading-relaxed" id="det-motivasi">
                    "Alasan bergabung..."
                </div>
            </div>

            <!-- Kuisioner -->
            <div class="space-y-4">
                <h4 class="text-xs font-bold text-primary uppercase tracking-[0.2em]">Jawaban Kuisioner</h4>
                <div id="det-kuisioner" class="space-y-4">
                    <!-- Dynamic Questions -->
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
            <button onclick="closeDetailModal()" class="px-6 py-3 text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors">Tutup</button>
            <p class="text-[10px] text-slate-400 italic self-center mr-auto">Tinjau dengan seksama sebelum mengambil keputusan.</p>
        </div>
    </div>
</div>

<!-- Modal: Alasan Penolakan -->
<div id="rejectModal" class="hidden fixed inset-0 z-[120] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeRejectModal()"></div>
    <div id="rejectModalContent" class="relative bg-white w-full max-w-md rounded-3xl shadow-2xl transition-all duration-300 opacity-0 translate-y-4 overflow-hidden">
        <div class="p-8">
            <div class="w-12 h-12 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center mb-6">
                <span class="material-symbols-outlined text-2xl">cancel_presentation</span>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mb-2">Alasan Penolakan</h3>
            <p class="text-sm text-slate-500 mb-6">Berikan alasan mengapa pendaftar ini ditolak. Alasan ini akan terlihat oleh calon anggota.</p>
            
            <div class="space-y-4">
                <!-- Templates -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Gunakan Template:</label>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="setRejectTemplate('Maaf, kuota pendaftaran untuk periode ini sudah terpenuhi.')" class="px-3 py-1.5 bg-slate-100 hover:bg-primary/10 hover:text-primary text-[11px] font-bold text-slate-600 rounded-lg transition-all border border-transparent hover:border-primary/20">Kuota Penuh</button>
                        <button type="button" onclick="setRejectTemplate('Persyaratan administrasi belum terpenuhi (berkas tidak sesuai/tidak valid).')" class="px-3 py-1.5 bg-slate-100 hover:bg-primary/10 hover:text-primary text-[11px] font-bold text-slate-600 rounded-lg transition-all border border-transparent hover:border-primary/20">Syarat Tidak Sesuai</button>
                        <button type="button" onclick="setRejectTemplate('Data akademik (Jurusan/Kelas) tidak memenuhi kriteria pendaftaran organisasi.')" class="px-3 py-1.5 bg-slate-100 hover:bg-primary/10 hover:text-primary text-[11px] font-bold text-slate-600 rounded-lg transition-all border border-transparent hover:border-primary/20">Kriteria Akademik</button>
                        <button type="button" onclick="setRejectTemplate('Motivasi yang diberikan kurang selaras dengan visi misi organisasi saat ini.')" class="px-3 py-1.5 bg-slate-100 hover:bg-primary/10 hover:text-primary text-[11px] font-bold text-slate-600 rounded-lg transition-all border border-transparent hover:border-primary/20">Motivasi Kurang</button>
                    </div>
                </div>

                <!-- Textarea -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Alasan Kustom:</label>
                    <textarea id="rejectReasonInput" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" rows="4" placeholder="Ketik alasan lainnya di sini..."></textarea>
                </div>
            </div>

            <div class="flex gap-4 mt-8">
                <button type="button" onclick="closeRejectModal()" class="flex-1 py-3 px-4 bg-slate-100 text-slate-600 font-bold text-sm rounded-xl hover:bg-slate-200 transition-colors">Batal</button>
                <button type="button" onclick="submitReject()" class="flex-1 py-3 px-4 bg-red-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-red-200 hover:bg-red-700 transition-colors">Konfirmasi Tolak</button>
            </div>
        </div>
    </div>
</div>
