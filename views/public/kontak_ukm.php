<?php include __DIR__ . '/components/ukm_subnav.php'; ?>

<!-- Main Section -->
<main class="min-h-[calc(100vh-140px)] bg-slate-50/50 py-16">
    <div class="max-w-7xl mx-auto px-6">
        <!-- Header -->
        <div class="mb-12">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold uppercase tracking-widest mb-4">
                <span class="w-2 h-2 rounded-full bg-blue-500"></span> Informasi Publik
            </div>
            <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight leading-tight mb-4">Hubungi Kami</h1>
            <p class="text-lg text-slate-600 max-w-2xl leading-relaxed">Punya pertanyaan seputar pendaftaran, ajakan kolaborasi, undangan, atau hal teknis? Jangan ragu untuk mendatangi Basecamp kami atau menghubungi kami secara daring.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <!-- Left Panel: Contact Information -->
            <div class="lg:col-span-5 space-y-6">
                
                <!-- Location Address -->
                <div class="bg-white rounded-3xl p-8 border border-slate-200/60 shadow-sm flex gap-5 mix-blend-luminosity hover:mix-blend-normal transition-all duration-300">
                    <div class="w-14 h-14 shrink-0 rounded-2xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">location_on</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 mb-1">Basecamp Resmi (Sekretariat)</h3>
                        <p class="text-slate-600 leading-relaxed text-sm"><?= nl2br(htmlspecialchars($ukm['lokasi'] ?? 'Alamat belum diisi.')) ?></p>
                    </div>
                </div>

                <!-- Phone & Email -->
                <div class="bg-white rounded-3xl p-8 border border-slate-200/60 shadow-sm flex gap-5 mix-blend-luminosity hover:mix-blend-normal transition-all duration-300">
                    <div class="w-14 h-14 shrink-0 rounded-2xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">perm_phone_msg</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 mb-1">Telepon & Surel</h3>
                        <div class="space-y-3 mt-3">
                            <div class="flex items-center gap-3 text-sm font-medium text-slate-600 hover:text-blue-600 transition-colors">
                                <span class="material-symbols-outlined text-sm">call</span> +62 812-3456-7890 (Kadiv Humas)
                            </div>
                            <div class="flex items-center gap-3 text-sm font-medium text-slate-600 hover:text-blue-600 transition-colors">
                                <span class="material-symbols-outlined text-sm">mail</span> hello@<?= strtolower(str_replace(' ', '', htmlspecialchars($ukm['nama'] ?? 'ukm'))) ?>.ac.id
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Medias -->
                <div class="bg-white rounded-3xl p-8 border border-slate-200/60 shadow-sm flex gap-5 mix-blend-luminosity hover:mix-blend-normal transition-all duration-300">
                    <div class="w-14 h-14 shrink-0 rounded-2xl bg-purple-50 border border-purple-100 text-purple-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">alternate_email</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 mb-1">Ikuti Sosial Media Kami</h3>
                        <p class="text-slate-500 text-sm mb-4">Pantau terus pembaruan informasi, dokumentasi kegiatan, dan pencapaian kami.</p>
                        <div class="flex gap-3">
                            <?php if (empty($settings['instagram_url']) && empty($settings['facebook_url']) && empty($settings['twitter_url'])): ?>
                                <p class="text-sm text-slate-400 italic">Belum ada tautan sosial media terdaftar.</p>
                            <?php endif; ?>

                            <?php if (!empty($settings['instagram_url'])): ?>
                            <a href="<?= htmlspecialchars($settings['instagram_url']) ?>" target="_blank" class="w-10 h-10 rounded-full border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-[#E1306C] hover:text-white hover:border-[#E1306C] transition-colors">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                            </a>
                            <?php endif; ?>

                            <?php if (!empty($settings['facebook_url'])): ?>
                            <a href="<?= htmlspecialchars($settings['facebook_url']) ?>" target="_blank" class="w-10 h-10 rounded-full border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-[#1877F2] hover:text-white hover:border-[#1877F2] transition-colors">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M22.675 0h-21.35C.597 0 0 .597 0 1.325v21.351C0 23.403.597 24 1.325 24h11.495v-9.294H9.691v-3.622h3.129V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116c.73 0 1.323-.597 1.323-1.325V1.325C24 .597 23.403 0 22.675 0z"/></svg>
                            </a>
                            <?php endif; ?>

                            <?php if (!empty($settings['twitter_url'])): ?>
                            <a href="<?= htmlspecialchars($settings['twitter_url']) ?>" target="_blank" class="w-10 h-10 rounded-full border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-black hover:text-white hover:border-black transition-colors">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Call to Actions -->
                <?php if (!empty($settings['whatsapp']) || !empty($settings['email_admin'])): ?>
                <div class="pt-2 flex flex-col gap-3">
                    <?php if (!empty($settings['whatsapp'])): ?>
                    <a href="https://wa.me/<?= formatWhatsAppPhone($settings['whatsapp']) ?>" target="_blank" class="flex items-center justify-center gap-3 w-full py-4 bg-emerald-600 text-white font-bold rounded-2xl shadow-xl shadow-emerald-200 hover:-translate-y-1 transition-transform group">
                        <svg class="w-5 h-5 fill-current shrink-0" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                        Hubungi via WhatsApp
                    </a>
                    <?php endif; ?>

                    <?php if (!empty($settings['email_admin'])): ?>
                    <a href="mailto:<?= htmlspecialchars($settings['email_admin']) ?>" class="flex items-center justify-center gap-3 w-full py-4 bg-red-500 text-white font-bold rounded-2xl shadow-xl shadow-red-200 hover:-translate-y-1 transition-transform group">
                        <span class="material-symbols-outlined shrink-0" style="font-variation-settings: 'FILL' 1">mail</span>
                        Hubungi via Gmail
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

            </div>

            <div class="lg:col-span-7 bg-white p-2 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 flex flex-col h-[700px]">
                <div class="w-full h-full rounded-[1.5rem] overflow-hidden bg-slate-100 relative">
                    <?php if (!empty($ukm['koordinat'])): ?>
                        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                        <div id="map" class="w-full h-full"></div>
                        <script>
                            (function() {
                                const coords = "<?= $ukm['koordinat'] ?>".split(',').map(c => parseFloat(c.trim()));
                                if (coords.length === 2 && !isNaN(coords[0]) && !isNaN(coords[1])) {
                                    const map = L.map('map').setView(coords, 16);
                                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                                    }).addTo(map);
                                    L.marker(coords).addTo(map)
                                        .bindPopup('<b><?= htmlspecialchars($ukm['nama']) ?></b><br><?= htmlspecialchars($ukm['lokasi'] ?? '') ?>')
                                        .openPopup();
                                } else {
                                    document.getElementById('map').innerHTML = '<div class="flex items-center justify-center h-full text-slate-400 flex-col gap-2"><span class="material-symbols-outlined text-4xl">map</span><p>Format koordinat tidak valid.</p></div>';
                                }
                            })();
                        </script>
                    <?php else: ?>
                        <div class="flex items-center justify-center h-full text-slate-400 flex-col gap-2">
                            <span class="material-symbols-outlined text-5xl">map</span>
                            <p class="font-bold">Peta belum tersedia</p>
                            <p class="text-sm">Admin belum mengatur koordinat lokasi.</p>
                        </div>
                    <?php endif; ?>

                    <!-- Hint badge on top of map -->
                    <div class="absolute top-6 left-6 bg-white/90 backdrop-blur px-5 py-3 rounded-2xl shadow-lg border border-white flex flex-col pointer-events-none z-[1000]">
                        <span class="font-black text-slate-800 text-sm"><?= htmlspecialchars($ukm['nama']) ?></span>
                        <span class="text-xs text-slate-500 font-medium"><?= htmlspecialchars($ukm['singkatan'] ?? '') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
