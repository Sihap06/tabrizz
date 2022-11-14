<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use App\Toko;
use Illuminate\Support\Facades\DB;
use App\Product;
use App\User;
use App\Order;
use App\Exports\RekapStockExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\ProductShop;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;
use RealRashid\SweetAlert\Facades\Alert;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $active = 'toko';
        return view('admin.shop.index', compact('active'));
    }
    public function getshop(Request $request)
    {
        $toko = DB::table('shops')->select([
            'id',
            'name',
            'city',
            'region',
            'street_address',
        ]);

        $datatables = Datatables::of($toko)
            ->editColumn('name', function ($toko) {

                $id = $toko->id;
                return '<span id="' . $id . '" style="cursor:pointer;" class="btn-toko">' . $toko->name;
            })
            ->addColumn('action', function ($row) {
                $btn = '<button id="edit-user" data-toggle="modal" data-target="#editModal" data-city="' . $row->city . '" data-name="' . $row->name . '" data-id="' . $row->id . '" data-region="' . $row->region . '" data-street_address="' . $row->street_address . '" class="delete btn btn-info btn-sm">
                <i class="fas fa-edit"></i>

                <a href="shop/' . $row->id . '">
                </button>
            <button class="delete btn btn-primary btn-sm">
            <i class="fas fa-info-circle"></i>
            </button>
            </a>';
                return $btn;
                // dd($row);
            })
            ->rawColumns(['name', 'action'])
            ->addIndexColumn();

        return $datatables->make(true);
    }

    public function show($id)
    {
        $active = 'toko';
        $shop = Toko::findOrFail($id);
        $shop_id = $shop->id;
        $temp_user = DB::table('shop_user')->where('shop_id', $shop_id)->count();
        $temp_product = DB::table('product_shop')->where('shop_id', $shop_id)->count();
        $rekap = Order::where('shop_id', $shop_id)->count();
        // dd($temp_product);
        return view('admin.shop.singleshop', compact(['shop_id', 'temp_product', 'temp_user', 'rekap', 'shop', 'active']));
    }
    public function getproductcat($id)
    {
        $product = Category::with('products')->where('id', $id)->get()->all();
        // dd();
        return json_encode([$product[0]->products->pluck('product_name', 'id'), $product[0]->products->pluck('warna')]);
    }

    public function rekapPegawai($id)
    {
        return view('admin.shop.rekap_pegawai', compact('id'));
    }

    public function getrekappegawai(Request $request, $id)
    {
        DB::statement(DB::raw('set @rownum=0'));
        $rekap = DB::table('save_orders')->where('shop_id', $id)->select([
            DB::raw('@rownum  := @rownum  + 1 AS rownum'),
            'id',
            'shop_id',
            'product_id',
            'user_id',
            'qty',
            'total',
            'tanggal',
        ])->orderBy('created_at', 'asc');


        $datatables = Datatables::of($rekap)
            ->editColumn('shop_id', function ($row) {
                return DB::table('shops')->where('id', $row->shop_id)->value('name');
            })
            ->editColumn('product_id', function ($row) {
                $product = DB::table('products')->where('id', $row->product_id)->value('product_name');
                $warna = DB::table('products')->where('id', $row->product_id)->value('warna');
                return $product . ' - ' . $warna;
            })
            ->editColumn('user_id', function ($row) {
                return DB::table('users')->where('id', $row->user_id)->value('name');
            })
            ->editColumn('total', function ($row) {
                return number_format($row->total);
            })
            ->rawColumns(['shop_id', 'product_id', 'total']);
        // ->addIndexColumn();

        return $datatables->make(true);
    }

    public function rekaptoko($id)
    {
        $active = 'toko';
        $shop = Toko::findOrFail($id)->name;
        return view('admin.shop.rekap', compact('id', 'shop', 'active'));
    }

    public function getrekap(Request $request, $id)
    {

        if (!empty($request->from_date)) {
            $tgl_awal = date('Y-m-d', strtotime($request->from_date));
            $tgl_akhir = date('Y-m-d', strtotime($request->to_date));

            $rekap = DB::table('save_orders as so')
                ->select('so.id', 'so.qty', 'so.total', 'so.tanggal', 'sh.name', 'pd.product_name', 'pd.warna', 'us.name as nama_pegawai')
                ->join('shops as sh', 'so.shop_id', '=', 'sh.id')
                ->join('products as pd', 'so.product_id', '=', 'pd.id')
                ->join('users as us', 'so.user_id', '=', 'us.id')
                ->where('so.shop_id', $id)
                ->whereBetween('so.tanggal', [$tgl_awal, $tgl_akhir])
                ->where('so.deleted_at', NULL)
                ->orderBy('so.created_at', 'asc');
        } else {
            $rekap = DB::table('save_orders as so')
                ->select('so.id', 'so.qty', 'so.total', 'so.tanggal', 'sh.name', 'pd.product_name', 'pd.warna', 'us.name as nama_pegawai')
                ->join('shops as sh', 'so.shop_id', '=', 'sh.id')
                ->join('products as pd', 'so.product_id', '=', 'pd.id')
                ->join('users as us', 'so.user_id', '=', 'us.id')
                ->where('so.shop_id', $id)
                ->where('so.deleted_at', NULL)
                ->orderBy('so.created_at', 'asc');
        }


        $datatables = Datatables::of($rekap)
            ->editColumn('product_name', function ($row) {
                return $row->product_name . ' - ' . $row->warna;
            })
            ->editColumn('total', function ($row) {
                return number_format($row->total);
            })
            ->editColumn('tanggal', function ($row) {
                return date('d/m/Y', strtotime($row->tanggal));
            })
            ->addIndexColumn();

        return $datatables->make(true);
    }


    public function update(Request $request, $id)
    {
        $data = Toko::findOrFail($request->id);
        $data->city = $request->get('city');
        $data->name = $request->get('name');
        $data->region = $request->get('region');
        $data->street_address = $request->get('street_address');
        $data->save();

        Alert::success('Toko berhasil diperbarui');
        return back();
    }

    public function addproduct(Request $request)
    {
        // dd($request->all());
        if ($request->get('qty') <= $request->get('stock_')) {
            $product_id = $request->get('product_id');
            $shop_id = $request->get('shop_id');
            $qty = $request->get('qty');

            $cek = count(DB::table('product_shop')->where(['product_id' => $product_id,  'shop_id' => $shop_id])->where('deleted_at', NULL)->get());
            if ($cek > 0) {
                $qtyNow = DB::table('product_shop')->where(['product_id' => $product_id,  'shop_id' => $shop_id])->where('deleted_at', NULL)->value('temp_stock');
                DB::table('product_shop')->where(['product_id' => $product_id,  'shop_id' => $shop_id])->where('deleted_at', NULL)->update([
                    'temp_stock' => $qtyNow + $qty
                ]);
            } else {
                DB::table('product_shop')->insert([
                    'shop_id' => $shop_id,
                    'product_id' => $product_id,
                    'temp_stock' => $qty,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            Alert::success('Produk berhasil di Tambahkan');
            return back();
        } elseif ($request->get('qty') > $request->get('stock_')) {
            Alert::error('Stok yang dimasukkan melebihi stok yang tersedia');
            return back();
        }
    }

    public function product($id)
    {
        $active = 'toko';
        $id_ = $id;
        $shop = Toko::findOrFail($id)->name;
        $shop_all = Toko::all();
        $category = Category::all();

        return view('admin.shop.product', compact(['id_', 'active', 'shop', 'shop_all', 'category']));
    }

    private function getPrice($start_date, $end_date, $price, $diskon, $diskon_type)
    {
        $now = Carbon::now();
        if ($now >= $start_date && $now <= $end_date) {
            if ($diskon_type == 'persen') {
                $final_price = $price - ($price * $diskon / 100);
            } else {
                $final_price = $price - $diskon;
            }
        } else {
            $final_price = $price;
        }

        return $final_price;
    }

    public function getproduct(Request $request, $id)
    {

        $shop = Toko::findOrFail($id);

        if (!empty($request->kategori)) {
            $barang = DB::select(
                "SELECT pr.product_name, pr.price ,pr.diskon, pr.diskon_type, pr.start_date, pr.end_date ,pr.warna, pr.final_price, pr.id as product_id, ps.id, ps.temp_stock FROM products pr JOIN product_shop ps on pr.id = ps.product_id JOIN category_product cp ON pr.id = cp.product_id JOIN categories ca ON ca.id = cp.category_id WHERE ps.shop_id = '$id' and cp.category_id = '$request->kategori' and ps.deleted_at is null"
            );
        } else {
            $barang = DB::select(
                "SELECT pr.product_name, pr.price ,pr.diskon, pr.diskon_type, pr.start_date, pr.end_date, pr.warna, pr.final_price, pr.id as product_id, ps.id, ps.temp_stock FROM products pr JOIN product_shop ps on pr.id = ps.product_id WHERE ps.shop_id = '$id' and ps.deleted_at is null"
            );
        }


        $datatables = Datatables::of($barang)
            ->editColumn('product_id', function ($row) {
                return $row->product_name . ' - ' . $row->warna;
            })
            ->addColumn('harga', function ($row) {
                $final_price = $this->getPrice($row->start_date, $row->end_date, $row->price, $row->diskon, $row->diskon_type);
                return number_format($final_price);
            })
            ->addColumn('total', function ($row) {
                $final_price = $this->getPrice($row->start_date, $row->end_date, $row->price, $row->diskon, $row->diskon_type);
                $total = $final_price * $row->temp_stock;
                return number_format($total);
            })
            ->addColumn('action', function ($row) use ($shop) {
                $final_price = $this->getPrice($row->start_date, $row->end_date, $row->price, $row->diskon, $row->diskon_type);
                $btn = '<button id="edit-user" data-toggle="modal" data-target="#editModal" data-name="' . $row->product_name . '" data-warna="' . $row->warna . '" data-id="' . $row->id . '" data-product_id = "' . $row->product_id . '" data-harga="' . $final_price . '" data-stock = "' . $row->temp_stock . '" class="btn btn-info btn-sm">
                <i class="fas fa-edit"></i>
                </button>';
                $btn .= '<button data-toggle="modal" data-target="#transferProdukModal" data-product_id="' . $row->product_id . '" data-shop_name="' . $shop->name . '" data-id="' . $shop->id . '" data-stok_tersedia="' . $row->temp_stock . '"  class="btn btn-primary btn-sm">
                <i class="ni ni-curved-next"></i>
                </button>';

                $btn .= '<button onclick="hapusProduk(' . $row->product_id . ', ' . $shop->id . ')"  class="delete btn btn-danger btn-sm">
                <i class="fas fa-trash"></i>
                </button>';

                return $btn;
            })
            ->rawColumns(['product_id', 'harga', 'total', 'action'])
            ->addIndexColumn();

        return $datatables->make(true);
    }

    public function getstock($id)
    {
        $stock_ = Product::where('id', $id)->value('stock');
        $stock__ = DB::table('product_shop')->where('product_id', $id)->sum('temp_stock');
        $stock = ($stock_ - $stock__);
        return response()->json([
            'stock' => $stock,
        ]);
    }


    public function addstock(Request $request)
    {
        // dd($request->all());
        if ($request->get('exampleRadios') == "tambah") {
            $stock = DB::table('product_shop')->where('id', $request->id)->where('deleted_at', NULL)->value('temp_stock');
            $add = $stock + $request->get('stock');
            if ($request->get('stock') <= $request->get('temp_stock_')) {
                $data = DB::table('product_shop')->where('id', $request->id)->where('deleted_at', NULL)->update([
                    'temp_stock' => $add
                ]);

                return back()->with('success', 'Stock berhasil ditambahkan');
            } else {
                return back()->with('danger', 'Stock yang dimasukkan melebihi stock yang ada');
            }
        } else {
            // dd($request->id);
            $stock = DB::table('product_shop')->where('id', $request->id)->where('deleted_at', NULL)->value('temp_stock');
            $add = $stock - $request->get('stock');
            if ($request->get('stock') <= $stock) {
                $data = DB::table('product_shop')->where('id', $request->id)->where('deleted_at', NULL)->update([
                    'temp_stock' => $add
                ]);

                return back()->with('success', 'Stock berhasil ditarik');
            } else {
                return back()->with('danger', 'Stock yang dimasukkan melebihi stock yang ada');
            }
        }
    }
    public function user($id)
    {
        $active = 'toko';
        $id_ = $id;
        $shop = Toko::findOrFail($id)->name;
        $pegawai = User::whereNotIn('id', ['153127'])->get()->all();
        return view('admin.shop.pegawai', compact(['id_', 'shop', 'active', 'pegawai']));
    }

    public function adduser(Request $request)
    {
        // dd($request->all());
        $user_id = $request->get('user_id');
        $shop_id = $request->get('shop_id');

        $cek = count(DB::table('shop_user')->where(['user_id' => $user_id])->get());
        if ($cek > 0) {
            Alert::success('Pegawai Sudah di Tambahkan');
            return back();
        } else {
            DB::table('shop_user')->insert([
                'shop_id' => $shop_id,
                'user_id' => $user_id,
            ]);
            Alert::success('Pegawai berhasil di Tambahkan');
            return back();
        }
    }

    public function getuser($id)
    {
        $barang = DB::select("SELECT us.name, us.email, su.id FROM users us JOIN shop_user su on us.id = su.user_id WHERE su.shop_id = '$id'");


        $datatables = DataTables::of($barang)
            ->addColumn('action', function ($row) {
                $btn = '<button id="edit-user" data-toggle="modal" data-target="#editModal" data-id="' . $row->id . '"   class="delete btn btn-danger btn-sm">
                <i class="fas fa-trash"></i>
                </button>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->addIndexColumn();

        return $datatables->make(true);
    }
    public function deletuser(Request $request)
    {
        $data = DB::table('shop_user')->where('id', $request->get('id'))->delete();
        // $data->delete();
        Alert::success('Pegawai berhasil dihapus');
        return back();
    }

    public function rekapstocktoko($id)
    {
        $tanggal = date('d M Y', strtotime(Carbon::now()));
        $data = DB::select("SELECT pd.product_name, pd.stock, pd.warna FROM products pd JOIN product_shop ps ON pd.id = ps.product_id WHERE ps.shop_id = '$id' and ps.deleted_at is null");

        return Excel::download(new RekapStockExport($data), "rekap_stock_$tanggal.xlsx");
    }

    public function rekapstock()
    {
        $tanggal = date('d M Y', strtotime(Carbon::now()));
        $data = Product::all();
        // dd($data);
        return Excel::download(new RekapStockExport($data), "rekap_stock_$tanggal.xlsx");
    }

    public function ajaxChartPenjualan($id)
    {
        $year = date('Y');

        $totalPenjualan = DB::select("SELECT sum(total) as total, MONTHNAME(tanggal) as bulan FROM save_orders where YEAR(tanggal) = '$year' and shop_id = '$id' and deleted_at IS NULL GROUP BY MONTHNAME(tanggal) ORDER BY MONTH(tanggal) asc");

        $totalPenjualanBulan = array();
        $totalPenjualanValue = array();
        foreach ($totalPenjualan as $value) {
            $totalPenjualanBulan[] = $value->bulan;
            $totalPenjualanValue[] = $value->total;
        }

        $data = [$totalPenjualanBulan, $totalPenjualanValue];

        return response()->json($data);
    }

    public function ajaxChartTerjual($id)
    {
        $year = date('Y');

        $totalPenjualan = DB::select("SELECT sum(qty) as total, MONTHNAME(tanggal) as bulan FROM save_orders where YEAR(tanggal) = '$year' and shop_id = '$id' and deleted_at IS NULL GROUP BY MONTHNAME(tanggal) ORDER BY MONTH(tanggal) asc");

        $totalPenjualanBulan = array();
        $totalPenjualanValue = array();
        foreach ($totalPenjualan as $value) {
            $totalPenjualanBulan[] = $value->bulan;
            $totalPenjualanValue[] = $value->total;
        }

        $data = [$totalPenjualanBulan, $totalPenjualanValue];
        return response()->json($data);
    }

    public function transfer_produk(Request $request)
    {
        $fromShop = ProductShop::where('shop_id', $request->id)
            ->where('product_id', $request->product_id)->first();

        $toShop = ProductShop::where('shop_id', $request->ke)
            ->where('product_id', $request->product_id)->first();

        if ($request->stok_dikirim > $fromShop->temp_stock) {
            Alert::error('Stok yang dikirim melebihi stok yang tersedia');
            return back();
        }

        if ($toShop == null) {
            ProductShop::create([
                'product_id' => $request->product_id,
                'shop_id' => $request->ke,
                'temp_stock' => $request->stok_dikirim,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            ProductShop::where('shop_id', $request->id)
                ->where('product_id', $request->product_id)->update([
                    'temp_stock' => $fromShop->temp_stock - $request->stok_dikirim
                ]);
        } else {
            ProductShop::where('shop_id', $request->ke)
                ->where('product_id', $request->product_id)->update([
                    'temp_stock' => $toShop->temp_stock + $request->stok_dikirim
                ]);

            ProductShop::where('shop_id', $request->id)
                ->where('product_id', $request->product_id)->update([
                    'temp_stock' => $fromShop->temp_stock - $request->stok_dikirim
                ]);
        }

        Alert::success('Produk berhasil di transfer ke toko tujuan');
        return back();
    }

    public function hapusProduk($product_id, $shop_id)
    {
        ProductShop::where('shop_id', $shop_id)
            ->where('product_id', $product_id)
            ->delete();

        Alert::success('Produk berhasil di hapus dari toko');
        return back();
    }
}
