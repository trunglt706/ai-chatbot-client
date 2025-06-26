{{-- Chỉ hiển thị nếu người dùng đã đăng nhập --}}
@auth
    <div class="relative me-3" x-data="{ open: false, notifications: {{ Auth::user()->unreadNotifications->toJson() }}, markAsReadLink: '{{ url('/notifications') }}', markAllAsReadLink: '{{ url('/notifications/read-all') }}' }" @click.outside="open = false" x-init="window.Echo.private('users.{{ Auth::id() }}')
        .listen('.ModulePublished', (e) => {
            const newNotification = {
                id: 'fake_id_' + Date.now(), // ID tạm thời cho thông báo real-time
                data: {
                    module_id: e.id,
                    module_name: e.name,
                    status: e.status,
                    description: e.message,
                    link: e.link,
                    icon: 'bell'
                },
                created_at: new Date().toISOString(), // Dùng thời gian hiện tại
                read_at: null // Mặc định là chưa đọc
            };
            // Thêm thông báo vào đầu danh sách
            this.notifications.unshift(newNotification);

            // Tạo một flash message hoặc toast notification
            Toastify({
                text: newNotification.data.description,
                duration: 5000,
                newWindow: true,
                close: true,
                gravity: 'top',
                position: 'right',
                stopOnFocus: true,
                style: {
                    background: '#4CAF50', // Màu xanh lá
                    cursor: 'pointer',
                },
                onClick: function() {
                    // Chuyển hướng khi click vào Toast
                    window.location.href = newNotification.data.link;
                }
            }).showToast();

        });">
        <div>
            <button @click="open = !open"
                class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition duration-150 ease-in-out">
                <span class="sr-only">Open user menu</span>
                <svg class="h-6 w-6 text-gray-500 hover:text-gray-700" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.405L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span x-show="notifications.filter(n => !n.read_at).length > 0"
                    class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                    <span x-text="notifications.filter(n => !n.read_at).length"></span>
                </span>
            </button>
        </div>

        <div x-show="open" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute right-0 z-10 mt-2 w-80 origin-top-right rounded-md bg-white dark:bg-gray-700 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none max-h-80 overflow-y-auto"
            role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
            <div class="px-4 py-2 text-xs text-gray-500 dark:text-gray-300">
                Thông báo của bạn
            </div>
            <div class="py-1">
                <template x-for="notification in notifications" :key="notification.id">
                    <a :href="notification.data.link || '#'"
                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 border-b dark:border-gray-600"
                        :class="{
                            'hover:bg-gray-100 dark:hover:bg-gray-600': !notification
                                .read_at,
                            'bg-gray-50 dark:bg-gray-800': notification.read_at
                        }"
                        role="menuitem" tabindex="-1"
                        @click.prevent="if (!notification.read_at) markNotificationAsRead(notification.id); window.location.href = notification.data.link;">
                        <div class="font-bold" :class="{ 'font-normal': notification.read_at }">
                            <span x-text="notification.data.icon === 'bell' ? '🔔 ' : ''"></span>
                            <span x-html="notification.data.description"></span>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="notification.created_at"></div>
                    </a>
                </template>
                <p x-show="notifications.length === 0" class="block px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                    Không có thông báo nào.
                </p>
                <a href="#" x-show="notifications.filter(n => !n.read_at).length > 0" @click.prevent="markAllAsRead()"
                    class="block px-4 py-2 text-xs text-indigo-600 dark:text-indigo-400 hover:bg-gray-100 dark:hover:bg-gray-600 text-right">
                    Đánh dấu tất cả đã đọc
                </a>
            </div>
        </div>
    </div>

    {{-- Script cho Toastify và mark as read --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

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
                            this.notifications[index].read_at = new Date().toISOString(); // Đánh dấu đã đọc
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
