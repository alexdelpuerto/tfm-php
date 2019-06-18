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
    public static $requestId;

    public static function setUpBeforeClass(){
        self::$client = static::createClient();
        self::$username = 'user7';
        self::$usernameError = 'asdf';
        self::$requestId = 5;
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

    /**
     * Implements testPostRequest
     * @covers ::postRequest
     */
    public function testPostRequest(): int {
        $data = [
            'userSend'=>'user7',
            'userReceive'=>'user6'
        ];

        self::$client->request(Request::METHOD_POST, ApiRequestController::REQUEST_API_PATH,
            [], [], [], json_encode($data));
        self::assertEquals(Response::HTTP_CREATED, self::$client->getResponse()->getStatusCode());

        $body = self::$client->getResponse()->getContent();
        $dataDecoder = json_decode($body, true);
        return $dataDecoder['request']['id'];
    }

    /**
     * Implements testPostRequestError
     * @covers ::postRequest
     */
    public function testPostRequestError(): void {
        $data = [
            'userSend'=>'user7',
            'userReceive'=>'user6'
        ];

        self::$client->request(Request::METHOD_POST, ApiRequestController::REQUEST_API_PATH,
            [], [], [], json_encode($data));
        self::assertEquals(Response::HTTP_BAD_REQUEST, self::$client->getResponse()->getStatusCode());
    }

    /**
     * Implements testAcceptRequest
     * @covers ::acceptRequest
     */
    public function testAcceptRequest(): void {
        $data = [
            'userSend'=>'user7',
            'userReceive'=>'user6'
        ];

        self::$client->request(Request::METHOD_POST, ApiRequestController::REQUEST_API_PATH . '/accept',
            [], [], [], json_encode($data));
        self::assertEquals(Response::HTTP_CREATED, self::$client->getResponse()->getStatusCode());
    }

    /**
     * Implements testAcceptRequestError
     * @covers ::acceptRequest
     */
    public function testAcceptRequestError(): void {
        $data = [
            'userSend'=>'user12345',
            'userReceive'=>'user7'
        ];

        self::$client->request(Request::METHOD_POST, ApiRequestController::REQUEST_API_PATH . '/accept',
            [], [], [], json_encode($data));
        self::assertEquals(Response::HTTP_NOT_FOUND, self::$client->getResponse()->getStatusCode());
    }

    /**
     * Implements testCancelRequest
     * @covers ::cancelRequest
     */
    public function testCancelRequest(): void {
        self::$client->request(Request::METHOD_DELETE, ApiRequestController::REQUEST_API_PATH . '/' . self::$requestId);
        self::assertEquals(Response::HTTP_NO_CONTENT, self::$client->getResponse()->getStatusCode());
    }

    /**
     * Implements testOptionsRequest
     * @covers ::optionsRequest
     */
    public function testOptionsRequest(): void {
        self::$client->request(Request::METHOD_OPTIONS, ApiRequestController::REQUEST_API_PATH . '/' . self::$requestId);
        $head = self::$client->getResponse()->headers->get("Allow");
        self::assertEquals($this->optionsRequest(), $head);
    }

    public function optionsRequest():string {
        return
            Request::METHOD_GET . ', ' .
            Request::METHOD_PUT . ', ' .
            Request::METHOD_DELETE . ', ' .
            Request::METHOD_OPTIONS;
    }
}