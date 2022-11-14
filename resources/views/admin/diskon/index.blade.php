@extends('admin._layouts.master')

{{-- @section('css')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.min.css">

@endsection --}}

@section('content')

<section class="content">
  <div class="header bg-primary pb-6">
    <div class="container-fluid">
      <div class="header-body">
        <div class="row align-items-center py-4">
          <div class="col-lg-6 col-7">
            <h6 class="h2 text-white d-inline-block mb-0">Diskon Produk</h6>
            <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
              <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="fas fa-home"></i></a></li>
                <li class="breadcrumb-item"><a href="#">Diskon Produk</a></li>
              </ol>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="container-fluid mt--6">
    <!-- Table -->
    <div class="row">
      <div class="col">
        <div class="card">
          <!-- Card header -->
          <div class="card-header">
            <div class="row">
              <div class="col-4">
                <h3 class="mb-0">List Data Produk</h3>
              </div>
              <div class="col-8 text-right">
              </div>
            </div>
          </div>
          <div class="table-responsive py-4">
            <div class="row px-4">
              <div class="col-12">
                <div class="form-group row">
                  <label for="" class="col-sm-2 col-form-label">Kategori</label>
                  <div class="col-sm-4">
                    <select name="kategori" id="kategori" class="form-control" data-toggle="select">
                      <option selected disabled>Pilih Kategori</option>
                      <option value="">Semua</option>
                      @foreach ($category as $item)
                      <option value="{{$item->id}}">{{$item->name}}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-sm-6">
                    <button type="filter" name="filter" id="filter" class="btn btn-primary">Cari</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="row px-4">
              <div class="col-12">
                <div class="form-group row">
                  <label for="" class="col-sm-2 col-form-label">Periode Awal Diskon</label>
                  <div class="col-sm-3">
                    <input type="date" class="form-control" id="startDate" >
                  </div>
                  <div class="col-sm-2">
                    <input type="time" class="form-control" id="startTime" >
                  </div>
                </div>
              </div>
              <div class="col-12">
                <div class="form-group row">
                  <label for="" class="col-sm-2 col-form-label">Periode Akhir Diskon</label>
                  <div class="col-sm-3">
                    <input type="date" class="form-control" id="endDate" >
                  </div>
                  <div class="col-sm-2">
                    <input type="time" class="form-control" id="endTime" >
                  </div>
                </div>
              </div>
              <div class="col-12">
                <div class="form-group row">
                  <label for="" class="col-sm-2 col-form-label">Jenis Paket Diskon</label>
                  <div class="col-sm-3">
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="paketDiskon" value="persen" id="persentase" checked>
                      <label class="form-check-label">
                        Persentase Diskon
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="paketDiskon" value="nominal" id="nominal">
                      <label class="form-check-label">
                        Nominal Diskon
                      </label>
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="input-group mb-3">
                      <div class="input-group-prepend" id="prefix">
                        <span class="input-group-text">Rp</span>
                      </div>
                      <input type="text" class="form-control" name="nilaiDiskon" aria-describedby="nilaiDiskon">
                      <div class="input-group-append" id="suffix">
                        <span class="input-group-text">%</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-5 text-right">
                    <button class="btn btn-primary" id="btnKonfirmasi">Konfirmasi</button>
                  </div>
                </div>
              </div>
            </div>
            {{-- <div class="row px-4">
              
            </div> --}}
            <table class="table table-flush" id="dataTable">
              <thead class="thead-light">
                <tr>
                  <th></th>
                  <th>Nama</th>
                  <th>Harga</th>
                  <th>Jenis Diskon</th>
                  <th>Diskon</th>
                  <th>Periode Awal Dikson</th>
                  <th>Periode Akhir Diskon</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
    
  </div>
</section>
@endsection

@section('script')

<script>
  
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  
  $(document).ready(function(){
    $('#prefix').hide();
    
    
    function load_data(kategori = ''){
      $('#dataTable').DataTable({
        processing:true,
        searching:true,
        info:false,
        lengthMenu: [[-1],["All"]],
        serverSide:true,
        ordering:true,
        language: { 
          paginate: { previous: "<i class='fas fa-angle-left'>", next: "<i class='fas fa-angle-right'>"
          }
        },
        ajax:{
          url:"{{route('ajax.get.produk.diskon')}}",
          data:{kategori:kategori}
        },
        columnDefs: [ {
          targets: 0,
          checkboxes: {
            selectRow: true
          }
        } ],
        order: [[ 1, 'asc' ]],
        columns: [
        {
          data: 'id', 
          name: 'id'
        },
        {data: 'product_name', name: 'product_name'},
        {data: 'price', name: 'price'},
        {data: 'diskon_type', name: 'diskon_type'},
        {data: 'diskon', name: 'diskon'},
        {data: 'start_date', name: 'start_date'},
        {data: 'end_date', name: 'end_date'},
        ]
        
      });
    }
    
    $('#filter').click(function(){
      var kategori = $('#kategori').val();
      console.log(kategori);
      if(kategori != null)
      {
        $('#dataTable').DataTable().destroy();
        load_data(kategori);
      }
      else
      {
        alert('Kategori wajib diisi');
      }
    });
  });
</script>

<script>
  $('#btnKonfirmasi').on('click', function (){
    var row = $('#dataTable').DataTable().column(0).checkboxes.selected();
    const product_id = []
    
    $.each(row, function(index, rowId){
      product_id.push($('#'+rowId).val())
    })
    
    var startDate = $('#startDate').val()
    var startTime = $('#startTime').val()
    var endDate = $('#endDate').val()
    var endTime = $('#endTime').val()
    
    var start = startDate + ' '+ startTime;
    var end = endDate + ' '+ endTime;
    
    var diskon = $('input[name="nilaiDiskon"]').val();
    var type = $('input[name="paketDiskon"]:checked').val();
    
    if (product_id.length === 0) {
      
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Silahkan pilih produk yang akan di diskon',
      })
      
    }else {
      var data = {product_id: product_id, start: start, end: end, type: type, value: diskon}
      
      $.ajax({
        url: "{{ route('update-discount') }}",
        data: data,
        method: "POST",
        beforeSend: function(){
          Swal.fire({
            title: 'Mohon tunggu sebentar ...',
            html: 'Data sedang diproses',
            timerProgressBar: true,
            didOpen: () => {
              Swal.showLoading()
            },
          })
        }
      })
      .done(function(data){
        console.log(data);
        location.reload()
      })
      .fail(function(){
        location.reload()
      })
    }
    
    
    
    
  })
  
  $('#persentase').on('click', function(){
    $('#suffix').show();
    $('#prefix').hide();
  })
  
  $('#nominal').on('click', function(){
    $('#suffix').hide();
    $('#prefix').show();
  })
</script>


@stop
