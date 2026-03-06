<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function registerPage()
    {
        return view('auth.register');
    }

    public function loginPage()
    {
        return view('auth.login');
    }

    public function register(Request $request)
    {
   $data = $request->validate([
    'nisn' => 'required|unique:users',
    'name' => 'required',
    'kelas' => 'required',
    'phone' => 'required',
    'tanggal_lahir' => 'required', // ✅ TAMBAH
    'jenis_kelamin' => 'required',
    'photo' => 'required|image',
    'password' => 'required|min:6',
]);

$photoPath = $request->file('photo')->store('users','public');

User::create([
    'nisn' => $data['nisn'],
    'name' => $data['name'],
    'kelas' => $data['kelas'],
    'phone' => $data['phone'],
    'tanggal_lahir' => $data['tanggal_lahir'], // ✅ TAMBAH
    'jenis_kelamin' => $data['jenis_kelamin'],
    'photo' => $photoPath,
    'password' => bcrypt($data['password']),
]);


        return redirect('/login')->with('success','Registrasi berhasil');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nisn' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {

        auth()->user()->update([
            'is_online' => true,
            'last_seen' => now()
]);
        
            return redirect('home');
        }

        return back()->withErrors(['login'=>'NISN atau password salah']);
    }
}
