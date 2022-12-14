<div class="row">
    
    
    <div class="col-lg-8 col-md-7 col-sm-12">
        <div class="card">
            <!-- Card header -->
            <div class="card-header border-0">
                <div class="row justify-content-between px-3">
                    <div>
                        <h3 class="mb-0">Pembelian</h3>
                    </div>
                    <div>
                        <button class="btn btn-primary" onclick="lihatProduk()">Lihat Produk</button>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <div class="form-group" wire:ignore>
                    <select class="form-control" id="search">
                    </select>
                </div>
                
                <div class="table-responsive" style="min-height: 200px; overflow-y: auto;display: block; margin-top: 150px">
                    <table class="table table-flush">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Produk</th>
                                <th>Harga</th>
                                <th>Quantity</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $total = 0;
                            @endphp
                            @foreach ($temp_order as $key => $value)
                            <tr>
                                <td class="align-middle">
                                    <strong class="name mb-0 text-sm">{{$key+1}}</strong>
                                </td>
                                <td class="align-middle">
                                    <strong class="name mb-0 text-sm">{{$value->product_name}} - {{$value->warna}}</strong>
                                </td>
                                <td class="align-middle">
                                    @php
                                    $now = Carbon\Carbon::now();
                                    if ($now >= $value->start_date && $now <= $value->end_date) {
                                        if ($value->diskon_type == 'persen') {
                                            $final_price = $value->price - ($value->price * $value->diskon / 100);
                                        } else {
                                            $final_price = $value->price - $value->diskon;
                                        }
                                    } else {
                                        $final_price = $value->price;
                                    }
                                    @endphp
                                    <strong class="name mb-0 text-sm">Rp{{number_format($final_price)}}</strong>
                                </td>
                                <td class="align-middle">
                                    <form role="form" wire:submit.prevent="updateQty">
                                        <input type="text" class="form-control" wire:model.lazy="qty.{{$value->id}}">
                                    </form>
                                </td>
                                <td class="align-middle">
                                    <div class="btn btn-sm btn-primary m-0" style="cursor: pointer" wire:click="remove('{{$value->id}}')">X</div>
                                </td>
                            </tr>
                            @php
                            $total += $final_price * $value->qty;
                            @endphp
                            @endforeach
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-5 col-sm-12">
        <div class="card">
            <!-- Card header -->
            <div class="card-header border-0">
                <h3 class="mb-0">Pembayaran</h3>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <h4>Total Belanja</h4>
                        <h4>Diskon</h4>
                    </div>
                    <div class="col-4">
                        <div class="row justify-content-between">
                            <p class="text-bold text-darker mb-0">Rp</p>
                            <p class="text-bold text-darker mb-0">{{number_format($total)}}</p>
                        </div>
                        <div class="row justify-content-between">
                            <p class="text-bold text-darker mb-0">Rp</p>
                            <p class="text-bold text-darker mb-0">{{number_format($diskonShow)}}</p>
                        </div>
                    </div>
                </div>
                <div class="mb-5 row border-top">
                    <div class="col-8">
                        <h4>Total</h4>
                    </div>
                    <div class="col-4">
                        <div class="row justify-content-between">
                            <p class="text-bold text-darker mb-0">Rp</p>
                            <p class="text-bold text-darker mb-0">{{number_format($total - $diskonShow)}}</p>
                        </div>
                    </div>
                </div>
                <div class="mb-5">
                    <div class="text-center" wire:loading wire:target="submit">
                        <img src="{{asset('assets/img/loading_inline.gif')}}" alt="loading">
                    </div>
                </div>
                <form role="form" wire:submit.prevent="processDiskon({{$total}})">
                    <div class="form-group">
                        <label for="diskon">Diskon</label>
                        <div class="input-group has-validation">
                            <input class="form-control @error('diskon') is-invalid @enderror" type="text" wire:model.defer="diskon" id="diskon" aria-describedby="inputGroupPrepend">
                            <div class="input-group-append">
                                <span class="input-group-text" id="basic-addon2">%</span>
                            </div>
                            @error('diskon') 
                            <div class="invalid-feedback">
                                {{$message}}
                            </div>
                            @enderror
                        </div>
                    </div>
                    
                </form>
                <form wire:submit.prevent="submit({{$code}}, {{$total}})">
                    <div class="form-group">
                        <label for="bayar">Bayar</label>
                        <div class="input-group has-validation">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend">Rp</span>
                            </div>
                            <input class="form-control @error('bayar') is-invalid @enderror" type="text" wire:model.defer="bayar" id="bayar" aria-describedby="inputGroupPrepend">
                            @error('bayar') 
                            <div class="invalid-feedback">
                                {{$message}}
                            </div>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-5">
                        <button class="btn btn-primary btn-block" type="submit">Bayar</button>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
</div>

@section('script')

<script>
    
    $(document).ready(function() {
        $('#search').select2({
            multiple: false,
            minimumInputLength: 1,
            placeholder: "Masukkan Kode Barang Atau Nama Barang",
            ajax: {
                url: function (params) {
                    return "{{url('ajax-get-product')}}" + '/' + params.term;
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                data: function (value) {
                    var length = value.term.length; 
                    if (length >= 5) {
                        pickThis(value.term);
                    }
                }
            },
        });
        
        $('#search').select2('open')
        
        function pickThis(value) {
            
            @this.set('search', value);
        }
        
        $('#search').on('change', function (e) {
            var value = this.value;
            
            pickThis(value);
        });
        
        
        window.addEventListener('search', event => {
            if (event.detail.value === null) {
                $('#search').empty();
                $('.select2-search__field').val('')
                $('#select2-search-results').empty()
                $('#search').select2('open');
            }
        })
        
    });
</script>  

<script>
    function lihatProduk() {
        $('#dataTable').DataTable({
            processing:true,
            searching:true,
            bDestroy: true,
            info:false,
            ordering:false,
            lengthMenu: [[10,30,50,100,-1],[10,30,50,100,"All"]],
            serverSide:true,
            language: { 
                paginate: { previous: "<i class='fas fa-angle-left'>", next: "<i class='fas fa-angle-right'>"
                }
            },
            ajax:{
                url:"{{route('ajax.get.produk.kasir')}}",
            },
            columns: [
            {data: 'product_name', name: 'products.product_name'},
            {data: 'final_price', name: 'products.final_price'},
            {data: 'temp_stock', name: 'product_shop.temp_stock'},
            ]
            
        });
        
        $('#lihatProdukModal').modal('show')
    }
</script>

<script src="{{asset('js/recta.js')}}"></script>
<script>
    var printer = new Recta('1128899913', '1811');
    {!! session()->get('script') !!}
</script>
@if (session()->has('script1'))
@foreach (session()->get('script1') as $item)
<script>
    {!! $item !!}
</script>
@endforeach
@endif
<script>
    {!! session()->get('script2') !!}
</script>


@if (session()->has('kembalian'))
<script>
    var text = "{{session()->get('kembalian')}}";
    Swal.fire({
        'title': 'Pembayaran berhasil',
        'text': `Kembalian : Rp ${text}`,
        'timer': 10000,
        'icon': 'success',
        'showConfirmButton': false,
        'position': 'center',
        'timerProgressBar': true,
    });
</script>
@endif

@if (session()->has('error'))
<script>
    var text = "{{session()->get('error')}}";
    Swal.fire({
        'title': `${text}`,
        'timer': 3000,
        'icon': 'error',
        'showConfirmButton': false,
        'position': 'center',
        'timerProgressBar': true,
    });
</script>
@endif
@endsection



