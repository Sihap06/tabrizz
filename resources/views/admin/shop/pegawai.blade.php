@extends('admin._layouts.master')

@section('content')

<section class="content">
	<div class="header bg-primary pb-6">
		<div class="container-fluid">
			<div class="header-body">
				<div class="row align-items-center py-4">
					<div class="col-lg-6 col-7">
						<h6 class="h2 text-white d-inline-block mb-0">Pegawai Toko</h6>
						<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
							<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
								<li class="breadcrumb-item"><a href="{{url('/')}}"><i class="fas fa-home"></i></a></li>
								<li class="breadcrumb-item"><a href="{{url('shop', $id_)}}">{{$shop}}</i></a></li>
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

			<form action="{{url('post-user-toko')}}" method="POST" enctype="multipart/form-data">
				@csrf

				<div class="modal-body">
					<input type="hidden" name="shop_id" id="shop_id" value="{{$id_}}">
					<div class="form-group">
						<label for="status" class="col-form-label">Nama Pegawai:</label>
						<select name="user_id" id="user_id" class="form-control" data-toggle="select">
							@foreach ($pegawai as $item)
							<option value="{{$item->id}}">{{$item->name}}</option>
							@endforeach
						</select>
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
				<h5 class="modal-title" id="modal-title">Hapus Pegawai</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form name="edit_product" action="{{url('edit_user_toko')}}" method="POST" enctype="multipart/form-data">
				@csrf
				{{-- {{method_field('PUT')}} --}}
				<div class="modal-body">
					<input type="hidden" name="id" id="id" value="">
					<p>
						Apakah anda yakin menonaktifkan pegawai ini ?
					</p>
					{{-- <input type="hidden" name="user_id" id="user_id" value=""> --}}
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary" id="save-btn">Save Change</button>
				</div>
			</form>
		</div>
	</div>
</div>

@endsection

@section('script')


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
			ordering:false,
			language: { 
				paginate: { previous: "<i class='fas fa-angle-left'>", next: "<i class='fas fa-angle-right'>"
				}
			},
			ajax:"{{url('getusertoko/'.$id_)}}",
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
			{data: 'email', name: 'email'},
			{data: 'action', name: 'action'},
			]

		});

		// console.log(data);



		$('#editModal').on('show.bs.modal', function (event) {
			var button = $(event.relatedTarget)
			var id = button.data('id')

			var modal = $(this)
			modal.find('.modal-body #id').val(id)
		});






	});
</script>


@stop
