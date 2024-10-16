<?php

namespace tyasa81\RequestWrapper\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see tyasa81\RequestWrapper\Services\GuzzleWrapper
 *
 * @method static mixed get(string $url, bool $json = false, array $headers = [], ?bool $allow_redirects = null, ?bool $decode_content = null)
 * @method static mixed post(string $url, string|array $payload = null, bool $json = false, array $headers = [], ?bool $allow_redirects = null, ?bool $decode_content = null)
 * @method static mixed put(string $url, string|array $payload = null, bool $json = false, array $headers = [], ?bool $allow_redirects = null, ?bool $decode_content = null)
 * @method static mixed delete(string $url, string|array $payload = null, bool $json = false, array $headers = [], ?bool $allow_redirects = null, ?bool $decode_content = null)
 */
class Guzzle extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'guzzlewrapper';
    }
}
