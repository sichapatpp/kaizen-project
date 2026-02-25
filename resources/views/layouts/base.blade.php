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
                <i class="fas fa-list nav-icon"></i> ติดตามสถานะ
            </a>
            <a href={{ route('activities.approve') }} class="nav-link">
                <i class="fas fa-list nav-icon"></i> อนุมัติและรายงาน
            </a>
        </nav>

        <div class="topbar-right">
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
</body>

</html>
