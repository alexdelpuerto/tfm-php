<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiEventController
 * @package App\Controller
 * @Route(path=ApiEventController::EVENT_API_PATH, name="api_event_")
 */
class ApiEventController extends AbstractController {

    public const EVENT_API_PATH = '/api/v1/events';

    /**
     * @param int $userId
     * @return Response
     * @Route(path="/{userId}", name="get", methods={"GET"})
     */
    public function getEvents($userId): Response {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($userId);
        $events = $user->getEvent()->getValues();

        return (empty($events))
            ? $this->error404()
            : new JsonResponse(['events'=> $events],
                Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(path="", name="post", methods={"POST"})
     */
    public function postEvents(Request $request):Response{
        $dataRequest = $request->getContent();
        $data = json_decode($dataRequest, true);

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(array('username' => $data['creator']));

        if($user===null){
            return $this->error400();
        }else {
            $event = new Event(
                $data['name'],
                $data['budget'],
                $data['creator']
            );
            $event->addUser($user);
            $user->addEvent($event);

            $em->persist($event);
            $em->persist($user);
            $em->flush();

            return new JsonResponse(
                ['event' => $event],
                Response::HTTP_CREATED
            );
        }
    }

    private function error404(): JsonResponse{
        $message = [
            'code' => Response::HTTP_NOT_FOUND,
            'message' => "No hay eventos para el usuario buscado"
        ];
        return new JsonResponse($message, Response::HTTP_NOT_FOUND);
    }

    private function error400(): JsonResponse{
        $message = [
            'code' => Response::HTTP_BAD_REQUEST,
            'message' => "Bad Request"
        ];
        return new JsonResponse($message, Response::HTTP_BAD_REQUEST);
    }
}