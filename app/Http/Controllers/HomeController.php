<?php

namespace App\Http\Controllers;

use function view;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('body_temperature.index');
    }


    public function showPushMessage()
    {
        return view('pushMessage');
    }
}
