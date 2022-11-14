<?php

namespace App\Http\Controllers;

use App\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
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
                return Carbon::parse($row->start_date)->format('d/m/Y H:i');
            })
            ->editColumn('end_date', function ($row) {
                return Carbon::parse($row->end_date)->format('d/m/Y H:i');
            })
            ->rawColumns(['product_name', 'price', 'diskon_type', 'diskon', 'start_date', 'end_date'])
            ->addIndexColumn();

        return $datatables->make(true);
    }

    public function updateDiscount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'value' => 'required|integer',
            'start' => 'required|date_format:Y-m-d H:i',
            'end' => 'required|date_format:Y-m-d H:i',
            'product_id' => 'required',
        ]);

        if ($validator->fails()) {
            Alert::error('Error !', 'Pastikan form sudah diisi dengan baik dan benar');
            return response()->json([
                'status' => 'errors',
                'message' => $validator->errors()
            ], 400);
        }
        $status = null;
        $message = null;
        DB::transaction(function () use ($request, &$status, &$message) {
            try {
                DB::table('products')
                    ->whereIn('id', $request->get('product_id'))
                    ->update([
                        'diskon_type' => $request->get('type'),
                        'diskon' => $request->get('value'),
                        'start_date' => $request->get('start'),
                        'end_date' => $request->get('end'),
                        'updated_at' => Carbon::now()
                    ]);

                $status = 'success';
                $message = 'sukses memperbarui data diskon!';
                Alert::success('Update Diskon', 'Diskon produk berhasil diperbarui');
            } catch (\Throwable $th) {
                DB::rollback();
                $status = 'failed';
                $message = $th->getMessage();
                Alert::error('Update Diskon', 'Diskon produk gagal diperbarui');
            }
        });
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }
}
