<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index');
    }

    public function update(Request $request)
    {
        try {

            $user = auth()->user();

            $request->validate([
                'name' => 'required',
                'kelas' => 'required',
                'phone' => 'required',
                'tanggal_lahir' => 'required|date',
                'jenis_kelamin' => 'required',
                'photo' => 'nullable|image|max:2048',
                'ewallet_type' => 'nullable|string|in:Dana,GoPay,OVO,ShopeePay',
                'ewallet_number' => 'nullable|string|max:20',
            ]);

            if ($request->hasFile('photo')) {

                if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                    Storage::disk('public')->delete($user->photo);
                }

                $user->photo = $request->file('photo')->store('profile', 'public');
            }

            $user->name = $request->name;
            $user->kelas = $request->kelas;
            $user->phone = $request->phone;
            $user->tanggal_lahir = $request->tanggal_lahir;
            $user->jenis_kelamin = $request->jenis_kelamin;
            $user->ewallet_type = $request->ewallet_type;
            $user->ewallet_number = $request->ewallet_number;

            $user->save();

            Log::info('Profile updated successfully', [
                'user_id' => $user->id
            ]);

            return back()->with('success', 'Berhasil diupdate');

        } catch (\Exception $e) {

            Log::error('Profile update failed', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'user_id' => auth()->id()
            ]);

            return back()->with('error', 'Terjadi kesalahan, cek log.');
        }
    }

    public function destroy()
    {
        try {

            $user = Auth::user();

            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            $user->delete();
            Auth::logout();

            Log::info('User deleted account', [
                'user_id' => $user->id
            ]);

            return redirect('/');

        } catch (\Exception $e) {

            Log::error('User delete failed', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'user_id' => auth()->id()
            ]);

            return back()->with('error', 'Terjadi kesalahan, cek log.');
        }
    }

    public function logout(Request $request)
    {
        auth()->user()->update([
            'is_online' => false,
            'last_seen' => now()
        ]);

        Auth::logout();

        return redirect('/login');
    }
}