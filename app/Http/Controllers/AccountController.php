<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showSettingsForm()
    {
        return view('admin.account-settings');
    }

    public function updateName(Request $request)
    {
        $request->validate([
            'new_name' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        $user = Auth::user();
        $user->name = $request->new_name;
        $user->save();

        return back()->with('success', 'Nama berhasil diperbarui!');
    }

    public function updateEmail(Request $request)
    {
        $request->validate([
            'current_email' => ['required', 'email', 'current_email'],
            'new_email' => ['required', 'email', 'unique:users,email', 'max:255'],
            'new_email_confirmation' => ['required', 'same:new_email'],
        ]);

        $user = Auth::user();
        $user->email = $request->new_email;
        $user->save();

        return back()->with('success', 'Email berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password berhasil diperbarui!');
    }
}