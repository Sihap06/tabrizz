<?php

namespace App\Http\Livewire;

use App\Code;
use App\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithPagination;
use RealRashid\SweetAlert\Facades\Alert;

class ProdukKasir extends Component
{
    use WithPagination;

    public $search;
    public $qty = [];
    public $bayar =  0;
    public $diskon = 0;
    public $diskonShow = 0;


    public function render()
    {
        $user_id = Auth::user()->id;
        $code = Code::where('user_id', $user_id)->value('code');
        $shopUser = DB::table('shop_user')->where('user_id', $user_id)->first()->shop_id;


        if ($this->search != null) {
            $data = DB::table('products')
                ->select('product_shop.temp_stock', 'products.product_name', 'products.warna', 'products.final_price', 'products.id', 'products.kode', 'products.price', 'products.diskon', 'products.diskon_type', 'products.start_date', 'products.end_date')
                ->join('product_shop', 'products.id', '=', 'product_shop.product_id')
                ->where('product_shop.shop_id', $shopUser)
                ->where(function ($query) {
                    $query->where('products.kode', $this->search)
                        ->orWhere('products.product_name', 'like', '%' . $this->search . '%');
                })
                ->orderBy('product_shop.temp_stock', 'desc')
                ->take(10)
                ->get();


            $cekProduct = Product::where('kode', $this->search)->first();
            if ($cekProduct != null) {
                $this->selectThis($cekProduct->id, $shopUser, $code);
                $this->search = null;
                $this->dispatchBrowserEvent('search', ['value' => null]);
            }
        } else {
            $data = '';
        }

        $temp_order = DB::table('orders')
            ->select('products.product_name', 'orders.qty', 'products.price', 'products.warna', 'orders.id', 'products.final_price', 'products.price', 'products.diskon', 'products.diskon_type', 'products.start_date', 'products.end_date')
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->where('orders.user_id', $user_id)
            ->where('orders.code', $code)
            ->orderBy('orders.created_at', 'desc')
            ->get();

        foreach ($temp_order as $value) {

            $this->qty[$value->id] = $value->qty;
        }

        return view('livewire.produk-kasir', ['data' => $data, 'search' => $this->search, 'id_shop' => $shopUser, 'code' => $code, 'temp_order' => $temp_order, 'diskon' => $this->diskonShow]);
    }

    public function selectThis($id_product, $id_shop, $code)
    {
        $user_id = Auth::user()->id;
        $x = DB::table('product_shop')->where(['shop_id' => $id_shop, 'product_id' => $id_product])->value('temp_stock');

        $cek = DB::table('orders')->where('product_id', $id_product)->where('code', $code)->where('user_id', $user_id)->first();


        if ($x > 0) {
            if ($cek != null) {
                if ($x > $cek->qty) {
                    $qtyNow = $cek->qty;
                    DB::table('orders')->where('product_id', $id_product)->where('code', $code)->where('user_id', $user_id)->update([
                        'qty' => $qtyNow + 1
                    ]);
                }
            } else {
                DB::table('orders')->insert([
                    'user_id' => $user_id,
                    'code' => $code,
                    'product_id' => $id_product,
                    'qty' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }


    public function remove($id)
    {
        DB::table('orders')->where('id', $id)->delete();
    }

    public function updateQty()
    {
        foreach ($this->qty as $key => $value) {
            DB::table('orders')->where('id', $key)->update([
                'qty' => $this->qty[$key]
            ]);
        }
    }

    private function getTotalPrice($id)
    {
        $product = Product::findOrFail($id);
        $now = Carbon::now();
        if ($now >= $product->start_date && $now <= $product->end_date) {
            if ($product->diskon_type == 'persen') {
                $final_price = $product->price - ($product->price * $product->diskon / 100);
            } else {
                $final_price = $product->price - $product->diskon;
            }
        } else {
            $final_price = $product->price;
        }

        return $final_price;
    }

    public function submit($code, $total)
    {
        $this->validate(
            [
                'bayar' => 'required|numeric'
            ],
            [
                'bayar.required' => 'Nominal pembayaran wajib diisi !',
                'bayar.numeric' => 'Nominal pembayaran wajib berupa angka !'
            ]
        );

        if ($this->bayar >= ($total - $this->diskonShow)) {
            $user = Auth::user();
            $shop_id = DB::table('shop_user')->where('user_id', $user->id)->value('shop_id');
            $data = DB::table('orders')->where('code', $code)->get();
            $dateNow = date('Y-m-d', strtotime(Carbon::now()));
            $jamNow = date('H:i:s', strtotime(Carbon::now()));

            foreach ($data as $dt) {
                DB::table('save_orders')->insert([
                    'user_id' => $dt->user_id,
                    'product_id' => $dt->product_id,
                    'shop_id' => $shop_id,
                    'qty' => $dt->qty,
                    'diskon' => $this->diskonShow,
                    'total' => $this->getTotalPrice($dt->product_id) * $dt->qty,
                    'tanggal' => $dateNow,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
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

            $kembalian = floatval($this->bayar) - floatval($total);

            $name = Auth::user()->name;

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
            $bayarPrint = number_format($this->bayar);
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
                .feed(1)
                .text('Jam $jamNow')
                .cut()
                .print()
            });   ";

            session()->flash('script', $script);
            session()->flash('script1', $script1);
            session()->flash('script2', $script2);
            session()->flash('kembalian', $kembalian);


            $this->bayar = 0;

            return redirect()->to('/');
        } else {
            session()->flash('error', 'Pembayaran Gagal');

            return redirect()->to('/');
        }
    }

    public function processDiskon($total)
    {
        $tempDiskon = $total * $this->diskon / 100;
        $this->diskonShow = $tempDiskon;

        // dd($this->diskonShow);
    }
}
