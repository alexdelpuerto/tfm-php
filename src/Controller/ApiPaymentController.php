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
        $gift = $em->getRepository(Gift::class)->findOneBy(array('name' => $data['giftname']));

        $price = $gift->getPrice();

        $event = $gift->getEvent();

        $numUsers = $event->getUser()->count();

        for($i=0; $i<$numUsers; $i++){
            if($event->getUser()[$i]->getUsername()!=$data['buyer']){
                $payment = new Payment(
                    $data['buyer'],
                    $event->getUser()[$i]->getUsername(),
                    $price/$numUsers,
                    $gift->getName()
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
     * @Route(path="Col/{username}", name="getCollections", methods={"GET"})
     */
    public function getCollections($username): Response {
        $em = $this->getDoctrine()->getManager();
        $collection = $em->getRepository(Payment::class)->findBy(array('buyer' => $username));

        return (empty($collection))
            ? $this->error404()
            : new JsonResponse(
                ['collections'=>$collection], Response::HTTP_OK);
    }

    /**
     * @param $username
     * @return Response
     * @Route(path="/{username}", name="getPayments", methods={"GET"})
     */
    public function getPayments($username): Response {
        $em = $this->getDoctrine()->getManager();
        $payment = $em->getRepository(Payment::class)->findBy(array('person' => $username));

        return (empty($payment))
            ? $this->error404p()
            : new JsonResponse(
                ['payments'=>$payment], Response::HTTP_OK);
    }

    /**
     * @param int $paymentId
     * @return Response
     * @Route(path="/{paymentId}", name="options", methods={"OPTIONS"})
     */
    public function optionsPayment(int $paymentId): Response {
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository(Payment::class)->find($paymentId);

        if($result != null){
            return new JsonResponse(null, 200, ['Allow'=> 'GET, PUT, DELETE, OPTIONS']);
        }
    }

    /**
     * @param int $paymentId
     * @return Response
     * @Route(path="/{paymentId}", name="delete", methods={"DELETE"})
     */
    public function deletePayment(int $paymentId): Response {
        $em = $this->getDoctrine()->getManager();
        $collection = $em->getRepository(Payment::class)->find($paymentId);

        $em->remove($collection);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    private function error404(): JsonResponse{
        $message = [
            'code' => Response::HTTP_NOT_FOUND,
            'message' => "El usuario no tiene cobros pendientes"
        ];
        return new JsonResponse($message, Response::HTTP_NOT_FOUND);
    }

    private function error404p(): JsonResponse{
        $message = [
            'code' => Response::HTTP_NOT_FOUND,
            'message' => "El usuario no tiene pagos pendientes"
        ];
        return new JsonResponse($message, Response::HTTP_NOT_FOUND);
    }

}