<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectController extends Controller
{
    public function redirectToRoleBasedDashboard()
    {
        // if management, redirect to /management/dashboard
        $user = auth()->user();

        // check if not has user, redirect to login
        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->role === 'management') {
            return redirect('/management/dashboard');
        } elseif ($user->role === 'kasir') {
            return redirect('/kasir/dashboard');
        } else {
            return redirect('/login');
        }
    }

    /**
     * Logout user and redirect to login page
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('status', 'Anda telah berhasil logout.');
    }
}
