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
     * @Route(path="/{eventId}", name="get", methods={"GET"})
     */
    public function getGifts($eventId): Response{

        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository(Event::class)->find($eventId);
        $gifts = $event->getGifts()->getValues();

        return (empty($gifts))
            ? $this->error404()
            : new JsonResponse(['gifts'=> $gifts],
                Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(path="", name="post", methods={"POST"})
     */
    public function postGifts(Request $request): Response {
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

    private function error404(): JsonResponse{
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