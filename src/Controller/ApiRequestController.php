<?php


namespace App\Controller;


use App\Entity\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
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
    public function getRequests($username): Response {
        $em = $this->getDoctrine()->getManager();
        $requests = $em->getRepository(Request::class)->findBy(array('userReceive' => $username));

        return (empty($requests))
            ? $this->error404()
            : new JsonResponse(['requests' => $requests], Response::HTTP_OK);
    }

    private function error404(): JsonResponse{
        $message = [
            'code' => Response::HTTP_NOT_FOUND,
            'message' => "No hay solicitudes para el usuario buscado"
        ];
        return new JsonResponse($message, Response::HTTP_NOT_FOUND);
    }

}