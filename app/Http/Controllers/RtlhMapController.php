<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RtlhMapController extends Controller
{
    public function index(Request $request): View
    {
        return view('rtlh.map');
    }
}