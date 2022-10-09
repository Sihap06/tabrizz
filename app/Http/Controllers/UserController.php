<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Auth;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $active = 'pegawai';
        return view('admin.users.index', compact('active'));
    }

    public function getuser(Request $request)
    {
        DB::statement(DB::raw('set @rownum=0'));
        $user = DB::table('users')->whereNotIn('id', ['153127'])->select([
            DB::raw('@rownum  := @rownum  + 1 AS rownum'),
            'id',
            'name',
            'email',
            'status',
            'password'
        ]);


        $datatables = DataTables::of($user)
            ->editColumn('name', function ($user) {
                $id = $user->id;
                return '<span id="' . $id . '" style="cursor:pointer;" class="btn-user">' . $user->name;
            })
            ->addColumn('action', function ($row) {
                $btn1 = '<button id="edit-user" data-toggle="modal" data-target="#editModal" data-email="' . $row->email . '" data-nama="' . $row->name . '" data-password="' . $row->password . '" data-id="' . $row->id . '" class="delete btn btn-info btn-sm">
                <i class="fas fa-edit"></i>
                </button>
            <button id="delete-user" data-toggle="modal" data-target="#hapusModal" data-id="' . $row->id . '" class="delete btn btn-dark btn-sm">
           <i class="fas fa-toggle-off"></i>
            </button>';

                $btn2 = '<button id="edit-user" data-toggle="modal" data-target="#editModal" data-email="' . $row->email . '" data-nama="' . $row->name . '" data-password="' . $row->password . '" data-id="' . $row->id . '" class="delete btn btn-info btn-sm">
                <i class="fas fa-edit"></i>
                </button>
                <button id="delete-user" data-toggle="modal" data-target="#aktifModal" data-id="' . $row->id . '" class="delete btn btn-dark btn-sm">
              <i class="fas fa-toggle-on"></i>
                </button>';

                if ($row->status == 'aktif') {
                    return $btn1;
                } elseif ($row->status == 'non-aktif') {
                    return $btn2;
                }
            })
            ->rawColumns(['name', 'action'])
            ->addIndexColumn();

        // if ($keyword = $request->get('search')['value']) {
        //     $datatables->filterColumn('rownum', 'whereRaw', '@rownum  + 1 like ?', ["%{$keyword}%"]);
        // }

        return $datatables->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $kasir = Role::where('name', 'kasir')->first();

        $new_user = new User;
        $new_user->name = $request->get('name');
        $new_user->email = $request->get('email');
        $new_user->password = bcrypt($request->get('password'));
        $new_user->save();
        $new_user->roles()->attach($kasir);
        // dd($new_user);

        // $new_code = new Code;
        // $new_code->

        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return view('admin.users.show')->with('users', User::findOrFail($user->id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = User::findOrFail($request->id);
        // dd($data);
        $data->name = $request->name;
        $data->email = $request->email;
        $data->password = bcrypt($request->password);
        $data->save();

        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $data = User::findOrFail($request->id);
        $data->password = bcrypt(str_random(60));
        $data->status = 'non-aktif';
        $data->save();

        return back()->with('success', 'Pegawai berhasil di non-aktifkan');
    }

    public function tambahuser()
    {
        return view('admin.users.create');
    }

    public function simpanuser(Request $request)
    {
        $kasir = Role::where('name', 'kasir')->first();
        $new_user = new User;
        $new_user->name = $request->get('name');
        $new_user->email = $request->get('email');
        $new_user->password = bcrypt($request->get('password'));
        $new_user->save();
        $new_user->roles()->attach($kasir);
        // dd($new_user);

        return redirect()->route('users.index');
    }
    public function resetpassword($id)
    {
        return view('auth.resetpassword', compact('id'));
    }

    public function change(Request $request)
    {
        Validator::make($request->all(), [
            'password'  => 'required|min:5|max:20',
            'ulangipassword'   => 'required|same:password',
        ])->validate();
        // dd($request->all());
        $data = \App\User::findOrFail($request->get('id'));
        $data->password = bcrypt($request->get('password'));
        $data->save();

        return back()->with('success', 'Password berhasil diubah');
    }

    public function userAktif(Request $request)
    {
        // dd($request->all());

        $data = User::findOrFail($request->id);
        $data->password =  bcrypt($request->get('password'));
        $data->status = 'aktif';
        $data->save();

        return back()->with('success', 'Pegawai berhasil aktif');
    }
}
