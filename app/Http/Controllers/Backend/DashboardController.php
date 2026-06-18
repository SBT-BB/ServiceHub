<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index($page = 'dashboard')
    {
        $allowedPages = ['dashboard'];

        if (in_array($page, $allowedPages) && view()->exists($page)) {
            return view($page);
        }

        abort(404);
    }
}
