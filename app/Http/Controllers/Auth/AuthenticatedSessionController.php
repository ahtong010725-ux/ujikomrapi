<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        // Check registration status
        if ($user->registration_status === 'pending') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return back()->withErrors([
                'login' => 'Akun anda masih menunggu persetujuan admin. Silakan tunggu hingga akun diverifikasi.'
            ]);
        }

        if ($user->registration_status === 'rejected') {
            $reason = $user->rejection_reason ?? 'Tidak ada alasan yang diberikan.';
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return back()->withErrors([
                'login' => 'Akun anda ditolak oleh admin. Alasan: ' . $reason
            ]);
        }

        // Set online status
        $user->update([
            'is_online' => true,
            'last_seen' => now()
        ]);

        $request->session()->regenerate();

        return redirect()->route('home');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            $user->update([
                'is_online' => false,
                'last_seen' => now()
            ]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
