
<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal-title">Edit Produk</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="editForm" action="{{route('product.update', 'id')}}" method="POST" enctype="multipart/form-data">
        @csrf
        {{method_field('PUT')}}
        <div class="modal-body">
          <input type="hidden" name="id" id="id" value="">
          <div class="row">
            <div class="form-group col-6">
              <label for="status" class="col-form-label">Nama Produk:</label>
              <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror">
              
              @error('nama') <div class="invalid-feedback">{{$message}}</div> @enderror
            </div>
            <div class="form-group col-6">
              <label for="status" class="col-form-label">Warna Produk:</label>
              <input type="text" name="warna" id="warna" class="form-control">
            </div>
            <div class="form-group col-6">
              <label for="status" class="col-form-label">Kode Produk:</label>
              <input type="text" name="kode" id="kode" class="form-control @error('harga') is-invalid @enderror">
              @error('harga') <div class="invalid-feedback">{{$message}}</div> @enderror
            </div>
            <div class="form-group col-6">
              <label for="status" class="col-form-label">Harga Beli:</label>
              <input type="number" name="harga_beli" id="harga_beli" class="form-control @error('harga') is-invalid @enderror">
              @error('harga') <div class="invalid-feedback">{{$message}}</div> @enderror
            </div>
            <div class="form-group col-6">
              <label for="status" class="col-form-label">Harga Jual:</label>
              <input type="number" name="harga" id="harga" class="form-control @error('harga') is-invalid @enderror">
              @error('harga') <div class="invalid-feedback">{{$message}}</div> @enderror
            </div>
            {{-- <div class="form-group col-6">
              <label for="status" class="col-form-label">Diskon (dalam %):</label>
              <input type="number" name="diskon" id="diskon" class="form-control @error('diskon') is-invalid @enderror">
              @error('diskon') <div class="invalid-feedback">{{$message}}</div> @enderror
            </div> --}}
            <div class="form-group col-6">
              <label for="status" class="col-form-label">Stock Produk:</label>
              <input id="stock" disabled class="form-control">
            </div>
            <div class="col-6">
              <div class="form-check">
                <div class="row">
                  <div class="col-md-6">
                    <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="tambah" checked>
                    <label class="form-check-label" for="exampleRadios1">
                      Tambah Stock
                    </label>
                  </div>
                  <div class="col-md-6">
                    <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="kurang">
                    <label class="form-check-label" for="exampleRadios2">
                      Tarik Stock
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="status" class="col-form-label">Stock Produk:</label>
                <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror">
                @error('stock') <div class="invalid-feedback">{{$message}}</div> @enderror
              </div>
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

<!-- Edit Modal -->
<div class="modal fade" id="hapusModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal-title">Hapus Produk</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{route('product.destroy', 'id')}}" method="POST" enctype="multipart/form-data">
        @csrf
        {{method_field('DELETE')}}
        <div class="modal-body">
          <input type="hidden" name="id" id="id" value="">
          Apakah anda yakin akan menghapus produk ini ?
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Keluar</button>
          <button type="submit" class="btn btn-primary" id="save-btn">Hapus</button>
        </div>
      </form>
    </div>
  </div>
</div>