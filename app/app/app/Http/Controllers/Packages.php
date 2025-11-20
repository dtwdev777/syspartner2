<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;


class Packages extends Controller
{
 
  
    public function create(Request $request){
 
     
         return Inertia::render('PackageCreate', [
        'package' => '',
    ]);
    }
}
