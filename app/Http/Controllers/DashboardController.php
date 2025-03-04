<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::user();

        return view('dashboard', [
            'generations' => $user->imageGenerations()
                ->latest()
                ->take(5)
                ->get(),
            'activities' => $user->activities()
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}
