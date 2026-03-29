<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function coach()
    {
        return view('coach.dashboard');
    }

    public function manager()
    {
        return view('manager.dashboard');
    }

    public function player()
    {
        return view('player.dashboard');
    }
}
