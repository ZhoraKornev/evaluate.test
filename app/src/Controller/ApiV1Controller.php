<?php

namespace App\Controller;

use App\DTO\NewSubscriptionRequestDTO;
use App\Repository\SubscriptionUserRepository;
use App\Service\UserSubscriptionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * @Route("/api/v1", name="api_v1")
 */
class ApiV1Controller extends AbstractController
{
    #[Route('/subscription', name:'api_new_subscription', methods:['PATCH'])]
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

    #[Route('/subscription', name:'api_user_subscriptions', methods:['GET'])]
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
