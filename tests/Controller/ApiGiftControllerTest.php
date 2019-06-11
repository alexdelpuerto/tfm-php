<?php

namespace App\Tests\Controller;

use App\Controller\ApiGiftController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiGiftControllerTest extends WebTestCase {

    /**@var Client $client*/
    private static $client;

    public static $eventId;
    public static $eventIdError;
    public static $giftId;


    public static function setUpBeforeClass(){
        self::$client = static::createClient();
        self::$eventId = 2;
        self::$eventIdError = 6;
        self::$giftId = 6;
    }

    /**
     * Implements testGetGifts
     * @covers ::getGifts
     */
    public function testGetGifts(): void{
        self::$client->request(Request::METHOD_GET, ApiGiftController::GIFT_API_PATH . '/event/' . self::$eventId);
        $body = self::$client->getResponse()->getContent();
        self::assertJson($body);

        $data = json_decode($body, true);
        self::assertArrayHasKey('gifts', $data);
        self::assertEquals(Response::HTTP_OK, self::$client->getResponse()->getStatusCode());
    }

    /**
     * Implements testGetGiftsError
     * @covers ::getGifts
     */
    public function testGetGiftsError(): void{
        self::$client->request(Request::METHOD_GET, ApiGiftController::GIFT_API_PATH . '/event/' . self::$eventIdError);
        self::assertEquals(Response::HTTP_NOT_FOUND, self::$client->getResponse()->getStatusCode());
        $body = self::$client->getResponse()->getContent();
        self::assertJson($body);

        $message = json_decode($body, true);
        self::assertArrayHasKey('code', $message);
        self::assertArrayHasKey('message', $message);
        self::assertEquals(Response::HTTP_NOT_FOUND, $message['code']);
    }

    /**
     * Implements testPostGift
     * @return int
     * @covers ::postGift
     */
    public function testPostGift():int{
        $data = [
            'name'=>'Regalo prueba',
            'price'=>13.2,
            'description'=>'Descripcion',
            'eventId'=>2
        ];

        self::$client->request(Request::METHOD_POST, ApiGiftController::GIFT_API_PATH,
            [], [], [], json_encode($data));
        self::assertEquals(Response::HTTP_CREATED, self::$client->getResponse()->getStatusCode());

        $body = self::$client->getResponse()->getContent();
        $dataDecoder = json_decode($body, true);
        return $dataDecoder['gift']['id'];
    }

    /**
     * Implements testPostGiftError
     * @covers ::postGift
     */
    public function testPostGiftError(): void{
        $data = [
            'name'=>'Regalo prueba123',
            'price'=>13.2,
            'description'=>null,
            'eventId'=>null
        ];

        self::$client->request(Request::METHOD_POST, ApiGiftController::GIFT_API_PATH,
            [], [], [], json_encode($data));
        self::assertEquals(Response::HTTP_BAD_REQUEST, self::$client->getResponse()->getStatusCode());
    }

    /**
     * Implements testGetAGift
     * @covers ::getAGift
     */
    public function testGetAGift(): void {
        self::$client->request(Request::METHOD_GET, ApiGiftController::GIFT_API_PATH . '/' . self::$giftId);
        self::assertEquals(Response::HTTP_OK, self::$client->getResponse()->getStatusCode());
        $body = self::$client->getResponse()->getContent();
        self::assertJson($body);

        $data = json_decode($body, true);
        self::assertArrayHasKey('gift', $data);
        self::assertEquals(self::$giftId, $data['gift']['id']);
        self::assertEquals(Response::HTTP_OK, self::$client->getResponse()->getStatusCode());
    }

    /**
     * Implements testOptionsGift
     * @covers ::optionsGift
     */
    public function testOptionsGift(): void {
        self::$client->request(Request::METHOD_OPTIONS, ApiGiftController::GIFT_API_PATH . '/' . self::$giftId);
        $head = self::$client->getResponse()->headers->get("Allow");
        self::assertEquals($this->optionsGifts(), $head);
    }

    /**
     * Implements testPutGift
     * @covers ::putGift
     */
    public function testPutGift(): int {
        $data = [
            'name'=> 'regalo6',
            'description'=>'descripcion',
            'price'=>2.5,
        ];

        self::$client->request(Request::METHOD_PUT, ApiGiftController::GIFT_API_PATH . '/' . self::$giftId,
            [],[],[],json_encode($data));
        self::assertEquals(209, self::$client->getResponse()->getStatusCode());

        $body = self::$client->getResponse()->getContent();
        $dataDecoder = json_decode($body, true);
        return $dataDecoder['gift']['id'];
    }

    private function optionsGifts(): string {
        return
            Request::METHOD_GET . ', ' .
            Request::METHOD_PUT . ', ' .
            Request::METHOD_DELETE . ', ' .
            Request::METHOD_OPTIONS;
    }
}