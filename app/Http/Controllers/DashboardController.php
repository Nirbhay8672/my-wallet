<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use App\Models\Website;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
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

    public function index(): Response
    {
        return Inertia::render('Dashboard', [
            'total_users' => User::all()->count(),
            'page_name' => 'Dashboard'
        ]);
    }
}
