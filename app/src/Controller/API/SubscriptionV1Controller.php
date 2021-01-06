<?php

namespace App\Controller\API;

use App\DTO\FondyPaymentDTO;
use App\DTO\NewSubscriptionRequestDTO;
use App\Repository\SubscriptionTypeRepository;
use App\Repository\SubscriptionUserRepository;
use App\Service\UserSubscriptionPaymentService;
use Doctrine\ORM\EntityNotFoundException;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Doctrine\ORM\EntityNotFoundException as ORMEntityNotFoundException;

/**
 * @Route("/api/v1/subscription", name="api_v1")
 */
class SubscriptionV1Controller extends AbstractController
{
    /**
     * @var SubscriptionTypeRepository
     */
    private SubscriptionTypeRepository $subscriptions;
    /**
     * @var UserSubscriptionPaymentService
     */
    private UserSubscriptionPaymentService $userSubscriptionService;

    /**
     * SubscriptionController constructor.
     *
     * @param SubscriptionTypeRepository     $subscriptionTypeRepository
     * @param UserSubscriptionPaymentService $userSubscriptionService
     */
    public function __construct(SubscriptionTypeRepository $subscriptionTypeRepository,UserSubscriptionPaymentService $userSubscriptionService) {
        $this->subscriptions = $subscriptionTypeRepository;
        $this->userSubscriptionService = $userSubscriptionService;

    }

    #[Route('/', name:'subscription_plan', methods:['GET'])]
    public function plans():Response
    {
        return $this->json(
            $this->subscriptions->findAll(),
            Response::HTTP_OK,
            [],
            [AbstractNormalizer::ATTRIBUTES => ['price', 'name', 'period', 'id', 'contents' => ['name']]]
        );
    }

    #[Route('/pay', name:'confirm_subscription', methods:['POST'])]
    public function pay(FondyPaymentDTO $paymentDTO):Response
    {
        try {
            return $this->json([
                'result' => $this->userSubscriptionService->payUserSubscription($paymentDTO),
            ], Response::HTTP_OK);
        } catch (LogicException | ORMEntityNotFoundException $e) {
            return $this->json([
                'status' => false,
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/new', name:'api_new_subscription', methods:['PATCH'])]
    public function newSubscription(
        NewSubscriptionRequestDTO $requestDTO,
        UserSubscriptionPaymentService $service
    ):Response {
        try {
            return $this->json([
                'status' => 'OK',
                'order_id' => $service->createOrder($requestDTO),
            ], Response::HTTP_OK);
        } catch (EntityNotFoundException| LogicException $e) {
            return $this->json([
                'status' => 'fail',
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/user', name:'api_user_subscriptions', methods:['GET'])]
    public function userSubscriptions(
        SubscriptionUserRepository $userSubscriptions
    ):Response {
        return $this->json(
            ['user' => $this->getUser(),'subscriptions' =>$userSubscriptions->findBy(['user' => $this->getUser()])],
            Response::HTTP_OK,
            [],
            [
                AbstractNormalizer::ATTRIBUTES => [
                    'active',
                    'validDue',
                    'createdAt',
                    'activateAt',
                    'id',
                    'email',
                    'subscription' => ['name', 'contents' => ['name', 'description']]
                ]
            ]);
    }
}
