<?php


namespace App\Tests\Controller;


use App\Controller\ApiRequestController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiRequestControllerTest extends WebTestCase{

    /**@var Client $client*/
    private static $client;

    public static $username;
    public static $usernameError;

    public static function setUpBeforeClass(){
        self::$client = static::createClient();
        self::$username = 'user7';
        self::$usernameError = 'asdf';
    }

    /**
     * Implements testGetRequests
     * @covers ::getRequests
     */
    public function testGetRequests(): void{
        self::$client->request(Request::METHOD_GET, ApiRequestController::REQUEST_API_PATH . '/' . self::$username);
        $body = self::$client->getResponse()->getContent();
        self::assertJson($body);

        $data = json_decode($body, true);
        self::assertArrayHasKey('requests', $data);
        self::assertEquals(Response::HTTP_OK, self::$client->getResponse()->getStatusCode());
    }

    /**
     * Implements testGetRequestsError
     * @covers ::getRequests
     */
    public function testGetRequestsError(): void{
        self::$client->request(Request::METHOD_GET, ApiRequestController::REQUEST_API_PATH . '/' . self::$usernameError);
        self::assertEquals(Response::HTTP_NOT_FOUND, self::$client->getResponse()->getStatusCode());
        $body = self::$client->getResponse()->getContent();
        self::assertJson($body);

        $message = json_decode($body, true);
        self::assertArrayHasKey('code', $message);
        self::assertArrayHasKey('message', $message);
        self::assertEquals(Response::HTTP_NOT_FOUND, $message['code']);
    }
}