@extends('layouts.master')

@section('content')

{{-- @dd($id_) --}}

<div class="container-fluid mt-8 pb-5">
	<div class="row justify-content-center mt-3">
		<div class="col-md-10">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">Rekap Penjualan</h3>
				</div>
				
				<!-- /.card-header -->
				<div class="card-body">
					<div class="mb-3">
						<div class="row input-daterange datepicker align-items-center">
							<div class="col-md-3 mb-3">
								<input type="text" name="from_date" id="from_date" class="form-control " placeholder="Dari tanggal" autocomplete="off" />
							</div>
							<div class="col-md-3 mb-3">
								<input type="text" name="to_date" id="to_date" class="form-control " placeholder="Sampai tanggal" autocomplete="off" />
							</div>
							<div class="col-md-3 mb-3">
								<button type="filter" name="filter" id="filter" class="btn btn-primary">Filter</button>
								<button type="button" name="refresh" id="refresh" class="btn btn-default">Refresh</button>
							</div>
							<div class="ml-auto mr-2 mb-3">
								<button type="button" data-target="#printModal" data-toggle="modal" class="btn btn-info">Cetak Laporan</button>
							</div>
						</div>
					</div>
					<div class="table-responsive py-4">
						<table class="table table-flush" id="dataTable">
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
							<tfoot>
								<tr>
									<th colspan="4" style="text-align:right">Total:</th>
									<th></th>
									<th></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
				<!-- /.card-body -->
			</div>
			<!-- /.card -->
			<!-- /.card -->
		</div>
		<!-- /.col -->
	</div>
	<!-- /.row -->
</div>

<!-- Modal -->
<div class="modal fade" id="printModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modal-title">Pilih tanggal rekap</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form name="edit_product" action="{{url('rekapPegawaiExcel')}}" method="POST" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="user_id" id="user_id" value="{{$id}}" />
				<div class="modal-body row input-daterange datepicker">
					<div class="col-md-6 mb-3">
						<input type="text" name="from_date" id="from_date" class="form-control" placeholder="Dari tanggal" autocomplete="off" />
					</div>
					<div class="col-md-6 mb-3">
						<input type="text" name="to_date" id="to_date" class="form-control" placeholder="Sampai tanggal" autocomplete="off" />
					</div>
					
					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
					<button type="submit" class="btn btn-primary" id="save-btn">Print</button>
				</div>
			</form>
		</div>
	</div>
</div>


@endsection

@section('script')

<script>
	
	$(document).ready(function(){
		
		load_data();
		function load_data(from_date = '', to_date = '')
		{
			$('#dataTable').dataTable({
				footerCallback: function (row, data, start, end, display) {
					var api = this.api();
					
					// Remove the formatting to get integer data for summation
					var intVal = function (i) {
						return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
					};
					
					// TotalStok over this page
					pageTotalStok = api
					.column(4, { page: 'current' })
					.data()
					.reduce(function (a, b) {
						return intVal(a) + intVal(b);
					}, 0);
					
					pageTotalHarga = api
					.column(5, { page: 'current' })
					.data()
					.reduce(function (a, b) {
						return intVal(a) + intVal(b);
					}, 0);
					
					// Update footer
					$(api.column(4).footer()).html(pageTotalStok);
					$(api.column(5).footer()).html('Rp'+ pageTotalHarga.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
				},
				processing:true,
				searching:true,
				info:false,
				lengthMenu: [[10,50,100,-1],[10,50,100,"All"]],
				serverside:true,
				ordering:false,
				language: {
					'paginate': {
						'previous': '<span class="fas fa-angle-left"></span>',
						'next': '<span class="fas fa-angle-right"></span>'
					}
				},
				ajax: {
					url:"{{url('getrekapuser/'.$id)}}",
					data:{from_date:from_date, to_date:to_date}
				},
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
				{data: 'nama_pegawai', name: 'nama_pegawai'},
				{data: 'product_name', name: 'product_name'},
				{data: 'qty', name: 'qty'},
				{data: 'total', name: 'total'},
				{data: 'tanggal', name: 'tanggal'},
				]
				
			});
		}
		
		
		$('#filter').click(function(){
			let from_date = $('#from_date').val();
			let to_date = $('#to_date').val();
			if(from_date != '' &&  to_date != '')
			{
				$('#dataTable').DataTable().destroy();
				load_data(from_date, to_date);
			}
			else
			{
				alert('Tanggal wajib diisi');
			}
		});
		
		$('#refresh').click(function(){
			$('#from_date').val('');
			$('#to_date').val('');
			$('#dataTable').DataTable().clear().draw();
		});
		// console.log(data);
		
		
	});
</script>

@stop
