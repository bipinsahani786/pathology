<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ url('/') }}" wire:navigate class="b-brand">
                @php
                    $logoPath = null;
                    if (auth()->user()->company && auth()->user()->company->logo) {
                        $logoPath = secure_storage_url(auth()->user()->company->logo);
                    } else {
                        $siteLogo = \App\Models\SiteSetting::get('site_logo');
                        if ($siteLogo) {
                            $logoPath = secure_storage_url($siteLogo);
                        }
                    }
                @endphp

                @if($logoPath)
                    <img src="{{ $logoPath }}" alt="Logo" height="50px" class="logo logo-lg" style="object-fit: contain;" />
                @else
                    <img src="{{ asset('assets/images/icon.webp') }}" alt="Logo" height="50px" class="logo logo-lg" />
                @endif

                <img src="{{ \App\Models\Configuration::getFor('lab_favicon') ? secure_storage_url(\App\Models\Configuration::getFor('lab_favicon')) : asset('assets/images/logo-abbr.png') }}" alt="Logo" class="logo logo-sm" />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="nxl-navbar">

                @role('super_admin')
                    <li class="nxl-item nxl-caption">
                        <label>Main Cabinet</label>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-airplay"></i></span>
                            <span class="nxl-mtext">Dashboard</span>
                        </a>
                    </li>

                    <li class="nxl-item nxl-caption">
                        <label>Master Catalog</label>
                    </li>
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

                    <li class="nxl-item nxl-caption">
                        <label>Business & Finance</label>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('admin.plans') ? 'active' : '' }}">
                        <a href="{{ route('admin.plans') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-credit-card"></i></span>
                            <span class="nxl-mtext">Pricing Plans</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('admin.labs') ? 'active' : '' }}">
                        <a href="{{ route('admin.labs') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-dollar-sign"></i></span>
                            <span class="nxl-mtext">Labs</span>
                        </a>
                    </li>

                    <li class="nxl-item nxl-caption">
                        <label>Website CMS</label>
                    </li>
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
                            <span class="nxl-micon"><i class="feather-inbox"></i></span>
                            <span class="nxl-mtext">Enquiries</span>
                            @php $newEnquiries = \App\Models\Enquiry::new()->count(); @endphp
                            @if($newEnquiries > 0)
                                <span class="badge bg-danger rounded-pill ms-auto">{{ $newEnquiries }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('admin.audit-logs') ? 'active' : '' }}">
                        <a href="{{ route('admin.audit-logs') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-shield"></i></span>
                            <span class="nxl-mtext">Global Audit Logs</span>
                        </a>
                    </li>
                @endrole


                @if(auth()->user()->hasAnyRole(['lab_admin', 'staff', 'branch_admin']))
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
                            <span class="nxl-micon"><i class="feather-clipboard"></i></span>
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
                            <li class="nxl-item {{ request()->routeIs('lab.departments') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('lab.departments') }}" wire:navigate><i class="feather-grid me-2 fs-12"></i>Departments</a>
                            </li>
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

                    @if(config('features.inventory', true))
                    <li class="nxl-item nxl-caption">
                        <label>Inventory & Assets</label>
                    </li>
                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('lab.inventory.*') ? 'active nxl-trigger' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-box"></i></span>
                            <span class="nxl-mtext">Inventory</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item {{ request()->routeIs('lab.inventory.dashboard') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('lab.inventory.dashboard') }}" wire:navigate><i class="feather-pie-chart me-2 fs-12"></i>Analytics Dashboard</a>
                            </li>
                            <li class="nxl-item {{ request()->routeIs('lab.inventory.items') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('lab.inventory.items') }}" wire:navigate><i class="feather-list me-2 fs-12"></i>Items Master</a>
                            </li>
                            <li class="nxl-item {{ request()->routeIs('lab.inventory.suppliers') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('lab.inventory.suppliers') }}" wire:navigate><i class="feather-truck me-2 fs-12"></i>Suppliers</a>
                            </li>
                            <li class="nxl-item {{ request()->routeIs('lab.inventory.purchase') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('lab.inventory.purchase') }}" wire:navigate><i class="feather-download me-2 fs-12"></i>Receive Stock</a>
                            </li>
                            <li class="nxl-item {{ request()->routeIs('lab.inventory.issuance') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('lab.inventory.issuance') }}" wire:navigate><i class="feather-external-link me-2 fs-12"></i>Staff Issuance</a>
                            </li>
                            <li class="nxl-item {{ request()->routeIs('lab.inventory.stock') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('lab.inventory.stock') }}" wire:navigate><i class="feather-database me-2 fs-12"></i>Current Stock</a>
                            </li>
                        </ul>
                    </li>
                    @endif

                    <li class="nxl-item nxl-caption">
                        <label>Settings & Account</label>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('lab.audit-logs') ? 'active' : '' }}">
                        <a href="{{ route('lab.audit-logs') }}" class="nxl-link" wire:navigate>
                            <span class="nxl-micon"><i class="feather-shield"></i></span>
                            <span class="nxl-mtext">Audit Logs</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('lab.settings') ? 'active' : '' }}">
                        <a href="{{ route('lab.settings') }}" class="nxl-link" wire:navigate>
                            <span class="nxl-micon"><i class="feather-settings"></i></span>
                            <span class="nxl-mtext">Lab Settings</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('lab.profile') ? 'active' : '' }}">
                        <a href="{{ route('lab.profile') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-user"></i></span>
                            <span class="nxl-mtext">My Profile</span>
                        </a>
                    </li>
                    <li class="nxl-item">
                        <form method="POST" action="{{ route('logout') }}" id="logout-form-lab" class="d-none">
                            @csrf
                        </form>
                        <a href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('logout-form-lab').submit();" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-log-out text-danger"></i></span>
                            <span class="nxl-mtext text-danger">Logout</span>
                        </a>
                    </li>
                    @endrole

                @role('branch_admin')
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
                            <span class="nxl-micon"><i class="feather-clipboard"></i></span>
                            <span class="nxl-mtext">Test Reports</span>
                        </a>
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
                        <label>Network</label>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('lab.collection.centers') ? 'active' : '' }}">
                        <a class="nxl-link" href="{{ route('lab.collection.centers') }}" wire:navigate>
                            <span class="nxl-micon"><i class="feather-map"></i></span>
                            <span class="nxl-mtext">Collection Centers</span>
                        </a>
                    </li>

                    <li class="nxl-item nxl-caption">
                        <label>Finance</label>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('lab.settlements') ? 'active' : '' }}">
                        <a href="{{ route('lab.settlements') }}" class="nxl-link" wire:navigate>
                            <span class="nxl-micon"><i class="feather-dollar-sign"></i></span>
                            <span class="nxl-mtext">Settlements</span>
                        </a>
                    </li>

                    <li class="nxl-item nxl-caption">
                        <label>Account</label>
                    </li>
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
                        <form method="POST" action="{{ route('logout') }}" id="logout-form-branch" class="d-none">
                            @csrf
                        </form>
                        <a href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('logout-form-branch').submit();" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-log-out text-danger"></i></span>
                            <span class="nxl-mtext text-danger">Logout</span>
                        </a>
                    </li>
                @endrole

                @if(!auth()->user()->patientProfile && (auth()->user()->hasAnyRole(['doctor', 'agent', 'collection_center']) || auth()->user()->collection_center_id || auth()->user()->doctorProfile || auth()->user()->agentProfile))
                    <li class="nxl-item nxl-caption">
                        <label>Partner Portal</label>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('partner.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('partner.dashboard') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-airplay"></i></span>
                            <span class="nxl-mtext">Dashboard</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('partner.patients') ? 'active' : '' }}">
                        <a href="{{ route('partner.patients') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">My Referrals</span>
                        </a>
                    </li>
                    @can('create pos')
                    <li class="nxl-item {{ request()->routeIs('lab.pos') ? 'active' : '' }}">
                        <a href="{{ route('lab.pos') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-plus-circle"></i></span>
                            <span class="nxl-mtext">Create Bill (POS)</span>
                        </a>
                    </li>
                    @endcan
                    
                    @can('view invoices')
                    @php
                        $isCC = auth()->user()->collection_center_id || auth()->user()->hasRole('collection_center') || collect(auth()->user()->roles->pluck('name'))->contains(fn($r) => str_contains(strtolower($r), 'collection'));
                    @endphp
                    @if(!$isCC)
                    <li class="nxl-item {{ request()->routeIs('lab.invoices') ? 'active' : '' }}">
                        <a href="{{ route('lab.invoices') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-file-text"></i></span>
                            <span class="nxl-mtext">Invoice History</span>
                        </a>
                    </li>
                    @endif
                    @endcan



                    @can('view reports')
                    @php
                        $isCC = auth()->user()->collection_center_id || auth()->user()->hasRole('collection_center') || collect(auth()->user()->roles->pluck('name'))->contains(fn($r) => str_contains(strtolower($r), 'collection'));
                    @endphp
                    @if(!$isCC)
                    <li class="nxl-item {{ request()->routeIs('lab.reports') ? 'active' : '' }}">
                        <a href="{{ route('lab.reports') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-clipboard"></i></span>
                            <span class="nxl-mtext">Test Reports</span>
                        </a>
                    </li>
                    @endif
                    @endcan

                    <li class="nxl-item {{ request()->routeIs('partner.settlements') ? 'active' : '' }}">
                        <a href="{{ route('partner.settlements') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-dollar-sign"></i></span>
                            <span class="nxl-mtext">Settlement History</span>
                        </a>
                    </li>
                    @php
                        $isCC = auth()->user()->collection_center_id || auth()->user()->hasRole('collection_center') || collect(auth()->user()->roles->pluck('name'))->contains(fn($r) => str_contains(strtolower($r), 'collection'));
                    @endphp
                    @if(!$isCC)
                    <li class="nxl-item {{ request()->routeIs('partner.invoices') ? 'active' : '' }}">
                        <a href="{{ route('partner.invoices') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-file-text"></i></span>
                            <span class="nxl-mtext">Invoice History</span>
                        </a>
                    </li>
                    @endif

                    @php
                        $isCC = auth()->user()->collection_center_id || auth()->user()->hasRole('collection_center') || collect(auth()->user()->roles->pluck('name'))->contains(fn($r) => str_contains(strtolower($r), 'collection'));
                    @endphp
                    @if($isCC)
                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('partner.doctors') || request()->routeIs('partner.agents') ? 'active nxl-trigger' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">Referral Network</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item {{ request()->routeIs('partner.doctors') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('partner.doctors') }}" wire:navigate><i class="feather-user-check me-2 fs-12"></i>Manage Doctors</a>
                            </li>
                            <li class="nxl-item {{ request()->routeIs('partner.agents') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('partner.agents') }}" wire:navigate><i class="feather-briefcase me-2 fs-12"></i>Manage Agents</a>
                            </li>
                        </ul>
                    </li>
                    @endif

                    <li class="nxl-item nxl-caption">
                        <label>Account Settings</label>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('partner.profile') ? 'active' : '' }}">
                        <a href="{{ route('partner.profile') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-user"></i></span>
                            <span class="nxl-mtext">My Profile</span>
                        </a>
                    </li>
                    <li class="nxl-item">
                        <form method="POST" action="{{ route('logout') }}" id="logout-form-sidebar" class="d-none">
                            @csrf
                        </form>
                        <a href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-log-out text-danger"></i></span>
                            <span class="nxl-mtext text-danger">Logout</span>
                        </a>
                    </li>
                @endif

                @if(auth()->user()->patientProfile)
                    <li class="nxl-item nxl-caption">
                        <label>Patient Portal</label>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('portal.dashboard') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-airplay"></i></span>
                            <span class="nxl-mtext">Overview</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('portal.reports') ? 'active' : '' }}">
                        <a href="{{ route('portal.reports') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-clipboard"></i></span>
                            <span class="nxl-mtext">My Test Reports</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('portal.membership') ? 'active' : '' }}">
                        <a href="{{ route('portal.membership') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-award"></i></span>
                            <span class="nxl-mtext">My Membership</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('portal.profile') ? 'active' : '' }}">
                        <a href="{{ route('portal.profile') }}" wire:navigate class="nxl-link">
                            <span class="nxl-micon"><i class="feather-user"></i></span>
                            <span class="nxl-mtext">My Profile</span>
                        </a>
                    </li>
                    <li class="nxl-item">
                        <form method="POST" action="{{ route('logout') }}" id="logout-form-patient" class="d-none">
                            @csrf
                        </form>
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
        /* Increase main menu font size */
        .nxl-navigation .nxl-navbar .nxl-item > .nxl-link .nxl-mtext {
            font-size: 14.5px !important;
            font-weight: 600 !important;
        }
        
        /* Decrease submenu font size */
        .nxl-navigation .nxl-navbar .nxl-item .nxl-submenu .nxl-item .nxl-link {
            font-size: 12.5px !important;
            font-weight: 500 !important;
            padding-top: 8px !important;
            padding-bottom: 8px !important;
        }

        /* Adjust icon size in submenu if needed */
        .nxl-navigation .nxl-navbar .nxl-item .nxl-submenu .nxl-item .nxl-link i {
            font-size: 11px !important;
        }

        /* Caption/Label styling */
        .nxl-navigation .nxl-navbar .nxl-caption label {
            font-size: 10px !important;
            letter-spacing: 1px !important;
            font-weight: 700 !important;
            color: #64748b !important;
        }
    </style>
</nav>
