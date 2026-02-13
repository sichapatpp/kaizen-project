<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <style>
        body {
            background: linear-gradient(135deg, #dfe4f2 0%, #e5e5f7 100%);
            min-height: 100vh;
            margin: 0;
            font-family: 'Nunito', sans-serif;
        }

        /* Navbar Styling */
        .navbar {
            background-color: #ffffff !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            padding: 1rem 0;
        }
        
        .navbar-brand {
            color: #1e293b !important;
            font-weight: 700;
            font-size: 1.25rem;
        }
        
        .nav-link {
            color: #64748b !important;
            font-weight: 500;
        }

        /* Main Content Area - จัดให้อยู่กึ่งกลาง */
        main {
            min-height: calc(100vh - 72px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        /* Card Styling (กรอบสีขาว) */
        .auth-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 2.5rem 3rem;
            max-width: 500px;
            width: 100%;
        }

        .auth-card-header {
            background-color: #f8f9fa;
            padding: 1rem 1.5rem;
            margin: -2.5rem -3rem 2rem -3rem;
            border-radius: 12px 12px 0 0;
            border-bottom: 1px solid #e9ecef;
            text-align: center;
        }

        .auth-card-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .form-label {
            font-weight: 500;
            color: #475569;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            display: block;
            text-align: left;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-control {
            border: 2px solid #cbd5e1;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            width: 100%;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            outline: none;
        }

        /* ปุ่มสีม่วง Gradient */
        .btn-primary-custom {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border: none;
            border-radius: 8px;
            padding: 0.85rem;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            color: #ffffff;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4);
            filter: brightness(1.1);
        }

        .btn-link {
            color: #6366f1;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .form-check {
            margin: 1rem 0;
            display: flex;
            align-items: center;
        }

        .form-check-input {
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">Kaizen System</a>
                <div class="ms-auto">
                    @guest
                        <a class="nav-link d-inline-block me-3" href="{{ route('login') }}">Login</a>
                        <a class="nav-link d-inline-block" href="{{ route('register') }}">Register</a>
                    @endguest
                </div>
            </div>
        </nav>

        <main>
            @yield('content')
        </main>
    </div>
</body>
</html>