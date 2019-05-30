<?php


namespace App\Tests\Controller;


use App\Controller\ApiPaymentController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiPaymentControllerTest extends WebTestCase{

    /**@var Client $client*/
    private static $client;

    public static $username;
    public static $usernameError;
    public static $paymentId;

    public static function setUpBeforeClass(){
        self::$client = static::createClient();
        self::$username = 'user7';
        self::$usernameError = 'user75';
        self::$paymentId = 13;
    }

    /**
     * Implements testPostPayments
     * @covers ::postPayments
     */
    public function testPostPayments():void{
        $data = [
            'giftId'=>6,
            'buyer'=>'user2',
        ];

        self::$client->request(Request::METHOD_POST, ApiPaymentController::PAYMENT_API_PATH,
            [], [], [], json_encode($data));
        self::assertEquals(Response::HTTP_CREATED, self::$client->getResponse()->getStatusCode());
    }

    /**
     * Implements testGetCollections
     * @covers ::getCollections
     */
    public function testGetCollections(): void {
        self::$client->request(Request::METHOD_GET, ApiPaymentController::PAYMENT_API_PATH . 'Col/' . self::$username);
        $body = self::$client->getResponse()->getContent();
        self::assertJson($body);

        $data = json_decode($body, true);
        self::assertArrayHasKey('collections', $data);
        self::assertEquals(Response::HTTP_OK, self::$client->getResponse()->getStatusCode());
    }

    /**
     * Implements testGetCollectionsError
     * @covers ::getCollections
     */
    public function testGetCollectionsError(): void {
        self::$client->request(Request::METHOD_GET, ApiPaymentController::PAYMENT_API_PATH . 'Col/' . self::$usernameError);
        self::assertEquals(Response::HTTP_NOT_FOUND, self::$client->getResponse()->getStatusCode());
        $body = self::$client->getResponse()->getContent();
        self::assertJson($body);

        $message = json_decode($body, true);
        self::assertArrayHasKey('code', $message);
        self::assertArrayHasKey('message', $message);
        self::assertEquals(Response::HTTP_NOT_FOUND, $message['code']);
    }

    /**
     * Implements testGetPayments
     * @covers ::getPayments
     */
    public function testGetPayments(): void {
        self::$client->request(Request::METHOD_GET, ApiPaymentController::PAYMENT_API_PATH . '/' . self::$username);
        $body = self::$client->getResponse()->getContent();
        self::assertJson($body);

        $data = json_decode($body, true);
        self::assertArrayHasKey('payments', $data);
        self::assertEquals(Response::HTTP_OK, self::$client->getResponse()->getStatusCode());
    }

    /**
     * Implements testGetPaymentsError
     * @covers ::getPayments
     */
    public function testGetPaymentsError(): void {
        self::$client->request(Request::METHOD_GET, ApiPaymentController::PAYMENT_API_PATH . '/' . self::$usernameError);
        self::assertEquals(Response::HTTP_NOT_FOUND, self::$client->getResponse()->getStatusCode());
        $body = self::$client->getResponse()->getContent();
        self::assertJson($body);

        $message = json_decode($body, true);
        self::assertArrayHasKey('code', $message);
        self::assertArrayHasKey('message', $message);
        self::assertEquals(Response::HTTP_NOT_FOUND, $message['code']);
    }

    /**
     * Implements testDeletePayments
     * @covers ::deletePayments
     */
    public function testDeletePayments(): void{
        self::$client->request(Request::METHOD_DELETE, ApiPaymentController::PAYMENT_API_PATH . '/' . self::$paymentId);
        self::assertEquals(Response::HTTP_NO_CONTENT, self::$client->getResponse()->getStatusCode());
    }
}