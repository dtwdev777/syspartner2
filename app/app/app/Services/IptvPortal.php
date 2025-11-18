<?php 

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use DebugBar\DebugBar;
use Throwable;

class IptvPortal
{
  private $url = "";
  private $login = "";
  private $password = "";
  private $result = "";
  private $token = "";
  protected $id =1;

  public function __construct()
  {
    $this->url  = env('IPTV_URL');
    $this->login  = env('IPTV_LOGIN');
    $this->password = env('IPTV_PASSWORD');
    $token = $this->get_token();
    if ($token) {
   
      $this->token = $token->result->session_id;
    }
  }

  /**
   * get auth token
   */
  private function get_token()
  {

    $databody  =   [
      "jsonrpc" => "2.0",
      "id" => 1.0,
      "method" => "authorize_user",
      "params" => [
        "username" => $this->login,
        "password" => $this->password
      ]
    ];

    $params = ['json' => $databody];
    $header_opt = ['verify' => false, 'timeout'  => 60, 'headers' => ['User-Agent' => "User-Agent: Mozilla/5.0", 'Content-Type' => 'application/json']];
    $client = new Client($header_opt);
    $promise =  $client->postAsync($this->url . '/jsonrpc/', $params);
    $promise->then(
      function ($response) {
        $res =  $response->getBody();
        $this->headers = $response->getHeaders();
        if ($response->getStatusCode()  == 200) {

          $this->result = json_decode($res);
        }
      },
      function ($error) {

        echo $error->getMessage();
      }
    );
    $promise->wait();
    return $this->result;
  }
  /**
   * add account
   */
  public function add_account($data)
  {
    $databody  =   [
      "jsonrpc" => "2.0",
      "id" => $this->id++,
      "method" => "insert",
      "params" => [
        "into" => "subscriber",
        "columns" => ["username", "password", "surname", "email"],
        "values" => [["{$data['login']}", "{$data['password']}", "{$data['full_name']}", "laratest@duga.tv"]],
        "returning" => "id"
      ]
    ];
    $params = ['json' => $databody];
    $header_opt = ['verify' => false, 'timeout'  => 60, 'headers' => ['Iptvportal-Authorization' => "sessionid={$this->token}", 'User-Agent' => "User-Agent: Mozilla/5.0", 'Content-Type' => 'application/json']];
    $client = new Client($header_opt);
    $promise =  $client->postAsync($this->url . '/jsonsql/', $params);
  
    $promise->then(
      function ($response) {
        $res =  $response->getBody();
        $this->headers = $response->getHeaders();
        if ($response->getStatusCode()  == 200) {

          $this->result = json_decode($res);
        }
      },
      function ($error) {

        echo $error->getMessage();
      }
    );
    $promise->wait();
     $this->set_tarrif($this->result->result[0], $data['tariff_plan'] ,$data['expire']);
    
    return $this->result;
  }
  /**
   *  get all tariffs
   */
  public function get_tariffs()
  {

    $params = [
      "jsonrpc" => "2.0",
      "id" => 2,
      "method" => "select",
      "params" => [
        "data" => ["id", "name"],
        "from" => "package"
      ]
    ];
    $params = ['json' => $params];
    $header_opt = ['verify' => false, 'timeout'  => 60, 'headers' => ['Iptvportal-Authorization' => "sessionid={$this->token}", 'User-Agent' => "User-Agent: Mozilla/5.0", 'Content-Type' => 'application/json']];
    $client = new Client($header_opt);
    $promise =  $client->postAsync($this->url . '/jsonsql/', $params);
  
    $promise->then(
      function ($response) {
        $res =  $response->getBody();
        $this->headers = $response->getHeaders();
        if ($response->getStatusCode()  == 200) {

          $this->result = json_decode($res);
        
        }
      },
      function ($error) {

        echo $error->getMessage();
      }
    );
    $promise->wait();
    return $this->result;
  
  }

  /**
   * add user id 
   */
  public function get_user_id($user)
  {
    $params = [
      "jsonrpc" => "2.0",
      "id" => 2,
      "method" => "select",
      "params" => [
        "data" => ["id"],
        "from" => "subscriber",
        "where" =>  ["eq" => ["username", "$user"]],
      ]
    ];
    $params = ['json' => $params];
    $header_opt = ['verify' => false, 'timeout'  => 60, 'headers' => ['Iptvportal-Authorization' => "sessionid={$this->token}", 'User-Agent' => "User-Agent: Mozilla/5.0", 'Content-Type' => 'application/json']];
    $client = new Client($header_opt);
    $promise =  $client->postAsync($this->url . '/jsonsql/', $params);
    $promise->then(
      function ($response) {
        $res =  $response->getBody();
        $this->headers = $response->getHeaders();
        if ($response->getStatusCode()  == 200) {

          $this->result = json_decode($res);
        }
      },
      function ($error) {

        echo $error->getMessage();
      }
    );
    $promise->wait();
    return $this->result;
  }
  /**
   * set tariff
   */
  public function set_tarrif($user_id, $package_id, $expire=null)
  {
    $data = [];
   
   foreach($package_id   as $key=> $val){
     array_push($data ,[$user_id, $val , $expire ,true]) ;
   }
   

    \Debugbar::info("info",$this->id++);
    $databody  =   [
      "jsonrpc" => "2.0",
      "id" => $this->id++,
      "method" => "insert",
      "params" => [
        "into" => "subscriber_package",
        "columns" => ["subscriber_id", "package_id" ,"expired_on","enabled"],
        "values" => $data ,
        "returning" => "id"
      ]
    ];
  

    $params = ['json' => $databody];
    $header_opt = ['verify' => false, 'timeout'  => 60, 'headers' => ['Iptvportal-Authorization' => "sessionid={$this->token}", 'User-Agent' => "User-Agent: Mozilla/5.0", 'Content-Type' => 'application/json']];
    $client = new Client($header_opt);
    $promise =  $client->postAsync($this->url . '/jsonsql/', $params);
  
    $promise->then(
      function ($response) {
        $res =  $response->getBody();
        $this->headers = $response->getHeaders();
        if ($response->getStatusCode()  == 200) {

          $this->result = json_decode($res);
          \Debugbar::info("responce from api server", $this->result);
        }
      },
      function ($error) {
        \Debugbar::info("responce from api server", $error->getMessage());
        echo $error->getMessage();
      }
    );
  
 
    $promise->wait();
    \Debugbar::info("responce from api server", $this->result);
   // dd("stop");
    return $this->result;
  }

  public function info_debug(){
    try{
      $databody  =   [
        "jsonrpc" => "2.0",
        "id" => $this->id++,
        "method" => "select",
        "params" =>[
        "data" =>["subscriber_id", "package_id" ,"expired_on"],
        "from" => "subscriber_package"
        ]
      ];
  
      $params = ['json' => $databody];
      $header_opt = ['verify' => false, 'timeout'  => 60, 'headers' => ['Iptvportal-Authorization' => "sessionid={$this->token}", 'User-Agent' => "User-Agent: Mozilla/5.0", 'Content-Type' => 'application/json']];
      $client = new Client($header_opt);
      $promise =  $client->postAsync($this->url . '/jsonsql/', $params);
      $promise->then(
        function ($response) {
          $res =  $response->getBody();
          $this->headers = $response->getHeaders();
          if ($response->getStatusCode()  == 200) {
  
            $this->result = json_decode($res);
            \Debugbar::info("from subscriber", $this->result);
          }
        },
        function ($error) {
          \Debugbar::info("responce from api server", $error->getMessage());
          echo $error->getMessage();
        }
      );
    
    
      $promise->wait();
      return $this->result;

    }
    catch(Throwable $err){
      echo $err->getMessage();
    }
  }

  /**
   * disable account
   */

  public function disable_account($user, $status)
  {

    $databody = [
      "jsonrpc" => "2.0",
      "id" => 2,
      "method" => "update",
      "params" => [
        "table" => "subscriber",
        "set" => ["disabled" => $status],
        "where" =>  ["eq" => ["username", "$user"]],
        "returning" => "id",
      ]
    ];
    $params = ['json' => $databody];
    $header_opt = ['verify' => false, 'timeout'  => 60, 'headers' => ['Iptvportal-Authorization' => "sessionid={$this->token}", 'User-Agent' => "User-Agent: Mozilla/5.0", 'Content-Type' => 'application/json']];
    $client = new Client($header_opt);
    $promise =  $client->postAsync($this->url . '/jsonsql/', $params);
    $promise->then(
      function ($response) {
        $res =  $response->getBody();
        $this->headers = $response->getHeaders();
        if ($response->getStatusCode()  == 200) {

          $this->result = json_decode($res);
        }
      },
      function ($error) {

        echo $error->getMessage();
      }
    );
    $promise->wait();
    return $this->result;
  }
  
  public function destroy_account($user){
    $databody = [
      "jsonrpc" => "2.0",
      "id" => 2,
      "method" => "delete",
      "params" => [
        "from" => "subscriber",
        "where" =>  ["eq" => ["username", "$user"]],
        "returning" => "id",
      ]
    ];
    $params = ['json' => $databody];
    $header_opt = ['verify' => false, 'timeout'  => 60, 'headers' => ['Iptvportal-Authorization' => "sessionid={$this->token}", 'User-Agent' => "User-Agent: Mozilla/5.0", 'Content-Type' => 'application/json']];
    $client = new Client($header_opt);
    $promise =  $client->postAsync($this->url . '/jsonsql/', $params);
    $promise->then(
      function ($response) {
        $res =  $response->getBody();
        $this->headers = $response->getHeaders();
        if ($response->getStatusCode()  == 200) {

          $this->result = json_decode($res);
        }
      },
      function ($error) {

        echo $error->getMessage();
      }
    );
    $promise->wait();
    return $this->result;
  }

  public function delete_tarrif_by_id($id){
    try{
      $databody = [
        "jsonrpc" => "2.0",
        "id" => $this->id++,
        "method" => "delete",
        "params" => [
          "from" => "subscriber_package",
          "where" =>  ["eq" => ["subscriber_id", "$id"]],
          "returning" => "id",
        ]
      ];
      $params = ['json' => $databody];
      $header_opt = ['verify' => false, 'timeout'  => 60, 'headers' => ['Iptvportal-Authorization' => "sessionid={$this->token}", 'User-Agent' => "User-Agent: Mozilla/5.0", 'Content-Type' => 'application/json']];
      $client = new Client($header_opt);
      $promise =  $client->postAsync($this->url . '/jsonsql/', $params);
      $promise->then(
        function ($response) {
          $res =  $response->getBody();
          $this->headers = $response->getHeaders();
          if ($response->getStatusCode()  == 200) {
  
            $this->result = json_decode($res);
          }
        },
        function ($error) {
  
          echo $error->getMessage();
        }
      );
      $promise->wait();
      return $this->result;

    }
    catch(Throwable $err){
       dd($err->getMessage());
    }
  }

  public function change_tarrifs($data){
    try{
      $res = $this-> get_user_id($data['id']);
     
      if($res->result[0][0] != null) {
       $this->delete_tarrif_by_id($res->result[0][0]);
       $this->set_tarrif($res->result[0][0], $data['tariffs']);
      }

    }
  
    catch(Throwable $err){
      dd($err->getMessage());
   }
   
  
 }

}