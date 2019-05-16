<?php


namespace App\Tests\Controller;


use App\Controller\ApiPaymentController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiPaymentControllerTest extends WebTestCase{

    /**@var Client $client*/
    private static $client;

    public static function setUpBeforeClass(){
        self::$client = static::createClient();
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
}