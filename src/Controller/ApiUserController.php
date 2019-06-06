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
 * Class ApiUserController
 * @package App\Controller
 * @Route(path=ApiUserController::USER_API_PATH, name="api_user_")
 */
class ApiUserController extends AbstractController {

    public const USER_API_PATH = '/api/v1/users';

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
            ? $this->error404login()
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

    /**
     * @param $username
     * @return Response
     * @Route(path="/search/{username}", name="search", methods={"GET"})
     */
    public function searchUsers($username): Response{
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT u.id, u.username, u.name, u.surname FROM App\Entity\User u WHERE u.username LIKE :search');
        $query->setParameter('search', '%'.$username.'%');

        $users = $query->getResult();
        return (empty($users) || empty($username))
            ? $this->error404()
            : new JsonResponse(
                ['users'=>$query->getResult()], Response::HTTP_OK);
    }

    /**
     * @param $username
     * @return Response
     * @Route(path="/friends/{username}", name="getFriends", methods={"GET"})
     */
    public function getFriends($username): Response{
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(array('username' => $username));
        $friends = $user->getMyFriends()->getValues();
        $otherfriends = $user->getFriendsWithMe()->getValues();
        $totalFriends = array_merge($friends, $otherfriends);

        return (empty($totalFriends))
            ? $this->error404()
            : new JsonResponse(['friends'=> $totalFriends], Response::HTTP_OK);
    }

    /**
     * @param $eventId
     * @return Response
     * @Route(path="/event/{eventId}", name="getUsers", methods={"GET"})
     */
    public function getUsers($eventId): Response {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(Event::class)->find($eventId)->getUser()->getValues();

        return new JsonResponse(['users'=>$users], Response::HTTP_OK);
    }

    /**
     * @param int $eventId
     * @param Request $request
     * @return Response
     * @Route(path="/event/{eventId}", name="add", methods={"PUT"})
     */
    public function addUser(int $eventId, Request $request): Response {
        $em= $this->getDoctrine()->getManager();
        $event = $em->getRepository(Event::class)->find($eventId);

        $dataRequest = $request->getContent();
        $data = json_decode($dataRequest, true);

        $user = $em->getRepository(User::class)->find($data['id']);
        $event->addUser($user);
        $user->addEvent($event);

        $em->persist($event);
        $em->persist($user);
        $em->flush();

        return new JsonResponse([], 209);
    }

    /**
     * @param int $eventId
     * @return Response
     * @Route(path="/event/{eventId}", name="options", methods={"OPTIONS"})
     */
    public function optionsUsers(int $eventId): Response {
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository(Event::class)->find($eventId);

        if($result != null){
            return new JsonResponse(null, 200, ['Allow'=> 'GET, PUT, DELETE, OPTIONS']);
        }
    }

    /**
     * @param $userId
     * @return Response
     * @Route(path="/{userId}", name="getUser", methods={"GET"})
     */
    public function getAUser(int $userId): Response {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($userId);

        if($user != null) {
            return new JsonResponse(['user'=>$user], Response::HTTP_OK);
        }
    }

    /**
     * @param int $userId
     * @return Response
     * @Route(path="/{userId}", name="options", methods={"OPTIONS"})
     */
    public function optionsUser(int $userId): Response {
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository(User::class)->find($userId);

        if($result != null){
            return new JsonResponse(null, 200, ['Allow'=> 'GET, PUT, DELETE, OPTIONS']);
        }
    }

    /**
     * @param $userId
     * @param Request $request
     * @return Response
     * @Route(path="/{userId}", name="put", methods={"PUT"})
     */
    public function putUser(int $userId, Request $request): Response {
        $em= $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($userId);

        if($user != null) {
            $dataRequest = $request->getContent();
            $data = json_decode($dataRequest, true);

            $user->setPassword($data['password']);
            $user->setName($data['name']);
            $user->setSurname($data['surname']);

            $usernameExist = $em->getRepository(User::class)->findOneBy(array('username' => $data['username']));

            if($usernameExist !== null && $usernameExist->getUsername() !== $user->getUsername()){
                return $this->error400();
            } else{
                $user->setUsername($data['username']);
                $em->persist($user);
                $em->flush();

                return new JsonResponse(
                    ['user' => $user],
                    209
                );
            }
        }
    }

    public function error404login(): JsonResponse{
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

    private function error404(){
        $message = [
            'code' => Response::HTTP_NOT_FOUND,
            'message' => "No hay resultados"
        ];
        return new JsonResponse($message, Response::HTTP_NOT_FOUND);
    }
}