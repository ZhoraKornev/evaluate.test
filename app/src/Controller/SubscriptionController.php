<?php

namespace App\Controller;

use App\DTO\FondyPaymentDTO;
use App\Repository\SubscriptionTypeRepository;
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
     * SubscriptionController constructor.
     *
     * @param SubscriptionTypeRepository $subscriptionTypeRepository
     */
    public function __construct(SubscriptionTypeRepository $subscriptionTypeRepository) {
        $this->subscriptions = $subscriptionTypeRepository;

    }
    #[Route('/subscriptions/plan', name:'subscription_plan', methods:['GET'])]
    public function plans():Response
    {
        return $this->json($this->subscriptions->findAll(), Response::HTTP_OK, [],
            [AbstractNormalizer::ATTRIBUTES => ['price', 'name', 'period', 'contents' => ['name']]]);
    }

    #[Route('/subscription/pay', name:'confirm_subscription', methods:['POST'])]
    public function pay(
        FondyPaymentDTO $paymentDTO
    ):Response
    {
        return $this->json([
            'message' => $paymentDTO->order_id,
        ]);
    }
}
