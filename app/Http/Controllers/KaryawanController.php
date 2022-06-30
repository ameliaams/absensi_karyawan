<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Absensi;
use App\Models\Ket_Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KaryawanController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $absensi = Absensi::with('user')->where('user_id', $user->id)->first();
        $paginate = Absensi::orderBy('tgl', 'desc')->paginate(7);
        return view('karyawan.home', ['absensi' => $absensi, 'user' => $user, 'paginate' => $paginate]);
    }

    public function profil()
    {
        return view('karyawan.profil');
    }

    public function edit($id){
        $absensi = Absensi::with('ket_absensi')->where('id', $id)->first();
        $ket_absensi = Ket_Absensi::all();
        return view('karyawan.edit', ['ket' => $ket_absensi, 'absensi' => $absensi]);
    }

    public function absensi(Request $request, $id)
    {
        $absensi1 = Absensi::with('Ket_Absensi')->where('id', $id)->first();
        $id = Auth::user()->id;
        $this->validate($request, [
            'ket' => 'required'
        ]);

        $absensi = new Absensi;
        $absensi->tgl = $absensi1->tgl;
        $absensi->ket_id = $request->get('ket');
        $absensi->user_id = $id;

        $ket_absensi = new Ket_Absensi;
        $ket_absensi->id = request('ket_absensi');

        $user = new User;
        $user->id = request('user');

        $absensi->user()->associate($user);
        $absensi->ket_absensi()->associate($ket_absensi);
        $absensi->save();

        return redirect()->route('home') //jika data berhasil ditambahkan kembali ke hal. utama
            ->with('success', 'Absensi Telah Berhasil');
    }
}
