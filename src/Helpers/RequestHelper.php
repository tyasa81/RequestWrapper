<?php

namespace tyasa81\RequestWrapper\Helpers;

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use Illuminate\Support\Facades\Crypt;

class RequestHelper
{
    public static function formatCookieJarToString($cookieJar)
    {
        $string = [];
        foreach ($cookieJar->toArray() as $cookie) {
            $string[] = "{$cookie['Name']}={$cookie['Value']}";
        }

        return implode('; ', $string);
    }

    public static function getCookieValue(CookieJar $cookieJar, $cookieName)
    {
        $value = null;
        foreach ($cookieJar->toArray() as $cookie) {
            if (strtolower($cookie['Name']) == strtolower($cookieName)) {
                $value = $cookie['Value'];
                break;
            }
        }

        return $value;
    }

    public static function convertGuzzleCookieJarToSession(CookieJar $cookieJar)
    {
        $cookies = [];
        foreach ($cookieJar->toArray() as $cookie) {
            $c = [];
            foreach ($cookie as $key => $value) {
                $c[$key] = $value;
            }
            $cookies[] = $c;
        }

        return Crypt::encryptString(json_encode($cookies));
    }

    public static function getCookieJarFromSession($session)
    {
        $cookies = json_decode(Crypt::decryptString($session), true);
        $cookie = [];
        $cookieJar = new CookieJar;
        if (is_array($cookies)) {
            foreach ($cookies as $c) {
                $cookie = $c;
                $cookie_string = "{$c['Name']}={$c['Value']};path={$c['Path']};domain={$c['Domain']}";
                $cookie = SetCookie::fromString($cookie_string);
                $cookieJar->setCookie($cookie);
            }
        }

        return $cookieJar;
    }
}
