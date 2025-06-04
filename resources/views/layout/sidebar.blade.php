<style>
    .icon-img {
        width: 1.5rem;
        /* Scales with text size */
        height: 1.5rem;
        /* Scales with text size */
    }


    .custom-sidebar {
        background-color: #f5f0e1;
        /* Soft beige for a warm, professional look */
        border-right: 3px solid #d4b895;
        /* Stylish accent */
    }

    .sidebar-header {
        border-bottom: 2px solid #d4b895;
        /* Consistent separator */
    }

    .sidebar-toggler span {
        background-color: #8c6e63;
        /* Earthy brown for a cozy touch */
    }

    .sidebar-toggler.not-active:hover span {
        background-color: #6b4f4f;
        /* Darker brown on hover */
    }
</style>

<nav class="sidebar custom-sidebar">
    <div class="sidebar-header">
        <a href="#" class="sidebar-brand">
            <img src="{{ url('assets/images/logo.jpg') }}" height="50px" width="100%" alt="logo" />
        </a> &nbsp;&nbsp;
        <div class="sidebar-toggler not-active">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="sidebar-body">
        <ul class="nav">
            @if (Auth::user()->role == 'Full')
                <li class="nav-item nav-category">Main</li>
                <li class="nav-item {{ request()->is('/*') ? 'active' : '' }} ">
                    <a href="{{ Route('dashboard') }}" class="nav-link">
                        <img src="{{ asset('assets/images/icons/icon-dashboard.png') }}" class="icon-img">
                        <span class="link-title">Dashboard</span>
                    </a>
                </li>

                <li class="nav-item nav-category">Master</li>
                <li class="nav-item {{ request()->is('/*') ? 'active' : '' }} ">
                    <a href="{{ Route('MasterNoUnit') }}" class="nav-link">
                        <img src="{{ asset('assets/images/icons/icon-dayact.png') }}" class="icon-img">
                        <span class="link-title">No. Unit</span>
                    </a>
                </li>

                <li class="nav-item {{ request()->is('/*') ? 'active' : '' }} ">
                    <a href="{{ Route('MasterActivity') }}" class="nav-link">
                        <img src="{{ asset('assets/images/icons/icon-training.png') }}" class="icon-img">
                        <span class="link-title">Activity</span>
                    </a>
                </li>

                <li class="nav-item nav-category">MOP</li>

                <li class="nav-item {{ request()->is('MOP*') ? 'active' : '' }}">
                    <a href="{{ Route('MOPIndex') }}" class="nav-link">
                        <img src="{{ asset('assets/images/icons/icon-data.png') }}" class="icon-img">
                        <span class="link-title">Data</span>
                    </a>
                </li>
            @else
            @endif
            <li class="nav-item nav-category">Mentoring</li>

            {{-- <li class="nav-item {{ request()->is('Mentoring/data') ? 'active' : '' }}">
                <a href="{{ Route('MentoringDashboard') }}" class="nav-link">
                    <img src="{{ asset('assets/images/icons/icon-dashboard.png') }}" class="icon-img">
                    <span class="link-title">Dashboard</span>
                </a>
            </li> --}}

            <li class="nav-item {{ request()->is('Mentoring/data') ? 'active' : '' }}">
                <a href="{{ Route('MentoringIndex') }}" class="nav-link">
                    <img src="{{ asset('assets/images/icons/icon-data.png') }}" class="icon-img">
                    <span class="link-title">Data</span>
                </a>
            </li>

            <li class="nav-item {{ request()->is('Mentoring/digger') ? 'active' : '' }}">
                <a href="{{ Route('MentoringCreate', 'DIGGER') }}" class="nav-link">
                    <img src="{{ asset('assets/images/icons/icon-digger.png') }}" class="icon-img">
                    <span class="link-title">Form Digger</span>
                </a>
            </li>

            <li class="nav-item {{ request()->is('Mentoring/hauler') ? 'active' : '' }}">
                <a href="{{ Route('MentoringCreate', 'HAULER') }}" class="nav-link">
                    <img src="{{ asset('assets/images/icons/icon-hauler.png') }}" class="icon-img">
                    <span class="link-title">Form Hauler</span>
                </a>
            </li>

            <li class="nav-item {{ request()->is('Mentoring/buldozer') ? 'active' : '' }}">
                <a href="{{ Route('MentoringCreate', 'BULLDOZER') }}" class="nav-link">
                    <img src="{{ asset('assets/images/icons/icon-buldozer.png') }}" class="icon-img">
                    <span class="link-title">Form Buldozer</span>
                </a>
            </li>

            <li class="nav-item {{ request()->is('Mentoring/grader') ? 'active' : '' }}">
                <a href="{{ Route('MentoringCreate', 'GRADER') }}" class="nav-link">
                    <img src="{{ asset('assets/images/icons/icon-grader.png') }}" class="icon-img">
                    <span class="link-title">Form Grader</span>
                </a>
            </li>

            <li class="nav-item nav-category">Trainer</li>

            <li class="nav-item {{ request()->is('Trainer/daily-activity') ? 'active' : '' }}">
                <a href="{{ Route('DayActIndex') }}" class="nav-link">
                    <img src="{{ asset('assets/images/icons/icon-routine.png') }}" class="icon-img">
                    <span class="link-title">Daily Activity</span>
                </a>
            </li>

            <li class="nav-item {{ request()->is('Trainer/train-hours') ? 'active' : '' }}">
                <a href="{{ Route('HMTrainIndex') }}" class="nav-link">
                    <img src="{{ asset('assets/images/icons/icon-time.png') }}" class="icon-img">
                    <span class="link-title">Train Hours</span>
                </a>
            </li>
            @if (Auth::user()->role == 'Full')
                <li class="nav-item nav-category">Report</li>

                <li class="nav-item {{ request()->is('Report*') ? 'active' : '' }}">
                    <a href="{{ Route('ReportMOP') }}" class="nav-link">
                        <img src="{{ asset('assets/images/icons/icon-performance.png') }}" class="icon-img">
                        <span class="link-title">MOP</span>
                    </a>
                </li>

                <li class="nav-item {{ request()->is('Report*') ? 'active' : '' }}">
                    <a href="{{ Route('ReportDayKPI') }}" class="nav-link">
                        <img src="{{ asset('assets/images/icons/icon-dayact.png') }}" class="icon-img">
                        <span class="link-title">Daily Activity</span>
                    </a>
                </li>

                <li class="nav-item {{ request()->is('Report*') ? 'active' : '' }}">
                    <a href="{{ Route('ReportHMT') }}" class="nav-link">
                        <img src="{{ asset('assets/images/icons/icon-training.png') }}" class="icon-img">
                        <span class="link-title">HM Train Hours</span>
                    </a>
                </li>
            @else
            @endif
        </ul>
    </div>
</nav>
