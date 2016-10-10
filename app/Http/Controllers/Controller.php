<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Menu;
use View;
use Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;    
    
    public function __construct()
    {
       // if(Auth::check())
       // {
            View::composer('layouts/sidebar', function($view){
                $menus = Menu::menuLoad();
                $view->with('menus', $menus);
            });
      //  }
    }


}
