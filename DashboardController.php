<?php

namespace App\Http\Controllers\adminpanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class DashboardController extends Controller
{
    public function index() {
       
        $user=User::query()->get();
           
        $totaluser=$user->count();
         
    	$data = array(
    		
            'user' => $totaluser,
           
    	);
    	return view('adminpanel.dashboard', compact('data'));
    }
}
