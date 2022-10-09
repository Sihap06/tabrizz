<table>
    <thead>
        <tr>
            <th></th>
            <th><strong>Laporan</strong></th>
        </tr>
        <tr>
            <th></th>
            <th>{{date('d M Y', strtotime($tgl_awal))}} - {{date('d M Y', strtotime($tgl_akhir))}}</th>
        </tr>
        <tr>
            <th>No.</th>
            <th>Toko</th>
            <th>Pegawai</th>
            <th>Nama Produk</th>
            <th>Jumlah</th>
            <th>Total</th>
            <th>Tanggal</th>
        </tr>
    </thead>
    <tbody>
        @php
            $total = 0;
        @endphp
        @foreach ($data as $index => $item)
        <tr>
            <td>{{ $index+1}}</td>
            <td>{{$item->name}}</td>
            <td>{{$item->nama_pegawai}}</td>
            <td>{{ $item->product_name .' - '. $item->warna }}</td>
            <td>{{ $item->qty }}</td>
            <td>{{ number_format($item->total) }}</td>
            <td>{{ $item->tanggal }}</td>
        </tr>
        @php
            $total += $item->total;
        @endphp
        @endforeach
        <tr></tr>
        <tr></tr>
        <tr>
            <td></td>
            <td><strong>Total</strong></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{number_format($total)}}</td>
        </tr>
    </tbody>
</table>