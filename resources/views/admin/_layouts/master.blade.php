<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Start your development with a Dashboard for Bootstrap 4.">
    <meta name="author" content="Creative Tim">
    <title>Dashboard | {{config('app.name')}}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ URL('/') }}/assetsCustomer/img/fav_logo.png">
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">
    <!-- Icons -->
    <link rel="stylesheet" href="{{asset('assets/vendor/nucleo/css/nucleo.css')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('assets/vendor/@fortawesome/fontawesome-free/css/all.min.css')}}" type="text/css">
    <!-- Page plugins -->
    <link rel="stylesheet" href="{{asset('assets/vendor/select2/dist/css/select2.min.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/dropzone.css" integrity="sha512-UrCkMTUH0evgGYJJ1Gb5XGuBXDrsSNoyN6Y6OecnEldtTg0TnqZACVJXyEY1wmvf6H8sKET/Yb85cG1xOjSnsw==" crossorigin="anonymous" />
    <link rel="stylesheet" href="{{asset('assets/vendor/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendor/datatables.net-checkboxes/css/dataTables.checkboxes.css')}}">
    <!-- Argon CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/argon.css?v=1.1.0')}}" type="text/css">  
    
    {{-- @livewireStyles --}}
    
    @yield('css')
    
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<style>
    .error {
        color: red;
        font-size: 12px;
    }
    .dropzone {
        border: 1px dashed #dee2e6;
        border-radius: 0.375rem;
    }
    
</style>

<body>
    @include('sweetalert::alert')
    <!-- Sidenav -->
    @include('admin._layouts.sidebar')
    <!-- Main content -->
    <div class="main-content" id="panel">
        
        @include('admin._layouts.navbar')
        
        @yield('header')
        
        @yield('content')
        
        @yield('modal')
        
    </div>
    
    <!-- Argon Scripts -->
    <!-- Core -->
    <script src="{{asset('assets/vendor/jquery/dist/jquery.min.js')}}"></script>
    <script src="{{asset('assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/vendor/js-cookie/js.cookie.js')}}"></script>
    <script src="{{asset('assets/vendor/jquery.scrollbar/jquery.scrollbar.min.js')}}"></script>
    <script src="{{asset('assets/vendor/jquery-scroll-lock/dist/jquery-scrollLock.min.js')}}"></script>
    <!-- Optional JS -->
    <script src="{{asset('assets/vendor/chart.js/dist/Chart.min.js')}}"></script>
    <script src="{{asset('assets/vendor/chart.js/dist/Chart.extension.js')}}"></script>
    <script src="{{asset('assets/vendor/list.js/dist/list.min.js')}}"></script>
    <script src="{{asset('assets/vendor/quill/dist/quill.min.js')}}"></script>
    <script src="{{asset('assets/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('assets/vendor/select2/dist/js/select2.min.js')}}"></script>
    <script src="{{asset('assets/vendor/datatables.net/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/vendor/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('assets/vendor/datatables.net-checkboxes/js/datatables.checkboxes.min.js')}}"></script>
    <!-- Argon JS -->
    <script src="{{asset('assets/js/argon.js?v=1.1.0')}}"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="{{asset('vendor/sweetalert/sweetalert.all.js')}}"></script>
  
    
    {{-- @livewireScripts --}}
    
    @yield('script')
    
</body>

</html>