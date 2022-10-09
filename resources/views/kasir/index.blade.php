@extends('layouts.master')

@section('content')
<div class="container-fluid mt-8 pb-5">
    @livewire('produk-kasir')
</div>
@endsection

@section('modal')

<!-- Modal -->
<div class="modal fade" id="lihatProdukModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">List Data Produk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive py-4">
                    <table class="table table-flush" id="dataTable">
                        <thead class="thead-light">
                            <tr>
                                <th>Nama Produk</th>
                                <th>Harga</th>
                                <th>Stok</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
