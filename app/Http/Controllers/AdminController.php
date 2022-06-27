<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Absensi;
use App\Models\Ket_Absensi;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function index()
    {
        $absensi = Absensi::with('ket_absensi')->get();
        $absensi -> user = Absensi::with('user')->get();
        return view('admin.home', ['absensi' => $absensi]);
    }

    public function indexK()
    {
        $user = User::with('role')->where('role_id', 2)->get();
        // $paginate = User::orderBy('id', 'desc')->paginate(3);
        return view('admin.karyawan', ['user' => $user]);
    }

    public function createA()
    {
        $ket_absensi = Ket_Absensi::all();
        return view('admin.createA', ['ket_absensi' => $ket_absensi]);
    }

    public function createU()
    {
        $role = Role::all();
        return view('admin.createU', ['roles' => $role]);
    }

    public function storeA(Request $request)
    {
        $absensi = new Absensi;
        $absensi->tgl = $request->get('Tgl');
        $absensi->save();

        $ket_absensi = new Ket_Absensi;
        $ket_absensi->id = request('Ket_Absensi');

        $ket_absensi->ket_absensi()->associate($ket_absensi);
        $ket_absensi->save();

        return redirect()->route('admin.index') //jika data berhasil ditambahkan kembali ke hal. utama
            ->with('success', 'Absensi Berhasil Ditambahkan');
    }

    public function storeU(Request $request)
    {
        $user = new User;
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->role_id = $request->get('role');
        $user->password = $request->get('password');
        $user->save();

        $role = new Role;
        $role->id = request('role');

        $role->role()->associate($role);
        $role->save();

        return redirect()->route('admin.home') //jika data berhasil ditambahkan kembali ke hal. utama
            ->with('success', 'Data User Berhasil Ditambahkan');
    }

    public function show($id)
    {
        // $absensi = Absensi::with('Ket_Absensi')->where('id', $id)->first();
        // return view('admin.detail', ['absensi' => $absensi]);
    }

    public function editA($id)
    {
        $absensi = Absensi::with('Ket_Absensi')->where('id', $id)->get();
        $ket_absensi = Ket_Absensi::all();
        $user = User::all();
        return view('admin.editA', ['user' => $user, 'ket_absensi' => $ket_absensi, 'absensi' => $ket_absensi]);
    }

    public function editU($id)
    {
        $user = User::with('Role')->where('id', $id)->first();
        $role = Role::all();
        return view('admin.editU', ['user' => $user, 'role' => $role]);
    }

    public function updateA(Request $request, $id)
    {
        $request->validate([
            'tgl' => 'required',
            'user_id' => 'required',
            'ket_id' => 'required',
        ]);

        $absensi = Absensi::with('Ket_Absensi','User')->where('id', $id)->first();

        // if ($user->foto && file_exists(storage_path('app/public' . $user->foto))) {
        //     Storage::delete('public/' . $user->foto);
        // }

        // $imageName = $request->file('foto')->store('images', 'public');

        // $user->foto = $imageName;
        $absensi->tgl = $request->get('tgl');
        $absensi->user_id = $request->get('user_id');
        $absensi->ket_id = $request->get('ket_id');

        $ket_absensi = new Ket_Absensi;
        $ket_absensi->id = request('ket_absensi');

        $user = new User;
        $user->id = request('user');

        $absensi->user()->associate($user);
        $absensi->ket_absensi()->associate($ket_absensi);
        $absensi->save();

        return redirect()->route('admin.home')
            ->with('success', 'Data User Berhasil Diupdate');
    }

    public function updateU(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
        ]);

        $user = User::with('role')->where('id', $id)->first();

        // if ($user->foto && file_exists(storage_path('app/public' . $user->foto))) {
        //     Storage::delete('public/' . $user->foto);
        // }

        // $imageName = $request->file('foto')->store('images', 'public');

        // $user->foto = $imageName;
        $user->name = $request->get('name');
        $user->email = $request->get('email');

        $role = new Role;
        $role->id = request('role');

        $user->role()->associate($role);
        $user->save();

        return redirect()->route('admin.karyawan')
            ->with('success', 'Data User Berhasil Diupdate');
    }

    public function destroyA($id)
    {
        Absensi::where('id', $id)->delete();
        return redirect()->route('admin.index')
            ->with('success', 'Data Absensi Berhasil Dihapus');
    }

    public function destroyU($id)
    {
        User::where('id', $id)->delete();
        return redirect()->route('admin.home')
            ->with('success', 'Data Karyawan Berhasil Dihapus');
    }

    public function cetakAbsen($id)
    {
    }
}