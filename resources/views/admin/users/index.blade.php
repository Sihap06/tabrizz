@extends('admin._layouts.master')

@section('content')

<section class="content">
    <div class="header bg-primary pb-6">
        <div class="container-fluid">
            <div class="header-body">
                <div class="row align-items-center py-4">
                    <div class="col-lg-6 col-7">
                        <h6 class="h2 text-white d-inline-block mb-0">Pegawai Saya</h6>
                        <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                                <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="fas fa-home"></i></a></li>
                                <li class="breadcrumb-item"><a href="#">Pegawai</a></li>
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
                                <h3 class="mb-0">List Data Pegawai</h3>
                            </div>
                            <div class="col-8 text-right">
                                <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#createModal" id="create">Tambah Pegawai</button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive py-4">
                        <table class="table table-flush" id="dataTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Status</th>
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

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Tambah Pegawai</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addForm" action="{{route('users.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="modal-body">
                    {{-- <input type="hidden" name="user_id" id="user_id" value=""> --}}
                    <div class="form-group">
                        <label for="status" class="col-form-label">Nama Pegawai:</label>
                        <input type="text" name="name" id="name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="status" class="col-form-label">Email:</label>
                        <input type="email" name="email" id="email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="status" class="col-form-label">Password:</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="status" class="col-form-label">Konfirmasi Password:</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control">
                    </div>
                    
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
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
                <h5 class="modal-title" id="modal-title">Edit Pegawai</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editForm" action="{{route('users.update', 'id')}}" method="POST" enctype="multipart/form-data">
                @csrf
                {{method_field('PUT')}}
                <div class="modal-body">
                    <input type="hidden" name="id" id="_id" value="">
                    <div class="form-group">
                        <label for="status" class="col-form-label">Nama Pegawai:</label>
                        <input type="text" name="name" id="_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="status" class="col-form-label">Email:</label>
                        <input type="email" name="email" id="_email" class="form-control">
                    </div> 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="hapusModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Nonaktif Pegawai</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('users.destroy', 'id')}}" method="POST" enctype="multipart/form-data">
                @csrf
                {{method_field('DELETE')}}
                <div class="modal-body">
                    <input type="hidden" name="id" id="__id" value="">
                    Apakah anda yakin akan menonaktifkan akun ini ?
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Edit Modal -->
<div class="modal fade" id="aktifModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Aktif Pegawai</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="aktifForm" action="{{url('userAktif')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" id="id" value="">
                    <div class="form-group">
                        <label for="status" class="col-form-label">Password Baru:</label>
                        <input type="password" name="password" id="_password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="status" class="col-form-label">Konfirmasi Password:</label>
                        <input type="password" name="confirm_password" id="_confirm_password" class="form-control">
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')

<script>
    
    $('#aktifForm').validate({
        rules: {
            password: {
                required: true,
                minlength: 6
            },
            confirm_password: {
                required: true,
                equalTo: "#_password",
                minlength: 6
            }
        }
    });
    $('#addForm').validate({
        rules: {
            name: {
                required: true
            },
            email: {
                required: true,
                email: true,
            },
            password: {
                required: true
            },
            confirm_password: {
                required: true,
                equalTo: "#password"
            }
        }
    });
    
    $('#editForm').validate({
        rules: {
            name: {
                required: true
            },
            email: {
                required: true,
                email: true,
            }
        }
    });
</script>
<script>
    $(document).ready(function(){
        var table = $('#dataTable').DataTable({
            "pageLength":5,
            processing:true,
            searching:true,
            order:[[0,'asc']],
            info:false,
            lengthMenu: [[2,3,4,5,-1],[2,3,4,5,"All"]],
            serverside:true,
            language: { 
                paginate: { previous: "<i class='fas fa-angle-left'>", next: "<i class='fas fa-angle-right'>"
                }
            },
            ajax:"{{route('ajax.get.user')}}",
            columns: [
            {data: 'rownum', name: 'rownum'},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'status', name: 'status'},
            {data: 'action', name: 'action'}
            ]
            
        });
        
        
        $('#editModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) 
            var id = button.data('id')
            var nama = button.data('nama')
            var email = button.data('email')
            var password = button.data('password')
            
            var modal = $(this)
            modal.find('.modal-body #_id').val(id)
            modal.find('.modal-body #_name').val(nama)
            modal.find('.modal-body #_email').val(email)
            modal.find('.modal-body #_password').val(password)
        });
        
        $('#hapusModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) 
            var id = button.data('id')
            
            var modal = $(this)
            modal.find('.modal-body #__id').val(id)
            
        });
        
        $('#aktifModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) 
            var id = button.data('id')
            
            var modal = $(this)
            modal.find('.modal-body #id').val(id)
            
        });
        
        
        
        
    });
</script>


@stop
