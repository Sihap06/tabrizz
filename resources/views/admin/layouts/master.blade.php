@include('admin.layouts.header')

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        
        @include('admin.layouts.navbar')
        @include('admin.layouts.sidebar')

        @yield('content')

        @include('admin.layouts.footer')
        
        
    </div>
    <!-- ./wrapper -->

    @include('admin.layouts.script')
    
    
</body>
</html>

