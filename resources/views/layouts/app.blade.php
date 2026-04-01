<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIMPELAJAR') - Sistem Monitoring Dokumen Pembelajaran Teknik Biosistem ITERA</title>
    <link rel="icon" href="{{asset('storage/images/TBS.png')}}" type="image/png">
    <!-- Google Fonts: Plus Jakarta Sans (Modern & Professional) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --sidebar-active-bg: linear-gradient(90deg, #0d9488 0%, #14b8a6 100%);
            --accent-color: #14b8a6;
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --bg-body: #f1f5f9;
            --card-bg: #ffffff;
            --transition-speed: 0.3s;
        }

        body {
            background-color: var(--bg-body);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-primary);
            overflow-x: hidden;
        }

        /* =========================================
           SIDEBAR STYLES
        ========================================= */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            transition: transform var(--transition-speed) ease;
            box-shadow: 4px 0 25px rgba(0, 0, 0, 0.1);
        }

        /* Brand Section */
        .sidebar-brand {
            padding: 1.75rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .sidebar-brand h5 {
            color: #ffffff;
            font-weight: 700;
            font-size: 1.25rem;
            letter-spacing: -0.5px;
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .sidebar-brand h5 i { color: var(--accent-color); font-size: 1.5rem; }
        .sidebar-brand small { color: #94a3b8; font-size: 0.75rem; font-weight: 500; }

        /* Navigation */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem 0;
        }
        .sidebar-nav::-webkit-scrollbar { width: 5px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

        .sidebar-section {
            color: #64748b;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 1rem 1.75rem 0.5rem;
            margin-top: 0.5rem;
        }

        .nav-link {
            color: #cbd5e1;
            padding: 0.75rem 1.5rem;
            margin: 0.15rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
            position: relative;
        }

        .nav-link i {
            width: 22px;
            font-size: 1.1rem;
            margin-right: 0.75rem;
            transition: transform 0.2s;
        }

        .nav-link:hover {
            background-color: var(--sidebar-hover);
            color: #ffffff;
        }
        .nav-link:hover i { transform: scale(1.1); }

        /* Active State - Elegant Gradient */
        .nav-link.active {
            background: var(--sidebar-active-bg);
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(20, 184, 166, 0.3);
        }
        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0px;
            top: 10%;
            bottom: 10%;
            width: 4px;
            background: #ffffff;
            border-radius: 0 4px 4px 0;
        }

        /* User Profile Card */
        .sidebar-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,0.05);
        }
        .user-card {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            background: rgba(255,255,255,0.03);
            border-radius: 12px;
            gap: 0.75rem;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            flex-shrink: 0;
        }
        .user-info { overflow: hidden; }
        .user-name {
            color: #fff;
            font-weight: 600;
            font-size: 0.875rem;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }
        .user-role {
            font-size: 0.7rem;
            padding: 0.15rem 0.5rem;
            border-radius: 20px;
            font-weight: 600;
        }
        .badge-gkmp { background: rgba(251, 191, 36, 0.2); color: #fbbf24; }
        .badge-dosen { background: rgba(56, 189, 248, 0.2); color: #38bdf8; }

        /* =========================================
           MAIN CONTENT STYLES
        ========================================= */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left var(--transition-speed) ease;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 1040;
        }

        .toggle-btn {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .toggle-btn:hover {
            background: var(--accent-color);
            border-color: var(--accent-color);
            color: #fff;
        }

        .content-area {
            padding: 2rem;
            flex: 1;
        }

        .card {
            border: none;
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
        }

        /* Alert Styling */
        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
            animation: slideIn 0.4s ease forwards;
        }
        .alert-success { background: #ecfdf5; color: #047857; }
        .alert-danger { background: #fef2f2; color: #b91c1c; }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* =========================================
           RESPONSIVE & MOBILE CONFIG
        ========================================= */

        /* Overlay for mobile */
        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.5);
            z-index: 1045;
            opacity: 0;
            visibility: hidden;
            transition: opacity var(--transition-speed) ease;
        }

        /* Breakpoint LG (992px) */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .sidebar-backdrop.show {
                opacity: 1;
                visibility: visible;
            }
            .toggle-btn {
                display: flex !important; /* Ensure toggle is visible */
            }
        }

        @media (min-width: 992px) {
            .toggle-btn {
                display: none !important; /* Hide toggle on desktop */
            }
            .sidebar-backdrop {
                display: none !important;
            }
        }

        @media (max-width: 575.98px) {
            .content-area { padding: 1rem; }
            .topbar { padding: 0.75rem 1rem; }
            .user-name-top { display: none; }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sidebar Backdrop (Overlay) -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Sidebar Navigation -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h5><i class="bi bi-mortarboard-fill"></i> SIMPELAJAR</h5>
            <small>Sistem Monitoring Dokumen Pembelajaran</small>
        </div>

        <div class="sidebar-nav">
            <!-- Menu Utama -->
            <div class="sidebar-section">Menu Utama</div>

            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>

            @if(Auth::user()->isGkmp())

            <a href="{{ route('validasi.index') }}" class="nav-link {{ request()->routeIs('validasi.*') ? 'active' : '' }}">
                <i class="bi bi-check2-all"></i> Validasi Dokumen
            </a>
            <a href="{{ route('laporan.index') }}" class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line"></i> Laporan
            </a>

            @endif

            <a href="{{ route('dokumen.index') }}" class="nav-link {{ request()->routeIs('dokumen.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-ruled"></i> Dokumen
            </a>


            <!-- kelola dosen  untuk gkmp pemberian akses ke mata kuliah yang diampu -->
            @if(Auth::user()->isGkmp())
            <a href="{{ route('dosen.index') }}" class="nav-link {{ request()->routeIs('dosen.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i> Kelola Dosen
            </a>
            @endif



            <a href="{{ route('mata-kuliah.index') }}" class="nav-link {{ request()->routeIs('mata-kuliah.*') ? 'active' : '' }}">
                <i class="bi bi-journal-bookmark"></i> Mata Kuliah
            </a>


            @if(Auth::user()->isDosen())
            <div class="sidebar-section">Pengaturan</div>
            <a href="{{ route('password-change.edit') }}" class="nav-link {{ request()->routeIs('password-change.*') ? 'active' : '' }}">
                <i class="bi bi-key-fill"></i> Ubah Password
            </a>
            @endif

            @if(Auth::user()->isGkmp())


            <a href="{{ route('tahap.index') }}" class="nav-link {{ request()->routeIs('tahap.*') ? 'active' : '' }}">
                <i class="bi bi-layers"></i> Tahap Upload
            </a>
            <a href="{{ route('semester.index') }}" class="nav-link {{ request()->routeIs('semester.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-week"></i> Semester
            </a>
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Manajemen User
            </a>
            @endif
        </div>

        <!-- User Profile Footer -->
        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <div class="user-info">
                    <div class="user-name" title="{{ Auth::user()->name }}">{{ Auth::user()->name }}</div>
                    <span class="user-role {{ Auth::user()->isGkmp() ? 'badge-gkmp' : 'badge-dosen' }}">
                        {{ strtoupper(Auth::user()->role) }}
                    </span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Wrapper -->
    <div class="main-content" id="mainContent">
        <!-- Topbar -->
        <header class="topbar d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <button class="toggle-btn" id="sidebarToggle" type="button" aria-label="Toggle navigation">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <nav aria-label="breadcrumb" class="mb-0 d-none d-sm-block">
                    <ol class="breadcrumb mb-0 small">
                        @yield('breadcrumb')
                    </ol>
                </nav>
            </div>

            <div class="d-flex align-items-center gap-3">
                <span class="text-secondary small user-name-top">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                        <i class="bi bi-box-arrow-right me-1"></i>
                        <span class="d-none d-md-inline">Logout</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Page Content -->
        <main class="content-area">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-octagon-fill"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Element Definitions
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleBtn = document.getElementById('sidebarToggle');
            const backdrop = document.getElementById('sidebarBackdrop');
            const navLinks = document.querySelectorAll('.sidebar .nav-link');

            // Breakpoint definition (must match CSS)
            const mobileBreakpoint = 992;

            // Helper: Check Screen Size
            const isMobile = () => window.innerWidth < mobileBreakpoint;

            // Helper: Open Sidebar
            const openSidebar = () => {
                sidebar.classList.add('show');
                backdrop.classList.add('show');
                document.body.style.overflow = 'hidden'; // Prevent background scroll
            };

            // Helper: Close Sidebar
            const closeSidebar = () => {
                sidebar.classList.remove('show');
                backdrop.classList.remove('show');
                document.body.style.overflow = ''; // Restore scroll
            };

            // 1. Toggle Button Click Event
            toggleBtn.addEventListener('click', function() {
                if (sidebar.classList.contains('show')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });

            // 2. Backdrop Click Event (Close sidebar when clicking outside)
            backdrop.addEventListener('click', closeSidebar);

            // 3. Nav Link Click Event (Auto-close on mobile after navigation click)
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (isMobile()) {
                        closeSidebar();
                    }
                });
            });

            // 4. Window Resize Event (Fix for toggling between mobile/desktop)
            window.addEventListener('resize', function() {
                // If window resizes to desktop while sidebar is open (via mobile)
                if (!isMobile() && sidebar.classList.contains('show')) {
                    closeSidebar();
                }
                // Ensure backdrop is hidden on desktop
                if (!isMobile()) {
                    backdrop.classList.remove('show');
                }
            });

            // 5. Keyboard Accessibility (ESC key)
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && sidebar.classList.contains('show')) {
                    closeSidebar();
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
