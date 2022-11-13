<?php

namespace App\Http\Controllers;

use App\Order;
use App\Product;
use Illuminate\Http\Request;
use Webpatser\Uuid\Uuid;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $produk = \App\Product::inRandomOrder()->take(1)->get();

        $code = DB::table('code')->where('id', 1)->value('code');
        if ($code == '0') {
            $code = Uuid::generate(4);
        }

        return view('kasir.index', compact('code'));
    }

    public function filter(Request $request)
    {
        $bulan = $request->bulan;

        $orders = Order::whereMonth('tanggal', $bulan)->get()->all();
        // $orders = $order->paginate(10);
        // dd($orders);

        return view('pimpinan.index', compact('orders'));
    }


    public function getproduk(Request $request)
    {
        $user_id = Auth::user()->id;
        $shop_id = DB::table('shop_user')->where('user_id', $user_id)->value('shop_id');
        // dd($shop_id);
        // DB::statement(DB::raw('set @rownum=0'));
        // $barang = DB::table('product_shop')->where('shop_id', $shop_id)->select([
        //     DB::raw('@rownum  := @rownum  + 1 AS rownum'),
        //     'id',
        //     'product_id',
        //     'temp_stock',
        // ]);

        $barang = DB::table('products')
            ->join('product_shop', 'products.id', '=', 'product_shop.product_id')
            ->where('products.deleted_at', NULL)
            ->where('product_shop.shop_id', $shop_id)
            ->select([
                'products.product_name',
                'products.warna',
                'product_shop.temp_stock',
                'products.final_price',
            ]);


        $datatables = Datatables::of($barang)
            ->editColumn('product_name', function ($row) {
                return $row->product_name . ' - ' . $row->warna;
            })
            ->editColumn('final_price', function ($row) {
                return number_format($row->final_price);
            })
            ->rawColumns(['product_id', 'final_price'])
            ->addIndexColumn();


        return $datatables->make(true);
    }

    public function get($id)
    {
        $barang = DB::table('products')->where('id', $id)->first();

        return response()->json([
            'id' => $barang->id,
            'product_name' => $barang->product_name,
            'price' => $barang->final_price
        ]);
    }

    public function submit(Request $request, $code)
    {
        // dd($request->all());
        Validator::make($request->all(), [
            "qty"      => "required|regex:/^[1-9][0-9]*$/",

        ])->validate();
        $id = $request->id;
        $qty = $request->qty;
        $user_id = Auth::user()->id;

        $shop_id = DB::table('shop_user')->where('user_id', Auth::user()->id)->value('shop_id');
        $x = DB::table('product_shop')->where(['shop_id' => $shop_id, 'product_id' => $id])->value('temp_stock');
        // dd($x);

        $cek = count(DB::table('orders')->where(['product_id' => $id, 'code' => $code, 'user_id' => $user_id])->get());
        $product = DB::table('product_shop')->where('product_id', $id)->get()->all();
        // dd($product);
        if ($qty <= $x) {
            if ($cek > 0) {
                $qtyNow = DB::table('orders')->where(['product_id' => $id, 'code' => $code, 'user_id' => $user_id])->value('qty');
                DB::table('orders')->where(['product_id' => $id, 'code' => $code, 'user_id' => $user_id])->update([
                    'qty' => $qtyNow + $qty
                ]);
            } else {
                DB::table('orders')->insert([
                    'user_id' => $user_id,
                    'code' => $code,
                    'product_id' => $id,
                    'qty' => $qty
                ]);
            }

            return redirect()->route('index');
        } else {
            return back()->with('danger', 'qty lebih dari stock');
        }
    }

    public function hapusItem($id, $code)
    {
        DB::table('orders')->where('id', $id)->where('code', $code)->delete();
        return redirect()->route('index');
    }

    public function selesai(Request $request, $code, $total)
    {
        $user_id = Auth::user()->id;
        $shop_id = DB::table('shop_user')->where('user_id', $user_id)->value('shop_id');
        $bayar = $request->bayar;
        $data = DB::table('orders')->where('code', $code)->get();
        $name = Auth::user()->name;
        $dateNow = date('d-m-Y H:i:s', strtotime(Carbon::now()));
        dd($dateNow);


        foreach ($data as $key => $dt) {
            DB::table('save_orders')->insert([
                'user_id' => $dt->user_id,
                'product_id' => $dt->product_id,
                'shop_id' => $shop_id,
                'qty' => $dt->qty,
                'total' => DB::table('products')->where('id', $dt->product_id)->value('final_price') * $dt->qty,
                'tanggal' => date("Y-m-d H:i:s")
            ]);

            $stock = DB::table('products')->where('id', $dt->product_id)->value('stock');
            $temp_stock = DB::table('product_shop')->where(['product_id' => $dt->product_id, 'shop_id' => $shop_id])->value('temp_stock');

            DB::table('products')->where('id', $dt->product_id)->update([
                'stock' => $stock - $dt->qty,
            ]);

            DB::table('product_shop')->where(['product_id' => $dt->product_id, 'shop_id' => $shop_id])->update([
                'temp_stock' => $temp_stock - $dt->qty,
                'updated_at' => Carbon::now()
            ]);
        }

        DB::table('orders')->where('code', $code)->delete();

        $kembalian = floatval($bayar) - floatval($total);

        Session::flash('pesan', 'Kembalian: Rp. ' . number_format($kembalian, 0));

        $script = "printer.open().then(function () {
            printer.align('center')
            .bold(false)
            .text('Tabriiz Cosmetic And Skin Care')
            .feed(1)
            .text('Jl Kalimantan no 77 Sumbersari')
            .feed(1)
            .text('Jember')
            .feed(1)
            .text('Nomor Telp.  082147948858')
            .feed(1)
            .text('================================')
            .feed(1)
            .align('left')
            .text('No. $code')
            .feed(1)
            .text('Kasir = $name')
            .feed(1)
            .text('Jam = ')
            .feed(1)
            .text('================================')
            .feed(1)
        });  ";

        $script1 = [];
        foreach ($data as $value) {
            $name = Product::findOrFail($value->product_id)->product_name;
            $price = Product::findOrFail($value->product_id)->final_price;
            $warna = Product::findOrFail($value->product_id)->warna;
            $priceX = number_format($price);
            $subtotal = $value->qty * $price;
            $subtotalX = number_format($subtotal);

            $countpriceX = strlen($priceX);
            $countsubtotalX = strlen($subtotalX);
            $space = 33 - ($countpriceX + $countsubtotalX + 4);

            $script1[] = "
                printer.open().then(function () {
                    printer
                    .align('left')
                    .text('$name - $warna')
                    .feed(1)
                    .text('$value->qty x $priceX')
                    .space($space)
                    .text('$subtotalX')
                })
                ";
        }

        $totalPrint = number_format($total);
        $bayarPrint = number_format($bayar);
        $kembalianPrint = number_format($kembalian);

        $countTotalPrint = strlen($totalPrint);
        $countBayarPrint = strlen($bayarPrint);
        $countKembalianPrint = strlen($kembalianPrint);

        $spaceTotal = 33 - (5 + $countTotalPrint);
        $spaceBayar = 33 - (5 + $countBayarPrint);
        $spaceKembalian = 33 - (9 + $countKembalianPrint);

        $script2 = "printer.open().then(function () {
                printer
                .feed(1)
                .text('================================')
                .feed(1)
                .align('left')
                .text('Total')
                .space($spaceTotal)
                .text('$totalPrint')
                .feed(1)
                .text('Bayar')
                .space($spaceBayar)
                .text('$bayarPrint')
                .feed(1)
                .text('Kembalian')
                .space($spaceKembalian)
                .text('$kembalianPrint')
                .feed(1)
                .align('center')
                .feed(1)
                .text('Barang yang sudah dibeli')
                .feed(1)
                .text('TIDAK DAPAT DIKEMBALIKAN')
                .feed(1)
                .text('Terimakasih Atas Kunjungannya')
                .feed(1)
                .text('Tanggal $dateNow')
                .cut()
                .print()
            });   ";

        Session::flash('script', $script);
        Session::flash('script1', $script1);
        Session::flash('script2', $script2);
        // dd(Session::get('script'));

        return back();
    }

    public function cetak(Request $request, $code, $total)
    {
        $bayar = $request->bayar;
        $pdf = PDF::loadview('order.index_pdf', compact(['code', 'bayar']));
        return $pdf->download('struck.pdf');
    }

    public function pdf()
    {
        $code = DB::table('code')->where('id', 1)->value('code');
        if ($code == '0') {
            $code = Uuid::generate(4);
        }

        return view('order.index_pdf', compact('code'));
    }
}
