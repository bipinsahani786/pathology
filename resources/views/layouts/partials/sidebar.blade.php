<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            @php
                $dashboardRoute = route('lab.dashboard'); // Default
                if (auth()->user()->hasRole('super_admin')) {
                    $dashboardRoute = route('admin.dashboard');
                } elseif (auth()->user()->patientProfile) {
                    $dashboardRoute = route('portal.dashboard');
                } elseif (auth()->user()->hasAnyRole(['doctor', 'agent', 'collection_center'])) {
                    $dashboardRoute = route('partner.dashboard');
                }
            @endphp
            <a href="{{ $dashboardRoute }}" wire:navigate class="b-brand">
                @php
                    $logoPath = null;
                    $faviconPath = null;

                    if (auth()->user()->company) {
                        if (auth()->user()->company->logo) {
                            $logoPath = secure_storage_url(auth()->user()->company->logo);
                        }
                        
                        $labFavicon = \App\Models\Configuration::getFor('lab_favicon');
                        if ($labFavicon) {
                            $faviconPath = secure_storage_url($labFavicon);
                        }
                    }

                    if (!$logoPath) {
                        $siteLogo = \App\Models\SiteSetting::get('site_logo');
                        if ($siteLogo) {
                            $logoPath = secure_storage_url($siteLogo);
                        }
                    }

                    if (!$faviconPath) {
                        $siteFavicon = \App\Models\SiteSetting::get('site_favicon');
                        if ($siteFavicon) {
                            $faviconPath = secure_storage_url($siteFavicon);
                        } else {
                            $faviconPath = asset('assets/images/icon.webp');
                        }
                    }
                @endphp

                @if($logoPath)
                    <img src="{{ $logoPath }}" alt="Logo" height="50px" class="logo logo-lg" style="object-fit: contain;" />
                @else
                    <img src="{{ asset('assets/images/icon.webp') }}" alt="Logo" height="50px" class="logo logo-lg" />
                @endif

                <img src="{{ $faviconPath }}" alt="Logo" class="logo logo-sm" />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="nxl-navbar">

                {{-- SUPER ADMIN --}}
                @role('super_admin')
                    <li class="nxl-item nxl-caption"><label>Main Cabinet</label></li>
                    <li class="nxl-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-airplay"></i></span>
                            <span class="nxl-mtext">Dashboard</span>
                        </a>
                    </li>

                    <li class="nxl-item nxl-caption"><label>Master Catalog</label></li>
                    <li class="nxl-item {{ request()->routeIs('admin.global-tests') ? 'active' : '' }}">
                        <a href="{{ route('admin.global-tests') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-layers"></i></span>
                            <span class="nxl-mtext">Global Tests</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('admin.departments') ? 'active' : '' }}">
                        <a href="{{ route('admin.departments') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-grid"></i></span>
                            <span class="nxl-mtext">System Departments</span>
                        </a>
                    </li>

                    <li class="nxl-item nxl-caption"><label>Business & Finance</label></li>
                    <li class="nxl-item {{ request()->routeIs('admin.labs') ? 'active' : '' }}">
                        <a href="{{ route('admin.labs') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-dollar-sign"></i></span>
                            <span class="nxl-mtext">Labs</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('admin.sales-agents') ? 'active' : '' }}">
                        <a href="{{ route('admin.sales-agents') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">Sales Agents</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('admin.plans') ? 'active' : '' }}">
                        <a href="{{ route('admin.plans') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-package"></i></span>
                            <span class="nxl-mtext">Subscription Plans</span>
                        </a>
                    </li>

                    <li class="nxl-item nxl-caption"><label>Website CMS</label></li>
                    <li class="nxl-item {{ request()->routeIs('admin.site-settings') ? 'active' : '' }}">
                        <a href="{{ route('admin.site-settings') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-globe"></i></span>
                            <span class="nxl-mtext">Site Settings</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('admin.landing-content') ? 'active' : '' }}">
                        <a href="{{ route('admin.landing-content') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-layout"></i></span>
                            <span class="nxl-mtext">Landing Content</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('admin.enquiries') ? 'active' : '' }}">
                        <a href="{{ route('admin.enquiries') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-mail"></i></span>
                            <span class="nxl-mtext">Enquiries</span>
                        </a>
                    </li>

                    <li class="nxl-item nxl-caption"><label>System & Settings</label></li>
                    <li class="nxl-item {{ request()->routeIs('admin.audit-logs') ? 'active' : '' }}">
                        <a href="{{ route('admin.audit-logs') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-shield"></i></span>
                            <span class="nxl-mtext">Audit Logs</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('admin.system-logs') ? 'active' : '' }}">
                        <a href="{{ route('admin.system-logs') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-server"></i></span>
                            <span class="nxl-mtext">System Logs</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('admin.maintenance') ? 'active' : '' }}">
                        <a href="{{ route('admin.maintenance') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-tool"></i></span>
                            <span class="nxl-mtext">Maintenance</span>
                        </a>
                    </li>
                    @if(config('features.support_tickets', true))
                    <li class="nxl-item {{ request()->routeIs('admin.support') ? 'active' : '' }}">
                        <a href="{{ route('admin.support') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-life-buoy"></i></span>
                            <span class="nxl-mtext">Support Tickets</span>
                        </a>
                    </li>
                    @endif
                @endrole

                {{-- LAB ADMIN / STAFF / BRANCH ADMIN (Consolidated) --}}
                @if(auth()->user()->hasAnyRole(['lab_admin', 'staff', 'branch_admin']))
                    <li class="nxl-item nxl-caption"><label>Main</label></li>
                    <li class="nxl-item {{ request()->routeIs('lab.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('lab.dashboard') }}" class="nxl-link" wire:navigate>
                            <span class="nxl-micon"><i class="feather-airplay"></i></span>
                            <span class="nxl-mtext">Dashboard</span>
                        </a>
                    </li>

                    <li class="nxl-item nxl-caption"><label>Sales & Operations</label></li>
                    <li class="nxl-item {{ request()->routeIs('lab.pos') ? 'active' : '' }}">
                        <a class="nxl-link" href="{{ route('lab.pos') }}" wire:navigate>
                            <span class="nxl-micon"><i class="feather-plus-circle"></i></span>
                            <span class="nxl-mtext">New Bill (POS)</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('lab.invoices') || request()->routeIs('lab.invoice.edit') ? 'active' : '' }}">
                        <a class="nxl-link" href="{{ route('lab.invoices') }}" wire:navigate>
                            <span class="nxl-micon"><i class="feather-file-text"></i></span>
                            <span class="nxl-mtext">All Invoices</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('lab.reports') || request()->routeIs('lab.reports.entry') ? 'active' : '' }}">
                        <a href="{{ route('lab.reports') }}" class="nxl-link" wire:navigate>
                            <span class="nxl-micon"><i class="feather-clipboard"></i></span>
                            <span class="nxl-mtext">Test Reports</span>
                        </a>
                    </li>

                    @if(auth()->user()->hasAnyRole(['lab_admin', 'staff']))
                    <li class="nxl-item nxl-caption"><label>Lab Management</label></li>
                    <li class="nxl-item {{ request()->routeIs('lab.departments') ? 'active' : '' }}">
                        <a class="nxl-link" href="{{ route('lab.departments') }}" wire:navigate>
                            <span class="nxl-micon"><i class="feather-grid"></i></span>
                            <span class="nxl-mtext">Departments</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('lab.tests') ? 'active' : '' }}">
                        <a class="nxl-link" href="{{ route('lab.tests') }}" wire:navigate>
                            <span class="nxl-micon"><i class="feather-activity"></i></span>
                            <span class="nxl-mtext">Test Catalog</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('lab.packages') ? 'active' : '' }}">
                        <a class="nxl-link" href="{{ route('lab.packages') }}" wire:navigate>
                            <span class="nxl-micon"><i class="feather-package"></i></span>
                            <span class="nxl-mtext">Test Packages</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('lab.branches') ? 'active' : '' }}">
                        <a class="nxl-link" href="{{ route('lab.branches') }}" wire:navigate>
                            <span class="nxl-micon"><i class="feather-home"></i></span>
                            <span class="nxl-mtext">Main Branches</span>
                        </a>
                    </li>
                    @endif

                    <li class="nxl-item {{ request()->routeIs('lab.collection.centers') ? 'active' : '' }}">
                        <a class="nxl-link" href="{{ route('lab.collection.centers') }}" wire:navigate>
                            <span class="nxl-micon"><i class="feather-map"></i></span>
                            <span class="nxl-mtext">Collection Centers</span>
                        </a>
                    </li>

                    <li class="nxl-item nxl-caption"><label>Relationships</label></li>
                    <li class="nxl-item {{ request()->routeIs('lab.patients') ? 'active' : '' }}">
                        <a href="{{ route('lab.patients') }}" class="nxl-link" wire:navigate>
                            <span class="nxl-micon"><i class="feather-user"></i></span>
                            <span class="nxl-mtext">Patients</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('lab.doctors') ? 'active' : '' }}">
                        <a class="nxl-link" href="{{ route('lab.doctors') }}" wire:navigate>
                            <span class="nxl-micon"><i class="feather-user-check"></i></span>
                            <span class="nxl-mtext">Referring Doctors</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('lab.agents') ? 'active' : '' }}">
                        <a class="nxl-link" href="{{ route('lab.agents') }}" wire:navigate>
                            <span class="nxl-micon"><i class="feather-briefcase"></i></span>
                            <span class="nxl-mtext">Referral Agents</span>
                        </a>
                    </li>

                    <li class="nxl-item nxl-caption"><label>Finance & Marketing</label></li>
                    <li class="nxl-item {{ request()->routeIs('lab.settlements') ? 'active' : '' }}">
                        <a href="{{ route('lab.settlements') }}" class="nxl-link" wire:navigate>
                            <span class="nxl-micon"><i class="feather-dollar-sign"></i></span>
                            <span class="nxl-mtext">Settlements</span>
                        </a>
                    </li>

                    @if(auth()->user()->can('view inventory') && config('features.inventory', true))
                    <li class="nxl-item nxl-caption"><label>Inventory</label></li>
                    <li class="nxl-item {{ request()->routeIs('lab.inventory.dashboard') ? 'active' : '' }}">
                        <a class="nxl-link" href="{{ route('lab.inventory.dashboard') }}" wire:navigate>
                            <span class="nxl-micon"><i class="feather-pie-chart"></i></span>
                            <span class="nxl-mtext">Inventory Dashboard</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('lab.inventory.suppliers') ? 'active' : '' }}">
                        <a class="nxl-link" href="{{ route('lab.inventory.suppliers') }}" wire:navigate>
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">Suppliers</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('lab.inventory.items') ? 'active' : '' }}">
                        <a class="nxl-link" href="{{ route('lab.inventory.items') }}" wire:navigate>
                            <span class="nxl-micon"><i class="feather-box"></i></span>
                            <span class="nxl-mtext">Items / Products</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('lab.inventory.stock') ? 'active' : '' }}">
                        <a class="nxl-link" href="{{ route('lab.inventory.stock') }}" wire:navigate>
                            <span class="nxl-micon"><i class="feather-database"></i></span>
                            <span class="nxl-mtext">Current Stock</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('lab.inventory.purchase') ? 'active' : '' }}">
                        <a class="nxl-link" href="{{ route('lab.inventory.purchase') }}" wire:navigate>
                            <span class="nxl-micon"><i class="feather-shopping-cart"></i></span>
                            <span class="nxl-mtext">Purchase / GRN</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('lab.inventory.issuance') ? 'active' : '' }}">
                        <a class="nxl-link" href="{{ route('lab.inventory.issuance') }}" wire:navigate>
                            <span class="nxl-micon"><i class="feather-send"></i></span>
                            <span class="nxl-mtext">Stock Issuance</span>
                        </a>
                    </li>
                    @endif

                    <li class="nxl-item nxl-caption"><label>Account Settings</label></li>
                    <li class="nxl-item {{ request()->routeIs('lab.profile') ? 'active' : '' }}">
                        <a href="{{ route('lab.profile') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-user"></i></span>
                            <span class="nxl-mtext">My Profile</span>
                        </a>
                    </li>
                    @can('view settings')
                    <li class="nxl-item {{ request()->routeIs('lab.settings') ? 'active' : '' }}">
                        <a href="{{ route('lab.settings') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-settings"></i></span>
                            <span class="nxl-mtext">Lab Settings</span>
                        </a>
                    </li>
                    @endcan
                    <li class="nxl-item">
                        <form method="POST" action="{{ route('logout') }}" id="logout-form-common" class="d-none">@csrf</form>
                        <a href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('logout-form-common').submit();" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-log-out text-danger"></i></span>
                            <span class="nxl-mtext text-danger">Logout</span>
                        </a>
                    </li>
                @endif

                {{-- PARTNER PORTAL --}}
                @if(!auth()->user()->patientProfile && auth()->user()->hasAnyRole(['doctor', 'agent', 'collection_center']))
                    <li class="nxl-item nxl-caption"><label>Partner Portal</label></li>
                    <li class="nxl-item {{ request()->routeIs('partner.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('partner.dashboard') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-airplay"></i></span>
                            <span class="nxl-mtext">Dashboard</span>
                        </a>
                    </li>
                    <li class="nxl-item">
                        <form method="POST" action="{{ route('logout') }}" id="logout-form-partner" class="d-none">@csrf</form>
                        <a href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('logout-form-partner').submit();" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-log-out text-danger"></i></span>
                            <span class="nxl-mtext text-danger">Logout</span>
                        </a>
                    </li>
                @endif

                {{-- PATIENT PORTAL --}}
                @if(auth()->user()->patientProfile)
                    <li class="nxl-item nxl-caption"><label>Patient Portal</label></li>
                    <li class="nxl-item {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('portal.dashboard') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-airplay"></i></span>
                            <span class="nxl-mtext">Overview</span>
                        </a>
                    </li>
                    <li class="nxl-item">
                        <form method="POST" action="{{ route('logout') }}" id="logout-form-patient" class="d-none">@csrf</form>
                        <a href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('logout-form-patient').submit();" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-log-out text-danger"></i></span>
                            <span class="nxl-mtext text-danger">Logout</span>
                        </a>
                    </li>
                @endif

            </ul>
        </div>
    </div>
    <style>
        .nxl-navigation .nxl-navbar .nxl-item > .nxl-link .nxl-mtext { font-size: 14px !important; font-weight: 600 !important; }
        .nxl-navigation .nxl-navbar .nxl-caption label { font-size: 10px !important; letter-spacing: 1px !important; font-weight: 700 !important; color: #64748b !important; text-transform: uppercase; }
        .nxl-navigation .nxl-navbar .nxl-item { margin-bottom: 2px; }
    </style>
</nav>
