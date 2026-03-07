<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ url('/') }}" wire:navigate class="b-brand">
                <img src="{{ asset('assets/images/logo-full.png') }}" alt="Logo" class="logo logo-lg" />
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
                    <a href="{{ route('admin.global-tests') }}" wire:navigate class="nxl-link">
                        <span class="nxl-micon"><i class="feather-layers"></i></span>
                        <span class="nxl-mtext">Global Tests</span>
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

                <li class="nxl-item">
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