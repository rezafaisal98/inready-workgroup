<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Alert;
use App\Helper\Data;
use App\Http\Controllers\Controller;
use App\Models\Generation;
use App\Models\Period;
use Carbon\Carbon;
use Generator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class GenerationController extends Controller
{
    public function index(){
        $data = [
            'generation' => Generation::orderBy('id', 'DESC')->get(),
            'period' => Period::orderBy('id', 'DESC')->get()
        ];
        // return $data['generation'];
        return view('admin.generation', Data::view('generation', $data));
    }

    public function create(Request $request){
        $latest = $request->latest;

        $success = Generation::create([
            'name' => (int)$latest+1,
            'status' => 'member'
        ]);

        if($success){
            return Alert::default(true, 'Ditambah');
        }
        return Alert::default(false, 'Ditambah');
    }

    public function set(Request $request){
        $status = $request->status;
        $id = Crypt::decryptString($request->id);

        if($status == 'outgoing'){
            // cek jika masih ada pengurus, maka tidak bisa menjadi demisioner
            if(Generation::where('name', $id-1)->first()->status == 'active') return Alert::error('Terjadi Kesalahan', 'Kepengurusan belum selesai, tidak bisa menjadi demisioner!');

            // cek apakah sudah tersedia pewaris
            if(Generation::where('name', $id+1)->first() == null || Generation::where('name', $id+1)->first()->status == 'member') return Alert::error('Terjadi Kesalahan', 'Setidaknya jadikan pengurus generasi selanjutnya sebelum demisioner');
        }

        $success = Generation::where('name', $id)->update([
            'status' => ($status == 'active') ? 'active' : 'outgoing'
        ]);

        if($success) return Alert::default(true, 'Diperbarui');
        return Alert::default(false, 'Diperbarui');
    }

    public function createPeriod()
    {
        $year = Period::orderBy('id', 'DESC')->first()->year;
        if($year == Carbon::now()->format('Y')) return Alert::error('Gagal', 'Gagal menambahkan periode, periode tahun ini sudah ada');

        $new_period = Carbon::now()->format('Y').' - '.Carbon::now()->addYear()->format('Y');
        $success = Period::create([
            'period' => $new_period,
            'year' => Carbon::now()->format('Y')
        ]);

        if($success) return Alert::success('Berhasil', 'Periode baru berhasil ditambahkan');

        return Alert::error('Gagal', 'Terjadi kesalahan');
    }
}
