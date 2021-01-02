<?php

namespace App\Controller;

use App\DTO\FondyPaymentDTO;
use App\Repository\SubscriptionTypeRepository;
use App\Service\UserSubscriptionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class SubscriptionController extends AbstractController
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
     */
    public function __construct(SubscriptionTypeRepository $subscriptionTypeRepository,UserSubscriptionService $userSubscriptionService) {
        $this->subscriptions = $subscriptionTypeRepository;
        $this->userSubscriptionService = $userSubscriptionService;

    }

    #[Route('/subscriptions/plan', name:'subscription_plan', methods:['GET'])]
    public function plans():Response
    {
        return $this->json(
            $this->subscriptions->findAll(),
            Response::HTTP_OK,
            [],
            [AbstractNormalizer::ATTRIBUTES => ['price', 'name', 'period', 'id', 'contents' => ['name']]]
        );
    }

    #[Route('/subscription/pay', name:'confirm_subscription', methods:['POST'])]
    public function pay(
        FondyPaymentDTO $paymentDTO
    ):Response
    {
        $this->userSubscriptionService->payUserSubscription($paymentDTO);
        return $this->json([
            'result' => $this->userSubscriptionService->payUserSubscription($paymentDTO),
        ]);
    }
}
