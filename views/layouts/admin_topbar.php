<header class="sticky top-0 w-full z-40 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md flex justify-between lg:justify-end items-center px-6 lg:px-8 h-16">
    <!-- Hamburger Menu Button (Mobile Only) -->
    <div class="lg:hidden flex items-center">
        <button onclick="toggleSidebar()" class="relative cursor-pointer hover:text-blue-600 transition-colors p-2 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/30 focus:outline-none flex items-center justify-center -ml-2" title="Toggle Menu" aria-label="Toggle Menu">
            <span class="material-symbols-outlined text-[24px] text-on-surface-variant">menu</span>
        </button>
    </div>
    <div class="flex items-center gap-6">
        <!-- Notification Bell -->
        <div class="relative" id="notif-wrapper">
            <button id="notif-bell" class="relative cursor-pointer hover:text-blue-600 transition-colors p-1 rounded-full hover:bg-blue-50 dark:hover:bg-blue-900/30 focus:outline-none" title="Notifikasi" aria-label="Notifikasi">
                <span class="material-symbols-outlined text-[22px] text-on-surface-variant" data-icon="notifications">notifications</span>
                <!-- Badge Counter -->
                <span id="notif-badge" class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center ring-2 ring-white dark:ring-slate-900 transition-all duration-300 scale-0 opacity-0" style="line-height:1;">0</span>
            </button>

            <!-- Notification Dropdown Panel -->
            <div id="notif-panel" class="absolute right-0 top-full mt-2 w-[400px] max-h-[520px] bg-white dark:bg-slate-900 rounded-2xl shadow-2xl shadow-black/10 border border-slate-200 dark:border-slate-700 overflow-hidden transition-all duration-300 ease-out origin-top-right scale-95 opacity-0 pointer-events-none z-50" style="transform-origin: top right;">
                <!-- Panel Header -->
                <div class="sticky top-0 z-10 bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm px-5 py-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg text-primary" style="font-variation-settings: 'FILL' 1;">notifications</span>
                            <h3 class="text-sm font-bold text-on-surface tracking-tight">Notifikasi</h3>
                            <span id="notif-header-count" class="bg-primary/10 text-primary text-[10px] font-bold px-2 py-0.5 rounded-full hidden">0 baru</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <button id="notif-mark-all" class="text-[11px] font-semibold text-blue-600 hover:text-blue-800 hover:bg-blue-50 px-2.5 py-1.5 rounded-lg transition-colors whitespace-nowrap" title="Tandai semua sudah dibaca">
                                <span class="material-symbols-outlined text-[14px] align-middle mr-0.5">done_all</span>
                                Baca Semua
                            </button>
                            <button id="notif-delete-all" class="text-[11px] font-semibold text-red-500 hover:text-red-700 hover:bg-red-50 px-2.5 py-1.5 rounded-lg transition-colors whitespace-nowrap" title="Hapus semua notifikasi">
                                <span class="material-symbols-outlined text-[14px] align-middle mr-0.5">delete_sweep</span>
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Notification Items List -->
                <div id="notif-list" class="overflow-y-auto max-h-[420px] custom-scrollbar">
                    <!-- Skeleton Loading -->
                    <div id="notif-skeleton" class="p-4 space-y-3">
                        <div class="flex gap-3 animate-pulse">
                            <div class="w-9 h-9 rounded-xl bg-slate-200 dark:bg-slate-700 flex-shrink-0"></div>
                            <div class="flex-1 space-y-2">
                                <div class="h-3.5 bg-slate-200 dark:bg-slate-700 rounded w-3/4"></div>
                                <div class="h-3 bg-slate-100 dark:bg-slate-800 rounded w-full"></div>
                                <div class="h-2.5 bg-slate-100 dark:bg-slate-800 rounded w-1/3"></div>
                            </div>
                        </div>
                        <div class="flex gap-3 animate-pulse">
                            <div class="w-9 h-9 rounded-xl bg-slate-200 dark:bg-slate-700 flex-shrink-0"></div>
                            <div class="flex-1 space-y-2">
                                <div class="h-3.5 bg-slate-200 dark:bg-slate-700 rounded w-2/3"></div>
                                <div class="h-3 bg-slate-100 dark:bg-slate-800 rounded w-5/6"></div>
                                <div class="h-2.5 bg-slate-100 dark:bg-slate-800 rounded w-1/4"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div id="notif-empty" class="hidden py-12 text-center">
                        <span class="material-symbols-outlined text-5xl text-slate-300 dark:text-slate-600 mb-3 block" style="font-variation-settings: 'FILL' 1;">notifications_off</span>
                        <p class="text-sm font-semibold text-slate-400 dark:text-slate-500">Tidak ada notifikasi</p>
                        <p class="text-xs text-slate-300 dark:text-slate-600 mt-1">Semua sudah terbaca ✓</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="h-8 w-[1px] bg-outline-variant"></div>
        <div class="flex items-center gap-3 cursor-pointer group">
            <div class="text-right">
                <p class="text-sm font-semibold text-on-surface"><?= h(Session::get('admin_nama', 'Admin User')) ?></p>
                <p class="text-[10px] text-on-surface-variant font-medium"><?= Session::get('admin_role') === 'superadmin' ? 'Super Administrator' : 'Admin ' . h(getEntityLabel()) ?></p>
            </div>
            <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-primary-fixed group-hover:border-primary transition-colors bg-slate-100 flex items-center justify-center font-bold text-slate-600">
                <?php if (Session::get('admin_foto')): ?>
                    <img class="w-full h-full object-cover" alt="Profile" src="<?= h(Session::get('admin_foto')) ?>"/>
                <?php else: ?>
                    <?= strtoupper(substr(Session::get('admin_nama', 'A'), 0, 1)) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<!-- Notification AJAX Polling Script -->
<script>
(function() {
    const POLL_INTERVAL = 15000; // 15 detik
    const bell = document.getElementById('notif-bell');
    const badge = document.getElementById('notif-badge');
    const panel = document.getElementById('notif-panel');
    const wrapper = document.getElementById('notif-wrapper');
    const listContainer = document.getElementById('notif-list');
    const skeleton = document.getElementById('notif-skeleton');
    const emptyState = document.getElementById('notif-empty');
    const headerCount = document.getElementById('notif-header-count');
    const markAllBtn = document.getElementById('notif-mark-all');
    const deleteAllBtn = document.getElementById('notif-delete-all');
    
    let isOpen = false;
    let lastUnreadCount = 0;
    let pollTimer = null;
    let initialLoad = true;

    // Icon mapping for notification types
    const typeIcons = {
        'pendaftaran_baru': { icon: 'person_add', color: 'bg-emerald-100 text-emerald-600', colorDark: 'dark:bg-emerald-900/30 dark:text-emerald-400' },
        'login_perangkat_baru': { icon: 'devices', color: 'bg-amber-100 text-amber-600', colorDark: 'dark:bg-amber-900/30 dark:text-amber-400' },
    };
    const defaultIcon = { icon: 'notifications', color: 'bg-blue-100 text-blue-600', colorDark: 'dark:bg-blue-900/30 dark:text-blue-400' };

    // Toggle panel
    bell.addEventListener('click', function(e) {
        e.stopPropagation();
        isOpen = !isOpen;
        if (isOpen) {
            panel.classList.remove('scale-95', 'opacity-0', 'pointer-events-none');
            panel.classList.add('scale-100', 'opacity-100', 'pointer-events-auto');
            fetchNotifications();
        } else {
            closePanel();
        }
    });

    // Close on outside click
    document.addEventListener('click', function(e) {
        if (isOpen && !wrapper.contains(e.target)) {
            closePanel();
        }
    });

    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isOpen) {
            closePanel();
        }
    });

    function closePanel() {
        isOpen = false;
        panel.classList.add('scale-95', 'opacity-0', 'pointer-events-none');
        panel.classList.remove('scale-100', 'opacity-100', 'pointer-events-auto');
    }

    const CSRF_TOKEN = '<?= Session::csrfToken() ?>';

    // Update badge
    function updateBadge(count) {
        badge.textContent = count > 99 ? '99+' : count;
        if (count > 0) {
            badge.classList.remove('scale-0', 'opacity-0');
            badge.classList.add('scale-100', 'opacity-100');
            // Animate new notification
            if (count > lastUnreadCount && !initialLoad) {
                bell.classList.add('animate-bounce');
                setTimeout(() => bell.classList.remove('animate-bounce'), 1500);
            }
        } else {
            badge.classList.add('scale-0', 'opacity-0');
            badge.classList.remove('scale-100', 'opacity-100');
        }
        // Update header badge
        if (count > 0) {
            headerCount.textContent = count + ' baru';
            headerCount.classList.remove('hidden');
        } else {
            headerCount.classList.add('hidden');
        }
        lastUnreadCount = count;
    }

    // Render notifications
    function renderNotifications(notifications) {
        skeleton.classList.add('hidden');

        // Remove all rendered items (keep skeleton and empty state)
        const existing = listContainer.querySelectorAll('.notif-item');
        existing.forEach(el => el.remove());

        if (!notifications || notifications.length === 0) {
            emptyState.classList.remove('hidden');
            return;
        }

        emptyState.classList.add('hidden');

        notifications.forEach(function(n, index) {
            const iconData = typeIcons[n.jenis] || defaultIcon;
            const unreadClass = n.is_dibaca ? 'bg-white dark:bg-slate-900' : 'bg-blue-50/50 dark:bg-blue-950/20';
            const unreadDot = n.is_dibaca ? '' : '<span class="absolute top-3 right-3 w-2 h-2 bg-blue-500 rounded-full ring-2 ring-white dark:ring-slate-900 animate-pulse"></span>';

            const item = document.createElement('div');
            item.className = `notif-item relative flex gap-3 px-5 py-3.5 border-b border-slate-50 dark:border-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all duration-200 cursor-pointer group ${unreadClass}`;
            item.style.animationDelay = (index * 40) + 'ms';
            item.dataset.id = n.id;
            
            item.innerHTML = `
                ${unreadDot}
                <div class="notif-content flex-1 flex gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl ${iconData.color} ${iconData.colorDark} flex items-center justify-center flex-shrink-0 transition-transform group-hover:scale-110">
                        <span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1;">${iconData.icon}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[13px] font-semibold text-on-surface leading-snug truncate">${n.judul}</p>
                        <p class="text-[12px] text-on-surface-variant leading-relaxed mt-0.5 line-clamp-2">${n.pesan}</p>
                        <div class="flex items-center gap-2 mt-1.5">
                            <span class="material-symbols-outlined text-[12px] text-slate-400">schedule</span>
                            <span class="text-[11px] text-slate-400 font-medium">${n.waktu}</span>
                        </div>
                    </div>
                </div>
                <!-- Delete Single Button -->
                <button class="notif-delete-single self-start mt-0.5 opacity-0 group-hover:opacity-100 p-1.5 text-slate-300 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all" title="Hapus notifikasi">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                </button>
            `;

            // Mark as read and Navigate
            item.querySelector('.notif-content').addEventListener('click', function(e) {
                if (!n.is_dibaca) {
                    markAsRead(n.id);
                }
                setTimeout(() => {
                    window.location.href = n.link || '#';
                }, 50); // Small delay to let fetch fire
            });

            // Delete individual
            item.querySelector('.notif-delete-single').addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                deleteNotif(n.id);
            });

            listContainer.appendChild(item);
        });
    }

    // Fetch notifications via AJAX
    function fetchNotifications() {
        fetch('index.php?action=notifikasi_poll', {
            credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) return;
            updateBadge(data.unread_count || 0);
            if (isOpen || initialLoad) {
                renderNotifications(data.notifications || []);
            }
            initialLoad = false;
        })
        .catch(err => {
            console.warn('Notifikasi poll gagal:', err);
        });
    }

    // Mark single notification as read
    function markAsRead(id) {
        fetch('index.php?action=notifikasi_read', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify({ id: id })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                fetchNotifications();
            }
        });
    }

    // Delete single notification
    function deleteNotif(id) {
        fetch('index.php?action=notifikasi_delete', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify({ id: id })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                fetchNotifications();
            }
        });
    }

    // Mark all as read
    markAllBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        fetch('index.php?action=notifikasi_read_all', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                fetchNotifications();
            }
        });
    });

    // Delete all
    deleteAllBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        if (!confirm('Hapus semua notifikasi? Tindakan ini tidak dapat dibatalkan.')) return;
        fetch('index.php?action=notifikasi_delete_all', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                fetchNotifications();
            }
        });
    });

    // Initial fetch + start polling
    fetchNotifications();
    pollTimer = setInterval(fetchNotifications, POLL_INTERVAL);

    // Stop polling when tab is hidden, resume when visible
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            clearInterval(pollTimer);
        } else {
            fetchNotifications();
            pollTimer = setInterval(fetchNotifications, POLL_INTERVAL);
        }
    });
})();
</script>

<style>
    #notif-panel { transition: transform 0.25s cubic-bezier(0.16,1,0.3,1), opacity 0.2s ease; }
    #notif-list .notif-item { animation: notifSlideIn 0.3s ease forwards; opacity: 0; transform: translateY(6px); }
    @keyframes notifSlideIn { to { opacity: 1; transform: translateY(0); } }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    #notif-list.custom-scrollbar::-webkit-scrollbar { width: 4px; }
    #notif-list.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.12); border-radius: 4px; }
</style>
