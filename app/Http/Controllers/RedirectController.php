<?php

namespace App\Http\Controllers;

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
}
