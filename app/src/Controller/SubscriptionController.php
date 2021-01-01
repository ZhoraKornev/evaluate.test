<?php

namespace App\Controller;

use App\Repository\SubscriptionTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends AbstractController
{
    /**
     * @var SubscriptionTypeRepository
     */
    private SubscriptionTypeRepository $subscriptions;

    /**
     * SubscriptionController constructor.
     */
    public function __construct(SubscriptionTypeRepository $subscriptionTypeRepository) {
        $this->subscriptions = $subscriptionTypeRepository;

    }
    #[Route('/subscriptions/plan', name: 'subscription_plan',methods: ['GET'])]
    public function plans(): Response
    {
        $subscriptionPlans = $this->subscriptions->findAll();
        dd($subscriptionPlans);
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/SubscriptionController.php',
        ]);
    }

    #[Route('/subscription/pay', name: 'confirm_subscription',methods: ['GET'])]
    public function pay(Request $request): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/SubscriptionController.php',
        ]);
    }
}
