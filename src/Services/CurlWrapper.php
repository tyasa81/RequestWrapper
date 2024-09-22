<?php

namespace tyasa81\RequestWrapper\Services;

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use tyasa81\RequestWrapper\Interfaces\RequestWrapperInterface;

class CurlWrapper implements RequestWrapperInterface {
    protected $cookieJar;
    
    public function __construct(CookieJar $cookieJar=null, bool $verify=false,bool $exceptions=false,bool $cookies=false,array $basic_auth=null) 
    {
        if($cookieJar===null) {
            $this->cookieJar = new CookieJar();
        } else {
            $this->cookieJar = $cookieJar;
        }
    }

    public function request(string $url, $payload=null, bool $json=false, array $headers=array(), string $type="POST", string $proxy_type=null, int $proxy_index=null, int $retry=1, int $sleep=1, int $timeout=15, bool $allow_redirects=null, bool $decode_content=null, bool $is_http2 = null, bool $verbose=false)
    {
        // return ['code'=>200,'url'=>$url,'payload'=>$payload,'json'=>$json,'args'=>$args,'timeout'=>$timeout,'type'=>$type];
        $debug = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        if($type == "POST") {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$payload);
        }
        $is_encoded = "";
        $curl_headers = array();
        if(count($headers)>0) {
            foreach($headers as $key=>$value) {
                if(stripos($key,'Accept-Encoding') !== false && $value) {
                    $is_encoded=true;
                } elseif(stripos($key,'cookie') !== false) {
                    continue;
                }
                $curl_headers[] = "{$key}: {$value}";
            }
        }
        $base_url = substr($url,8,strpos($url,"/",9)-8);
        $cookie_strings=array();
        foreach($this->cookieJar->toArray() as $cookie) {
            if(strpos($cookie['Domain'],$base_url)!==false) {
                $cookie_strings[] = "{$cookie['Name']}={$cookie['Value']};";
            }
        }
        if(count($cookie_strings)) {
            $curl_headers[] = "Cookie: ".implode(";",$cookie_strings);
        }
        if(count($curl_headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);
            $debug['headers'] = $curl_headers;
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        
        // setup proxies
        $proxies = array();
        if($proxy_type == "FAST") {
            $proxies=config("guzzlewrapper.fastproxies");
        } elseif($proxy_type == "SOCKS") {
            $proxies = config("guzzlewrapper.socksproxies");
        } elseif($proxy_type !== null) {
            $proxies =  config("guzzlewrapper.globalproxies");
        }

        if(count($proxies)) {
            $proxy_index=0;
            if(count($proxies)>1) {
                $proxy_index = mt_rand(0,count($proxies)-1);
            }
            curl_setopt($ch, CURLOPT_PROXY, $proxies[$proxy_index]);
            // curl_setopt($ch, CURLOPT_VERBOSE, true);
            $debug['proxy_index'] = $proxy_index;
        }
        
        if($is_http2) {
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
        }
        if($is_encoded) {
            curl_setopt($ch,CURLOPT_ENCODING , "");
        }
        $debug['proxy'] = $proxies;
        if($allow_redirects) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $headers = array();
        curl_setopt($ch, CURLOPT_HEADERFUNCTION,
            function($curl, $header) use (&$headers)
            {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2) // ignore invalid headers
                return $len;

                $headers[str_replace(" ","-",ucwords(str_replace("-"," ",trim($header[0]))))][] = trim($header[1]);
                return $len;
            }
        );

        if($verbose) {
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
        }
        
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        $error = curl_error($ch);
        $errno = curl_errno($ch);

        if (is_resource($ch)) {
            curl_close($ch);
        }
        if($json) {
            try {
                $json = json_decode($response,true);
                $response = $json;
            } catch(\Exception $e) {
                $debug['content']=$response;
            }
        } 
        $debug['info']=$info;
        $debug['error'] = $error;
        $debug['errno'] = $errno;
        foreach($headers as $key=>$values) {
            if($key=="Set-Cookie") {
                foreach($values as $value) {
                    self::setCookie($value, $url);
                }
            }
        }
        return ['code'=>$info['http_code'],"content"=>$response,'headers'=>$headers,'debug'=>$debug];
    }

    private function setCookie(string $cookie_string, $url) {
        if(stripos($cookie_string,"domain")===false) {
            $base = substr($url,8,strpos($url,"/",9)-8);
            $cookie_string.=";Domain=$base";
        }
        $cookie = SetCookie::fromString($cookie_string);
        $this->cookieJar->setCookie($cookie);
    }
}

