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
                        <label>Main</label>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('lab.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('lab.dashboard') }}" class="nxl-link" wire:navigate>
                            <span class="nxl-micon"><i class="feather-airplay"></i></span>
                            <span class="nxl-mtext">Dashboard</span>
                        </a>
                    </li>

                    <li class="nxl-item nxl-caption">
                        <label>Sales & Operations</label>
                    </li>
                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('lab.pos') || request()->routeIs('lab.invoices') || request()->routeIs('lab.invoice.edit') ? 'active nxl-trigger' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-shopping-cart"></i></span>
                            <span class="nxl-mtext">Billing & Invoices</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item {{ request()->routeIs('lab.pos') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('lab.pos') }}" wire:navigate><i class="feather-plus-circle me-2 fs-12"></i>New Bill (POS)</a>
                            </li>
                            <li class="nxl-item {{ request()->routeIs('lab.invoices') || request()->routeIs('lab.invoice.edit') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('lab.invoices') }}" wire:navigate><i class="feather-file-text me-2 fs-12"></i>All Invoices</a>
                            </li>
                        </ul>
                    </li>

                    <li class="nxl-item {{ request()->routeIs('lab.reports') || request()->routeIs('lab.reports.entry') ? 'active' : '' }}">
                        <a href="{{ route('lab.reports') }}" class="nxl-link" wire:navigate>
                            <span class="nxl-micon"><i class="feather-flask-conical"></i></span>
                            <span class="nxl-mtext">Test Reports</span>
                        </a>
                    </li>

                    <li class="nxl-item nxl-caption">
                        <label>Lab Management</label>
                    </li>
                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('lab.tests') || request()->routeIs('lab.packages') ? 'active nxl-trigger' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-layers"></i></span>
                            <span class="nxl-mtext">Lab Catalog</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item {{ request()->routeIs('lab.tests') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('lab.tests') }}" wire:navigate><i class="feather-activity me-2 fs-12"></i>Test Catalog</a>
                            </li>
                            <li class="nxl-item {{ request()->routeIs('lab.packages') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('lab.packages') }}" wire:navigate><i class="feather-package me-2 fs-12"></i>Test Packages</a>
                            </li>
                        </ul>
                    </li>

                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('lab.collection.centers') || request()->routeIs('lab.branches') ? 'active nxl-trigger' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-map-pin"></i></span>
                            <span class="nxl-mtext">Network</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item {{ request()->routeIs('lab.branches') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('lab.branches') }}" wire:navigate><i class="feather-home me-2 fs-12"></i>Main Branches</a>
                            </li>
                            <li class="nxl-item {{ request()->routeIs('lab.collection.centers') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('lab.collection.centers') }}" wire:navigate><i class="feather-map me-2 fs-12"></i>Collection Centers</a>
                            </li>
                        </ul>
                    </li>

                    <li class="nxl-item nxl-caption">
                        <label>Relationships</label>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('lab.patients') ? 'active' : '' }}">
                        <a href="{{ route('lab.patients') }}" class="nxl-link" wire:navigate>
                            <span class="nxl-micon"><i class="feather-user"></i></span>
                            <span class="nxl-mtext">Patients</span>
                        </a>
                    </li>
                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('lab.doctors') || request()->routeIs('lab.agents') ? 'active nxl-trigger' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">Partners</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item {{ request()->routeIs('lab.doctors') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('lab.doctors') }}" wire:navigate><i class="feather-user-check me-2 fs-12"></i>Referring Doctors</a>
                            </li>
                            <li class="nxl-item {{ request()->routeIs('lab.agents') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('lab.agents') }}" wire:navigate><i class="feather-briefcase me-2 fs-12"></i>Referral Agents</a>
                            </li>
                        </ul>
                    </li>

                    <li class="nxl-item nxl-caption">
                        <label>Finance & Marketing</label>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('lab.settlements') ? 'active' : '' }}">
                        <a href="{{ route('lab.settlements') }}" class="nxl-link" wire:navigate>
                            <span class="nxl-micon"><i class="feather-dollar-sign"></i></span>
                            <span class="nxl-mtext">Settlements</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('lab.payment.modes') ? 'active' : '' }}">
                        <a href="{{ route('lab.payment.modes') }}" class="nxl-link" wire:navigate>
                            <span class="nxl-micon"><i class="feather-credit-card"></i></span>
                            <span class="nxl-mtext">Payment Modes</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('lab.marketing') ? 'active' : '' }}">
                        <a href="{{ route('lab.marketing') }}" class="nxl-link" wire:navigate>
                            <span class="nxl-micon"><i class="feather-gift"></i></span>
                            <span class="nxl-mtext">Offers & Memberships</span>
                        </a>
                    </li>

                    <li class="nxl-item nxl-caption">
                        <label>Settings</label>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('lab.settings') ? 'active' : '' }}">
                        <a href="{{ route('lab.settings') }}" class="nxl-link" wire:navigate>
                            <span class="nxl-micon"><i class="feather-settings"></i></span>
                            <span class="nxl-mtext">Settings</span>
                        </a>
                    </li>
                @endrole

            </ul>
        </div>
    </div>
</nav>
