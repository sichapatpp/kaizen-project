<!doctype html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <link rel="shortcut icon" type="image/png" href="./assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/custom.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/activities.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/status.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/approve.css') }}" />
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
            <button class="bell-btn" aria-label="Notifications">
                <i class="fas fa-bell"></i>
                <span class="badge"></span>
            </button>
            <div class="user-section">
                <div class="user-info">
                    <div class="name">สิชาพัทธ สุขวิชัย</div>
                    <div class="role">พนักงาน</div>
                </div>
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <i class="fas fa-sign-out-alt logout-arrow"></i>
            </div>
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
    <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebarmenu.js"></script>
    <script src="./assets/js/app.min.js"></script>
    <script src="./assets/libs/simplebar/dist/simplebar.js"></script>
    <!-- solar icons -->
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>

</html>
