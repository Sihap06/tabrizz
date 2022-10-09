<?php

namespace App\Http\Controllers;

use App\Order;
use App\Product;
use Illuminate\Http\Request;
use Webpatser\Uuid\Uuid;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\App;
use App\Code;
use App\User;
use App\Toko;
use App\Exports\RekapExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month;
use RealRashid\SweetAlert\Facades\Alert;
use stdClass;

class HomeController extends Controller
{
    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function index()
    {
        $active = 'dashboard';
        $codes = DB::table('code')->where('user_id', Auth::user()->id)->get();
        $count = count($codes);
        // dd($count);
        if ($count == 0) {
            // $code = Uuid::generate(4);
            $codes = new Code;
            $codes->id = str_random(4);
            $codes->user_id = Auth::user()->id;
            $codes->code = mt_rand(100000, 999999);;
            $codes->save();
        }
        
        $code = DB::table('code')->where('user_id', Auth::user()->id)->value('code');
        $productMonth = Product::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count();
        $product = Product::count();
        $orderMonth = Order::whereMonth('tanggal', date('m'))->whereYear('tanggal', date('Y'))->sum('total');
        $order = Order::sum('total');
        
        
        
        
        return view('welcome', compact(['code', 'productMonth', 'product', 'orderMonth', 'order', 'active']));
    }
    
    public function ajaxChartPenjualan()
    {
        $year = date('Y');
        
        $totalPenjualan = DB::select("SELECT sum(total) as total, MONTHNAME(tanggal) as bulan FROM save_orders where YEAR(tanggal) = '$year' and deleted_at IS NULL GROUP BY MONTHNAME(tanggal) ORDER BY MONTH(tanggal) asc");
        
        $totalPenjualanBulan = array();
        $totalPenjualanValue = array();
        foreach ($totalPenjualan as $value) {
            $totalPenjualanBulan[] = $value->bulan;
            $totalPenjualanValue[] = $value->total;
        }
        
        $data = [$totalPenjualanBulan, $totalPenjualanValue];
        
        return response()->json($data);
    }
    
    public function ajaxChartTerjual()
    {
        $year = date('Y');
        
        $totalPenjualan = DB::select("SELECT sum(qty) as total, MONTHNAME(tanggal) as bulan FROM save_orders where YEAR(tanggal) = '$year' and deleted_at IS NULL GROUP BY MONTHNAME(tanggal) ORDER BY MONTH(tanggal) asc");
        
        $totalPenjualanBulan = array();
        $totalPenjualanValue = array();
        foreach ($totalPenjualan as $value) {
            $totalPenjualanBulan[] = $value->bulan;
            $totalPenjualanValue[] = $value->total;
        }
        
        $data = [$totalPenjualanBulan, $totalPenjualanValue];
        return response()->json($data);
    }
    
    public function dashboard()
    {
        return view('order.index');
    }
    
    public function rekapPegawai($id)
    {
        return view('rekap', compact('id'));
    }
    
    public function getRekapPegawai(Request $request, $id)
    {
        if (!empty($request->from_date)) {
            $rekap = DB::table('save_orders as so')
            ->select('so.id', 'so.qty', 'so.total', 'so.tanggal', 'sh.name', 'pd.product_name', 'pd.warna', 'us.name as nama_pegawai')
            ->join('shops as sh', 'so.shop_id', '=', 'sh.id')
            ->join('products as pd', 'so.product_id', '=', 'pd.id')
            ->join('users as us', 'so.user_id', '=', 'us.id')
            ->whereBetween('so.tanggal', [$request->from_date, $request->to_date])
            ->where('user_id', $id)
            ->where('so.deleted_at', NULL)
            ->orderBy('so.created_at', 'asc');
        } else {
            $rekap = DB::table('save_orders as so')
            ->select('so.id', 'so.qty', 'so.total', 'so.tanggal', 'sh.name', 'pd.product_name', 'pd.warna', 'us.name as nama_pegawai')
            ->join('shops as sh', 'so.shop_id', '=', 'sh.id')
            ->join('products as pd', 'so.product_id', '=', 'pd.id')
            ->join('users as us', 'so.user_id', '=', 'us.id')
            ->where('user_id', $id)
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
        ->rawColumns(['shop_id', 'product_id', 'total', 'user_id'])
        ->addIndexColumn();
        
        return $datatables->make(true);
    }
    
    public function rekap()
    {
        $active = 'rekap';
        return view('admin.rekap', compact('active'));
    }
    
    public function rekap_pdf(Request $request)
    {
        // dd($request->all());
        $data = Order::whereBetween('tanggal', [$request->from_date, $request->to_date])->get()->all();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('admin.rekap_pdf', compact('data'));
        return $pdf->stream('rekap.pdf');
        // dd($data);
    }
    
    public function rekap_excel(Request $request)
    {
        $tgl_awal = date('Y-m-d', strtotime($request->from_date));
        $tgl_akhir = date('Y-m-d', strtotime($request->to_date));
        $tanggal = date('d M Y', strtotime(Carbon::now()));
        
        $data = DB::select("SELECT sh.name, pd.product_name, pd.warna, so.qty, so.total, so.tanggal, us.name as nama_pegawai FROM save_orders so JOIN shops sh ON so.shop_id = sh.id JOIN products pd ON so.product_id = pd.id JOIN users us ON so.user_id = us.id WHERE so.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir' and so.deleted_at IS NULL ORDER BY so.created_at asc");
        
        return Excel::download(new RekapExport($data, $tgl_awal, $tgl_akhir), "rekap_$tanggal.xlsx");
    }
    
    public function rekap_toko_excel(Request $request)
    {
        $id = $request->shop_id;
        $tgl_awal = date('Y-m-d', strtotime($request->from_date));
        $tgl_akhir = date('Y-m-d', strtotime($request->to_date));
        $tanggal = date('d M Y', strtotime(Carbon::now()));
        
        $data = DB::select("SELECT sh.name, pd.product_name, pd.warna, so.qty, so.total, so.tanggal, us.name as nama_pegawai FROM save_orders so JOIN shops sh ON so.shop_id = sh.id JOIN products pd ON so.product_id = pd.id JOIN users us ON so.user_id = us.id WHERE sh.id = '$id' and so.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir' and so.deleted_at IS NULL ORDER BY so.created_at asc");
        
        return Excel::download(new RekapExport($data, $tgl_awal, $tgl_akhir), "rekap_$tanggal.xlsx");
    }
    
    public function rekap_pegawai_excel(Request $request)
    {
        $id = $request->user_id;
        $tgl_awal = $request->from_date;
        $tgl_akhir = $request->to_date;
        $tanggal = date('d M Y', strtotime(Carbon::now()));
        
        $data = DB::select("SELECT sh.name, pd.product_name, pd.warna, so.qty, so.total, so.tanggal, us.name as nama_pegawai FROM save_orders so JOIN shops sh ON so.shop_id = sh.id JOIN products pd ON so.product_id = pd.id JOIN users us ON so.user_id = us.id WHERE us.id = '$id' and so.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir' and so.deleted_at IS NULL ORDER BY so.created_at asc");
        
        return Excel::download(new RekapExport($data, $tgl_awal, $tgl_akhir), "rekap_$tanggal.xlsx");
    }
    
    public function getrekap(Request $request)
    {
        if (!empty($request->from_date)) {
            $tgl_awal = date('Y-m-d', strtotime($request->from_date));
            $tgl_akhir = date('Y-m-d', strtotime($request->to_date));
            
            $rekap = DB::table('save_orders as so')
            ->select('so.id', 'so.qty', 'so.total', 'so.tanggal', 'sh.name', 'pd.product_name', 'pd.warna', 'us.name as nama_pegawai')
            ->join('shops as sh', 'so.shop_id', '=', 'sh.id')
            ->join('products as pd', 'so.product_id', '=', 'pd.id')
            ->join('users as us', 'so.user_id', '=', 'us.id')
            ->whereBetween('so.tanggal', [$tgl_awal, $tgl_akhir])
            ->where('so.deleted_at', NULL)
            ->orderBy('so.created_at', 'asc');
        } else {
            $rekap = DB::table('save_orders as so')
            ->select('so.id', 'so.qty', 'so.total', 'so.tanggal', 'sh.name', 'pd.product_name', 'pd.warna', 'us.name as nama_pegawai')
            ->join('shops as sh', 'so.shop_id', '=', 'sh.id')
            ->join('products as pd', 'so.product_id', '=', 'pd.id')
            ->join('users as us', 'so.user_id', '=', 'us.id')
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
        ->addColumn('action', function ($row) {
            $btn = '
            <button class="delete btn btn-danger btn-sm" data-id="' . $row->id . '" data-target="#hapusModal" data-toggle="modal">
            <i class="fas fa-trash"></i>
            </button>
            </a>';
            return $btn;
        })
        ->rawColumns(['total', 'action'])
        ->addIndexColumn();
        
        return $datatables->make(true);
    }
    
    public function hapus_rekap(Request $request)
    {
        
        $data = Order::findOrFail($request->id);
        $data->delete();
        
        Alert::success('Data penjualan berhasil dihapus');
        return back();
    }
    
}
