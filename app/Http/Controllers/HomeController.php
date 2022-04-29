<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->employee_id)
            return redirect()->route('employees.welcome');
        else if ($user->manager)
            return redirect()->route('manager.dashboard');
        else
            return view('home');
    }

    public function welcome()
    {
        return redirect()->route('home');
    }
}
