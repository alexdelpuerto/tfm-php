<?php

namespace App\Controller;

use App\Entity\Gift;
use App\Entity\Payment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiPaymentController
 * @package App\Controller
 * @Route(path=ApiPaymentController::PAYMENT_API_PATH, name="api_payment_")
 */
class ApiPaymentController extends AbstractController{

    public const PAYMENT_API_PATH = '/api/v1/payments';

    /**
     * @param Request $request
     * @return Response
     * @Route(path="", name="post", methods={"POST"})
     */
    public function postPayments(Request $request): Response{
        $dataRequest = $request->getContent();
        $data = json_decode($dataRequest, true);

        $em = $this->getDoctrine()->getManager();
        $gift = $em->getRepository(Gift::class)->findOneBy(array('id' => $data['giftId']));

        $price = $gift->getPrice();

        $event = $gift->getEvent();

        $numUsers = $event->getUser()->count();

        for($i=0; $i<$numUsers; $i++){
            if($event->getUser()[$i]->getUsername()!=$data['buyer']){
                $payment = new Payment(
                    $data['buyer'],
                    $event->getUser()[$i]->getUsername(),
                    $price/$numUsers
                );

                $em->persist($payment);
            }
        }

        $gift->setBought(true);
        $em->persist($gift);
        $em->flush();

        return new JsonResponse([],Response::HTTP_CREATED);
    }

    /**
     * @param $username
     * @return Response
     * @Route(path="/{username}", name="getCollections", methods={"GET"})
     */
    public function getCollections($username): Response {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT p.person, SUM(p.price) as totalPrice FROM App\Entity\Payment p WHERE p.buyer = ?1 GROUP BY p.person');
        $query->setParameter(1, $username);

        $results = $query->getResult();
        return (empty($results))
            ? $this->error404()
            : new JsonResponse(
                ['collections'=>$query->getResult()], Response::HTTP_OK);
    }

    private function error404(): JsonResponse{
        $message = [
            'code' => Response::HTTP_NOT_FOUND,
            'message' => "El usuario no tiene cobros pendientes"
        ];
        return new JsonResponse($message, Response::HTTP_NOT_FOUND);
    }

}