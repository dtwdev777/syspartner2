<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Client;
use Throwable;
class HomeView extends Controller
{
    public function index(){
      $clients = Client::all();
      return Inertia::render('Users',[
        "clients" => $clients
      ]);
    }

    public function check(Request $request){
     try{

      $token = $request->input('token');
      $client = Client::where(['token'=>$token])->where('is_active', true)->first();
      if($client['token'] == $token){
       $limit = (int)$client['limit_count'];
      return response('', 200)->header('X-UserId', $client['token'])
                        ->header('X-Max-Sessions', $limit);
                

      }
      else{
        
        return abort(403, 'Forbidden wrong token');
       
      }
     }
     catch(Throwable $err){
        return abort(403, 'Forbidden error');

     }
    }
}
