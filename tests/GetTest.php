<?php

use Threesquared\LaravelWpApi\WpApi;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

class GetTest extends PHPUnit_Framework_TestCase {

    protected static $wp;

    public static function setUpBeforeClass()
    {
        $mock = new MockHandler([
            new Response(200, ['X-WP-Total' => 2, 'X-WP-TotalPages' => 1], json_encode(['posts' => []])),
            new RequestException('Error Communicating with Server', new Request('GET', 'test'))
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        self::$wp = new WpApi('http://test.dev/wp-api', $client);
    }

    public function testCanFetchPosts()
    {
        $response = self::$wp->posts();

        $this->assertEquals($response['results'], ['posts' => []]);
        $this->assertEquals($response['total'], 2);
        $this->assertEquals($response['pages'], 1);
    }

    public function testCanHandlerError()
    {
        $response = self::$wp->posts();

        $this->assertEquals($response['error']['message'], 'Error Communicating with Server');
    }
}
