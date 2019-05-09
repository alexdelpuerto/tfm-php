<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiUserController
 * @package App\Controller
 * @Route(path=ApiUserController::USER_API_PATH, name="api_user_")
 */
class ApiUserController extends AbstractController {

    public const USER_API_PATH = '/api/v1/users';
    public const LOGIN = '/login';

    /**
     * @param Request $request
     * @return Response
     * @Route(path="/login", name="login", methods={"POST"})
     */
    public function login(Request $request): Response{
        $dataRequest = $request->getContent();
        $data = json_decode($dataRequest, true);

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(array('username' => $data['username'], 'password' => $data['password']));

        return ($user === null)
            ? $this->error404()
            : new JsonResponse(
                ['userId'=>$user->getId()],
                Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(path="", name="register", methods={"POST"})
     */
    public function register(Request $request): Response{
        $dataRequest = $request->getContent();
        $data = json_decode($dataRequest, true);

        $user = new User(
            $data['username'],
            $data['password'],
            $data['name'],
            $data['surname'],
            $data['event']
        );

        $em = $this->getDoctrine()->getManager();
        $usernameExist = $em->getRepository(User::class)->findOneBy(array('username' => $data['username']));

        if($usernameExist !== null){
            return $this->error400();
        }
        else{
            $em->persist($user);
            $em->flush();

            return new JsonResponse(
                ['user'=>$user],
                Response::HTTP_CREATED
            );
        }
    }

    public function error404(): JsonResponse{
        $message = [
            'code' => Response::HTTP_NOT_FOUND,
            'message' => "El usuario no existe o la contraseña es incorrecta"
        ];
        return new JsonResponse($message, Response::HTTP_NOT_FOUND);
    }

    private function error400(): JsonResponse{
        $message = [
            'code' => Response::HTTP_BAD_REQUEST,
            'message' => 'El nombre de usuario ya existe'
        ];
        return new JsonResponse($message, Response::HTTP_BAD_REQUEST);
    }
}