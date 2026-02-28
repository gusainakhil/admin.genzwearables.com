<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - {{ $companyDetail?->brand_name ?? 'Ecommerce' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
    <style>
        .sidebar-link.active {
            background: linear-gradient(135deg, #fb7185, #f97316);
            color: #0f172a;
            box-shadow: 0 10px 25px -10px rgba(244, 63, 94, 0.6);
        }
        body.sidebar-collapsed .admin-sidebar {
            transform: translateX(-100%);
        }
        body.sidebar-collapsed .admin-main {
            padding-left: 0;
        }
        @media (max-width: 1023px) {
            body.sidebar-collapsed .admin-main {
                padding-left: 0;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-rose-50 via-stone-50 to-amber-50">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div id="sidebarOverlay" class="fixed inset-0 z-20 hidden bg-stone-950/40 backdrop-blur-sm"></div>
        <aside class="admin-sidebar fixed inset-y-0 left-0 z-30 w-72 -translate-x-full bg-gradient-to-b from-stone-950 via-stone-900 to-stone-800 text-white overflow-y-auto transition-transform duration-300 lg:translate-x-0">
            <div class="p-6 border-b border-white/10">
                <div class="inline-flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                    @if($companyDetail?->logo)
                        <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-xl border border-white/10 bg-white shadow-sm">
                            <img src="{{ asset('storage/' . $companyDetail->logo) }}" alt="{{ $companyDetail->brand_name ?? 'Brand' }} Logo" class="h-full w-full object-contain">
                        </div>
                    @else
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-rose-500 to-amber-400 text-stone-900 shadow-sm">
                            <i class="fas fa-bolt"></i>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-lg font-semibold tracking-tight">{{ $companyDetail?->brand_name ?? 'Ecommerce' }}</h1>
                        <p class="text-[0.6rem] uppercase tracking-[0.35em] text-rose-200/80">Admin Panel</p>
                    </div>
                </div>
                <button id="sidebarToggle" type="button" class="mt-4 hidden items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white/80 transition hover:bg-white/10 hover:text-white lg:inline-flex" aria-expanded="true">
                    <i class="fas fa-bars"></i>Hide
                </button>
            </div>
            
            <nav class="mt-6 space-y-2 px-4">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold tracking-wide text-stone-200 transition hover:bg-white/10 hover:text-white {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/5">
                        <i class="fas fa-home"></i>
                    </span>
                    Dashboard
                </a>
                
                <a href="{{ route('admin.products.index') }}" class="sidebar-link flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold tracking-wide text-stone-200 transition hover:bg-white/10 hover:text-white {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/5">
                        <i class="fas fa-box"></i>
                    </span>
                    Products
                </a>
                
                <a href="{{ route('admin.orders.index') }}" class="sidebar-link flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold tracking-wide text-stone-200 transition hover:bg-white/10 hover:text-white {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/5">
                        <i class="fas fa-shopping-cart"></i>
                    </span>
                    Orders
                </a>

                @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.staff.index') }}" class="sidebar-link flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold tracking-wide text-stone-200 transition hover:bg-white/10 hover:text-white {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/5">
                        <i class="fas fa-user-shield"></i>
                    </span>
                    Staff
                </a>

                <a href="{{ route('admin.categories.index') }}" class="sidebar-link flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold tracking-wide text-stone-200 transition hover:bg-white/10 hover:text-white {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/5">
                        <i class="fas fa-folder"></i>
                    </span>
                    Categories
                </a>
                
                <a href="{{ route('admin.customers.index') }}" class="sidebar-link flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold tracking-wide text-stone-200 transition hover:bg-white/10 hover:text-white {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/5">
                        <i class="fas fa-users"></i>
                    </span>
                    Customers
                </a>
                
                <a href="{{ route('admin.coupons.index') }}" class="sidebar-link flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold tracking-wide text-stone-200 transition hover:bg-white/10 hover:text-white {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/5">
                        <i class="fas fa-tag"></i>
                    </span>
                    Coupons
                </a>
                
                <a href="{{ route('admin.sizes.index') }}" class="sidebar-link flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold tracking-wide text-stone-200 transition hover:bg-white/10 hover:text-white {{ request()->routeIs('admin.sizes.*') ? 'active' : '' }}">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/5">
                        <i class="fas fa-ruler"></i>
                    </span>
                    Sizes
                </a>
                
                <a href="{{ route('admin.colors.index') }}" class="sidebar-link flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold tracking-wide text-stone-200 transition hover:bg-white/10 hover:text-white {{ request()->routeIs('admin.colors.*') ? 'active' : '' }}">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/5">
                        <i class="fas fa-palette"></i>
                    </span>
                    Colors
                </a>
                
                <a href="{{ route('admin.reviews.index') }}" class="sidebar-link flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold tracking-wide text-stone-200 transition hover:bg-white/10 hover:text-white {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/5">
                        <i class="fas fa-star"></i>
                    </span>
                    Reviews
                </a>
                
                <a href="{{ route('admin.settings.index') }}" class="sidebar-link flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold tracking-wide text-stone-200 transition hover:bg-white/10 hover:text-white {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/5">
                        <i class="fas fa-cog"></i>
                    </span>
                    Settings
                </a>

                <a href="{{ route('admin.policies.index') }}" class="sidebar-link flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold tracking-wide text-stone-200 transition hover:bg-white/10 hover:text-white {{ request()->routeIs('admin.policies.*') ? 'active' : '' }}">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/5">
                        <i class="fas fa-file-alt"></i>
                    </span>
                    Policy Pages
                </a>

                <a href="{{ route('admin.company-details.index') }}" class="sidebar-link flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold tracking-wide text-stone-200 transition hover:bg-white/10 hover:text-white {{ request()->routeIs('admin.company-details.*') ? 'active' : '' }}">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/5">
                        <i class="fas fa-building"></i>
                    </span>
                    Company Details
                </a>
                @endif
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="admin-main flex-1 flex flex-col min-h-screen transition-[padding] duration-300 lg:pl-72">
            <button id="sidebarReveal" type="button" class="fixed left-4 top-4 z-30 hidden rounded-full border border-rose-200/70 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-stone-600 shadow-sm transition hover:border-rose-300 hover:text-stone-800 lg:inline-flex" aria-expanded="false">
                <i class="fas fa-bars"></i>
            </button>
            <!-- Header -->
            <header class="relative z-10">
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-rose-500 via-amber-400 to-rose-400"></div>
                <div class="bg-white/95 backdrop-blur-sm shadow-sm">
                    <div class="flex flex-col gap-4 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-[0.65rem] uppercase tracking-[0.4em] text-stone-400">Admin Suite</p>
                            <h2 class="text-2xl font-semibold text-stone-900">@yield('page-title', 'Dashboard')</h2>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <button id="sidebarMobileToggle" type="button" class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-stone-600 transition hover:bg-rose-50 sm:hidden" aria-expanded="false">
                                <i class="fas fa-bars"></i>Menu
                            </button>
                            
                            <span class="inline-flex items-center rounded-full bg-stone-900 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white">
                                {{ auth()->user()->name }}
                            </span>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-white px-4 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleButton = document.getElementById('sidebarToggle');
            const revealButton = document.getElementById('sidebarReveal');
            const mobileToggleButton = document.getElementById('sidebarMobileToggle');
            const overlay = document.getElementById('sidebarOverlay');
            const mediaQuery = window.matchMedia('(min-width: 1024px)');

            const setCollapsed = (collapsed) => {
                document.body.classList.toggle('sidebar-collapsed', collapsed);
                toggleButton.setAttribute('aria-expanded', String(!collapsed));
                revealButton.setAttribute('aria-expanded', String(!collapsed));
                revealButton.classList.toggle('hidden', !collapsed);
            };

            const setMobileOpen = (open) => {
                document.body.classList.toggle('sidebar-open', open);
                mobileToggleButton.setAttribute('aria-expanded', String(open));
                overlay.classList.toggle('hidden', !open);
                document.querySelector('.admin-sidebar')?.classList.toggle('-translate-x-full', !open);
            };

            toggleButton.addEventListener('click', function () {
                setCollapsed(!document.body.classList.contains('sidebar-collapsed'));
            });

            revealButton.addEventListener('click', function () {
                setCollapsed(false);
            });

            mobileToggleButton.addEventListener('click', function () {
                setMobileOpen(!document.body.classList.contains('sidebar-open'));
            });

            overlay.addEventListener('click', function () {
                setMobileOpen(false);
            });

            mediaQuery.addEventListener('change', function (event) {
                if (event.matches) {
                    setMobileOpen(false);
                }
            });
        });
    </script>
</body>
</html>
