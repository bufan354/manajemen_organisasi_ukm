<div class="flex-1 p-8 space-y-8 max-w-7xl mx-auto w-full relative">
    <div class="flex items-center gap-4">
        <a href="index.php?page=berita" class="w-10 h-10 rounded-full flex items-center justify-center bg-white border border-outline-variant hover:bg-surface-container transition-colors">
            <span class="material-symbols-outlined text-outline">arrow_back</span>
        </a>
        <div class="space-y-1">
            <h1 class="text-3xl font-extrabold tracking-tight text-on-surface">Buat Berita Baru</h1>
            <p class="text-on-surface-variant font-medium">Entri baru untuk portal informasi organisasi.</p>
        </div>
    </div>
    
    <?= renderFlash() ?>
    <form action="index.php?action=berita_store" method="POST" enctype="multipart/form-data" class="space-y-8 pb-32">
    <?= csrf_field() ?>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Content & Metadata -->
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-surface-container-lowest p-8 rounded-2xl shadow-sm space-y-6">
                    <?php if (Session::get('admin_role') === 'superadmin'): ?>
                    <div class="space-y-2 mb-6">
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">UKM</label>
                        <div class="relative">
                            <select name="ukm_id" required class="w-full bg-surface-container-low border-none rounded-xl py-3 px-4 text-sm font-medium focus:ring-2 focus:ring-primary/20 appearance-none cursor-pointer">
                                <option disabled selected value="">Pilih UKM...</option>
                                <?php foreach ($ukmList as $u): ?>
                                    <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nama']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant">expand_more</span>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="space-y-2">
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Judul Berita</label>
                        <input name="judul" class="w-full text-2xl font-bold bg-transparent border-none border-b-2 border-surface-container-high focus:border-primary focus:ring-0 px-0 pb-2 transition-all placeholder:text-surface-dim" placeholder="Masukkan judul yang menarik..." type="text" required/>
                    </div>
                    <div class="space-y-2 pt-4">
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Konten Berita</label>
                        <div class="bg-surface-container-low rounded-xl overflow-hidden border border-transparent focus-within:border-primary/20 transition-all">
                            <div class="flex items-center gap-1 p-2 bg-surface-container-high/50 border-b border-surface-container">
                                <button class="p-2 hover:bg-white rounded text-on-surface-variant format-btn" data-command="bold" type="button" title="Bold"><span class="material-symbols-outlined text-sm">format_bold</span></button>
                                <button class="p-2 hover:bg-white rounded text-on-surface-variant format-btn" data-command="italic" type="button" title="Italic"><span class="material-symbols-outlined text-sm">format_italic</span></button>
                                <button class="p-2 hover:bg-white rounded text-on-surface-variant format-btn" data-command="underline" type="button" title="Underline"><span class="material-symbols-outlined text-sm">format_underlined</span></button>
                                <div class="w-px h-4 bg-surface-container-highest mx-1"></div>
                                <button class="p-2 hover:bg-white rounded text-on-surface-variant format-btn" data-command="insertUnorderedList" type="button" title="Bullet List"><span class="material-symbols-outlined text-sm">format_list_bulleted</span></button>
                                <button class="p-2 hover:bg-white rounded text-on-surface-variant format-btn" data-command="createLink" type="button" title="Insert Link"><span class="material-symbols-outlined text-sm">link</span></button>
                                <button class="p-2 hover:bg-white rounded text-on-surface-variant format-btn" data-command="insertImage" type="button" title="Insert Image by URL"><span class="material-symbols-outlined text-sm">image</span></button>
                            </div>
                            <div id="rich-editor" contenteditable="true" class="w-full min-h-[350px] bg-transparent border-none focus:outline-none p-4 text-on-surface leading-relaxed format-content prose prose-blue max-w-none"></div>
                            <textarea id="hidden-konten" name="konten" class="hidden"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column: Settings & Media -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm space-y-4">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Gambar Utama</label>
                    <div class="relative aspect-video rounded-xl border-2 border-dashed border-outline-variant bg-surface-container-low flex flex-col items-center justify-center gap-3 p-4 text-center group cursor-pointer hover:bg-surface-container-high transition-all" onclick="document.getElementById('gambar-input').click()">
                        <input type="file" name="gambar" id="gambar-input" accept="image/*" class="hidden"/>
                        <div id="gambar-preview" class="absolute inset-0 pointer-events-none rounded-xl overflow-hidden z-10 bg-white" style="display: none;"></div>
                        <span class="material-symbols-outlined text-4xl text-outline group-hover:text-primary transition-colors">cloud_upload</span>
                        <div class="space-y-1">
                            <p class="text-sm font-bold text-on-surface">Unggah Media</p>
                            <p class="text-[11px] text-on-surface-variant px-4">Tarik & lepas file atau klik untuk memilih (Maks. 5MB)</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm space-y-6">
                    <div class="space-y-2">
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Kategori</label>
                        <div class="relative">
                            <select name="kategori" class="w-full bg-surface-container-low border-none rounded-xl py-3 px-4 text-sm font-medium focus:ring-2 focus:ring-primary/20 appearance-none cursor-pointer">
                                <option disabled="" selected="" value="">Pilih Kategori...</option>
                                <option value="Pengumuman">Pengumuman</option>
                                <option value="Prestasi">Prestasi</option>
                                <option value="Kegiatan">Kegiatan</option>
                                <option value="Informasi">Informasi</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant">expand_more</span>
                        </div>
                    </div>
                    
                    <div class="space-y-4 pt-2">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="block text-sm font-bold text-on-surface">Status Publikasi</label>
                                <p class="text-[11px] text-on-surface-variant">Terbitkan segera setelah simpan</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input name="published" class="sr-only peer" type="checkbox" value="1"/>
                                <div class="w-11 h-6 bg-surface-container-highest peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-secondary"></div>
                            </label>
                        </div>
                    </div>
                    
                    <div class="p-4 bg-primary-fixed/30 rounded-xl border-l-4 border-primary">
                        <div class="flex gap-3">
                            <span class="material-symbols-outlined text-primary text-xl">info</span>
                            <p class="text-[11px] text-on-primary-fixed-variant leading-relaxed">
                                Pastikan judul dan konten telah sesuai dengan standar jurnalisme kampus sebelum mempublikasikan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer Actions -->
        <div class="fixed bottom-0 right-0 left-[260px] bg-white/60 backdrop-blur-md border-t border-surface-container-high px-8 py-4 z-40 flex items-center justify-end gap-4 shadow-[0_-10px_40px_rgba(25,28,30,0.02)]">
            <button class="px-6 py-2.5 rounded-xl text-sm font-bold text-on-surface-variant hover:bg-surface-container-high transition-all" type="button">
                Batal
            </button>
            <button class="flex items-center gap-2 px-8 py-2.5 rounded-xl text-sm font-bold text-white bg-primary hover:bg-primary-container shadow-lg shadow-primary/20 transition-all active:scale-95" type="submit">
                <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">save</span>
                Simpan Berita
            </button>
        </div>
    </form>
</div>
<script>
// Image Preview Loader
document.getElementById('gambar-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('gambar-preview');
            preview.style.display = 'block';
            preview.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover"/>';
        }
        reader.readAsDataURL(file);
    }
});

// Rich Text Editor Commands
document.querySelectorAll('.format-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        let cmd = btn.dataset.command;
        if (cmd === 'createLink') {
            let url = prompt('Masukkan URL Link: ', 'https://');
            if(url) {
                document.execCommand(cmd, false, url);
                document.getSelection().anchorNode.parentElement.target = '_blank';
            }
        } else if (cmd === 'insertImage') {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.onchange = e => {
                const imgFile = e.target.files[0];
                if(imgFile){
                    const r = new FileReader();
                    r.onload = ev => {
                        document.execCommand(cmd, false, ev.target.result);
                    };
                    r.readAsDataURL(imgFile);
                }
            };
            input.click();
        } else {
            document.execCommand(cmd, false, null);
        }
        document.getElementById('rich-editor').focus();
    });
});

// Sync Content before Form Submit
document.querySelector('form').addEventListener('submit', function() {
    const editor = document.getElementById('rich-editor');
    // Ensure lists or styled blocks are properly encapsulated
    document.getElementById('hidden-konten').value = editor.innerHTML;
});

// Adding Placeholder dynamically via CSS inject on empty div
const editor = document.getElementById('rich-editor');
editor.addEventListener('focus', function() {
    if(this.innerHTML.trim() === '<p><br></p>' || this.innerHTML.trim() === '<br>') { this.innerHTML = ''; }
});
</script>
<style>
#rich-editor:empty:before {
  content: "Tuliskan konten berita Anda dengan format bebas di sini...";
  color: #94a3b8;
  pointer-events: none;
}
</style>
