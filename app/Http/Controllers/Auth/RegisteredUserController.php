<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;


class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
public function store(Request $request): RedirectResponse
{
    $request->validate([
        'nisn' => ['required', 'unique:users'],
        'name' => ['required'],
        'kelas' => ['required'],
        'phone' => ['required'],
        'tanggal_lahir' => ['required'],
        'jenis_kelamin' => ['required'],
        'photo' => ['required','image'],
        'password' => ['required','confirmed'],
    ]);

    $photoPath = $request->file('photo')->store('users','public');

    $user = User::create([
        'nisn' => $request->nisn,
        'name' => $request->name,
        'kelas' => $request->kelas,
        'phone' => $request->phone,
        'tanggal_lahir' => $request->tanggal_lahir,
        'jenis_kelamin' => $request->jenis_kelamin,
        'photo' => $photoPath,
        'password' => Hash::make($request->password),
    ]);

    event(new Registered($user));

    Auth::login($user);

  return redirect()->route('home');

}

}
