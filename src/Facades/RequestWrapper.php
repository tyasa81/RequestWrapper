<?php

namespace tyasa81\RequestWrapper\Facades;

use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Support\Facades\Facade;
use tyasa81\RequestWrapper\Helpers\RequestHelper;
use tyasa81\RequestWrapper\Services\CurlWrapper;
use tyasa81\RequestWrapper\Services\GuzzleWrapper;

/**
 * @see \tyasa81\RequestWrapper\RequestWrapper
 */
class RequestWrapper extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \tyasa81\RequestWrapper\Facades\RequestWrapper::class;
    }

    public static function postUrl($url, $payload, $json = false, &$args = [], $retry = 1, $sleep = 1, $timeout = 15, $type = 'POST')
    {
        $basic_auth = isset($args['basic_auth']) ? $args['basic_auth'] : null;
        $cookieJar = isset($args['cookies']) ? $args['cookies'] : null;
        $guzzlewrapper = new GuzzleWrapper(cookieJar: $cookieJar, basic_auth: $basic_auth);
        $headers = isset($args['headers']) ? $args['headers'] : [];
        $allow_redirects = isset($args['allow_redirects']) ? $args['allow_redirects'] : null;
        $decode_content = isset($args['decode_content']) ? $args['decode_content'] : null;

        return $guzzlewrapper->request(url: $url,
            payload: $payload,
            json: $json,
            headers: $headers,
            type: $type,
            allow_redirects: $allow_redirects,
            decode_content: $decode_content,
            retry: $retry,
            sleep: $sleep,
            timeout: $timeout);
    }

    public static function getUrl($url, $json = false, &$args = [], $retry = 1, $sleep = 1, $timeout = 15)
    {
        $basic_auth = isset($args['basic_auth']) ? $args['basic_auth'] : null;
        $cookieJar = isset($args['cookies']) ? $args['cookies'] : null;
        $guzzlewrapper = new GuzzleWrapper(cookieJar: $cookieJar, basic_auth: $basic_auth);
        $headers = isset($args['headers']) ? $args['headers'] : [];
        $allow_redirects = isset($args['allow_redirects']) ? $args['allow_redirects'] : null;
        $decode_content = isset($args['decode_content']) ? $args['decode_content'] : null;

        return $guzzlewrapper->request(url: $url,
            json: $json,
            headers: $headers,
            type: 'GET',
            allow_redirects: $allow_redirects,
            decode_content: $decode_content,
            retry: $retry,
            sleep: $sleep,
            timeout: $timeout);
    }

    public static function postMultiPartUrl($url, $multipart, $json = false, &$args = [], $retry = 1, $sleep = 1, $timeout = 15, $type = 'POST')
    {
        $basic_auth = isset($args['basic_auth']) ? $args['basic_auth'] : null;
        $cookieJar = isset($args['cookies']) ? $args['cookies'] : null;
        $guzzlewrapper = new GuzzleWrapper(cookieJar: $cookieJar, basic_auth: $basic_auth);
        $headers = isset($args['headers']) ? $args['headers'] : [];
        $allow_redirects = isset($args['allow_redirects']) ? $args['allow_redirects'] : null;
        $decode_content = isset($args['decode_content']) ? $args['decode_content'] : null;

        return $guzzlewrapper->request(url: $url,
            json: $json,
            payload: ['multipart' => $multipart],
            headers: $headers,
            type: $type,
            allow_redirects: $allow_redirects,
            decode_content: $decode_content,
            retry: $retry,
            sleep: $sleep,
            timeout: $timeout);
    }

    public static function getAsync($url_datas, $json = false, $args = [], $timeout = 15)
    {
        $basic_auth = isset($args['basic_auth']) ? $args['basic_auth'] : null;
        $cookieJar = isset($args['cookies']) ? $args['cookies'] : null;
        $guzzlewrapper = new GuzzleWrapper(cookieJar: $cookieJar, basic_auth: $basic_auth);
        $headers = isset($args['headers']) ? $args['headers'] : [];
        $allow_redirects = isset($args['allow_redirects']) ? $args['allow_redirects'] : null;
        $decode_content = isset($args['decode_content']) ? $args['decode_content'] : null;

        return $guzzlewrapper->getAsync(url_datas: $url_datas,
            json: $json,
            headers: $headers,
            allow_redirects: $allow_redirects,
            decode_content: $decode_content,
            timeout: $timeout);
    }

    public static function UrlwithProxy($url, $json = false, &$args = [], $proxy_type = 'FAST', $type = 'GET', $retry = 5, $sleep = 0, $timeout = 15)
    {
        $basic_auth = isset($args['basic_auth']) ? $args['basic_auth'] : null;
        $cookieJar = isset($args['cookies']) ? $args['cookies'] : null;
        $guzzlewrapper = new GuzzleWrapper(cookieJar: $cookieJar, basic_auth: $basic_auth);
        $headers = isset($args['headers']) ? $args['headers'] : [];
        $allow_redirects = isset($args['allow_redirects']) ? $args['allow_redirects'] : null;
        $decode_content = isset($args['decode_content']) ? $args['decode_content'] : null;

        return $guzzlewrapper->request(url: $url,
            json: $json,
            payload: $args,
            headers: $headers,
            type: $type,
            proxy_type: $proxy_type,
            allow_redirects: $allow_redirects,
            decode_content: $decode_content,
            retry: $retry,
            sleep: $sleep,
            timeout: $timeout);
    }

    public static function requestCurlProxyRetry($url, $payload, $json = false, &$args = [], $type = 'GET', $proxy_type = 'GLOBAL', $proxy_index = null, $timeout = 20, $retry = 4)
    {
        $basic_auth = isset($args['basic_auth']) ? $args['basic_auth'] : null;
        $cookieJar = isset($args['cookies']) ? $args['cookies'] : null;
        $curlwrapper = new CurlWrapper(cookieJar: $cookieJar, basic_auth: $basic_auth);
        $headers = isset($args['headers']) ? $args['headers'] : [];
        $allow_redirects = isset($args['allow_redirects']) ? $args['allow_redirects'] : null;
        $decode_content = isset($args['decode_content']) ? $args['decode_content'] : null;
        $is_http2 = isset($args['http2']) ? $args['http2'] : null;
        $response = null;
        for ($try = 0; $try < $retry; $try++) {
            $response = $curlwrapper->request(url: $url,
                json: $json,
                payload: $payload,
                headers: $headers,
                type: $type,
                proxy_type: $proxy_type,
                proxy_index: $proxy_index,
                allow_redirects: $allow_redirects,
                decode_content: $decode_content,
                retry: $retry,
                timeout: $timeout,
                is_http2: $is_http2);
            if ($response['code'] >= 200 && $response['code'] < 400) {
                break;
            }
        }

        return $response;
    }

    public static function requestCurlProxy($url, $payload, $json = false, &$args = [], $type = 'GET', $proxy_type = 'GLOBAL', $proxy_index = null, $timeout = 20)
    {
        $basic_auth = isset($args['basic_auth']) ? $args['basic_auth'] : null;
        $cookieJar = isset($args['cookies']) ? $args['cookies'] : null;
        $curlwrapper = new CurlWrapper(cookieJar: $cookieJar, basic_auth: $basic_auth);
        $headers = isset($args['headers']) ? $args['headers'] : [];
        $allow_redirects = isset($args['allow_redirects']) ? $args['allow_redirects'] : null;
        $decode_content = isset($args['decode_content']) ? $args['decode_content'] : null;
        $is_http2 = isset($args['http2']) ? $args['http2'] : null;

        return $curlwrapper->request(url: $url,
            json: $json,
            payload: $payload,
            headers: $headers,
            type: $type,
            proxy_type: $proxy_type,
            proxy_index: $proxy_index,
            allow_redirects: $allow_redirects,
            decode_content: $decode_content,
            timeout: $timeout,
            is_http2: $is_http2);
    }

    public static function requestCurl($url, $payload, $json = false, &$args = [], $type = 'GET', $proxy_type = null, $proxy_index = null, $timeout = 15, $retry = 0, $sleep = 0)
    {
        $basic_auth = isset($args['basic_auth']) ? $args['basic_auth'] : null;
        $cookieJar = isset($args['cookies']) ? $args['cookies'] : null;
        $curlwrapper = new CurlWrapper(cookieJar: $cookieJar, basic_auth: $basic_auth);
        $headers = isset($args['headers']) ? $args['headers'] : [];
        $allow_redirects = isset($args['allow_redirects']) ? $args['allow_redirects'] : null;
        $decode_content = isset($args['decode_content']) ? $args['decode_content'] : null;
        $is_http2 = isset($args['http2']) ? $args['http2'] : null;

        return $curlwrapper->request(url: $url,
            json: $json,
            payload: $payload,
            headers: $headers,
            type: $type,
            proxy_type: $proxy_type,
            proxy_index: $proxy_index,
            allow_redirects: $allow_redirects,
            decode_content: $decode_content,
            retry: $retry,
            sleep: $sleep,
            timeout: $timeout,
            is_http2: $is_http2);
    }

    // Helpers

    public static function formatCookieJarToString(CookieJar $cookieJar)
    {
        return RequestHelper::formatCookieJarToString($cookieJar);
    }

    public static function getCookieValue(CookieJar $cookieJar, string $cookieName)
    {
        return RequestHelper::getCookieValue($cookieJar, $cookieName);
    }

    public static function convertGuzzleCookieJarToSession(CookieJar $cookieJar)
    {
        return RequestHelper::convertGuzzleCookieJarToSession($cookieJar);
    }

    public static function getCookieJarFromSession(string $session)
    {
        return RequestHelper::getCookieJarFromSession($session);
    }
}
