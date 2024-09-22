<?php

namespace tyasa81\RequestWrapper\Tests\Unit;

use GuzzleHttp\Cookie\CookieJar;
use tyasa81\RequestWrapper\Facades\RequestWrapper;
use tyasa81\RequestWrapper\Tests\TestCase;

class FacadeTest extends TestCase
{
    public function test_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }

    public function test_getUrl(): void
    {
        $response = RequestWrapper::getUrl("https://httpbin.org/ip",true);
        $this->assertEquals( 
            200, 
            $response['code']);
    }

    public function test_getAsync(): void
    {
        $urls = [
            ["index"=>0,"url"=>"https://httpbin.org/ip"],
            ["index"=>1,"url"=>"https://httpbin.org/ip"],
        ];
        $response = RequestWrapper::getAsync($urls,true);
        $this->assertEquals( 
            2, 
            count($response['responses']));
        $this->assertEquals( 
            200, 
            $response['responses'][0]['code']);
        $this->assertEquals( 
            200, 
            $response['responses'][1]['code']);
    }

    public function test_cookiesUpdated(): void
    {
        $guzzle_options['cookies']= new CookieJar();
        $response = RequestWrapper::getUrl("https://httpbin.org/cookies/set?hello=world&test=2",true,$guzzle_options);
        $this->assertEquals( 
            2, 
            count($guzzle_options['cookies']->toArray()));
        $this->assertEquals( 
            "hello", 
            $guzzle_options['cookies']->toArray()[0]['Name']);
        $this->assertEquals( 
            2, 
            $guzzle_options['cookies']->toArray()[1]['Value']);
    }

    public function test_getCookieValue(): void
    {
        $guzzle_options['cookies']= new CookieJar();
        $response = RequestWrapper::getUrl("https://httpbin.org/cookies/set?hello=world&test=2",true,$guzzle_options);
        $this->assertEquals( 
            "world", 
            RequestWrapper::getCookieValue($guzzle_options['cookies'],"hello"));
        $this->assertEquals( 
            "2", 
            RequestWrapper::getCookieValue($guzzle_options['cookies'],"test"));
    }

    public function test_convertCookieJarToSessionAndBack(): void
    {
        $guzzle_options['cookies']= new CookieJar();
        $response = RequestWrapper::getUrl("https://httpbin.org/cookies/set?hello=world&test=2",true,$guzzle_options);
        $session = RequestWrapper::convertGuzzleCookieJarToSession($guzzle_options['cookies']);
        $cookieJar = RequestWrapper::getCookieJarFromSession($session);
        $this->assertEquals( 
            "world", 
            RequestWrapper::getCookieValue($cookieJar,"hello"));
        $this->assertEquals( 
            "2", 
            RequestWrapper::getCookieValue($cookieJar,"test"));
            
    }

    public function test_postUrl(): void
    {
        $response = RequestWrapper::postUrl("https://httpbin.org/post",'{"data":1}',true);
        $this->assertEquals( 
            200, 
            $response['code']);
    }

    public function test_requestCurl(): void
    {
        $response = RequestWrapper::requestCurl("https://httpbin.org/post",'{"data":1}',true,$guzzle_options,"POST");
        $this->assertEquals( 
            200, 
            $response['code']);
    }

    public function test_requestSetCurlCookies(): void
    {
        $guzzle_options['cookies']= new CookieJar();
        $response = RequestWrapper::requestCurl("https://httpbin.org/cookies/set?hello=world&test=2",null,true,$guzzle_options);
        $this->assertEquals( 
            "world", 
            RequestWrapper::getCookieValue($guzzle_options['cookies'],"hello"));
        $this->assertEquals( 
            "2", 
            RequestWrapper::getCookieValue($guzzle_options['cookies'],"test"));
    }

    public function test_requestCurlCookies(): void
    {
        $guzzle_options['cookies']= new CookieJar();
        $response = RequestWrapper::requestCurl("https://httpbin.org/cookies/set?hello=world&test=2",null,true,$guzzle_options);
        $response = RequestWrapper::requestCurl("https://httpbin.org/cookies",null,true,$guzzle_options);
        $this->assertEquals( 
            "world", 
            $response['content']['cookies']['hello']);
        $this->assertEquals( 
            "2", 
            $response['content']['cookies']['test']);
    }
}
