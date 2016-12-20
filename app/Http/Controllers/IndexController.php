<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Auth; 

class IndexController extends Controller
{
    public function index()
    {
//        return view('index');
        if(Auth::check())
        {

            return view('dashboard');
        }
        else
        {
            return view('index');
//            return view('auth/login');
        }  
    }
}
