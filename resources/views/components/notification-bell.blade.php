<div id="notification-bell"
     class="dropdown"
     data-recent-url="{{ route('usulan.notifications.recent') }}"
     data-mark-all-url="{{ route('usulan.notifications.mark-all-read') }}"
     data-index-url="{{ route('usulan.notifications.index') }}"
     data-unread-url="{{ route('usulan.notifications.unread-count') }}">
    <button type="button" id="notification-bell-button" class="btn btn-link position-relative">
        <i class="fas fa-bell"></i>
        <span id="notification-bell-count"
              class="top-0 position-absolute start-100 translate-middle badge rounded-pill bg-danger d-none"
              style="font-size: 0.6rem;">0</span>
    </button>

    <div id="notification-bell-menu" class="dropdown-menu dropdown-menu-right" style="width: 350px; max-height: 400px; overflow-y: auto; display: none;">
        <div class="dropdown-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Notifikasi</h6>
            <button type="button" id="notification-mark-all" class="btn btn-sm btn-link">Tandai Semua Dibaca</button>
        </div>

        <div id="notification-empty" class="text-center dropdown-item text-muted d-none">
            <i class="mb-2 fas fa-bell-slash"></i>
            <p class="mb-0">Tidak ada notifikasi</p>
        </div>

        <div id="notification-list"></div>

        <div class="dropdown-divider"></div>
        <div class="text-center dropdown-item">
            <a id="notification-view-all" href="{{ route('usulan.notifications.index', ['filter' => 'unread']) }}" class="btn btn-sm btn-outline-primary">
                Lihat Semua Notifikasi
            </a>
        </div>
    </div>
</div>

<script>
(function() {
    const root = document.getElementById('notification-bell');
    if (!root) return;

    const recentUrl = root.getAttribute('data-recent-url');
    const markAllUrl = root.getAttribute('data-mark-all-url');
    const indexUrl = root.getAttribute('data-index-url');
    const unreadUrl = root.getAttribute('data-unread-url');

    const btn = document.getElementById('notification-bell-button');
    const menu = document.getElementById('notification-bell-menu');
    const badge = document.getElementById('notification-bell-count');
    const list = document.getElementById('notification-list');
    const empty = document.getElementById('notification-empty');
    const markAllBtn = document.getElementById('notification-mark-all');

    let isOpen = false;
    let notifications = [];
    let unreadCount = 0;

    function toggleMenu(forceState) {
        isOpen = typeof forceState === 'boolean' ? forceState : !isOpen;
        menu.style.display = isOpen ? 'block' : 'none';
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffInMinutes = Math.floor((now - date) / (1000 * 60));
        if (diffInMinutes < 1) return 'Baru saja';
        if (diffInMinutes < 60) return `${diffInMinutes} menit yang lalu`;
        if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)} jam yang lalu`;
        return date.toLocaleDateString('id-ID');
    }

    function getIcon(type) {
        const map = {
            'usulan_baru': 'fas fa-file-alt',
            'verifikasi_selesai': 'fas fa-check-circle',
            'usulan_diterima': 'fas fa-thumbs-up',
            'usulan_ditolak': 'fas fa-times-circle',
        };
        return map[type] || 'fas fa-bell';
    }

    function getColor(type) {
        const map = {
            'usulan_baru': '#007bff',
            'verifikasi_selesai': '#ffc107',
            'usulan_diterima': '#28a745',
            'usulan_ditolak': '#dc3545',
        };
        return map[type] || '#6c757d';
    }

    function render() {
        const count = Number(unreadCount) || 0;
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : String(count);
            badge.classList.remove('d-none');
        } else {
            badge.classList.add('d-none');
        }

        list.innerHTML = '';
        if (!notifications.length) {
            empty.classList.remove('d-none');
            return;
        }
        empty.classList.add('d-none');

        notifications.forEach(n => {
            const type = (n.data && n.data.type) || null;
            const wrap = document.createElement('div');
            wrap.className = `dropdown-item ${!n.read_at ? 'bg-light' : ''}`;
            wrap.setAttribute('data-open-unread', '1');
            wrap.innerHTML = `
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0">
                        <i class="${getIcon(type)}" style="color: ${getColor(type)}"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">${(n.data && n.data.title) || 'Notifikasi'}</h6>
                        <p class="mb-1 text-muted small">${(n.data && n.data.message) || ''}</p>
                        <small class="text-muted">${formatDate(n.created_at)}</small>
                    </div>
                    <div class="flex-shrink-0">
                        <button type="button" class="btn btn-sm btn-link text-danger" data-delete-id="${n.id}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>`;
            list.appendChild(wrap);
        });
    }

    function load() {
        fetch(recentUrl, { credentials: 'same-origin' })
            .then(r => r.json())
            .then(json => {
                if (json && json.success) {
                    notifications = json.data || [];
                    render();
                }
            })
            .catch(() => {});
    }

    function loadCount() {
        if (!unreadUrl) return;
        fetch(unreadUrl, { credentials: 'same-origin' })
            .then(r => r.json())
            .then(json => {
                if (json && json.success && typeof json.count !== 'undefined') {
                    unreadCount = json.count;
                    render();
                }
            })
            .catch(() => {});
    }

    function markAll() {
        fetch(markAllUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
            credentials: 'same-origin'
        }).then(r => r.json()).then(json => {
            if (json && json.success) {
                const now = new Date().toISOString();
                notifications = notifications.map(n => ({ ...n, read_at: now }));
                unreadCount = 0;
                render();
            }
        }).catch(() => {});
    }

    function remove(id) {
        const numericId = Number(id);
        const toRemove = notifications.find(n => Number(n.id) === numericId);
        fetch(`${indexUrl}/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
            credentials: 'same-origin'
        }).then(r => r.json()).then(json => {
            if (json && json.success) {
                notifications = notifications.filter(n => Number(n.id) !== numericId);
                if (toRemove && !toRemove.read_at) {
                    unreadCount = Math.max(0, (Number(unreadCount) || 0) - 1);
                }
                render();
            }
        }).catch(() => {});
    }

    // Events
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        toggleMenu();
    });

    document.addEventListener('click', function(e) {
        if (!root.contains(e.target)) {
            toggleMenu(false);
        }
    });

    markAllBtn.addEventListener('click', function(e) {
        e.preventDefault();
        markAll();
    });

    list.addEventListener('click', function(e) {
        const btn = e.target.closest('[data-delete-id]');
        if (btn) {
            const id = btn.getAttribute('data-delete-id');
            remove(id);
            return;
        }
        const open = e.target.closest('[data-open-unread]');
        if (open) {
            window.location.href = `${indexUrl}?filter=unread`;
        }
    });

    // Init
    load();
    loadCount();
    setInterval(load, 30000);
    setInterval(loadCount, 30000);
})();
</script>
