<?php

namespace tyasa81\RequestWrapper\Contracts;

use GuzzleHttp\Cookie\CookieJar;

abstract class BaseRequestWrapper
{
    abstract public function __construct(?CookieJar $cookieJar = null, bool $verify = false, bool $exceptions = false, bool $cookies = false, ?array $basic_auth = null);

    abstract public function request(string $url, $payload = null, bool $json = false, array $headers = [], string $type = 'POST', ?string $proxy_type = null, ?int $proxy_index = null, int $retry = 1, int $sleep = 1, int $timeout = 15, ?bool $allow_redirects = null, ?bool $decode_content = null);

    public function get(string $url, bool $json = false, array $headers = [], ?bool $allow_redirects = null, ?bool $decode_content = null)
    {
        $this->request(url: $url, json: $json, headers: $headers, type: 'GET', allow_redirects: $allow_redirects, decode_content: $decode_content);
    }

    public function post(string $url, $payload = null, bool $json = false, array $headers = [], ?bool $allow_redirects = null, ?bool $decode_content = null)
    {
        $this->request(url: $url, payload: $payload, json: $json, headers: $headers, type: 'POST', allow_redirects: $allow_redirects, decode_content: $decode_content);
    }
}
