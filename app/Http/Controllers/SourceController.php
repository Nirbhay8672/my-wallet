<?php

namespace App\Http\Controllers;

use Inertia\Response;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class SourceController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('source/Index',[
            'page_name' => 'Sources',
            'roles' => Role::all(),
        ]);
    }
}
