<?php

namespace App\Http\Controllers;

use App\Order;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Category;
use App\CategoryProduct;
use App\ProductShop;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $active = 'produk';
        $category = Category::all();
        // dd($category);
        return view('admin.product.index', compact('category', 'active'));
    }

    public function getproduk(Request $request)
    {
        if (!empty($request->kategori)) {
            $barang = DB::table('products')
                ->join('category_product', 'products.id', '=', 'category_product.product_id')
                ->join('categories', 'categories.id', '=', 'category_product.category_id')
                ->where('category_id', $request->get('kategori'))
                ->where('products.deleted_at', NULL)
                ->select([
                    'products.id',
                    'products.product_name',
                    'products.kode',
                    'products.warna',
                    'products.price',
                    'products.harga_beli',
                    'products.stock',
                    'products.diskon',
                    'products.final_price',
                ]);
        } else {
            $barang = DB::table('products')
                ->where('deleted_at', NULL)
                ->select([
                    'id',
                    'product_name',
                    'kode',
                    'warna',
                    'price',
                    'harga_beli',
                    'stock',
                    'diskon',
                    'final_price',
                ]);
        }

        $datatables = DataTables::of($barang)
            ->editColumn('product_name', function ($row) {
                return $row->product_name . ' - ' . $row->warna;
            })

            ->editColumn('price', function ($row) {
                return number_format($row->price);
            })
            ->editColumn('harga_beli', function ($row) {
                return number_format($row->harga_beli);
            })
            ->editColumn('final_price', function ($row) {
                return number_format($row->final_price);
            })
            ->addColumn('temp_stock', function ($row) {
                $stock = DB::table('product_shop')->where('product_id', $row->id)->where('deleted_at', NULL)->sum('temp_stock');
                $stock_ = $row->stock;
                return $stock_ - $stock;
            })
            ->addColumn('action', function ($row) {
                $btn = '<button id="edit-user" data-toggle="modal" data-target="#editModal" data-harga="' . $row->price . '" data-kode="' . $row->kode . '" data-harga_beli="' . $row->harga_beli . '" data-diskon="' . $row->diskon . '" data-final_price="' . $row->final_price . '" data-nama="' . $row->product_name . '" data-id="' . $row->id . '" data-stock="' . $row->stock . '" data-warna="' . $row->warna . '" class="delete btn btn-info btn-sm">
            <i class="fas fa-edit"></i>
            </button>';
                $btn .= '<button data-toggle="modal" data-target="#hapusModal" data-id="' . $row->id . '" class="delete btn btn-danger btn-sm">
            <i class="fas fa-trash"></i>
            </button>';
                return $btn;
                // dd($row);
            })
            ->rawColumns(['product_name', 'price', 'harga_beli', 'temp_stock', 'action'])
            ->addIndexColumn();

        return $datatables->make(true);
    }

    public function rekap()
    {
        $orders = \App\Order::paginate(10);

        return view('pimpinan.index', compact('orders'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->get('category'));
        $data = new \App\Product;
        // $data->id = rand();
        $data->kode = $request->get('kode');
        $data->product_name = $request->get('nama');
        $data->warna = $request->get('warna');
        $data->price = $request->get('harga');
        $data->final_price = $request->get('harga');
        $data->harga_beli = $request->get('harga_beli');
        $data->stock = $request->get('stock');
        $data->created_by = Auth::user()->name;

        // $data->categories()->attach($request->get('category'));
        $data->save();
        // dd($data->id);

        DB::table('category_product')->insert([
            'product_id' => $data->id,
            'category_id' => $request->get('category'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);


        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->get('exampleRadios') == "tambah") {
            $data = \App\Product::findOrFail($request->id);
            $final_stock = $data->stock + $request->get('stock');
            $data->product_name = $request->get('nama');
            $data->kode = $request->get('kode');
            $data->warna = $request->get('warna');
            $data->price = $request->get('harga');
            $data->harga_beli = $request->get('harga_beli');
            $data->diskon = $request->get('diskon');
            $data->final_price = $request->get('harga') - ($request->get('harga') * $request->get('diskon') / 100);
            $data->stock = $final_stock;

            $data->save();

            Alert::success('Edit Produk', 'Produk berhasil diperbarui & stok berhasil ditambah');
            return back();
        } else {
            $data = \App\Product::findOrFail($request->id);
            $x = DB::table('product_shop')->where('product_id', $request->id)->where('deleted_at', NULL)->sum('temp_stock');
            $y = $data->stock - $x;
            if ($request->get('stock') <= $y) {
                $final_stock = $data->stock - $request->get('stock');
                $data->product_name = $request->get('nama');
                $data->kode = $request->get('kode');
                $data->warna = $request->get('warna');
                $data->price = $request->get('harga');
                $data->harga_beli = $request->get('harga_beli');
                $data->diskon = $request->get('diskon');
                $data->final_price = $request->get('harga') - ($request->get('harga') * $request->get('diskon') / 100);
                $data->stock = $final_stock;

                $data->save();

                Alert::success('Edit Produk', 'Produk berhasil diperbarui & stok berhasil ditarik');
                return back();
            } else {
                Alert::error('Toko masih memiliki stock product ini');
                return back();
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        Product::destroy($request->id);
        CategoryProduct::where('product_id', $request->id)->delete();
        ProductShop::where('product_id', $request->id)->delete();
        Order::where('product_id', $request->id)->delete();

        Alert::success('Hapus Produk', 'Produk berhasil dihapus');
        return back();
    }

    public function ajaxGetProduct($search)
    {
        $shopUser = DB::table('shop_user')->where('user_id', Auth::user()->id)->first()->shop_id;

        $data = DB::table('products')
            ->join('product_shop', 'products.id', '=', 'product_shop.product_id')
            ->select('product_shop.temp_stock', 'products.product_name', 'products.warna', 'products.final_price', 'products.id', 'products.kode')
            ->where('product_shop.shop_id', $shopUser)
            ->where(function ($query) use ($search) {
                $query->where('products.kode', $search)
                    ->orWhere('products.product_name', 'like', '%' . $search . '%');
            })
            ->orderBy('product_shop.temp_stock', 'desc')
            ->take(20)
            ->get();

        $formatted_tags = [];

        foreach ($data as $tag) {
            $formatted_tags[] = ['id' => $tag->kode, 'text' => $tag->product_name . ' - ' . $tag->warna];
        }

        return response()->json($formatted_tags);
    }
    public function updateDiscount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'value' => 'required|integer',
            'start' => 'required|date_format:Y-m-d H:i:s',
            'end' => 'required|date_format:Y-m-d H:i:s',
            'product_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'errors',
                'message' => $validator->errors()
            ], 400);
        }
        $status = null;
        $message = null;
        DB::transaction(function () use ($request, &$status, &$message) {
            try {
                $update = DB::table('products')
                    ->whereIn('id', $request->get('product_id'))
                    ->update([
                        'discount_type' => $request->get('type'),
                        'diskon' => $request->get('value'),
                        'start_discount' => $request->get('start'),
                        'end_discount' => $request->get('end'),
                    ]);
                $status = 'success';
                $message = 'sukses memperbarui data diskon!';
            } catch (\Throwable $th) {
                DB::rollback();
                $status = 'failed';
                $message = $th->getMessage();
            }
        });
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }
}
