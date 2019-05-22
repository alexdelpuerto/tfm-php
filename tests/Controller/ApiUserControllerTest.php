<?php

namespace App\Tests\Controller;

use App\Controller\ApiUserController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiUserControllerTest
 * @package App\Tests\Controller
 * @coversDefaultClass \App\Controller\ApiUserController
 */
class ApiUserControllerTest extends WebTestCase{

    /**@var Client $client*/
    private static $client;

    public static $searchUser;
    public static $searchUserError;

    public static function setUpBeforeClass(){
        self::$client = static::createClient();

        self::$searchUser = 'n';
        self::$searchUserError = '55';
    }

    /**
     * Implements @testLogin
     * @covers ::login
     */
    public function testLogin(): void {
        $data = [
            'username'=> 'user1',
            'password'=> 'user1'
        ];

        self::$client->request(Request::METHOD_POST, ApiUserController::USER_API_PATH . ApiUserController::LOGIN,
            [], [], [], json_encode($data));
        self::assertEquals(Response::HTTP_OK, self::$client->getResponse()->getStatusCode());

        $body = self::$client->getResponse()->getContent();
        self::assertJson($body);
        self::assertEquals(Response::HTTP_OK, self::$client->getResponse()->getStatusCode());
    }

    /**
     * Implements @testLoginError
     * @covers ::login
     */
    public function testLoginError(): void {
        //Test user with invalid pass
        $data1 = [
            'username'=> 'user1',
            'password'=> 'user'
        ];
        //Test user not found
        $data2 = [
            'username'=> 'user',
            'password'=> 'user'
        ];

        self::$client->request(Request::METHOD_POST, ApiUserController::USER_API_PATH . ApiUserController::LOGIN,
            [], [], [], json_encode($data1));
        self::assertEquals(Response::HTTP_NOT_FOUND, self::$client->getResponse()->getStatusCode());

        $body = self::$client->getResponse()->getContent();
        self::assertJson($body);

        $message = json_decode($body, true);
        self::assertArrayHasKey('code', $message);
        self::assertArrayHasKey('message', $message);
        self::assertEquals(Response::HTTP_NOT_FOUND, $message['code']);
    }

    /**
     * Implements testRegister
     * @return int
     * @covers ::register
     */
    public function testRegister(): int {
        $data = [
            'username'=> 'user',
            'password'=>'user',
            'name'=>'user',
            'surname'=>'u',
            'event'=>null
        ];

        self::$client->request(Request::METHOD_POST, ApiUserController::USER_API_PATH,
            [],[],[],json_encode($data));
        self::assertEquals(Response::HTTP_CREATED, self::$client->getResponse()->getStatusCode());

        $body = self::$client->getResponse()->getContent();
        $dataDecoder = json_decode($body, true);
        return $dataDecoder['user']['id'];
    }

    /**
     * Implements testRegisterError
     * @covers ::register
     */
    public function testRegisterError(): void{
        $data = [
            'username'=> 'user',
            'password'=>'user',
            'name'=>'user',
            'surname'=>'u',
            'event'=> null
        ];

        self::$client->request(Request::METHOD_POST, ApiUserController::USER_API_PATH,
            [],[],[],json_encode($data));
        self::assertEquals(Response::HTTP_BAD_REQUEST, self::$client->getResponse()->getStatusCode());
    }

    /**
     * Implements testSearchUsers
     * @covers ::searchUsers
     */
    public function testSearchUsers(): void {
        self::$client->request(Request::METHOD_GET, ApiUserController::USER_API_PATH . '/' . self::$searchUser);
        $body = self::$client->getResponse()->getContent();
        self::assertJson($body);

        $data = json_decode($body, true);
        self::assertArrayHasKey('users', $data);
        self::assertEquals(Response::HTTP_OK, self::$client->getResponse()->getStatusCode());
    }

    public function testSearchUsersError(): void{
        self::$client->request(Request::METHOD_GET, ApiUserController::USER_API_PATH . '/' . self::$searchUserError);
        self::assertEquals(Response::HTTP_NOT_FOUND, self::$client->getResponse()->getStatusCode());
        $body = self::$client->getResponse()->getContent();
        self::assertJson($body);

        $message = json_decode($body, true);
        self::assertArrayHasKey('code', $message);
        self::assertArrayHasKey('message', $message);
        self::assertEquals(Response::HTTP_NOT_FOUND, $message['code']);
    }
}