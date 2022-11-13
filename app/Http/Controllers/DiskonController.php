<?php

namespace App\Http\Controllers;

use App\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DiskonController extends Controller
{
    public function index()
    {
        $active = 'diskon';
        $category = Category::all();
        // dd($category);
        return view('admin.diskon.index', compact('category', 'active'));
    }

    public function getDiskon(Request $request)
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
                    'products.warna',
                    'products.price',
                    'products.diskon_type',
                    'products.diskon',
                    'products.start_date',
                    'products.end_date',
                ]);
        } else {
            $barang = DB::table('products')
                ->where('deleted_at', NULL)
                ->select([
                    'id',
                    'product_name',
                    'warna',
                    'price',
                    'diskon_type',
                    'diskon',
                    'start_date',
                    'end_date',
                ]);
        }

        $datatables = DataTables::of($barang)
            ->editColumn('product_name', function ($row) {
                return $row->product_name . ' - ' . $row->warna . '<span><input id="' . $row->id . '" value="' . $row->id . '" type="hidden"></span>';
            })
            ->editColumn('price', function ($row) {
                return number_format($row->price);
            })
            ->editColumn('diskon_type', function ($row) {
                return $row->diskon_type;
            })
            ->editColumn('diskon', function ($row) {
                return number_format($row->diskon);
            })
            ->editColumn('start_date', function ($row) {
                return Carbon::parse($row->start_date)->format('d/m/Y H:i:s');
            })
            ->editColumn('end_date', function ($row) {
                return Carbon::parse($row->end_date)->format('d/m/Y H:i:s');
            })
            ->rawColumns(['product_name', 'price', 'diskon_type', 'diskon', 'start_date', 'end_date'])
            ->addIndexColumn();

        return $datatables->make(true);
    }
}
