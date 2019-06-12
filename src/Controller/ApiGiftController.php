<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Gift;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiGiftController
 * @package App\Controller
 * @Route(path=ApiGiftController::GIFT_API_PATH, name="api_gift_")
 */
class ApiGiftController extends AbstractController {

    public const GIFT_API_PATH = '/api/v1/gifts';

    /**
     * @param $eventId
     * @return Response
     * @Route(path="/event/{eventId}", name="getGifts", methods={"GET"})
     */
    public function getGifts(int $eventId): Response{

        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository(Event::class)->find($eventId);
        $gifts = $event->getGifts()->getValues();

        return (empty($gifts))
            ? $this->error404event()
            : new JsonResponse(['gifts'=> $gifts],
                Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(path="", name="post", methods={"POST"})
     */
    public function postGift(Request $request): Response {
        $dataRequest = $request->getContent();
        $data = json_decode($dataRequest, true);

        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository(Event::class)->findOneBy(array('id' => $data['eventId']));

        if($event===null){
            return $this->error400();
        }else {
            $gift = new Gift(
                $data['name'],
                $data['description'],
                $data['price'],
                $event
            );

            $em->persist($gift);
            $em->flush();

            return new JsonResponse(
                ['gift' => $gift],
                Response::HTTP_CREATED
            );
        }
    }

    /**
     * @param $giftId
     * @return Response
     * @Route(path="/{giftId}", name="getGift", methods={"GET"})
     */
    public function getAGift(int $giftId): Response {
        $em = $this->getDoctrine()->getManager();
        $gift = $em->getRepository(Gift::class)->find($giftId);

        if($gift != null) {
            return new JsonResponse(['gift'=>$gift], Response::HTTP_OK);
        }
    }

    /**
     * @param int $giftId
     * @return Response
     * @Route(path="/{giftId}", name="options", methods={"OPTIONS"})
     */
    public function optionsGift(int $giftId): Response {
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository(Gift::class)->find($giftId);

        if($result != null){
            return new JsonResponse(null, 200, ['Allow'=> 'GET, PUT, DELETE, OPTIONS']);
        }
    }

    /**
     * @param int $giftId
     * @param Request $request
     * @return Response
     * @Route(path="/{giftId}", name="put", methods={"PUT"})
     */
    public function putGift(int $giftId, Request $request): Response {
        $em = $this->getDoctrine()->getManager();
        $gift = $em->getRepository(Gift::class)->find($giftId);

        if($gift != null) {
            $dataRequest = $request->getContent();
            $data = json_decode($dataRequest, true);

            $gift->setName($data['name']);
            $gift->setDescription($data['description']);
            $gift->setPrice($data['price']);

            $em->persist($gift);
            $em->flush();

            return new JsonResponse(
                ['gift' => $gift], 209
            );
        }
    }

    /**
     * @param int $giftId
     * @return Response
     * @Route(path="/{giftId}", name="delete", methods={"DELETE"})
     */
    public function deleteGift(int $giftId): Response {
        $em = $this->getDoctrine()->getManager();
        $gift = $em->getRepository(Gift::class)->find($giftId);

        if($gift != null) {
            $em->remove($gift);
            $em->flush();

            return new JsonResponse([], Response::HTTP_NO_CONTENT);
        } else {
            return $this->error404();
        }
    }

    private function error404(): JsonResponse{
        $message = [
            'code' => Response::HTTP_NOT_FOUND,
            'message' => "El regalo no existe"
        ];
        return new JsonResponse($message, Response::HTTP_NOT_FOUND);
    }

    private function error404event(): JsonResponse{
        $message = [
            'code' => Response::HTTP_NOT_FOUND,
            'message' => "No hay regalos para el evento buscado"
        ];
        return new JsonResponse($message, Response::HTTP_NOT_FOUND);
    }

    private function error400(): JsonResponse{
        $message = [
            'code' => Response::HTTP_BAD_REQUEST,
            'message' => "Elige un evento"
        ];
        return new JsonResponse($message, Response::HTTP_BAD_REQUEST);
    }

}