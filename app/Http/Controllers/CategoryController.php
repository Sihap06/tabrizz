<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Category;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Uuid;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $active = 'kategori';
        return view('admin.category.index', compact('active'));
    }

    public function getcategory(Request $request)
    {
        DB::statement(DB::raw('set @rownum=0'));
        $category = DB::table('categories')->select([
            DB::raw('@rownum  := @rownum  + 1 AS rownum'),
            'id',
            'name',

        ]);

        // dd($barang)


        $datatables = DataTables::of($category)
            ->addColumn('action', function ($row) {
                $btn = '<button id="edit-user" data-toggle="modal" data-target="#editModal" data-nama="' . $row->name . '" data-id="' . $row->id . '"  class="delete btn btn-info btn-sm">
                <i class="fas fa-edit"></i>
                </button>
            <button id="delete-user" data-toggle="modal" data-target="#hapusModal" data-id="' . $row->id . '" class="delete btn btn-danger btn-sm">
            <i class="fas fa-trash"></i>
            </button>';
                return $btn;
                // dd($row);
            })
            ->rawColumns(['action'])
            ->addIndexColumn();

        return $datatables->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $data = new Category;
        $data->id = rand();
        $data->name = $request->name;
        $data->slug = str_slug($request->get('name'), '-');
        $data->created_by = Auth::user()->name;
        $data->save();

        Alert::success('Kategori berhasil ditambahkan');
        return back();
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // dd($request->all());
        $data = Category::findOrFail($request->id);
        $data->name = $request->name;
        $data->slug = str_slug($request->name, '-');
        $data->created_by = Auth::user()->name;
        $data->save();

        Alert::success('Kategori berhasil diperbarui');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $cek = DB::table('category_product')->where('category_id', $request->id)->count();

        if ($cek == 0) {
            Category::destroy($request->id);
            Alert::success('Kategori berhasil dihapus');
        } else {
            Alert::error('Kategori gagal dihapus, karena kategori ini sedang di gunakan disalah satu produk');
        }
        return back();
    }
}
