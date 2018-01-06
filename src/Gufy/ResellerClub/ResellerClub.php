<?php 
namespace Gufy\ResellerClub;

use GuzzleHttp\Client;

class ResellerClub {
    
    private $config;
    private $prefix = 'https://httpapi.com/api/';
    
    public function __construct($user_id, $api_key) {
        return $this->config('params', ['auth-userid'=>$user_id,'api-key'=>$api_key]);
    }
    public function config($name, $value='') {
        if(func_num_args()==1) {
            if(isset($this->config[$name]))
            return $this->config[$name];
            else
            return null;
        }
        
        $this->config[$name] = $value;
        
        return $this;
    }
    public function pretend($value=false) {
        return $this->config('pretend', $value);
    }
    
    public function from($url) {
        return $this->config('url', $this->prefix.$url.'.json');
    }
    public function get($url='') {
        return $this->call('GET', $url);
    }
    public function post($url='') {
        return $this->call('POST', $url);
    }
    private function call($method, $url='') {
        
        if(!empty($url))
            $this->from($url);
        
        $this->config('method', $method);
        
        if($this->config('params')==null)
            $this->config('params', []);
        
        if($this->config('pretend'))
            return [];
    
        try {
            $client = new Client;
            $response = $client->request($method, $this->config('url'), [
                'query'=>$this->config('params'),
            ]);
            
            return json_decode($response->getBody());
        }
        catch(\GuzzleHttp\Exception\ConnectException $e) {
            return [
                'content'=>(string)$e->getMessage(),
                'result'=>'error'
            ];
        }
        catch(\GuzzleHttp\Exception\RequestException $e) {
            return [
                'content'=>(string)$e->getMessage(),
                'result'=>'error'
            ];
        }
    
    }
    
    public function where($name, $value='') {
        
        if(func_num_args()==1 && is_array($name)) {
            foreach($name as $key=>$value) {
                $this->where($key, $value);
            }
        } else {
            $params = $this->config('params');
            $params[$name] = $value;
            $this->config('params', $params);
        }
        return $this;
        
    }
    
}
