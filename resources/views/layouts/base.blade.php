<!doctype html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/custom.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/activities.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/status.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/approve.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <!-- ─── TOP NAVBAR ─── -->
    <header class="topbar">
        <div class="topbar-left">
            <div class="logo-badge">K</div>
            <div class="logo-text">
                <span class="brand">Kaizen System</span>
                <span class="sub">ฝ่ายซ่อม</span>
            </div>
        </div>

        <nav class="topbar-center">
            <a href={{ route('dashboard') }} class="nav-link active">
                <i class="fas fa-th-large nav-icon"></i> Dashboard
            </a>
            <a href= {{ route('activities.index') }} class="nav-link">
                <i class="fas fa-plus-circle nav-icon"></i> สร้างกิจกรรม
            </a>
            <a href={{ route('activities.status') }} class="nav-link">
               <i class="fas fa-list-alt si-icon"></i> ติดตามสถานะ
            </a>
           @if(auth()->check() && auth()->user()?->role?->role_name !== 'user')
     <a href="{{ route('activities.approve') }}" class="sidebar-item {{ request()->routeIs('activities.approve') ? 'active' : '' }}">
         <i class="fas fa-trophy si-icon"></i> อนุมัติและรายงาน
     </a>
     @endif
        </nav>

        <div class="topbar-right align-items-center d-flex gap-3">
            @auth
                <!-- Notification Bell -->
                <div class="nav-item dropdown">
                    <a class="nav-link position-relative notification-bell" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell fs-5"></i>
                        <span class="position-absolute top-10 start-80 translate-middle badge rounded-pill bg-danger" id="notification-count" style="display: none;">
                            0
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end p-0 shadow overflow-hidden" aria-labelledby="notificationDropdown" style="width: 320px; max-height: 400px; overflow-y: auto;">
                        <li class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                            <span class="fw-bold">การแจ้งเตือน</span>
                            <a href="#" class="text-primary text-decoration-none small" id="mark-all-read">อ่านทั้งหมด</a>
                        </li>
                        <div id="notification-list">
                            <li class="p-3 text-center text-muted small">ไม่มีการแจ้งเตือนใหม่</li>
                        </div>
                    </ul>
                </div>
            @endauth

            @guest
                <a href="{{ route('login') }}" class="btn-primary" style="text-decoration:none; padding: 8px 16px; border-radius: 6px; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-sign-in-alt"></i> ลงชื่อเข้าใช้
                </a>
            @else
                <div class="user-section dropdown">
                    <div class="d-flex align-items-center" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                        <div class="user-info">
                            <div class="name">{{ Auth::user()->name }}</div>
                            <div class="role">{{ Auth::user()->role->role_name ?? 'พนักงาน' }}</div>
                        </div>
                        <div class="user-avatar ms-2">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                            <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i> ออกจากระบบ
                            </a>
                        </li>
                    </ul>
                </div>
            @endguest
        </div>
    </header>
    <!-- Sidebar Start -->
    @include('layouts.partials.sidebar')
    <!--  Sidebar End -->
    <!--  Main wrapper -->
    <main class="main-content">
        @yield('content')

    </main>

    </div>
    </div>
    <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('assets/js/app.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/dist/simplebar.js') }}"></script>
    <!-- solar icons -->
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

    @auth
    <script>
        $(document).ready(function() {
            function fetchNotifications() {
                $.ajax({
                    url: '{{ route("notifications.unread") }}',
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            const countLabel = $('#notification-count');
                            if (response.count > 0) {
                                countLabel.text(response.count).show();
                            } else {
                                countLabel.hide();
                            }

                            const listContainer = $('#notification-list');
                            listContainer.empty();

                            if (response.notifications.length > 0) {
                                response.notifications.forEach(function(notification) {
                                    // Make notification item clickable to mark as read
                                    const bgColor = notification.is_read ? 'bg-white' : 'bg-light';
                                    const textClass = notification.is_read ? 'text-dark' : 'text-primary fw-bold';
                                    const kaizenUrl = notification.kaizen_project_id ? `/activities/show/${notification.kaizen_project_id}` : '/activities/status';

                                    const li = `
                                        <li class="p-3 border-bottom notification-item ${bgColor}" data-id="${notification.id}" data-url="${kaizenUrl}">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1 ${textClass}" style="font-size: 0.85rem;">${notification.title}</h6>
                                                <small class="text-muted" style="font-size: 0.75rem;">${notification.created_at}</small>
                                            </div>
                                            <p class="mb-0 text-dark small" style="white-space: normal;">${notification.message}</p>
                                        </li>
                                    `;
                                    listContainer.append(li);
                                });
                            } else {
                                listContainer.append('<li class="p-3 text-center text-muted small">ไม่มีการแจ้งเตือนใหม่</li>');
                            }
                        }
                    }
                });
            }

            // Initial fetch
            fetchNotifications();

            // Poll every 30 seconds
            setInterval(fetchNotifications, 30000);

            // Mark single notification as read
            $(document).on('click', '.notification-item', function() {
                const id = $(this).data('id');
                $.ajax({
                    url: `/notifications/${id}/read`,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        fetchNotifications();
                    }
                });
            });

            // Mark all as read
            $('#mark-all-read').on('click', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '{{ route("notifications.readAll") }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        fetchNotifications();
                    }
                });
            });
        });
    </script>
    @endauth
</body>

</html>
