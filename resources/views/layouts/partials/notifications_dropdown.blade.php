{{-- Chỉ hiển thị nếu người dùng đã đăng nhập --}}
@auth
    <div class="position-relative me-3" x-data="{ open: false, notifications: {{ Auth::user()->unreadNotifications->toJson() }}, markAsReadLink: '{{ url('/notifications') }}', markAllAsReadLink: '{{ url('/notifications/read-all') }}' }" @click.outside="open = false" x-init="window.Echo.private('users.{{ Auth::id() }}')
        .listen('.ModulePublished', (e) => {
            const newNotification = {
                id: 'fake_id_' + Date.now(),
                data: {
                    module_id: e.id,
                    module_name: e.name,
                    status: e.status,
                    description: e.message,
                    link: e.link,
                    icon: 'bell'
                },
                created_at: new Date().toISOString(),
                read_at: null
            };
            this.notifications.unshift(newNotification);
    
            Toastify({
                text: newNotification.data.description,
                duration: 5000,
                newWindow: true,
                close: true,
                gravity: 'top',
                position: 'right',
                stopOnFocus: true,
                style: {
                    background: '#4CAF50',
                    cursor: 'pointer',
                },
                onClick: function() {
                    window.location.href = newNotification.data.link;
                }
            }).showToast();
        });">
        <div>
            <button @click="open = !open" class="btn btn-link position-relative p-0 border-0">
                <span class="visually-hidden">Open notifications</span>
                <i class="bi bi-bell fs-4 text-secondary"></i>
                <span x-show="notifications.filter(n => !n.read_at).length > 0"
                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    <span x-text="notifications.filter(n => !n.read_at).length"></span>
                </span>
            </button>
        </div>

        <div x-show="open" x-transition:enter="transition fade-in" x-transition:leave="transition fade-out"
            class="dropdown-menu dropdown-menu-end shadow show mt-2 p-0"
            style="min-width: 22rem; max-width: 24rem; max-height: 22rem; overflow-y: auto; z-index: 1050;" role="menu"
            aria-orientation="vertical" tabindex="-1">
            <div class="px-3 py-2 small text-secondary border-bottom">
                {{ __('Thông báo của bạn') }}
            </div>
            <div>
                <template x-for="notification in notifications" :key="notification.id">
                    <a :href="notification.data.link || '#'"
                        class="dropdown-item d-flex flex-column px-3 py-2 border-bottom"
                        :class="{
                            'bg-light': !notification.read_at,
                            'text-secondary': notification.read_at
                        }"
                        role="menuitem" tabindex="-1"
                        @click.prevent="if (!notification.read_at) markNotificationAsRead(notification.id); window.location.href = notification.data.link;">
                        <div class="fw-bold" :class="{ 'fw-normal': notification.read_at }">
                            <span x-text="notification.data.icon === 'bell' ? '🔔 ' : ''"></span>
                            <span x-html="notification.data.description"></span>
                        </div>
                        <div class="small text-muted mt-1" x-text="notification.created_at"></div>
                    </a>
                </template>
                <p x-show="notifications.length === 0" class="dropdown-item text-center text-muted small mb-0">
                    {{ __('Không có thông báo nào.') }}
                </p>
                <a href="#" x-show="notifications.filter(n => !n.read_at).length > 0" @click.prevent="markAllAsRead()"
                    class="dropdown-item text-end text-primary small">
                    {{ __('Đánh dấu tất cả đã đọc') }}
                </a>
            </div>
        </div>
    </div>

    <script>
        function markNotificationAsRead(notificationId) {
            fetch(this.markAsReadLink + '/' + notificationId + '/read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const index = this.notifications.findIndex(n => n.id === notificationId);
                        if (index !== -1) {
                            this.notifications[index].read_at = new Date().toISOString();
                        }
                    }
                })
                .catch(error => console.error('Error marking notification as read:', error));
        }

        function markAllAsRead() {
            fetch(this.markAllAsReadLink, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.notifications = this.notifications.map(n => ({
                            ...n,
                            read_at: new Date().toISOString()
                        }));
                    }
                })
                .catch(error => console.error('Error marking all notifications as read:', error));
        }
    </script>
@endauth
