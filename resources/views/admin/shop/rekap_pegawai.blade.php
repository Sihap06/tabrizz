@extends('layouts.master')

@section('content')

{{-- @dd($id_) --}}

<section class="content">
	<div class="row justify-content-center mt-3">
		<div class="col-md-10">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">Rekap Penjualan</h3>
				</div>

				<!-- /.card-header -->
				<div class="card-body">
					<div class="mb-3">
						{{-- <button class="btn btn-primary" data-toggle="modal" data-target="#createModal" id="create">Rekap Penjualan</button> --}}
					</div>
					<table id="dataTable" class="table table-bordered table-hover">
						<thead>
							<tr>
								<th>No</th>
								<th>Toko</th>
								<th>Pegawai</th>
								<th>Produk</th>
								<th>Qty</th>
								<th>Total</th>
								<th>Tanggal</th>
							</tr>
						</thead>

					</table>
				</div>
				<!-- /.card-body -->
			</div>
			<!-- /.card -->
			<!-- /.card -->
		</div>
		<!-- /.col -->
	</div>
	<!-- /.row -->
</section>


@endsection

@section('footer')

<script>
	$(document).ready(function(){

		$('#dataTable').DataTable({
			"pageLength":5,
			processing:true,
			searching:true,
			info:false,
			lengthMenu: [[2,3,4,5,-1],[2,3,4,5,"All"]],
			serverside:true,
			ordering:false,
			ajax:
			{
				url:"{{url('getrekappegawai/'.$id)}}",
			},
			columns: [
			{data: 'rownum', name: 'rownum'},
			{data: 'shop_id', name: 'shop_id'},
			{data: 'user_id', name: 'user_id'},
			{data: 'product_id', name: 'product_id'},
			{data: 'qty', name: 'qty'},
			{data: 'total', name: 'total'},
			{data: 'tanggal', name: 'tanggal'},
			]

		});
		// console.log(data);


	});
</script>

@stop
