/**
 * Formulir Pendaftaran Anggota — Multi-Step dengan Validasi Ketat
 * Validasi per-tahap (client-side) sebelum melanjutkan ke tahap berikutnya.
 */

// ─── Helper: Tampilkan pesan error di bawah field ────────────────────────────
function showError(input, message) {
    clearError(input);
    input.classList.add('border-red-400', 'bg-red-50', 'focus:border-red-500', 'focus:ring-red-500/20');
    input.classList.remove('border-slate-200', 'bg-slate-50');

    const errEl = document.createElement('p');
    errEl.className = 'field-error text-xs text-red-600 font-semibold mt-1 flex items-center gap-1';
    errEl.innerHTML = '<span class="material-symbols-outlined text-[14px]">error</span>' + message;
    input.parentNode.appendChild(errEl);
}

// ─── Helper: Hapus pesan error dari field ────────────────────────────────────
function clearError(input) {
    input.classList.remove('border-red-400', 'bg-red-50', 'focus:border-red-500', 'focus:ring-red-500/20');
    input.classList.add('border-slate-200', 'bg-slate-50');
    const existing = input.parentNode.querySelector('.field-error');
    if (existing) existing.remove();
}

// ─── Helper: Tampilkan banner error di atas step ─────────────────────────────
function showStepBanner(stepEl, message) {
    removeStepBanner(stepEl);
    const banner = document.createElement('div');
    banner.id = 'step-error-banner';
    banner.className = 'flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 rounded-2xl px-5 py-4 mb-6 text-sm font-semibold animate-pulse';
    banner.innerHTML = '<span class="material-symbols-outlined shrink-0" style="font-variation-settings: \'FILL\' 1;">error</span>' + message;
    // Insert after the subtitle paragraph
    const subtitle = stepEl.querySelector('p');
    if (subtitle) {
        subtitle.insertAdjacentElement('afterend', banner);
    } else {
        stepEl.prepend(banner);
    }
    // Stop pulsing after 600ms
    setTimeout(() => banner.classList.remove('animate-pulse'), 700);
}

function removeStepBanner(stepEl) {
    const old = stepEl.querySelector('#step-error-banner');
    if (old) old.remove();
}

// ─── Validasi Step 1: Identitas Dasar ────────────────────────────────────────
function validateStep1() {
    const step = document.getElementById('step-1');
    let valid = true;
    removeStepBanner(step);

    const fields = [
        { name: 'nama',    label: 'Nama Lengkap' },
        { name: 'no_wa',   label: 'Nomor WhatsApp' },
        { name: 'email',   label: 'Alamat Email' },
        { name: 'jurusan', label: 'Jurusan / Program Studi' },
        { name: 'kelas',   label: 'Kelas / Semester' },
    ];

    // Cek dropdown UKM (jika ada dan tidak read-only)
    const ukmSelect = step.querySelector('select[name="ukm_id"]');
    if (ukmSelect) {
        clearError(ukmSelect);
        if (!ukmSelect.value) {
            showError(ukmSelect, 'Silakan pilih UKM yang ingin Anda ikuti.');
            valid = false;
        }
    }

    fields.forEach(f => {
        const el = step.querySelector(`[name="${f.name}"]`);
        if (!el) return;
        clearError(el);
        const val = el.value.trim();
        if (!val) {
            showError(el, `${f.label} wajib diisi.`);
            valid = false;
        } else if (f.name === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
            showError(el, 'Format email tidak valid.');
            valid = false;
        } else if (f.name === 'no_wa' && !/^[0-9+\-\s]{7,15}$/.test(val)) {
            showError(el, 'Nomor WhatsApp hanya boleh mengandung angka dan minimal 7 digit.');
            valid = false;
        }
    });

    if (!valid) {
        showStepBanner(step, 'Semua field harus diisi sebelum melanjutkan.');
        // Scroll to first error
        const firstErr = step.querySelector('.border-red-400');
        if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    return valid;
}

// ─── Validasi Step 2: Kuisioner Tambahan ─────────────────────────────────────
function validateStep2() {
    const step = document.getElementById('step-2');
    let valid = true;
    removeStepBanner(step);

    // Hanya validasi textarea yang memiliki atribut 'required'
    const requiredTextareas = step.querySelectorAll('textarea[required]');
    requiredTextareas.forEach(ta => {
        clearError(ta);
        if (!ta.value.trim()) {
            showError(ta, 'Pertanyaan ini wajib dijawab.');
            valid = false;
        }
    });

    if (!valid) {
        showStepBanner(step, 'Jawab semua pertanyaan yang ditandai Wajib sebelum melanjutkan.');
        const firstErr = step.querySelector('.border-red-400');
        if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    return valid;
}

// ─── Validasi Step 3: Motivasi & Persetujuan ─────────────────────────────────
function validateStep3() {
    const step = document.getElementById('step-3');
    let valid = true;
    removeStepBanner(step);

    // Validasi textarea "alasan"
    const alasan = step.querySelector('textarea[name="alasan"]');
    if (alasan) {
        clearError(alasan);
        if (!alasan.value.trim()) {
            showError(alasan, 'Motivasi bergabung tidak boleh kosong.');
            valid = false;
        }
    }

    // Validasi checkbox persetujuan
    const checkbox = step.querySelector('input[type="checkbox"][name="persetujuan"]');
    if (checkbox) {
        const label = checkbox.closest('label');
        // Reset state
        const oldCbErr = step.querySelector('#checkbox-error');
        if (oldCbErr) oldCbErr.remove();
        if (label) label.classList.remove('border-red-300', 'bg-red-50');

        if (!checkbox.checked) {
            if (label) label.classList.add('border-2', 'border-red-300', 'bg-red-50', 'rounded-xl');
            const errEl = document.createElement('p');
            errEl.id = 'checkbox-error';
            errEl.className = 'text-xs text-red-600 font-semibold mt-2 flex items-center gap-1 px-4';
            errEl.innerHTML = '<span class="material-symbols-outlined text-[14px]">error</span>Anda harus menyetujui persyaratan dengan mencentang kotak persetujuan.';
            if (label) label.insertAdjacentElement('afterend', errEl);
            valid = false;
        }
    }

    if (!valid) {
        showStepBanner(step, 'Lengkapi semua field dan centang kotak persetujuan untuk melanjutkan.');
        const firstErr = step.querySelector('.border-red-400, .border-red-300');
        if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    return valid;
}

// ─── Navigasi Antar Step ─────────────────────────────────────────────────────
function nextStep(step) {
    const currentStep = step - 1;

    // Jalankan validasi per step
    if (currentStep === 1 && !validateStep1()) return;
    if (currentStep === 2 && !validateStep2()) return;

    // Sembunyikan semua step
    document.getElementById('step-1').classList.add('hidden');
    document.getElementById('step-2').classList.add('hidden');
    document.getElementById('step-3').classList.add('hidden');

    // Tampilkan step target
    document.getElementById(`step-${step}`).classList.remove('hidden');
    updateIndicator(step);

    // Scroll ke atas form
    document.getElementById(`step-${step}`).scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function prevStep(step) {
    document.getElementById('step-1').classList.add('hidden');
    document.getElementById('step-2').classList.add('hidden');
    document.getElementById('step-3').classList.add('hidden');

    document.getElementById(`step-${step}`).classList.remove('hidden');
    updateIndicator(step);
    document.getElementById(`step-${step}`).scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ─── Validasi Sebelum Submit Final ───────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('registration-form');
    if (!form) return;

    // Real-time: hapus error saat field diubah
    form.addEventListener('input', function (e) {
        if (e.target.matches('input, select, textarea')) {
            clearError(e.target);
        }
    });
    form.addEventListener('change', function (e) {
        if (e.target.matches('input[type="checkbox"]')) {
            const step = document.getElementById('step-3');
            const oldCbErr = step ? step.querySelector('#checkbox-error') : null;
            if (oldCbErr) oldCbErr.remove();
            const label = e.target.closest('label');
            if (label) label.classList.remove('border-2', 'border-red-300', 'bg-red-50');
        }
    });

    form.addEventListener('submit', function (e) {
        // Validasi semua step sebelum submit
        const s1 = validateStep1();
        const s2 = validateStep2();
        const s3 = validateStep3();

        if (!s3) {
            e.preventDefault();
            // Pastikan step 3 terlihat
            document.getElementById('step-1').classList.add('hidden');
            document.getElementById('step-2').classList.add('hidden');
            document.getElementById('step-3').classList.remove('hidden');
            updateIndicator(3);
            return;
        }
        if (!s2) {
            e.preventDefault();
            document.getElementById('step-1').classList.add('hidden');
            document.getElementById('step-2').classList.remove('hidden');
            document.getElementById('step-3').classList.add('hidden');
            updateIndicator(2);
            return;
        }
        if (!s1) {
            e.preventDefault();
            document.getElementById('step-1').classList.remove('hidden');
            document.getElementById('step-2').classList.add('hidden');
            document.getElementById('step-3').classList.add('hidden');
            updateIndicator(1);
            return;
        }
    });
});

// ─── Update Indikator Tahap ───────────────────────────────────────────────────
function updateIndicator(step) {
    const indicators = [
        document.getElementById('step-1-indicator'),
        document.getElementById('step-2-indicator'),
        document.getElementById('step-3-indicator')
    ];

    indicators.forEach((el, index) => {
        if (index + 1 < step) {
            el.className = "w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold border-4 border-slate-50 shadow-sm transition-colors duration-500 step-indicator";
            el.innerHTML = '<span class="material-symbols-outlined text-[20px]" style="font-variation-settings: \'FILL\' 1;">check_circle</span>';
        } else if (index + 1 === step) {
            el.className = "w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold border-4 border-blue-50/50 shadow-sm transition-colors duration-500 step-indicator";
            el.innerHTML = step;
        } else {
            el.className = "w-10 h-10 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center font-bold border-4 border-slate-50 shadow-sm transition-colors duration-500 step-indicator";
            el.innerHTML = index + 1;
        }
    });

    // Update Progress Bar
    const progressBar = document.getElementById('progress-bar');
    if (!progressBar) return;
    if (step === 1) progressBar.style.width = '0%';
    if (step === 2) progressBar.style.width = '50%';
    if (step === 3) progressBar.style.width = '100%';
}
