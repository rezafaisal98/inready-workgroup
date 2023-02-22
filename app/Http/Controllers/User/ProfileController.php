<?php

namespace App\Http\Controllers\User;

use App\Helper\Alert;
use App\Helper\Data;
use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User\Biography;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SebastianBergmann\CodeUnit\FunctionUnit;

class ProfileController extends Controller
{
    
    public function index(){
        $data = Auth::user();
        return view('user.profile.index', Data::view('profile', $data));
    }

    public function profile(){
        return view('user.profile.profile', Data::view('profile'));
    }

    public function personal(){
        return view('user.profile.personal', Data::view('personal'));
    }
    
    public function account(){
        return view('user.profile.account', Data::view('account'));
    }

    public function etcetera(){
        $data = [
            'organization' => Biography::where('user_id', Auth::id())->where('type', 'organization')->get(),
            'sd' => Biography::where(['user_id' => Auth::id(), 'type' => 'sd'])->first(),
            'sltp' => Biography::where(['user_id' => Auth::id(), 'type' => 'sltp'])->first(),
            'slta' => Biography::where(['user_id' => Auth::id(), 'type' => 'slta'])->first(),
            'social' => Profile::where('user_id', Auth::id())->first(['instagram', 'twitter', 'facebook', 'linkedin'])
        ];
        return view('user.profile.etcetera', Data::view('etcetera', $data));
    }

    public function education(Request $request){
       $request->validate([
            'sd' => 'required',
            'sltp' => 'required',
            'slta' => 'required'
        ]);

        $filtered = $request->collect()->except('_token')->toArray();

        $types = ['sd', 'sltp', 'slta'];

        foreach ($types as $type) {
            $success = Biography::updateOrCreate(
                ['user_id' => Auth::id(), 'type' => $type],
                ['name' => $filtered[$type] ?? null, 'period' => $filtered[$type.'_period'] ?? null]
            );
        }

        if($success){
            return Alert::success('Berhasil Diupdate', 'Data Pendidikan Berhasil Diupdate');
        }

        return Alert::error('Gagal', 'Gagal Memperbarui');  
    }

    public function organization(Request $request){
        $avalaible_key = [];
        for ($i=1; $i <= 10 ; $i++) { 
            array_push($avalaible_key, 'organization_'.$i);
        }

        $filtered = $request->collect()->filter(function($value){
            return $value !== null;
        })->except('_token')->toArray();

        foreach ($filtered as $key => $value) {
           if(in_array($key, $avalaible_key)){
                (isset($filtered[$key.'_id'])) ? $biography = Biography::find($filtered[$key.'_id']) : $biography = new Biography();
                $biography->user_id = Auth::id();
                $biography->name = $value;
                $biography->type = 'organization';
                $biography->period = $filtered[$key.'_year'] ?? null;
                $success = $biography->save();
            }
        }

        $success = $success ?? false;
        if($success){
            return Alert::success('Berhasil Diupdate', 'Data Organisasi Berhasil Diupdate');
        }

        return Alert::error('Gagal', 'Gagal Memperbarui');    
    }

    public function social(Request $request){
        $success = Profile::where('user_id', Auth::id())->update([
            'instagram' => $request->instagram,
            'twitter' => $request->twitter,
            'facebook' => $request->facebook,
            'linkedin' => $request->linkedin,
        ]);

        if($success){
            return Alert::success('Berhasil Update', 'Data Sosial Media Berhasil Diperbarui');
        }

        return Alert::error('Gagal', 'Terjadi Kesalahan');
    }
}
