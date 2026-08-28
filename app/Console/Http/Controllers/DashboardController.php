<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class DashboardController extends Controller
{
    public function dashboard()
    {
         return view('dashboard');
    }
}
