<style>
.custom-navbar {
    background: linear-gradient(rgba(255, 255, 255, 0.6), rgba(255, 255, 255, 0.3)),
                url("{{ asset('assets/images/navbar.jpg') }}") no-repeat center center;
    background-size: cover;
    color: #fff; /* Optional: Ensures text contrasts well */
    border-bottom: 3px solid #ffcc00; /* Optional: Adds a stylish bottom border */
}
</style>

<nav class="navbar custom-navbar">
    <a href="#" class="sidebar-toggler">
      <i data-feather="menu"></i>
    </a>
    <div class="navbar-content">
      <ul class="navbar-nav">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <img class="wd-30 ht-30 rounded-circle" src="{{ Avatar::create(Auth::User()->name)->toBase64() }}" alt="profile">
          </a>
          <div class="dropdown-menu p-0" aria-labelledby="profileDropdown">
            <div class="d-flex flex-column align-items-center border-bottom px-5 py-3">
              <div class="mb-3">
                <img class="wd-80 ht-80 rounded-circle" src="{{ Avatar::create(Auth::User()->name)->toBase64() }}" alt="">
              </div>
              <div class="text-center">
                <p class="tx-16 fw-bolder">{{ Auth::user()->username }}</p>
                <p class="tx-12 text-muted">{{ Auth::user()->name }}</p>
              </div>
            </div>
            <ul class="list-unstyled p-1">
              <li class="dropdown-item py-2">
                  <a href="{{ route('logout') }}" class="text-body ms-0">
                      <i class="me-2 icon-md" data-feather="log-out"></i>
                      <span>Log Out</span>
                  </a>
              </li>
            </ul>
          </div>
        </li>
      </ul>
    </div>
  </nav>
