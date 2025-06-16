<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Hệ thống quản lý sinh viên')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: #343a40;
            color: #fff;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.5rem 1rem;
            margin: 0.2rem 0;
        }
        
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar .nav-link.active {
            color: #fff;
            background-color: #007bff;
        }
        
        .sidebar .nav-link i {
            margin-right: 0.5rem;
        }
        
        .content {
            padding: 20px;
        }
        
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 20px;
        }
        
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
            font-weight: 600;
        }
        
        .table th {
            background-color: #f8f9fa;
        }
        
        /* Custom pagination styles */
        .pagination {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .pagination .page-item {
            margin: 0 2px;
        }
        
        .pagination .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 12px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            color: #495057;
            background-color: #fff;
            border: 1px solid #dee2e6;
            transition: all 0.2s ease-in-out;
        }
        
        .pagination .page-link:hover {
            z-index: 2;
            color: #0056b3;
            text-decoration: none;
            background-color: #e9ecef;
            border-color: #dee2e6;
        }
        
        .pagination .page-item.active .page-link {
            z-index: 3;
            color: #fff;
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        
        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            pointer-events: none;
            background-color: #fff;
            border-color: #dee2e6;
        }
        
        .pagination-container {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 12px 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        /* Sidebar collapse on small screens */
        @media (max-width: 768px) {
            body.sidebar-collapsed .sidebar {
                display: none;
            }
            body.sidebar-collapsed .content {
                margin-left: 0 !important;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-md navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <i class="fas fa-graduation-cap me-2"></i>Quản lý sinh viên
                </a>
                <button id="sidebarToggle" class="btn btn-outline-light d-md-none me-2" type="button">
                    <i class="fas fa-bars"></i>
                </button>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard.index') }}">Dashboard</a>
                            </li>
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">Đăng nhập</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">Đăng ký</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        Đăng xuất
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="container-fluid">
        <div class="row">
            @auth
                <div class="col-md-2 sidebar py-3">
                    <div class="d-flex flex-column">
                        <div class="p-2 mb-3 border-bottom">
                            <div class="text-center">
                                <i class="fas fa-user-circle fa-3x mb-2"></i>
                                <div>{{ Auth::user()->name }}</div>
                                <small class="text-muted">{{ ucfirst(Auth::user()->role) }}</small>
                            </div>
                        </div>
                        
                        <div class="nav flex-column">
                            <a class="nav-link {{ request()->routeIs('dashboard.*') ? 'active' : '' }}" href="{{ route('dashboard.index') }}">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>

                            @if(Auth::user()->role == 'admin' || Auth::user()->role == 'teacher')
                                @php
                                    $isActive = request()->routeIs(
                                        'faculties.*',
                                        'majors.*',
                                        'classes.*',
                                        'subjects.*',
                                        'teachers.*',
                                        'students.*',
                                        'grades.*'
                                    );
                                @endphp
                                <div class="nav-group">
                                    <a class="nav-link" data-bs-toggle="collapse" href="#managementMenu" role="button" aria-expanded="{{ $isActive ? 'true' : 'false' }}" aria-controls="managementMenu">
                                        <i class="fas fa-users-cog"></i> Quản lý
                                    </a>
                                    <div class="collapse {{ $isActive ? 'show' : '' }}" id="managementMenu">
                                        <ul class="nav flex-column ms-3">
                                            @if(Auth::user()->role == 'admin')
                                                <li class="nav-item">
                                                    <a class="nav-link {{ request()->routeIs('faculties.*') ? 'active' : '' }}" href="{{ route('faculties.index') }}">
                                                        <i class="fas fa-building"></i> Quản lý khoa
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link {{ request()->routeIs('majors.*') ? 'active' : '' }}" href="{{ route('majors.index') }}">
                                                        <i class="fas fa-book"></i> Quản lý ngành học
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link {{ request()->routeIs('classes.*') ? 'active' : '' }}" href="{{ route('classes.index') }}">
                                                        <i class="fas fa-users"></i> Quản lý lớp học
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link {{ request()->routeIs('subjects.*') ? 'active' : '' }}" href="{{ route('subjects.index') }}">
                                                        <i class="fas fa-book-open"></i> Quản lý môn học
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link {{ request()->routeIs('teachers.*') ? 'active' : '' }}" href="{{ route('teachers.index') }}">
                                                        <i class="fas fa-chalkboard-teacher"></i> Quản lý giáo viên
                                                    </a>
                                                </li>
                                            @endif
                                            <li class="nav-item">
                                                <a class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}" href="{{ route('students.index') }}">
                                                    <i class="fas fa-user-graduate"></i> Quản lý sinh viên
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link {{ request()->routeIs('grades.*') ? 'active' : '' }}" href="{{ route('grades.index') }}">
                                                    <i class="fas fa-chart-bar"></i> Quản lý điểm số
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            @if(Auth::user()->role == 'admin')
                                @php
                                    $isActive = request()->routeIs(
                                        'academic-years.*',
                                        'semesters.*',
                                        'degrees.*',
                                        'class-size-coefficients.*',
                                        'teaching-rates.*'
                                    );
                                @endphp
                                <div class="nav-group">
                                    <a class="nav-link" data-bs-toggle="collapse" href="#configMenu" role="button" aria-expanded="{{ $isActive ? 'true' : 'false' }}" aria-controls="configMenu">
                                        <i class="fas fa-sliders-h"></i> Cấu hình
                                    </a>
                                    <div class="collapse {{ $isActive ? 'show' : '' }}" id="configMenu">
                                        <ul class="nav flex-column ms-3">
                                            <li class="nav-item">
                                                <a class="nav-link {{ request()->routeIs('academic-years.*') ? 'active' : '' }}" href="{{ route('academic-years.index') }}">
                                                    <i class="fas fa-calendar-alt"></i> Quản lý năm học
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link {{ request()->routeIs('semesters.*') ? 'active' : '' }}" href="{{ route('semesters.index') }}">
                                                    <i class="fas fa-calendar"></i> Quản lý học kỳ
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link {{ request()->routeIs('degrees.*') ? 'active' : '' }}" href="{{ route('degrees.index') }}">
                                                    <i class="fas fa-award"></i> Quản lý học vị
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link {{ request()->routeIs('class-size-coefficients.*') ? 'active' : '' }}" href="{{ route('class-size-coefficients.index') }}">
                                                    <i class="fas fa-users-cog"></i> Hệ số sĩ số lớp
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link {{ request()->routeIs('teaching-rates.*') ? 'active' : '' }}" href="{{ route('teaching-rates.index') }}">
                                                    <i class="fas fa-money-bill-wave"></i> Mức lương giảng dạy
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="nav-group">
                                    @php
                                        $isActive = request()->routeIs(
                                            'course-offerings.*',
                                            'class-sections.*',
                                            'reports.*'
                                        );
                                    @endphp
                                    <a class="nav-link" data-bs-toggle="collapse" href="#operationsMenu" role="button" aria-expanded="{{ $isActive ? 'true' : 'false' }}" aria-controls="operationsMenu">
                                        <i class="fas fa-cogs"></i> Nghiệp vụ
                                    </a>
                                    <div class="collapse {{ $isActive ? 'show' : '' }}" id="operationsMenu">
                                        <ul class="nav flex-column ms-3">
                                            <li class="nav-item">
                                                <a class="nav-link {{ request()->routeIs('course-offerings.*') ? 'active' : '' }}" href="{{ route('course-offerings.index') }}">
                                                    <i class="fas fa-calendar-alt"></i> Mở môn học
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link {{ request()->routeIs('class-sections.*') ? 'active' : '' }}" href="{{ route('class-sections.index') }}">
                                                    <i class="fas fa-list"></i> Lớp học phần
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.sections') }}">
                                                    <i class="fas fa-chart-pie"></i> Báo cáo
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            @if(Auth::user()->role == 'admin' || Auth::user()->role == 'teacher')
                                <a class="nav-link {{ request()->routeIs('payrolls.*') ? 'active' : '' }}" href="{{ route('payrolls.index') }}">
                                    <i class="fas fa-money-check-alt"></i> Bảng lương
                                </a>
                            @endif

                            @if(Auth::user()->role == 'student' && Auth::user()->student)
                                <a class="nav-link {{ request()->routeIs('students.transcript') ? 'active' : '' }}" href="{{ route('students.transcript', Auth::user()->student->id) }}">
                                    <i class="fas fa-chart-line"></i> Bảng điểm
                                </a>
                                <a class="nav-link {{ request()->routeIs('enrollments.*') ? 'active' : '' }}" href="{{ route('enrollments.index') }}">
                                    <i class="fas fa-book-open"></i> Đăng ký lớp
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                
                <main class="col-md-10 content">
                    @include('partials.alerts')
                    @yield('content')
                </main>
            @else
                <main class="col-12 py-4">
                    @include('partials.alerts')
                    @yield('content')
                </main>
            @endauth
        </div>
    </div>

    @include('layouts.footer')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Toggle sidebar on mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    if (window.innerWidth < 768) {
                        document.body.classList.toggle('sidebar-collapsed');
                    }
                });
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html> 