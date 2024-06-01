<!-- Sidebar -->
<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-sm-start" href="{{ url('/') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <!-- <i class="fas fa-laugh-wink"></i> -->
            <img src="{{ asset('public/img/logo-icon.webp') }}" alt="img" class="img-fluid">
        </div>
        <div class="sidebar-brand-text mx-2">PMS</div>
    </a>
    <hr class="sidebar-divider my-0">
    <li class="nav-item {{ request()->is('/') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('/') }}">
            <i class="fa-solid fa-house"></i>
            <span>Dashboard</span></a>
    </li>
    {{-- <li class="nav-item {{ request()->is(['users', 'roles', 'products']) ? 'active' : '' }}">
        <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#settings" aria-expanded="false"
            aria-controls="settings">
            <i class="fa-solid fa-screwdriver-wrench"></i>
            <span>Maintenance</span>
        </a>
        <ul id="settings" class="collapse collapse-inner rounded" aria-labelledby="headingUtilities"
            data-bs-parent="#accordionSidebar">
            <li class="collapse-item">
                <div class="subhead">Maintenance</div>
            </li>
            <li class="collapse-item">
                @canany(['read-user', 'create-user', 'edit-user', 'delete-user'])
                    <a href="{{ route('users.index') }}"><i class="bi bi-people"></i> Manage
                        Users</a>
                @endcanany
            </li>
            <li class="collapse-item">
                @canany(['read-role', 'create-role', 'edit-role', 'delete-role'])
                    <a href="{{ route('roles.index') }}"><i class="bi bi-person-fill-gear"></i> Manage
                        Roles</a>
                @endcanany
            </li>
            <li class="collapse-item">
                @canany(['read-product', 'create-product', 'edit-product', 'delete-product'])
                    <a href="{{ route('products.index') }}"><i class="bi bi-bag"></i> Manage
                        Assets</a>
                @endcanany
            </li>
            <li class="collapse-item">
                @canany(['read-position', 'create-position', 'edit-position', 'delete-position'])
                    <a href="{{ route('positions.index') }}"><i class="bi bi-person-circle"></i> Manage
                        Positions</a>
                @endcanany
            </li>
        </ul>
    </li> --}}
    <li class="nav-item {{ request()->is(['tools', 'equipments', 'facilities', 'products']) ? 'active' : '' }}">
        <a class="nav-link {{ request()->is(['tools', 'equipments', 'facilities', 'products']) ? '' : 'collapsed' }}"
            href="#" data-bs-toggle="collapse" data-bs-target="#assets"
            aria-expanded="{{ request()->is(['tools', 'equipments', 'facilities', 'products']) ? 'true' : 'false' }}"
            aria-controls="assets">
            <i class="fa-solid fa-toolbox"></i>
            <span>Assets</span>
        </a>
        <ul id="assets"
            class="collapse collapse-inner rounded {{ request()->is(['tools', 'equipments', 'facilities', 'products']) ? '' : '' }}"
            aria-labelledby="headingUtilities" data-bs-parent="#accordionSidebar">
            <li class="collapse-item">
                <div class="subhead">Assets</div>
            </li>
            <li class="collapse-item {{ request()->is(['products']) ? 'active-class' : '' }}">
                @canany(['read-product', 'create-product', 'edit-product', 'delete-product'])
                    <a href="{{ route('products.index') }}"><i class="bi bi-bag"></i> Manage
                        Assets</a>
                @endcanany
            </li>
            <li class="collapse-item {{ request()->is(['facilities']) ? 'active-class' : '' }}">
                @canany(['read-facility', 'create-facility', 'edit-facility', 'delete-facility'])
                    <a href="{{ route('facilities.index') }}"><i class="bi bi-person-fill-gear"></i> Manage
                        Facilities</a>
                @endcanany
            </li>
            <li class="collapse-item {{ request()->is(['equipments']) ? 'active-class' : '' }}">
                @canany(['read-product', 'create-product', 'edit-product', 'delete-product'])
                    <a href="javascript:void(0);"><i class="bi bi-bag"></i> Manage
                        Equipments</a>
                @endcanany
            </li>
            <li class="collapse-item {{ request()->is(['tools']) ? 'active-class' : '' }}">
                @canany(['read-position', 'create-position', 'edit-position', 'delete-position'])
                    <a href="javascript:void(0);"><i class="bi bi-person-circle"></i> Manage
                        Tools</a>
                @endcanany
            </li>
        </ul>
    </li>
    {{-- <li class="nav-item {{ request()->is(['users', 'roles', 'products']) ? 'active' : '' }}">
        <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#supplies" aria-expanded="false"
            aria-controls="supplies">
            <i class="fa-solid fa-truck-moving"></i>
            <span>Supplies</span>
        </a>
        <ul id="supplies" class="collapse collapse-inner rounded" aria-labelledby="headingUtilities"
            data-bs-parent="#accordionSidebar">
            <li class="collapse-item">
                <div class="subhead">Supplies</div>
            </li>
            <li class="collapse-item">
                @canany(['read-user', 'create-user', 'edit-user', 'delete-user'])
                    <a href="{{ route('users.index') }}"><i class="bi bi-people"></i> Manage
                        Users</a>
                @endcanany
            </li>

            <li class="collapse-item">
                @canany(['read-role', 'create-role', 'edit-role', 'delete-role'])
                    <a href="{{ route('roles.index') }}"><i class="bi bi-person-fill-gear"></i> Manage
                        Roles</a>
                @endcanany
            </li>
            <li class="collapse-item">
                @canany(['read-product', 'create-product', 'edit-product', 'delete-product'])
                    <a href="{{ route('products.index') }}"><i class="bi bi-bag"></i> Manage
                        Assets</a>
                @endcanany
            </li>
            <li class="collapse-item">
                @canany(['read-position', 'create-position', 'edit-position', 'delete-position'])
                    <a href="{{ route('positions.index') }}"><i class="bi bi-person-circle"></i> Manage
                        Positions</a>
                @endcanany
            </li>
        </ul>
    </li> --}}
    <li class="nav-item {{ request()->is(['users', 'roles', 'positions']) ? 'active' : '' }}">
        <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#settings" aria-expanded="false"
            aria-controls="settings">
            <i class="fa-solid fa-gear"></i>
            <span>Settings</span>
        </a>
        <ul id="settings" class="collapse collapse-inner rounded" aria-labelledby="headingUtilities"
            data-bs-parent="#accordionSidebar">
            <li class="collapse-item">
                <div class="subhead">Settings</div>
            </li>
            <li class="collapse-item {{ request()->is(['users']) ? 'active-class' : '' }}">
                @canany(['read-user', 'create-user', 'edit-user', 'delete-user'])
                    <a href="{{ route('users.index') }}"><i class="bi bi-people"></i> Manage
                        Users</a>
                @endcanany
            </li>

            <li class="collapse-item {{ request()->is(['roles']) ? 'active-class' : '' }}">
                @canany(['read-role', 'create-role', 'edit-role', 'delete-role'])
                    <a href="{{ route('roles.index') }}"><i class="bi bi-person-fill-gear"></i> Manage
                        Roles</a>
                @endcanany
            </li>
            <li class="collapse-item {{ request()->is(['positions']) ? 'active-class' : '' }}">
                @canany(['read-position', 'create-position', 'edit-position', 'delete-position'])
                    <a href="{{ route('positions.index') }}"><i class="bi bi-person-circle"></i> Manage
                        Positions</a>
                @endcanany
            </li>
        </ul>
    </li>
    <hr class="sidebar-divider my-0">

    <li class="nav-item">
        <a class="nav-link dropdown-item" href="{{ route('logout') }}"
            onclick="event.preventDefault();
    document.getElementById('logout-form').submit();">
            <i class="fa-solid fa-power-off" style="color: #ff0000;"></i>
            <span class=""> {{ __('Log Off') }}</span> </a>
    </li>
    <div class="text-center d-none d-md-inline mt-4">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
<!-- End of Sidebar -->

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
