# Simple Request Wrapper

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tyasa81/requestwrapper.svg?style=flat-square)](https://packagist.org/packages/tyasa81/requestwrapper)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/tyasa81/requestwrapper/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/tyasa81/requestwrapper/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/tyasa81/requestwrapper/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/tyasa81/requestwrapper/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/tyasa81/requestwrapper.svg?style=flat-square)](https://packagist.org/packages/tyasa81/requestwrapper)

This is a wrapper for request using Guzzle and Curl. It is mainly used to simplify request code in a request heavy project that deals with cookies and proxy. Facade are made for backward compatiblity on previous project where a lot of static methods are used. Refactoring code into a service class and package are made since it made using Guzzle and Curl much cleaner even for projects that are not cookies or proxy centric.

## Installation

You can install the package via composer:

```bash
composer require tyasa81/requestwrapper
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="requestwrapper-config"
```

## Usage

Using Facades:

```php
use tyasa81\RequestWrapper\Facades\RequestWrapper;
$response = RequestWrapper::getUrl("https://httpbin.org/ip",true);
dd($response);
```

Available functions:
    public static function postUrl($url, $payload, $json=false, &$args=array(), $retry=1, $sleep=1, $timeout=15, $type="POST") 
    public static function getUrl($url, $json=false, &$args=array(), $retry=1, $sleep=1, $timeout=15) 
    public static function postMultiPartUrl($url, $multipart, $json=false, &$args=array(), $retry=1, $sleep=1, $timeout=15, $type="POST") 
    public static function getAsync($url_datas, $json=false, $args=array(), $timeout=15) 
    public static function UrlwithProxy($url, $json=false, &$args=array(), $proxy_type="FAST", $type="GET", $retry=5, $sleep=0, $timeout=15) 
    public static function requestCurlProxyRetry($url, $payload, $json=false, &$args=array(), $type="GET", $proxy_type="GLOBAL", $proxy_index=null, $timeout=20,$retry = 4)
    public static function requestCurlProxy($url, $payload, $json=false, &$args=array(), $type="GET", $proxy_type="GLOBAL", $proxy_index=null, $timeout=20)
    public static function requestCurl($url, $payload, $json=false, &$args=array(), $type="GET", $proxy_type=null, $proxy_index=null, $timeout=15,$retry=0,$sleep=0)
    
    // Helpers Functions
    public static function formatCookieJarToString(CookieJar $cookieJar)
    public static function getCookieValue(CookieJar $cookieJar, string $cookieName)
    public static function convertGuzzleCookieJarToSession(CookieJar $cookieJar)
    public static function getCookieJarFromSession(string $session) 

Using Service Class:
    $curlwrapper = new CurlWrapper(cookieJar: $cookieJar);
    $response = $curlwrapper->request(url: "https://httpbin.org/ip", json: true);
    OR
    $guzzlewrapper = new GuzzleWrapper(cookieJar: $cookieJar);
    $response = $guzzlewrapper->request(url: "https://httpbin.org/ip", json: true);
    
Interface:
    request(string $url, $payload=null, bool $json=false, array $headers=array(), string $type="POST", string $proxy_type=null, int $proxy_index=null, int $retry=1, int $sleep=1, int $timeout=15, bool $allow_redirects=null, bool $decode_content=null)


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Tony Yasa](https://github.com/tyasa81)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
