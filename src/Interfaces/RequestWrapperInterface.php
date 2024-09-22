<?php

namespace tyasa81\RequestWrapper\Interfaces;

use GuzzleHttp\Cookie\CookieJar;

interface RequestWrapperInterface
{
    public function __construct(?CookieJar $cookieJar = null, bool $verify = false, bool $exceptions = false, bool $cookies = false, ?array $basic_auth = null);

    public function request(string $url, $payload = null, bool $json = false, array $headers = [], string $type = 'POST', ?string $proxy_type = null, ?int $proxy_index = null, int $retry = 1, int $sleep = 1, int $timeout = 15, ?bool $allow_redirects = null, ?bool $decode_content = null);
}
