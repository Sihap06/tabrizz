<table>
    <thead>
        <tr>
            <th >No.</th>
            <th >Nama Produk</th>
            <th >Stock</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $index => $item)
        <tr>
            <td >{{ $index+1}}</td>
            <td>{{ $item->product_name .' - '. $item->warna }}</td>
            <td >{{ $item->stock }}</td>
        </tr>
        @endforeach
    </tbody>
</table>