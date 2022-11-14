<!-- Sidenav -->
<nav class="sidenav navbar navbar-vertical fixed-left navbar-expand-xs navbar-light bg-white" id="sidenav-main">
  <div class="scrollbar-inner">
    <!-- Brand -->
    <div class="sidenav-header d-flex align-items-center">
      <a class="navbar-brand" href="{{url('/')}}">
        {{-- <h3>Dashboard</h3> --}}
        <img src="{{asset('img/logo.jpg')}}" class="navbar-brand-img" alt="...">
      </a>
      <div class="ml-auto">
        <!-- Sidenav toggler -->
        <div class="sidenav-toggler d-none d-xl-block" data-action="sidenav-unpin" data-target="#sidenav-main">
          <div class="sidenav-toggler-inner">
            <i class="sidenav-toggler-line"></i>
            <i class="sidenav-toggler-line"></i>
            <i class="sidenav-toggler-line"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="navbar-inner">
      <!-- Collapse -->
      <div class="collapse navbar-collapse" id="sidenav-collapse-main">
        <!-- Nav items -->
        <ul class="navbar-nav">
          @if ($active == 'dashboard')
          <li class="nav-item">
            <a class="nav-link active" href="{{url('/')}}">
              <i class="ni ni-archive-2 text-green"></i>
              <span class="nav-link-text">Dashboard</span>
            </a>
          </li>
          @else
          <li class="nav-item">
            <a class="nav-link" href="{{url('/')}}">
              <i class="ni ni-archive-2 text-green"></i>
              <span class="nav-link-text">Dashboard</span>
            </a>
          </li>
          @endif
          

          @if ($active == 'produk')
          <li class="nav-item">
            <a class="nav-link active" href="{{route('product.index')}}">
              <i class="ni ni-box-2 text-blue"></i>
              <span class="nav-link-text">Produk</span>
            </a>
          </li>
          @else
          <li class="nav-item">
            <a class="nav-link" href="{{route('product.index')}}">
              <i class="ni ni-box-2 text-blue"></i>
              <span class="nav-link-text">Produk</span>
            </a>
          </li>
          @endif

          @if ($active == 'diskon')
          <li class="nav-item">
            <a class="nav-link active" href="{{route('diskon')}}">
              <i class="ni ni-box-2 text-blue"></i>
              <span class="nav-link-text">Diskon</span>
            </a>
          </li>
          @else
          <li class="nav-item">
            <a class="nav-link" href="{{route('diskon')}}">
              <i class="ni ni-box-2 text-blue"></i>
              <span class="nav-link-text">Diskon</span>
            </a>
          </li>
          @endif

          @if ($active == 'toko')
          <li class="nav-item">
            <a class="nav-link active" href="{{route('shop.index')}}">
              <i class="ni ni-shop text-yellow"></i>
              <span class="nav-link-text">Toko</span>
            </a>
          </li>
          @else
          <li class="nav-item">
            <a class="nav-link" href="{{route('shop.index')}}">
              <i class="ni ni-shop text-yellow"></i>
              <span class="nav-link-text">Toko</span>
            </a>
          </li>
          @endif

          @if ($active == 'pegawai')
          <li class="nav-item">
            <a class="nav-link active" href="{{route('users.index')}}">
              <i class="ni ni-single-02 text-red"></i>
              <span class="nav-link-text">Pegawai</span>
            </a>
          </li>
          @else
          <li class="nav-item">
            <a class="nav-link" href="{{route('users.index')}}">
              <i class="ni ni-single-02 text-red"></i>
              <span class="nav-link-text">Pegawai</span>
            </a>
          </li>
          @endif

          @if ($active == 'kategori')
          <li class="nav-item">
            <a class="nav-link active" href="{{route('categories.index')}}">
              <i class="ni ni-vector text-cyan"></i>
              <span class="nav-link-text">Kategori</span>
            </a>
          </li>
          @else
          <li class="nav-item">
            <a class="nav-link" href="{{route('categories.index')}}">
              <i class="ni ni-vector text-cyan"></i>
              <span class="nav-link-text">Kategori</span>
            </a>
          </li>
          @endif

          @if ($active == 'rekap')
          <li class="nav-item">
            <a class="nav-link active" href="{{url('rekap')}}">
              <i class="ni ni-archive-2 text-indigo"></i>
              <span class="nav-link-text">Rekap Penjualan</span>
            </a>
          </li>
          @else
          <li class="nav-item">
            <a class="nav-link" href="{{url('rekap')}}">
              <i class="ni ni-archive-2 text-indigo"></i>
              <span class="nav-link-text">Rekap Penjualan</span>
            </a>
          </li>
          @endif

        </ul>
      </div>
    </div>
  </div>
</nav>