 <nav id="navbar-main" class="navbar navbar-horizontal navbar-transparent navbar-main navbar-expand-lg navbar-light">
    <div class="container-fluid">
      <a class="navbar-brand" href="{{url('/')}}">
        <img style="height: 90px !important" src="{{asset('img/logo_no_bg.png')}}">
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-collapse" aria-controls="navbar-collapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="navbar-collapse navbar-custom-collapse collapse" id="navbar-collapse">
        <div class="navbar-collapse-header">
          <div class="row">
            <div class="col-6 collapse-brand">
              <a href="{{url('')}}">
                <img style="height: 90px !important" src="{{asset('img/logo_no_bg.png')}}">
              </a>
            </div>
            <div class="col-6 collapse-close">
              <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbar-collapse" aria-controls="navbar-collapse" aria-expanded="false" aria-label="Toggle navigation">
                <span></span>
                <span></span>
              </button>
            </div>
          </div>
        </div>
        <hr class="d-lg-none" />
        <ul class="navbar-nav align-items-center ml-auto">
        <li class="nav-item dropdown">
          <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <div class="media align-items-center">
              <span class="avatar avatar-sm rounded-circle bg-white">
                @if(Auth::user()->foto == null)
                <img src="{{asset('img/person.png')}}" alt="" style="border-radius:15px; object-fit:cover;">
                @else
                <img src="{{asset('storage/public/'. Auth::user()->foto)}}" alt="" style="border-radius:15px; object-fit:cover;">
                @endif
              </span>
              <div class="media-body ml-2 d-none d-lg-block">
                <span class="mb-0 text-md text-capitalize  font-weight-bold">{{Auth::user()->name}}</span>
              </div>
            </div>
          </a>
          <div class="dropdown-menu dropdown-menu-right">
            <div class="dropdown-header noti-title">
              <h6 class="text-overflow m-0">Welcome!</h6>
            </div>
            <div class="dropdown-divider"></div>
            <a href="{{ url('/') }}" class="dropdown-item">
              <span>Kasir</span>
            </a>
            <a href="{{ url('reset', Auth::user()->id) }}" class="dropdown-item">
              <span>Ubah Password</span>
            </a>
            <a href="{{ url('rekap', Auth::user()->id) }}" class="dropdown-item">
              <span>Rekap Penjualan</span>
            </a>
            <a href="{{ route('logout') }}" onclick="event.preventDefault();
            document.getElementById('logout-form').submit();" class="dropdown-item">
            <span>Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
              @csrf
            </form>
          </div>
        </li>
      </ul>
      </div>
    </div>
  </nav>