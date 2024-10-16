<?php

namespace tyasa81\RequestWrapper\Tests\Feature;

use tyasa81\RequestWrapper\Facades\Guzzle;
use tyasa81\RequestWrapper\Tests\TestCase;

// use Illuminate\Foundation\Testing\RefreshDatabase;

class GuzzleTest extends TestCase
{
    public function test_getUrl(): void
    {
        $response = Guzzle::get('https://httpbin.org/ip', true);
        $this->assertEquals(
            200,
            $response['code']);
    }

    public function test_getUrl400Status(): void
    {
        $response = Guzzle::get('https://httpbin.org/status/400', true);
        $this->assertEquals(
            400,
            $response['code']);
    }

    public function test_getAsync(): void
    {
        $urls = [
            ['index' => 0, 'url' => 'https://httpbin.org/ip'],
            ['index' => 1, 'url' => 'https://httpbin.org/ip'],
        ];
        $response = Guzzle::getAsync($urls, true);
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

    public function test_postUrl(): void
    {
        $response = Guzzle::post('https://httpbin.org/post', '{"data":1}', true);
        $this->assertEquals([
            200,
            '{"data":1}',
        ], [
            $response['code'],
            $response['content']['data'],
        ]);
    }

    public function test_putUrl(): void
    {
        $response = Guzzle::put('https://httpbin.org/put', '{"data":1}', true);
        // print_r($response);
        $this->assertEquals([
            200,
            '{"data":1}',
        ], [
            $response['code'],
            $response['content']['data'],
        ]);
    }

    public function test_deleteUrl(): void
    {
        $response = Guzzle::delete('https://httpbin.org/delete', '{"data":1}', true);
        // print_r($response);
        $this->assertEquals([
            200,
            '{"data":1}',
        ], [
            $response['code'],
            $response['content']['data'],
        ]);
    }
}
