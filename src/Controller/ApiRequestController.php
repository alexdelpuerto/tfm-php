<?php

namespace App\Controller;

use App\Entity\Friendrequest;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiRequestController
 * @package App\Controller
 * @Route(path=ApiRequestController::REQUEST_API_PATH, name="api_request_")
 */
class ApiRequestController extends AbstractController {

    public const REQUEST_API_PATH = '/api/v1/requests';

    /**
     * @param $username
     * @return Response
     * @Route(path="/{username}", name="get", methods={"GET"})
     */
    public function getRequests(string $username): Response {
        $em = $this->getDoctrine()->getManager();
        $friendrequests = $em->getRepository(Friendrequest::class)->findBy(array('userReceive' => $username));

        return (empty($friendrequests))
            ? $this->error404()
            : new JsonResponse(['requests' => $friendrequests], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(path="", name="post", methods={"POST"})
     */
    public function postRequest(Request $request): Response {
        $dataRequest = $request->getContent();
        $data = json_decode($dataRequest, true);

        $em = $this->getDoctrine()->getManager();
        $requestExists = $em->getRepository(Friendrequest::class)->findOneBy(array('userSend' => $data['userSend'],
            'userReceive' => $data['userReceive']));

        if ($requestExists != null) {
            return $this->error400();
        } else {
            $friendRequest = new Friendrequest(
                $data['userSend'],
                $data['userReceive']
            );

            $em->persist($friendRequest);
            $em->flush();

            return new JsonResponse(
                ['request' => $friendRequest],
                Response::HTTP_CREATED
            );
        }
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(path="/accept", name="accept", methods={"POST"})
     */
    public function acceptRequest(Request $request): Response{
        $dataRequest = $request->getContent();
        $data = json_decode($dataRequest, true);

        $em = $this->getDoctrine()->getManager();
        $userExist = $em->getRepository(User::class)->findOneBy(array('username' => $data['userSend']));
        $userReceive = $em->getRepository(User::class)->findOneBy(array('username' => $data['userReceive']));

        if($userExist == null){
            return $this->error404user();
        } else {
            $userExist->addFriend($userReceive);
            $em->persist($userExist);
            $em->flush();

            $friendRequest = $em->getRepository(Friendrequest::class)->
                findOneBy(array('userSend' => $data['userSend'], 'userReceive' => $data['userReceive']));

            $requestId = $friendRequest->getId();

            $this->cancelRequest($requestId);

            return new JsonResponse(
                [],Response::HTTP_CREATED
            );
        }
    }

    /**
     * @param int $requestId
     * @return Response
     * @Route(path="/{requestId}", name="cancel", methods={"DELETE"})
     */
    public function cancelRequest(int $requestId): Response{
        $em = $this->getDoctrine()->getManager();
        $friendRequest = $em->getRepository(Friendrequest::class)->find($requestId);

        $em->remove($friendRequest);
        $em->flush();

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    private function error404(): JsonResponse{
        $message = [
            'code' => Response::HTTP_NOT_FOUND,
            'message' => "No hay solicitudes para el usuario buscado"
        ];
        return new JsonResponse($message, Response::HTTP_NOT_FOUND);
    }

    private function error400(): JsonResponse{
        $message = [
            'code' => Response::HTTP_BAD_REQUEST,
            'message' => "Ya se le ha enviado una solicitud de amistad"
        ];
        return new JsonResponse($message, Response::HTTP_BAD_REQUEST);
    }

    private function error404user(): JsonResponse{
        $message = [
            'code' => Response::HTTP_NOT_FOUND,
            'message' => "No existe el usuario"
        ];
        return new JsonResponse($message, Response::HTTP_NOT_FOUND);
    }
}