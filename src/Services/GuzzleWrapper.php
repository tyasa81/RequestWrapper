<?php

namespace tyasa81\RequestWrapper\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\ConnectException;
use tyasa81\RequestWrapper\Interfaces\RequestWrapperInterface;

class GuzzleWrapper implements RequestWrapperInterface {
    protected $client;
    protected $headers;
    protected $options;
    protected $cookieJar;

    public function __construct(CookieJar $cookieJar=null, bool $verify=false,bool $exceptions=false,bool $cookies=false,array $basic_auth=null) 
    {
        if(is_array($basic_auth) && count($basic_auth)===2) {
            $this->client = new Client([
                'verify' => $verify,
                'request.options' => [
                    'exceptions' => $exceptions,
                ],
                'cookies'=>$cookies,
                'auth' => $basic_auth
            ]);
        } else {
            $this->client = new Client([
                'verify' => $verify,
                'request.options' => [
                    'exceptions' => $exceptions,
                ],
                'cookies'=>$cookies,
            ]);
        }
        if($cookieJar===null) {
            $this->cookieJar = new CookieJar();
        } else {
            $this->cookieJar = $cookieJar;
        }
    }

    public function request(string $url, $payload=null, bool $json=false, array $headers=array(), string $type="POST", string $proxy_type=null, int $proxy_index=null, int $retry=1, int $sleep=1, int $timeout=15, bool $allow_redirects=null, bool $decode_content=null) 
    {
        $pass = false;
        $return = ['code'=>0, 'content'=>null];
        
        self::setHeaders($headers);
        self::setOptions($allow_redirects,$decode_content);
        
        $this->options['cookies'] = $this->cookieJar;
        $this->options['headers'] = $this->headers;
        $this->options['timeout'] = $timeout;
        $this->options['connect_timeout'] = $timeout-2;
        
        if(is_array($payload)) {
            if(isset($payload['form_params'])) {
                $this->options['form_params'] = $payload['form_params'];
            } elseif(isset($payload['body'])) {
                $this->options['body'] = $payload;
            } elseif(isset($payload['multipart'])) {
                $this->options['multipart'] = $payload['multipart'];
            }
        } elseif(is_string($payload)) {
            $this->options['body'] = $payload;
        }
        
        if($proxy_type === null) {
            $proxies = array();
        } elseif($proxy_type == "SOCKS") {
            $proxies =config("guzzlewrapper.socksproxies");
        } elseif($proxy_type == "FAST") {
            $proxies=config("guzzlewrapper.fastproxies");
        } else {
            $proxies=config("guzzlewrapper.globalproxies");
        }
        if($proxy_index===null || $proxy_index>=count($proxies)) {
            $proxy_index=0;
        }
        $elapsed = microtime(true);
        for($retry_loop = 0; $retry_loop<=$retry; $retry_loop++) {
            if(count($proxies)>0) {
                $proxy_index++;
                if($proxy_index >= count($proxies)) {
                    $proxy_index = 0;
                }
                $this->options['proxy'] = $proxies[$proxy_index];
            }
            try{
                if($type=="HEAD") {
                    $response = $this->client->head($url, $this->options);
                    $return['code'] = $response->getStatusCode();
                    $return['headers'] = $response->getHeaders();
                    $pass = true;
                } else {
                    $response = $this->client->request($type, $url, $this->options);
                    if($response->getStatusCode() >= 200 && ($response->getStatusCode() < 300 || $response->getStatusCode() == 302)) {
                        $content = $response->getBody()->getContents();
                        $return['code'] = $response->getStatusCode();
                        if($json) {
                            $return['content'] = json_decode($content,true);
                        } else {
                            $return['content'] = $content;
                        }
                        $return['headers'] = $response->getHeaders();
                        $pass = true;
                    }
                }
            } catch (ConnectException $e) { // timeout
                $return['code'] =$e->getCode();
                $return['message'] =$e->getMessage();
                try {
                    $return['content'] = $response->getBody()->getContents();
                } catch(\Exception $e) {
                }
            } catch(\Exception $e) { // response code error
                // print("General Exception\n");
                $return['code'] =$e->getCode();
                $return['message'] =$e->getMessage();
                try {
                    if($return['code']!==0) {
                        $return['content'] = $e->getResponse()?$e->getResponse()->getBody()->getContents():"";
                        if($json) {
                            $return['content'] = json_decode($return['content'],true);
                        }
                    }
                } catch(\Exception $e) {
                }
                try {
                    if($return['code']!==0) {
                        $return['headers'] = $e->getResponse()?$e->getResponse()->getHeaders():[];
                    }
                } catch(\Exception $e) {
                }
                try {
                    if($e->getCode() >=400 && $e->getCode() < 500) { // 400 errors do not retry
                        break;
                    }
                } catch(\Exception $e) {
                }
            }
            if($pass == true) { // 500 errors or timeout
                break;
            } elseif($retry_loop!=$retry) {
                sleep($sleep);
            }
        }
        $return['elapsed'] = microtime(true) - $elapsed;
        return $return;
    }
    
    // $urls = [
    //     "index"=>0...n
    //     "url"=>https://...
    //     "type"=>"GET",
    //     "payload"=>null,
    // ]
    public function getAsync(array $url_datas, bool $json=false, array $headers=array(), $allow_redirects=null, $decode_content=null, int $timeout=15)
    {
        $promise = array();
        self::setHeaders($headers);
        self::setOptions($allow_redirects,$decode_content);
        
        $this->options['cookies'] = $this->cookieJar;
        $this->options['headers'] = $this->headers;
        $this->options['timeout'] = $timeout;
        $this->options['connect_timeout'] = $timeout-2;

        $elapsed = microtime(true);
        foreach($url_datas as $index=>$url_data){
            $client = clone $this->client;
            $promise[$url_data['index']] = $client->getAsync($url_data['url'],$this->options)->then(
                function ($response) use($json) {
                    $return['code'] = $response->getStatusCode();
                    $return['content'] = $response->getBody()->getContents();
                    if($json) {
                        $return['content'] = json_decode($return['content'],true);
                    }
                    $return['headers'] = $response->getHeaders();
                    return $return;
                }, function ($exception) use($json) {
                    try {
                        $return['code'] = $exception->getCode();
                    } catch(\Exception $e) {
                        $return['code'] = 0;
                    }
                    $return['message'] = $exception->getMessage();
                    try {
                        if($return['code']!=0) {
                            $return['content'] = $e->getResponse()?$exception->getResponse()->getBody()->getContents():"";
                            if($json) {
                                $return['content'] = json_decode($return['content'],true);
                            }
                        }
                    } catch(\Exception $e) {
                    }
                    try {
                        if($return['code']!=0) {
                            $return['headers'] = $e->getResponse()?$exception->getResponse()->getHeaders():[];
                        }
                    } catch(\Exception $e) {
                    }
                    return $return;
                }
            );
        }
        $responses = array();
        foreach($promise as $key => $p) {
            $responses[$key] = $p->wait();
        }
        $return['elapsed'] = microtime(true) - $elapsed;
        $return['responses'] = $responses;
        return $return;
    }

    public function getCookieJar() {
        return $this->cookieJar;
    }
    
    private function setHeaders(array $headers)
    {
        if(count($headers)) {
            $this->headers = $headers;
        }        
        if(!isset($headers['User-Agent'])) {
            $this->headers['User-Agent'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0';
        }
        if(!isset($headers['Accept'])) {
            $this->headers['Accept'] = '*/*';
        }
        if(!isset($headers['Accept-Encoding'])) {
            $this->headers['Accept-Encoding'] = 'gzip, deflate';
        }
        if(!isset($headers['Accept-Language'])) {
            $this->headers['Accept-Language'] = 'en-US,en;q=0.5';
        }	
        return $this->headers;
    }

    private function setOptions($allow_redirects=null,$decode_content=null)
    {
        if($allow_redirects!==null) {
            $this->options['allow_redirects'] = $allow_redirects;
        } else {
            unset($this->options['allow_redirects']);
        }
        if($decode_content!==null) {
            $this->options['decode_content'] = $decode_content;
        } else {
            unset($this->options['decode_content']);
        }
        
        return $this->options;
    }
}

