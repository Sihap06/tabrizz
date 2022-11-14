@extends('admin._layouts.master')

@section('content')

<section class="content">
    <div class="header bg-primary pb-6">
        <div class="container-fluid">
            <div class="header-body">
                <div class="row align-items-center py-4">
                    <div class="col-lg-6 col-7">
                        <h6 class="h2 text-white d-inline-block mb-0">Produk Saya</h6>
                        <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                                <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="fas fa-home"></i></a></li>
                                <li class="breadcrumb-item"><a href="#">Produk Saya</a></li>
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
                                <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#createModal" id="create">Tambah Produk</button>
                                <a href="{{url('rekapStock')}}" class="btn btn-sm btn-primary">Rekap Stok</a>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive py-4">
                        <div class="row px-4">
                            <div class="col-md-4 mb-4">
                                <select name="kategori" id="kategori" class="form-control" data-toggle="select">
                                    <option selected disabled>Pilih Kategori</option>
                                    @php
                                    $category = \DB::table('categories')->get();
                                    @endphp
                                    @foreach ($category as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-4">
                                <button type="filter" name="filter" id="filter" class="btn btn-primary">Filter</button>
                                <button type="button" name="refresh" id="refresh" class="btn btn-default">Refresh</button>
                            </div>
                        </div>
                        <table class="table table-flush" id="dataTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Harga Beli</th>
                                    <th>Harga Jual</th>
                                    <th>Diskon</th>
                                    <th>Harga Total</th>
                                    <th>Stok</th>
                                    <th>Stok Tersedia</th>
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

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Tambah Produk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="tambahForm" name="tambahForm" action="{{route('product.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="modal-body row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="status" class="col-form-label">Kategori:</label>
                            <div class="input-group mb-3">
                                <select class="form-control @error('category') is-invalid @enderror" name="category" id="category" data-toggle="select">
                                    {{-- <option selected>Choose...</option> --}}
                                    @foreach ($category as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                                @error('category') <div class="invalid-feedback">{{$message}}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="status" class="col-form-label">Kode Produk:</label>
                            <input type="text" name="kode" id="kode" class="form-control @error('kode')
                            is-invalid @enderror">
                            @error('kode') <div class="invalid-feedback">{{$message}}</div> @enderror
                        </div>
                    </div>
                    
                    <div class="col-6">
                        <div class="form-group">
                            <label for="status" class="col-form-label">Nama Produk:</label>
                            <input type="text" name="nama" id="nama" class="form-control">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="status" class="col-form-label">Warna Produk:</label>
                            <input type="text" name="warna" id="warna" class="form-control">
                        </div>
                    </div>
                    
                    <div class="col-6">
                        <div class="form-group">
                            <label for="status" class="col-form-label">Harga Beli:</label>
                            <input type="number" name="harga_beli" id="harga_beli" class="form-control">
                        </div>
                    </div>
                    
                    <div class="col-6">
                        <div class="form-group">
                            <label for="status" class="col-form-label">Harga Jual:</label>
                            <input type="number" name="harga" id="harga" class="form-control">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="status" class="col-form-label">Stock Produk:</label>
                            <input type="number" name="stock" id="stock" class="form-control">
                        </div>
                    </div>
                    
                    
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Keluar</button>
                    <button type="submit" class="btn btn-primary" id="save-btn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('admin.product.modal')
@endsection

@section('script')
@include('admin.product.script')
@stop
