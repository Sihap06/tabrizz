<script>
    $('#tambahForm').validate({
        rules: {
            category: {
                required: true
            },
            kode: {
                required: true
            },
            nama: {
                required: true
            },
            warna: {
                required: true
            },
            harga: {
                required: true,
                digits: true
            },
            harga_beli: {
                required: true,
                digits: true,
            },
            stock: {
                required: true,
                digits: true
            }
        }
    });
</script>

<script>
    $('#editForm').validate({
        rules: {
            category: {
                required: true
            },
            kode: {
                required: true
            },
            nama: {
                required: true
            },
            warna: {
                required: true
            },
            harga: {
                required: true,
                digits: true
            },
            harga_beli: {
                required: true,
                digits: true,
            },
            stock: {
                required: true,
                digits: true
            },
            diskon: {
                required: true,
                digits: true
            }
            
        }
    });
</script>

<script>
    $(document).ready(function(){
        load_data();
        function load_data(kategori = ''){
            $('#dataTable').DataTable({
                "pageLength":10,
                processing:true,
                searching:true,
                order:[[0,'asc']],
                info:false,
                lengthMenu: [[5,10,15,20,-1],[5,10,15,20,"All"]],
                serverSide:true,
                ordering:true,
                language: { 
                    paginate: { previous: "<i class='fas fa-angle-left'>", next: "<i class='fas fa-angle-right'>"
                    }
                },
                ajax:{
                    url:"{{route('ajax.get.produk')}}",
                    data:{kategori:kategori}
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
                {data: 'product_name', name: 'product_name'},
                {data: 'harga_beli', name: 'harga_beli'},
                {data: 'price', name: 'price'},
                {data: 'diskon', name: 'diskon'},
                {data: 'final_price', name: 'final_price'},
                {data: 'stock', name: 'stock'},
                {data: 'temp_stock', name: 'temp_stock'},
                {data: 'action', name: 'action'}
                ]
                
            });
        }
        
        $('#filter').click(function(){
            var kategori = $('#kategori').val();
            if(kategori != '')
            {
                $('#dataTable').DataTable().destroy();
                load_data(kategori);
            }
            else
            {
                alert('Tanggal wajib diisi');
            }
        });
        $('#refresh').click(function(){
            $('#kategori').val('');
            $('#dataTable').DataTable().destroy();
            load_data();
        });
        
        
        $('#editModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var nama = button.data('nama')
            var harga = button.data('harga')
            var harga_beli = button.data('harga_beli')
            var diskon = button.data('diskon')
            var stock = button.data('stock')
            var kode = button.data('kode')
            var warna = button.data('warna')
            
            var modal = $(this)
            modal.find('.modal-body #id').val(id)
            modal.find('.modal-body #nama').val(nama)
            modal.find('.modal-body #warna').val(warna)
            modal.find('.modal-body #harga').val(harga)
            modal.find('.modal-body #harga_beli').val(harga_beli)
            modal.find('.modal-body #diskon').val(diskon)
            modal.find('.modal-body #stock').val(stock)
            modal.find('.modal-body #kode').val(kode)
        });
        
        $('#hapusModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            
            var modal = $(this)
            modal.find('.modal-body #id').val(id)
            
        });
    });
</script>