@if (Auth::user()->hasAnyRole('kasir'))

@include('kasir.index')

@elseif(Auth::user()->hasAnyRole('admin'))

@include('admin.index')

@endif
