<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bluewash Laundry')</title>
    
    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@700;800;900&family=Poppins:wght@700;800;900&family=Space+Grotesk:wght@700&display=swap" rel="stylesheet">

    {{-- Remix Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />

    
    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('assets/logo.png') }}">
    
    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css" />

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}?v={{ time() }}">
    @yield('styles')
</head>
<body>


    {{-- Sidebar Backdrop --}}
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="app-container">
        {{-- Sidebar --}}
        <aside class="app-sidebar" id="appSidebar">
            <div class="sidebar-brand" style="display: flex; flex-direction: row; align-items: center; justify-content: space-between; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid rgba(14, 165, 233, 0.15); width: 100%;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <img src="{{ asset('assets/logo.png') }}" alt="Bluewash Logo" style="height: 52px; width: auto; object-fit: contain;">
                    <span style="font-weight: 800; font-size: 25px; font-family: 'Outfit', sans-serif; letter-spacing: -0.02em; line-height: 1;">
                        <span style="color: #0284c7;">Blue</span><span style="color: #1e3a8a;">wash</span>
                    </span>
                </div>
            </div>

            <nav class="sidebar-nav">
                @if(Auth::user()->isStaff())
                    {{-- Menu Kasir --}}
                    <a href="{{ route('dashboard') }}" class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}">
                        <i class="ri-home-3-line"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('orders.create') }}" class="nav-link {{ Route::is('orders.create') ? 'active' : '' }}">
                        <i class="ri-shopping-basket-2-line"></i>
                        <span>Transaksi Baru</span>
                    </a>
                    <a href="{{ route('orders.index') }}" class="nav-link {{ Route::is('orders.index') ? 'active' : '' }}">
                        <i class="ri-file-list-3-line"></i>
                        <span>Daftar Transaksi</span>
                    </a>
                    <a href="{{ route('customers.index') }}" class="nav-link {{ Route::is('customers.*') ? 'active' : '' }}">
                        <i class="ri-user-3-line"></i>
                        <span>Data Pelanggan</span>
                    </a>
                @else
                    {{-- Menu Admin --}}
                    <a href="{{ route('dashboard') }}" class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}">

                        <i class="ri-home-3-line"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('orders.index') }}" class="nav-link {{ Route::is('orders.*') ? 'active' : '' }}">
                        <i class="ri-shopping-basket-2-line"></i>
                        <span>Transaksi</span>
                    </a>
                    <a href="{{ route('customers.index') }}" class="nav-link {{ Route::is('customers.*') ? 'active' : '' }}">
                        <i class="ri-team-line"></i>
                        <span>Data Pelanggan</span>
                    </a>
                    <a href="{{ route('services.index') }}" class="nav-link {{ Route::is('services.*') ? 'active' : '' }}">
                        <i class="ri-price-tag-3-line"></i>
                        <span>Layanan & Harga</span>
                    </a>
                    <a href="{{ route('reports.index') }}" class="nav-link {{ Route::is('reports.*') ? 'active' : '' }}">
                        <i class="ri-bar-chart-box-line"></i>
                        <span>Laporan</span>
                    </a>
                    <a href="{{ route('users.index') }}" class="nav-link {{ Route::is('users.*') ? 'active' : '' }}">
                        <i class="ri-user-settings-line"></i>
                        <span>Manajemen User</span>
                    </a>
                    {{-- Sub-menu Pengaturan --}}
                    <div style="margin-top: 4px;">
                        <div class="nav-link {{ Route::is('settings.*') ? 'active' : '' }}" style="cursor: pointer; display: flex; justify-content: space-between; align-items: center;" onclick="toggleSettingsSubmenu()">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <i class="ri-settings-4-line"></i>
                                <span>Pengaturan</span>
                            </div>
                            <i class="ri-arrow-down-s-line" id="settingsArrow" style="transition: transform 0.2s; {{ Route::is('settings.*') ? 'transform: rotate(180deg);' : '' }}"></i>
                        </div>
                        <div id="settingsSubmenu" style="padding-left: 32px; margin-top: 4px; display: {{ Route::is('settings.*') ? 'flex' : 'none' }}; flex-direction: column; gap: 4px;">
                            <a href="{{ route('settings.index') }}?tab=store" style="padding: 8px 12px; font-size: 12px; font-weight: 600; border-radius: 8px; text-decoration: none; display: flex; align-items: center; gap: 8px; color: {{ Route::is('settings.*') && request('tab') !== 'wa' ? 'var(--primary)' : '#64748b' }}; background: {{ Route::is('settings.*') && request('tab') !== 'wa' ? 'rgba(37, 99, 235, 0.08)' : 'transparent' }};">
                                <i class="ri-store-2-line"></i>
                                <span>Pengaturan Toko</span>
                            </a>
                            <a href="{{ route('settings.index') }}?tab=wa" style="padding: 8px 12px; font-size: 12px; font-weight: 600; border-radius: 8px; text-decoration: none; display: flex; align-items: center; gap: 8px; color: {{ Route::is('settings.*') && request('tab') === 'wa' ? '#16a34a' : '#64748b' }}; background: {{ Route::is('settings.*') && request('tab') === 'wa' ? 'rgba(22, 163, 74, 0.08)' : 'transparent' }};">
                                <i class="ri-whatsapp-line"></i>
                                <span>WhatsApp Gateway</span>
                            </a>
                        </div>
                    </div>
                @endif
            </nav>
            
            {{-- Session User --}}
            @auth
            <div class="sidebar-user">
                <div class="user-info">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="user-details">
                        <span class="user-name">{{ Auth::user()->name }}</span>
                        <span class="user-role">
                            @if(Auth::user()->isAdmin()) Administrator
                            @else Staf Kasir
                            @endif
                        </span>
                    </div>
                </div>
                
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-logout-icon" title="Keluar (Logout)">
                        <i class="ri-logout-box-r-line"></i>
                    </button>
                </form>
            </div>
            @endauth
        </aside>

        {{-- Area Konten --}}
        <main class="app-content">
            {{-- Header --}}
            <header class="content-header">
                <div class="header-title-area" style="display: flex; align-items: center; gap: 12px;">
                    <button type="button" class="inline-menu-btn" id="mobileSidebarToggle" title="Buka Menu Navigation">
                        <i class="ri-menu-line"></i>
                    </button>
                    <div>
                        <h1>@yield('header_title')</h1>
                        <p>@yield('header_subtitle', 'Sistem Informasi Manajemen Laundry Bluewash')</p>
                    </div>
                </div>
                
                <div class="header-meta">
                    @yield('header_actions')
                    <div class="meta-date-card">
                        <i class="ri-calendar-todo-line"></i>
                        <span id="liveClock">{{ Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                </div>
            </header>

            {{-- Notifikasi --}}
            @if(session('success'))
                <div class="alert-message success" style="display: flex; margin-bottom: 20px;">
                    <div class="alert-message-content">
                        <i class="ri-checkbox-circle-line"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" class="alert-close-btn" onclick="this.closest('.alert-message').remove()" title="Tutup Notifikasi">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert-message error" style="display: flex; margin-bottom: 20px;">
                    <div class="alert-message-content">
                        <i class="ri-error-warning-line"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" class="alert-close-btn" onclick="this.closest('.alert-message').remove()" title="Tutup Notifikasi">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            @endif

            {{-- Konten Halaman --}}
            @yield('content')
        </main>
    </div>

    {{-- Script JS --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            if (typeof $.fn.dataTable !== 'undefined') {
                $.fn.dataTable.ext.errMode = 'none';
            }
            $('.datatable').each(function() {
                if (!$.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable({
                        order: [],
                        pageLength: 5,
                        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Semua"]],
                        ordering: true,
                        responsive: true,
                        autoWidth: false,
                        language: {
                            search: "Cari:",
                            lengthMenu: "Tampilkan _MENU_ data",
                            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                            emptyTable: "Belum ada data tersedia",
                            zeroRecords: "Data tidak ditemukan",
                            paginate: {
                                first: "Pertama",
                                last: "Terakhir",
                                next: "Selanjutnya",
                                previous: "Sebelumnya"
                            }
                        }
                    });
                }
            });
        });

        function toggleSettingsSubmenu() {
            const submenu = document.getElementById('settingsSubmenu');
            const arrow = document.getElementById('settingsArrow');
            if (submenu) {
                if (submenu.style.display === 'none' || submenu.style.display === '') {
                    submenu.style.display = 'flex';
                    if (arrow) arrow.style.transform = 'rotate(180deg)';
                } else {
                    submenu.style.display = 'none';
                    if (arrow) arrow.style.transform = 'rotate(0deg)';
                }
            }
        }

        function printReceiptIframe(orderId) {
            let iframe = document.getElementById('globalPrintIframe');
            if (!iframe) {
                iframe = document.createElement('iframe');
                iframe.id = 'globalPrintIframe';
                iframe.style.display = 'none';
                iframe.style.visibility = 'hidden';
                iframe.style.width = '0px';
                iframe.style.height = '0px';
                iframe.style.position = 'absolute';
                document.body.appendChild(iframe);
            }
            iframe.onload = function() {
                iframe.onload = null; // Prevent any loop or re-triggering
                setTimeout(function() {
                    try {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                    } catch(e) {}
                }, 250);
            };
            iframe.src = '/orders/' + orderId + '/receipt';
        }

        // Mobile Sidebar Drawer Controller
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('mobileSidebarToggle');
            const closeBtn  = document.getElementById('mobileSidebarClose');
            const backdrop  = document.getElementById('sidebarBackdrop');
            const sidebar   = document.getElementById('appSidebar');

            function openSidebar() {
                if (sidebar) sidebar.classList.add('mobile-open');
                if (backdrop) backdrop.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                if (sidebar) sidebar.classList.remove('mobile-open');
                if (backdrop) backdrop.classList.remove('active');
                document.body.style.overflow = '';
            }

            if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
            if (closeBtn)  closeBtn.addEventListener('click', closeSidebar);
            if (backdrop)  backdrop.addEventListener('click', closeSidebar);

            // Auto-close sidebar on mobile when a nav item is clicked
            const navLinks = document.querySelectorAll('.app-sidebar .nav-link:not([onclick])');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 1024) {
                        closeSidebar();
                    }
                });
            });
        });
    </script>

    @if(session('print_order_id'))
        <iframe id="autoPrintIframe" style="display: none; visibility: hidden; width: 0; height: 0; position: absolute;"></iframe>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const iframe = document.getElementById('autoPrintIframe');
                if (iframe) {
                    iframe.onload = function() {
                        iframe.onload = null; // Prevent any loop or re-triggering
                        setTimeout(function() {
                            try {
                                iframe.contentWindow.focus();
                                iframe.contentWindow.print();
                            } catch(e) {}
                        }, 250);
                    };
                    iframe.src = "{{ route('orders.receipt', session('print_order_id')) }}";
                }
            });
        </script>
    @endif

    @yield('scripts')
</body>
</html>
