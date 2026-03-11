<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ url('/') }}" wire:navigate class="b-brand">
                <img src="{{ asset('assets/images/icon.webp') }}" alt="Logo" height="50px" class="logo logo-lg" />
                <img src="{{ asset('assets/images/logo-abbr.png') }}" alt="Logo" class="logo logo-sm" />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="nxl-navbar">

                @role('super_admin')
                    <li class="nxl-item nxl-caption">
                        <label>Super Admin Panel</label>
                    </li>

                    <li class="nxl-item">
                        <a href="{{ route('admin.dashboard') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-airplay"></i></span>
                            <span class="nxl-mtext">Dashboard</span>
                        </a>
                    </li>

                    <li class="nxl-item">
                        <a href="{{ route('admin.global-tests') }}" wire:navigate
                            class="nxl-link {{ request()->routeIs('admin.global-tests') ? 'active' : '' }}">
                            <span class="nxl-micon"><i class="feather-layers"></i></span>
                            <span class="nxl-mtext">Global Tests</span>
                        </a>
                    </li>

                    <li class="nxl-item">
                        <a href="{{ route('admin.plans') }}" wire:navigate
                            class="nxl-link {{ request()->routeIs('admin.plans') ? 'active' : '' }}">
                            <span class="nxl-micon"><i class="feather-credit-card"></i></span>
                            <span class="nxl-mtext">Plans</span>
                        </a>
                    </li>
                @endrole


                @role('lab_admin')
                    <li class="nxl-item nxl-caption">
                        <label>Pathology Lab</label>
                    </li>

                    <li class="nxl-item">
                        <a href="{{ route('lab.dashboard') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-airplay"></i></span>
                            <span class="nxl-mtext">Dashboard</span>
                        </a>
                    </li>

                    {{-- <li class="nxl-item">
                        <a href="#" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">Patients</span>
                        </a>
                    </li>

                    <li class="nxl-item">
                        <a href="#" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-file-text"></i></span>
                            <span class="nxl-mtext">Test Reports</span>
                        </a>
                    </li> --}}
                    <li class="nxl-item {{ request()->routeIs('lab.tests') ? 'active' : '' }}">
                        <a href="{{ route('lab.tests') }}" class="nxl-link" wire:navigate>
                            <span class="nxl-micon"><i class="feather-list"></i></span>
                            <span class="nxl-mtext">Test Catalog</span>
                        </a>
                    </li>

                    <li class="nxl-item {{ request()->routeIs('lab.packages') ? 'active' : '' }}">
                        <a href="{{ route('lab.packages') }}" class="nxl-link" wire:navigate>
                            <span class="nxl-micon"><i class="feather-layers"></i></span>
                            <span class="nxl-mtext">Test Packages</span>
                        </a>
                    </li>

                    <li class="nxl-item {{ request()->routeIs('lab.patients') ? 'active' : '' }}">
                        <a href="{{ route('lab.patients') }}" class="nxl-link" wire:navigate>
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">Patients</span>
                        </a>
                    </li>

                    <li class="nxl-item {{ request()->routeIs('lab.marketing') ? 'active' : '' }}">
                        <a href="{{ route('lab.marketing') }}" class="nxl-link" wire:navigate>
                            <span class="nxl-micon"><i class="feather-gift"></i></span>
                            <span class="nxl-mtext">Marketing & Offers</span>
                        </a>
                    </li>

                    <li class="nxl-item {{ request()->routeIs('lab.payment.modes') ? 'active' : '' }}">
                        <a href="{{ route('lab.payment.modes') }}" class="nxl-link" wire:navigate>
                            <span class="nxl-micon"><i class="feather-credit-card"></i></span>
                            <span class="nxl-mtext">Payment Modes</span>
                        </a>
                    </li>

                    <li class="nxl-item {{ request()->routeIs('lab.collection.centers') ? 'active' : '' }}">
                        <a href="{{ route('lab.collection.centers') }}" class="nxl-link" wire:navigate>
                            <span class="nxl-micon"><i class="feather-map-pin"></i></span>
                            <span class="nxl-mtext">Collection Centers</span>
                        </a>
                    </li>


                    <li class="nxl-item {{ request()->routeIs('lab.branches') ? 'active' : '' }}">
                        <a href="{{ route('lab.branches') }}" class="nxl-link" wire:navigate>
                            <span class="nxl-micon"><i class="feather-layers"></i></span>
                            <span class="nxl-mtext">Branches</span>
                        </a>
                    </li>

                    <li class="nxl-item">
                        <a href="#" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-settings"></i></span>
                            <span class="nxl-mtext">Settings</span>
                        </a>
                    </li>
                @endrole

            </ul>
        </div>
    </div>
</nav>
