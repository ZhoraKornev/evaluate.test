<?php

namespace App\Controller\API;

use App\DTO\FondyPaymentDTO;
use App\DTO\NewSubscriptionRequestDTO;
use App\Repository\SubscriptionTypeRepository;
use App\Repository\SubscriptionUserRepository;
use App\Service\UserSubscriptionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

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
     * @var UserSubscriptionService
     */
    private UserSubscriptionService $userSubscriptionService;

    /**
     * SubscriptionController constructor.
     *
     * @param SubscriptionTypeRepository $subscriptionTypeRepository
     * @param UserSubscriptionService    $userSubscriptionService
     */
    public function __construct(SubscriptionTypeRepository $subscriptionTypeRepository,UserSubscriptionService $userSubscriptionService) {
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
    public function pay(
        FondyPaymentDTO $paymentDTO
    ):Response
    {
        $this->userSubscriptionService->payUserSubscription($paymentDTO);
        return $this->json([
            'result' => $this->userSubscriptionService->payUserSubscription($paymentDTO),
        ]);
    }

    #[Route('/new', name:'api_new_subscription', methods:['PATCH'])]
    public function newSubscription(
        NewSubscriptionRequestDTO $requestDTO,
        UserSubscriptionService $service
    )
    :Response {
        $service->create($requestDTO);

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ApiController.php',
        ]);
    }

    #[Route('/user', name:'api_user_subscriptions', methods:['GET'])]
    public function userSubscriptions(
        SubscriptionUserRepository $userSubscriptions
    ):Response {
        return $this->json(
            $userSubscriptions->findBy(['user' => $this->getUser()]),
            Response::HTTP_OK,
            [],
            [
                AbstractNormalizer::ATTRIBUTES => [
                    'active',
                    'validDue',
                    'createdAt',
                    'activateAt',
                    'id',
                    'subscription' => ['name', 'contents' => ['name', 'description']]
                ]
            ]);
    }
}
