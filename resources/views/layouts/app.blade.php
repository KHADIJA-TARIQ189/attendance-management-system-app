<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Attendance System')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #sidebar { --bs-offcanvas-bg: #212529; --bs-offcanvas-color: #fff; width: 240px; flex-shrink: 0; }
        @media (min-width: 992px) { #sidebar { background-color: #212529 !important; } }
        #sidebar .nav-link { color: rgba(255,255,255,.75); border-radius: .375rem; }
        #sidebar .nav-link:hover { background: rgba(255,255,255,.08); color: #fff; }
        #sidebar .nav-link.active { background: #0d6efd; color: #fff; }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand navbar-dark bg-dark px-3 sticky-top">
    @auth
    <button class="btn btn-outline-light btn-sm d-lg-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar" aria-label="Open menu">&#9776;</button>
    @endauth
    <a class="navbar-brand" href="{{ route('dashboard') }}">📋 Attendance & Gradebook System</a>
    @auth
    <div class="ms-auto d-flex align-items-center gap-3">
        <span class="text-white-50">{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</span>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-sm btn-outline-light">Logout</button>
        </form>
    </div>
    @endauth
</nav>

<div class="d-flex">
    @auth
    @php
        $menu = [
            ['dashboard',       '🏠', 'Dashboard',  ['dashboard']],
            ['menu.attendance', '📅', 'Attendance', ['menu.attendance', 'teacher.attendance.*', 'student.attendance']],
            ['menu.gradebook',  '📝', 'Gradebook',  ['menu.gradebook', 'teacher.gradebook', 'teacher.quizzes.*', 'teacher.assignments.*', 'student.gradebook']],
            ['profile',         '👤', 'Profile',    ['profile*']],
            ['settings',        '⚙️', 'Settings',   ['settings*']],
        ];
    @endphp
    <aside class="offcanvas-lg offcanvas-start" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
        <div class="offcanvas-header d-lg-none">
            <h5 class="offcanvas-title" id="sidebarLabel">Menu</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <nav class="nav flex-column gap-1 p-3 w-100">
                @foreach ($menu as [$route, $icon, $label, $active])
                    <a class="nav-link {{ request()->routeIs(...$active) ? 'active' : '' }}" href="{{ route($route) }}">{{ $icon }} {{ $label }}</a>
                @endforeach

                @if (auth()->user()->isAdmin())
                    <hr class="border-secondary my-2">
                    <small class="text-white-50 px-3">Admin</small>
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">👥 Users</a>
                    <a class="nav-link {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}" href="{{ route('admin.courses.index') }}">📚 Courses</a>
                @endif
            </nav>
        </div>
    </aside>
    @endauth

    <main class="flex-grow-1" style="min-width:0">
        <div class="container py-4">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
