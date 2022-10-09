@extends('admin._layouts.master')

@section('content')

<section class="content">
    <div class="header bg-primary pb-6">
        <div class="container-fluid">
            <div class="header-body">
                <div class="row align-items-center py-4">
                    <div class="col-lg-6 col-7">
                        <h6 class="h2 text-white d-inline-block mb-0">Toko</h6>
                        <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                                <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="fas fa-home"></i></a></li>
                                <li class="breadcrumb-item"><a href="#">Toko</a></li>
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
                                <h3 class="mb-0">List Toko</h3>
                            </div>
                            <div class="col-8 text-right">
                                <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#createModal" id="create">Tambah Toko</button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive py-4">
                        <table class="table table-flush" id="dataTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Alamat</th>
                                    <th>Action</th>
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

@section('modal')

<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Tambah Toko</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addForm" action="{{route('shop.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="modal-body">
                    {{-- <input type="hidden" name="user_id" id="user_id" value=""> --}}
                    <div class="form-group">
                        <label for="status" class="col-form-label">Nama Toko:</label>
                        <input type="text" name="name" id="name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="status" class="col-form-label">Kabupaten:</label>
                        <input type="text" name="city" id="city" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="status" class="col-form-label">Kecamatan:</label>
                        <input type="text" name="region" id="region" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="status" class="col-form-label">Alamat Lengkap:</label>
                        <input type="text" name="street_address" id="street_adrress" class="form-control">
                    </div>
                    
                    
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="save-btn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Edit Toko</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editForm" action="{{route('shop.update', 'id')}}" method="POST" enctype="multipart/form-data">
                @csrf
                {{method_field('PUT')}}
                <div class="modal-body">
                    <input type="hidden" name="id" id="id" value="">
                    <div class="form-group">
                        <label for="status" class="col-form-label">Nama Toko:</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror">
                        
                        @error('name') <div class="invalid-feedback">{{$message}}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="status" class="col-form-label">Kabupaten:</label>
                        <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror">
                        @error('city') <div class="invalid-feedback">{{$message}}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="status" class="col-form-label">Kecamatan:</label>
                        <input type="text" name="region" id="region" class="form-control @error('region') is-invalid @enderror">
                        @error('region') <div class="invalid-feedback">{{$message}}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="status" class="col-form-label">Alamat Lengkap:</label>
                        <input type="text" name="street_address" id="street_address" class="form-control @error('street_address') is-invalid @enderror">
                        @error('street_address') <div class="invalid-feedback">{{$message}}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="save-btn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')

<script>
    $('#addForm').validate({
        rules: {
            name: {
                required: true
            },
            city: {
                required: true
            },
            region: {
                required: true
            },
            street_address: {
                required: true
            }
        }
    });
</script>

<script>
    $('#editForm').validate({
        rules: {
            name: {
                required: true
            },
            city: {
                required: true
            },
            region: {
                required: true
            },
            street_address: {
                required: true
            }
        }
    });
</script>

<script>
    $(document).ready(function(){
        var table = $('#dataTable').DataTable({
            processing:true,
            searching:true,
            info:false,
            serverside:true,
            ordering:false,
            language: { 
                paginate: { previous: "<i class='fas fa-angle-left'>", next: "<i class='fas fa-angle-right'>"
                }
            },
            ajax:"{{route('ajax.get.shop')}}",
            columns: [
            {
					data: null,
					searchable: false,
					orderable: false,
					render: function (data, type, row, meta) {
						return meta.row + meta.settings._iDisplayStart + 1;
					}  
				},
            {data: 'name', name: 'name'},
            {data: 'street_address', name: 'street_address'},
            {data: 'action', name: 'action'}
            ]
            
        });
        
        
        $('#editModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) 
            var id = button.data('id')
            var name = button.data('name')
            var city = button.data('city')
            var region = button.data('region')
            var street_address = button.data('street_address')
            
            var modal = $(this)
            modal.find('.modal-body #id').val(id)
            modal.find('.modal-body #name').val(name)
            modal.find('.modal-body #city').val(city)
            modal.find('.modal-body #region').val(region)
            modal.find('.modal-body #street_address').val(street_address)
        });
        
        $('#hapusModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) 
            var id = button.data('id')
            
            var modal = $(this)
            modal.find('.modal-body #id').val(id)
            
        });
        
        
        
        
        
    });
</script>

@stop
