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
     * @Route(path="/user/{userId}", name="getEvents", methods={"GET"})
     */
    public function getEvents(int $userId): Response {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($userId);
        $events = $user->getEvent()->getValues();

        return (empty($events))
            ? $this->error404user()
            : new JsonResponse(['events'=> $events],
                Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(path="", name="post", methods={"POST"})
     */
    public function postEvent(Request $request):Response{
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

    /**
     * @param int $eventId
     * @return Response
     * @Route(path="/{eventId}", name="getEvent", methods={"GET"})
     */
    public function getAEvent(int $eventId): Response {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository(Event::class)->find($eventId);

        if($event != null){
            return new JsonResponse(['event' => $event], Response::HTTP_OK);
        }
    }

    /**
     * @param int $eventId
     * @return Response
     * @Route(path="/{eventId}", name="options", methods={"OPTIONS"})
     */
    public function optionsEvent(int $eventId): Response {
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository(Event::class)->find($eventId);

        if($result != null){
            return new JsonResponse(null, 200, ['Allow'=> 'GET, PUT, DELETE, OPTIONS']);
        }
    }

    /**
     * @param int $eventId
     * @param Request $request
     * @return Response
     * @Route(path="/{eventId}", name="put", methods={"PUT"})
     */
    public function putEvent(int $eventId, Request $request): Response {
        $em= $this->getDoctrine()->getManager();
        $event = $em->getRepository(Event::class)->find($eventId);

        if($event != null) {
            $dataRequest = $request->getContent();
            $data = json_decode($dataRequest, true);

            $event->setName($data['name']);
            $event->setBudget($data['budget']);

            $em->persist($event);
            $em->flush();

            return new JsonResponse(
                ['event' => $event], 209
            );
        }
    }

    /**
     * @param int $eventId
     * @return Response
     * @Route(path="/{eventId}", name="delete", methods={"DELETE"})
     */
    public function deleteEvent(int $eventId): Response {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository(Event::class)->find($eventId);

        if($event != null){
            $em->remove($event);
            $em->flush();

            return new JsonResponse([], Response::HTTP_NO_CONTENT);
        } else {
            return $this->error404();
        }
    }

    private function error404user(): JsonResponse{
        $message = [
            'code' => Response::HTTP_NOT_FOUND,
            'message' => "No hay eventos para este usuario"
        ];
        return new JsonResponse($message, Response::HTTP_NOT_FOUND);
    }

    private function error404(): JsonResponse{
        $message = [
            'code' => Response::HTTP_NOT_FOUND,
            'message' => "El evento no existe"
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