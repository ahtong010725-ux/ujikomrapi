<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
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

    /**
     * Check NISN via AJAX - returns student data if found
     */
    public function checkNisn($nisn)
    {
        $student = Student::where('nisn', $nisn)->first();

        if (!$student) {
            return response()->json([
                'found' => false,
                'message' => 'NISN tidak terdaftar. Hubungi admin untuk mendaftarkan NISN kamu.'
            ]);
        }

        if ($student->is_registered) {
            return response()->json([
                'found' => false,
                'message' => 'NISN ini sudah digunakan untuk mendaftar akun lain.'
            ]);
        }

        return response()->json([
            'found' => true,
            'name' => $student->name,
            'kelas' => $student->kelas,
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'nisn' => 'required',
            'phone' => 'required',
            'tanggal_lahir' => 'required',
            'jenis_kelamin' => 'required',
            'photo' => 'required|image',
            'password' => 'required|min:6',
        ]);

        // Validate NISN exists in students table
        $student = Student::where('nisn', $data['nisn'])->first();

        if (!$student) {
            return back()->withErrors(['nisn' => 'NISN tidak terdaftar. Hubungi admin untuk mendaftarkan NISN kamu.'])->withInput();
        }

        if ($student->is_registered) {
            return back()->withErrors(['nisn' => 'NISN ini sudah digunakan untuk mendaftar akun lain.'])->withInput();
        }

        // Check if NISN already exists in users table
        if (User::where('nisn', $data['nisn'])->exists()) {
            return back()->withErrors(['nisn' => 'NISN sudah terdaftar di sistem.'])->withInput();
        }

        $photoPath = $request->file('photo')->store('users', 'public');

        // Create user with data from students table (name & kelas from master data)
        $user = User::create([
            'nisn' => $student->nisn,
            'name' => $student->name,
            'kelas' => $student->kelas,
            'phone' => $data['phone'],
            'tanggal_lahir' => $data['tanggal_lahir'],
            'jenis_kelamin' => $data['jenis_kelamin'],
            'photo' => $photoPath,
            'password' => bcrypt($data['password']),
            'student_id' => $student->id,
            'registration_status' => 'approved', // Auto-approved karena NISN sudah di-whitelist
        ]);

        // Mark student as registered
        $student->update(['is_registered' => true]);

        return redirect('/login')->with('success', 'Registrasi berhasil! Akun kamu langsung aktif.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nisn' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {

        $user = auth()->user();

        // Check if user is hard-banned
        if ($user->isHardBanned()) {
            $banMsg = '🚫 Akun kamu telah di-banned. Alasan: ' . ($user->ban_reason ?? 'Tidak ada alasan.');
            if ($user->ban_expires_at) {
                $banMsg .= ' Berlaku sampai: ' . $user->ban_expires_at->format('d-m-Y H:i');
            }
            Auth::logout();
            return back()->withErrors(['login' => $banMsg]);
        }

        $user->update([
            'is_online' => true,
            'last_seen' => now()
        ]);
        
            return redirect('home');
        }

        return back()->withErrors(['login'=>'NISN atau password salah']);
    }
}
