<?php

namespace App\Http\Controllers\Contributor;

use Illuminate\Http\Request;

class DashboardController
{
    /**
     * Show dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('contributor.index');
    }
}
