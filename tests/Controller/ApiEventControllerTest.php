<?php

namespace App\Tests\Controller;

use App\Controller\ApiEventController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiEventControllerTest extends WebTestCase{

    /**@var Client $client*/
    private static $client;

    public static $userId;
    public static $userIdError;
    public static $eventId;
    public static $eventIdDelete;
    public static $eventIdError;


    public static function setUpBeforeClass(){
        self::$client = static::createClient();
        self::$userId = 9;
        self::$userIdError = 4;
        self::$eventId = 3;
        self::$eventIdDelete = 10;
        self::$eventIdError = 20;
    }

    /**
     * Implements testGetEvents
     * @covers ::getEvents
     */
    public function testGetEvents(): void{
        self::$client->request(Request::METHOD_GET, ApiEventController::EVENT_API_PATH . '/user/' . self::$userId);
        $body = self::$client->getResponse()->getContent();
        self::assertJson($body);

        $data = json_decode($body, true);
        self::assertArrayHasKey('events', $data);
        self::assertEquals(Response::HTTP_OK, self::$client->getResponse()->getStatusCode());
    }

    /**
     * Implements testGetEventsError
     * @covers ::getEvents
     */
    public function testGetEventsError(): void{
        self::$client->request(Request::METHOD_GET, ApiEventController::EVENT_API_PATH . '/user/' . self::$userIdError);
        self::assertEquals(Response::HTTP_NOT_FOUND, self::$client->getResponse()->getStatusCode());
        $body = self::$client->getResponse()->getContent();
        self::assertJson($body);

        $message = json_decode($body, true);
        self::assertArrayHasKey('code', $message);
        self::assertArrayHasKey('message', $message);
        self::assertEquals(Response::HTTP_NOT_FOUND, $message['code']);
    }

    /**
     * Implements testPostEvent
     * @return int
     * @covers ::postEvent
     */
    public function testPostEvent():int{
        $data = [
            'name'=>'Event prueba',
            'budget'=>13.2,
            'creator'=>'user1'
        ];

        self::$client->request(Request::METHOD_POST, ApiEventController::EVENT_API_PATH,
            [], [], [], json_encode($data));
        self::assertEquals(Response::HTTP_CREATED, self::$client->getResponse()->getStatusCode());

        $body = self::$client->getResponse()->getContent();
        $dataDecoder = json_decode($body, true);
        return $dataDecoder['event']['id'];
    }

    /**
     * Implements testPostEventError
     * @covers ::postEvent
     */
    public function testPostEventError(): void{
        $data = [
            'name'=>'Event prueba123',
            'budget'=>13.2,
            'creator'=>'user123'
        ];

        self::$client->request(Request::METHOD_POST, ApiEventController::EVENT_API_PATH,
            [], [], [], json_encode($data));
        self::assertEquals(Response::HTTP_BAD_REQUEST, self::$client->getResponse()->getStatusCode());
    }

    /**
     * Implements testGetAEvent
     * @covers ::getAEvent
     */
    public function testGetAEvent(): void {
        self::$client->request(Request::METHOD_GET, ApiEventController::EVENT_API_PATH . '/' . self::$eventId);
        self::assertEquals(Response::HTTP_OK, self::$client->getResponse()->getStatusCode());
        $body = self::$client->getResponse()->getContent();
        self::assertJson($body);

        $data = json_decode($body, true);
        self::assertArrayHasKey('event', $data);
        self::assertEquals(self::$eventId, $data['event']['id']);
        self::assertEquals(Response::HTTP_OK, self::$client->getResponse()->getStatusCode());
    }

    /**
     * Implements testOptionsEvent
     * @covers ::optionsEvent
     */
    public function testOptionsEvent(): void {
        self::$client->request(Request::METHOD_OPTIONS, ApiEventController::EVENT_API_PATH . '/' . self::$eventId);
        $head = self::$client->getResponse()->headers->get("Allow");
        self::assertEquals($this->optionsEvents(), $head);
    }

    /**
     * Implements testPutEvent
     * @covers ::putEvent
     */
    public function testPutEvent(): int {
        $data = [
            'name'=> 'event2',
            'budget'=>22.3
        ];

        self::$client->request(Request::METHOD_PUT, ApiEventController::EVENT_API_PATH . '/' . self::$eventId,
            [],[],[],json_encode($data));
        self::assertEquals(209, self::$client->getResponse()->getStatusCode());

        $body = self::$client->getResponse()->getContent();
        $dataDecoder = json_decode($body, true);
        return $dataDecoder['event']['id'];
    }

    /**
     * Implements testDeleteEvent
     * @covers ::deleteEvent
     */
    public function testDeleteEvent(): void {
        self::$client->request(Request::METHOD_DELETE, ApiEventController::EVENT_API_PATH . '/' . self::$eventIdDelete);
        self::assertEquals(Response::HTTP_NO_CONTENT, self::$client->getResponse()->getStatusCode());
    }

    /**
     * Implements testDeleteEventError
     * @covers ::deleteEvent
     */
    public function testDeleteEventError(): void{
        self::$client->request(Request::METHOD_DELETE, ApiEventController::EVENT_API_PATH . '/' . self::$eventIdError);
        self::assertEquals(Response::HTTP_NOT_FOUND, self::$client->getResponse()->getStatusCode());
    }

    private function optionsEvents(): string {
        return
            Request::METHOD_GET . ', ' .
            Request::METHOD_PUT . ', ' .
            Request::METHOD_DELETE . ', ' .
            Request::METHOD_OPTIONS;
    }
}