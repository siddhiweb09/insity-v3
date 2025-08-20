<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function leadDashboard(Request $request)
    {
        return view('dashboard.leadDashboard');
    }
}
